<?php

/**
 * Hotel module configuration.
 * NEW – SAFE ADDITION: Department-based filtering is OFF by default.
 * Set to true and assign permission to enable department filters in reports/listings.
 */
return [
    'department_filter_enabled' => env('HOTEL_DEPARTMENT_FILTER_ENABLED', false),
];
