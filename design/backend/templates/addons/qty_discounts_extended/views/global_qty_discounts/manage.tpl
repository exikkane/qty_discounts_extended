{capture name="mainbox"}
    <form action="{""|fn_url}" enctype="multipart/form-data" method="post" name="qty_discounts_form" class=" form-horizontal" id="qty_discounts_form">
    <div>
        <div class="table-responsive-wrapper">
            <table class="table table-middle table--relative table-responsive" width="100%">
                <thead class="cm-first-sibling">
                <tr>
                    <th width="5%">{__("quantity")}</th>
                    <th width="20%">"Discount value (prc.)"</th>

                    <th width="15%">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$discounts item="price" key="_key" name="prod_prices"}
                    <input type="hidden" name="discounts[{$_key}][id]" value="{$price.id}"/>
                    <input type="hidden" name="discounts[{$_key}][percentage_discount]" value="{$price.percentage_discount}"/>
                    <tr class="cm-row-item">
                        <td width="5%" data-th="{__("quantity")}">
                                <input type="text" name="discounts[{$_key}][lower_limit]" value="{$price.lower_limit}" class="input-micro cm-value-decimal" />
                            </td>
                        <td width="20%" data-th="Discount value (prc.)">
                            {if $price.percentage_discount == 0}{$price.price|default:"0.00"|fn_format_price:$primary_currency:null:false}{else}{$price.percentage_discount}{/if}</td>

                        <td width="15%" class="nowrap right">
                            {btn type="list" text=__("delete") class="cm-confirm" href="global_qty_discounts.delete?discount_id=`$price.id`" method="POST"}
                        </td>
                    </tr>
                {/foreach}
                {math equation="x+1" x=$_key|default:0 assign="new_key"}
                <tr class="{cycle values="table-row , " reset=1}{$no_hide_input_if_shared_product}" id="box_add_qty_discount">
                    <td width="5%" data-th="{__("quantity")}">
                        <input type="text" name="discounts[{$new_key}][lower_limit]" value="" class="input-micro cm-value-decimal" /></td>
                    <td width="20%" data-th="{__("value")}">
                        <input type="text" name="discounts[{$new_key}][percentage_discount]" value="0.00" size="10" class="input-medium cm-numeric" data-a-sep /></td>
                    <td width="15%" class="right">
                        {include file="buttons/multiple_buttons.tpl" item_id="add_qty_discount"}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
{/capture}
</form>
{capture name="buttons"}
    {include file="buttons/save_cancel.tpl" but_name="dispatch[global_qty_discounts.update]" but_role="action" but_target_form="qty_discounts_form" but_meta="cm-submit"}
{/capture}

{include file="common/mainbox.tpl" title="Extended quantity discounts" content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=true}