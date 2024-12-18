<?php

return [
    'default_path' => '/catalog',
    'storage_path' =>  env('APP_URL') . '/storage', // Adjust based on your storage setup
    'folder_icon' => env('APP_URL') . '/storage/folder.png',
    'pdf_icon' => env('APP_URL') . '/storage/folder.png',
    'hidden_names' => [
        'thumbnails',
        'index.html',
        'index.htm',
        'index.php',
        'index',
        '.gitignore',
        'folder.png',
    ],
    'hidden_paths' => [
        'catalog/thumbnails/products',
        'catalog/thumbnails/categories',
    ],
    'hide_input_for' => ['products', 'categories'],
];
