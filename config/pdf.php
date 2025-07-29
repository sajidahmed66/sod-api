<?php

return [
    'mode'                     => 'utf-8',
    'format'                   => 'A4',
    'default_font_size'        => '12',
    'default_font'             => 'siyamrupali',
    'margin_left'              => 10,
    'margin_right'             => 10,
    'margin_top'               => 10,
    'margin_bottom'            => 10,
    'margin_header'            => 0,
    'margin_footer'            => 0,
    'orientation'              => 'P',
    'title'                    => 'Laravel mPDF',
    'subject'                  => '',
    'author'                   => '',
    'watermark'                => '',
    'show_watermark'           => false,
    'show_watermark_image'     => false,
    'watermark_font'           => 'sans-serif',
    'display_mode'             => 'fullpage',
    'watermark_text_alpha'     => 0.1,
    'watermark_image_path'     => '',
    'watermark_image_alpha'    => 0.2,
    'watermark_image_size'     => 'D',
    'watermark_image_position' => 'P',
    'auto_language_detection'  => false,
    'temp_dir'                 => rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR),
    'pdfa'                     => false,
    'pdfaauto'                 => false,
    'use_active_forms'         => false,
    'custom_font_dir'  => base_path('resources/fonts/'), // don't forget the trailing slash!
    'custom_font_data' => [
        'siyamrupali' => [
            'R'  => 'SolaimanLipi_20-04-07.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
            'B'  => 'SolaimanLipi_20-04-07.ttf',
        ],
        'arial' => [
            'R'  => 'arial.ttf',
        ],
        'roboto' => [
            'R'  => 'Roboto-Regular.ttf',    // regular font
            'B'  => 'Roboto-Bold.ttf',       // optional: bold font
            'BI' => 'Roboto-BoldItalic.ttf' // optional: bold-italic font
        ]
    ]

];
