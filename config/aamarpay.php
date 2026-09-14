<?php

return [
    'store_id' => env('AAMARPAY_STORE_ID','mugdhlfd'),
    'signature_key' => env('AAMARPAY_KEY','32d4df494e009d2a7278853d6100fdde'),
    'sandbox' => env('AAMARPAY_SANDBOX', false),
    'redirect_url' => [
        'success' => [
            'route' => 'shippings.online' // payment.success
        ],
        'cancel' => [
            'route' => 'payment.cancel' // payment/cancel or you can use route also
        ]
    ]
];
