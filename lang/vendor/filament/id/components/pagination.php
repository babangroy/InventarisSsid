<?php

return [

    'label' => 'Navigasi halaman',

    'overview' => '{1} Menampilkan 1 hasil|[2,*] :first - :last dari :total data',

    'fields' => [

        'records_per_page' => [

            'label' => 'per halaman',

            'options' => [
                'all' => 'Semua',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Pertama',
        ],

        'go_to_page' => [
            'label' => 'Ke halaman :page',
        ],

        'last' => [
            'label' => 'Terakhir',
        ],

        'next' => [
            'label' => 'Selanjutnya',
        ],

        'previous' => [
            'label' => 'Sebelumnya',
        ],

    ],

];
