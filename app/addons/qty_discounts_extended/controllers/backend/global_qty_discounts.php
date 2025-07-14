<?php

defined('BOOTSTRAP') or die('Access denied');
/* @var $mode */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update') {
        $discounts = $_REQUEST['discounts'];

        fn_qty_discounts_extended_update_discounts($discounts);
    }
}

if ($mode == 'delete') {
    $discount_id = $_REQUEST['discount_id'];
    fn_qty_discounts_extended_delete_discount($discount_id);

    return [CONTROLLER_STATUS_REDIRECT, "global_qty_discounts.manage"];
}


if ($mode == 'manage') {
    $discounts = fn_qty_discounts_extended_get_discounts();

    Tygh::$app['view']->assign('discounts', $discounts);
}
