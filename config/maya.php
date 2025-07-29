<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maya Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Maya payment integration using sandbox environment
    | for testing purposes.
    |
    */

    'environment' => env('MAYA_ENVIRONMENT', 'sandbox'),

    'sandbox' => [
        'public_key' => env('MAYA_SANDBOX_PUBLIC_KEY', 'pk-Z0OSzLvIcOI2UIvDhdTGVVfRSSeiGStnceqwUE7n0Ah'),
        'secret_key' => env('MAYA_SANDBOX_SECRET_KEY', 'sk-X8qolYjy62kIzEbr0QRK1h4b4KDVHaNcwMYk39jInSl'),
        'webhook_secret' => env('MAYA_SANDBOX_WEBHOOK_SECRET', ''),
        'base_url' => 'https://pg-sandbox.paymaya.com',
        'disbursement_url' => 'https://pg-sandbox.paymaya.com/disbursements/v1',
    ],

    'production' => [
        'public_key' => env('MAYA_PRODUCTION_PUBLIC_KEY'),
        'secret_key' => env('MAYA_PRODUCTION_SECRET_KEY'),
        'webhook_secret' => env('MAYA_PRODUCTION_WEBHOOK_SECRET'),
        'base_url' => 'https://pg.paymaya.com',
        'disbursement_url' => 'https://pg.paymaya.com/disbursements/v1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission Settings
    |--------------------------------------------------------------------------
    |
    | PawMatch commission percentage from adoption fees
    |
    */
    'commission_percentage' => 20, // 20% commission

    /*
    |--------------------------------------------------------------------------
    | PawMatch Company Bank Account
    |--------------------------------------------------------------------------
    |
    | Bank account details for PawMatch to receive commission payments
    |
    */
    'company_bank' => [
        'bank_name' => env('PAWMATCH_BANK_NAME', 'BDO'),
        'bank_account_number' => env('PAWMATCH_BANK_ACCOUNT_NUMBER', ''),
        'bank_account_name' => env('PAWMATCH_BANK_ACCOUNT_NAME', 'PawMatch Inc.'),
        'bank_code' => env('PAWMATCH_BANK_CODE', 'BDO'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Disbursement Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for Maya Disbursement API
    |
    */
    'disbursement' => [
        'enabled' => env('MAYA_DISBURSEMENT_ENABLED', true),
        'auto_payout' => env('MAYA_AUTO_PAYOUT', true),
        'payout_delay_hours' => env('MAYA_PAYOUT_DELAY_HOURS', 24), // Delay before automatic payout
        'max_retry_attempts' => env('MAYA_MAX_RETRY_ATTEMPTS', 3),
        'webhook_secret' => env('MAYA_DISBURSEMENT_WEBHOOK_SECRET', ''),
        'test_mode' => env('MAYA_DISBURSEMENT_TEST_MODE', true), // Enable test mode for development
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Available payment methods for Maya Checkout
    |
    */
    'payment_methods' => [
        'credit_card' => 'Credit/Debit Card',
        'maya_wallet' => 'Maya Wallet',
        'qr_code' => 'QR Code Payment',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    |
    | Webhook URL for payment notifications
    |
    */
    'webhook_url' => env('MAYA_WEBHOOK_URL', '/api/maya/webhook'),

    /*
    |--------------------------------------------------------------------------
    | Test Cards (Sandbox Only)
    |--------------------------------------------------------------------------
    |
    | Test card details for sandbox testing
    |
    */
    'test_cards' => [
        'mastercard' => [
            'number' => '5123456789012346',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'cvv' => '111',
            'password' => null,
        ],
        'visa' => [
            'number' => '4123450131001381',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'cvv' => '123',
            'password' => 'mctest1',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Test Maya Wallet Accounts (Sandbox Only)
    |--------------------------------------------------------------------------
    |
    | Test wallet accounts for sandbox testing
    |
    */
    'test_wallets' => [
        'successful' => [
            'username' => '+639900100900',
            'password' => 'Password@1',
            'otp' => '123456',
        ],
        'insufficient_balance' => [
            'username' => '+639900100916',
            'password' => 'Password@1',
            'otp' => '123456',
        ],
    ],
]; 