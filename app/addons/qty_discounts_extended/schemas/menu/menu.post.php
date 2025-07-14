<?php

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['central']['products']['items']['qty_discounts_extended.menu_item'] = [
    'attrs' => [
        'class' => 'is-addon'
    ],
    'href'     => 'global_qty_discounts.manage',
    'position' => 601,
];

return $schema;
