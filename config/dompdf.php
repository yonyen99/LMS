<?php

return [
    'show_warnings' => env('DOMPDF_SHOW_WARNINGS', false),
    'public_path' => public_path(),
    'convert_entities' => true,
    'options' => [
        'font_dir' => storage_path('app/fonts'), // Use app/fonts to avoid conflicts
        'font_cache' => storage_path('app/fonts'), // Same as font_dir for simplicity
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => realpath(base_path()),
        'allowed_protocols' => [
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        'log_output_file' => storage_path('logs/dompdf.log'), // Enable logging
        'enable_font_subsetting' => true,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'landscape',
        'default_font' => 'Helvetica', // Use a widely available font
        'dpi' => 150, // Higher DPI for better quality
        'enable_php' => false,
        'enable_javascript' => false,
        'enable_remote' => false, // Disable remote assets to avoid issues
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,
    ],
];