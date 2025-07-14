<?php

/**
 * Returns all the currently available global qty discounts
 *
 * @return array
 */
function fn_qty_discounts_extended_get_discounts(): array
{
    return db_get_array('SELECT * FROM ?:global_qty_discounts ORDER BY lower_limit');
}

/*
 * Updates the global qty discounts
 * Using the new information, updates all the product prices according to the give qty discounts information
 */
function fn_qty_discounts_extended_update_discounts($discounts): bool
{
    if (empty($discounts)) {
        return false;
    }

    foreach ($discounts as $key => $discount) {
        if (!empty($discount['lower_limit']) && !empty($discount['percentage_discount'])) {
            if ($discount['percentage_discount'] > 99) {
                fn_set_notification(
                    "E",
                    __('notice'),
                    'Percent cannot be more than 99'
                );
                continue;
            }

            db_replace_into('global_qty_discounts', $discount);

            continue;
        }
        // remove an empty entry
        unset($discounts[$key]);
    }

    fn_qty_discounts_set_product_prices($discounts);

    return true;
}

/*
 * Removes the global discount and all the associated prices
 */
function fn_qty_discounts_extended_delete_discount($discount_id)
{
    $discount_data = db_get_row('SELECT lower_limit, percentage_discount FROM ?:global_qty_discounts WHERE id = ?i', $discount_id);

    db_query("DELETE FROM ?:global_qty_discounts WHERE id = ?i", $discount_id);
    db_query("DELETE FROM ?:product_prices WHERE percentage_discount = ?d AND lower_limit = ?i", $discount_data['percentage_discount'], $discount_data['lower_limit']);
}

/*
 * Updates the prices of products based on the provided discounts data
 */
function fn_qty_discounts_set_product_prices($discounts)
{
    $base_prices = db_get_hash_array('
        SELECT product_id, price, percentage_discount, lower_limit
        FROM ?:product_prices 
        WHERE lower_limit = 1 AND usergroup_id = 0
    ', 'product_id');

    $existing_discounts = db_get_hash_array('
        SELECT product_id, lower_limit, percentage_discount
        FROM ?:product_prices 
        WHERE lower_limit > 1 AND usergroup_id = 0
    ', 'product_id');

    $rows_to_insert = [];

    foreach ($base_prices as $product_id => $product) {
        $price = $product['price'];

        if (
            isset($existing_discounts[$product_id])
            && $existing_discounts[$product_id]['lower_limit'] == $product['lower_limit']
            && $existing_discounts[$product_id]['percentage_discount'] == $product['percentage_discount']
        ) {
            continue;
        }
        foreach ($discounts as $discount_data) {
            $rows_to_insert[] = [
                $product_id,
                $price,
                $discount_data['percentage_discount'],
                $discount_data['lower_limit'],
                0
            ];
        }
    }

    $chunk_size = 500;
    foreach (array_chunk($rows_to_insert, $chunk_size) as $chunk) {
        $values = [];

        foreach ($chunk as $row) {
            $values[] = db_quote("(?i, ?d, ?d, ?i, ?i)", ...$row);
        }

        if (!empty($values)) {
            db_query("
            INSERT INTO ?:product_prices 
                (product_id, price, percentage_discount, lower_limit, usergroup_id)
            VALUES " . implode(', ', $values) . "
            ON DUPLICATE KEY UPDATE 
                price = VALUES(price),
                percentage_discount = VALUES(percentage_discount)
        ");
        }
    }
}