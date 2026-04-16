<?php

return [
    'pricing' => [
        'trial' => [
            'title' => "You're currently on a Trial Plan!",
            'access_info' => 'You have access to :admin Admin and :technician Technician slots during this trial.',
            'upgrade_info' => 'Upgrade anytime to add more users or continue using the service with our Pay-As-You-Go plan.',
        ],
        'trial_ended' => [
            'title' => 'Your trial has ended',
            'access_info' => 'Your free trial is over. You currently had access to :admin Admin and :technician Technician to keep managing your work orders and technician schedules. To continue using this service, please select your Pay-As-You-Go plan.',
        ],
        'pay_as_you_go' => [
            'title' => 'Pay as you go prices',
            'admin_charges' => '1 Admin Charges',
            'technician_charges' => '1 Technician Charges',
            'price_per_month' => '$:price/month',
            'proceed' => 'Proceed',
        ],
        'subscribed' => [
            'title' => 'You have an subscription plan for :admin admin, :technician technician',
            'next_payment' => 'Next payment: :amount on :date',
        ],
    ],
];
