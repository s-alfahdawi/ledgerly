<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Transaction Edit Days
    |--------------------------------------------------------------------------
    |
    | Transactions older than this number of days cannot be edited,
    | only reversed. This prevents historical data corruption in accounting
    | applications. Set to null to allow editing of all transactions.
    |
    */
    'transaction_edit_days' => env('TRANSACTION_EDIT_DAYS', 30),
];
