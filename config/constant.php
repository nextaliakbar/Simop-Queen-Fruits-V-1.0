<?php

/**
const CACHE_BUSINESS_SETTINGS_TABLE = 'cache_business_settings_table';

const MANAGEMENT_SECTION = [
    'dashboard_management' => 'Manajemen Dashboard',
    'pos_management' => 'Manajemen Kasir',
    'order_management' => 'Manajemen Pesanan',
    'product_management' => 'Manajemen Produk',
    'promotion_management' => 'Manajemen Promosi',
    'user_management' => 'Manajemen Pengguna',
    'system_management' => 'Manajemen Sistem',
];

const PENDING = 'pending';
const CONFIRMED = 'confirmed';
const PROCESSING = 'processing';
const OUT_FOR_DELIVERY = 'out_for_delivery';
const DELIVERED = 'delivered';
const RETURNED = 'returned';
const FAILED = 'failed';
const CANCELED = 'canceled';
const COMPLETED = 'completed';

const CATEGORIES_WITH_CHILDES = 'categories_with_childes';
*/

if (!defined('CACHE_BUSINESS_SETTINGS_TABLE')) {
    define('CACHE_BUSINESS_SETTINGS_TABLE', 'cache_business_settings_table');
}

if (!defined('MANAGEMENT_SECTION')) {
    define('MANAGEMENT_SECTION', [
        'dashboard_management' => 'Manajemen Dashboard',
        'pos_management' => 'Manajemen Kasir',
        'order_management' => 'Manajemen Pesanan',
        'product_management' => 'Manajemen Produk',
        'promotion_management' => 'Manajemen Promosi',
        'user_management' => 'Manajemen Pengguna',
        'system_management' => 'Manajemen Sistem',
    ]);
}

if (!defined('PENDING')) define('PENDING', 'pending');
if (!defined('CONFIRMED')) define('CONFIRMED', 'confirmed');
if (!defined('PROCESSING')) define('PROCESSING', 'processing');
if (!defined('OUT_FOR_DELIVERY')) define('OUT_FOR_DELIVERY', 'out_for_delivery');
if (!defined('DELIVERED')) define('DELIVERED', 'delivered');
if (!defined('RETURNED')) define('RETURNED', 'returned');
if (!defined('FAILED')) define('FAILED', 'failed');
if (!defined('CANCELED')) define('CANCELED', 'canceled');
if (!defined('COMPLETED')) define('COMPLETED', 'completed');
if (!defined('CATEGORIES_WITH_CHILDES')) define('CATEGORIES_WITH_CHILDES', 'categories_with_childes');