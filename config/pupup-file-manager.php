<?php

return [
    'default_path' => '/catalog',
    'default_folders' => [
        'catalog' => ['categories', 'products'],
        'catalog/thumbnails' => ['categories', 'products'],
    ],
    'ignore_list' => [
        '.gitignore', 'folder.png',
        'catalog/thumbnails/products', 'catalog/thumbnails/categories',
        'products', 'categories', 'thumbnails',
        'index.html', 'index.htm', 'index.php', 'index',
    ],
    'storage_path' =>  env('APP_URL') . '/storage', // Adjust based on your storage setup
    'folder_icon' => env('APP_URL') . '/storage/folder.png',
    'pdf_icon' => env('APP_URL') . '/storage/folder.png',
];
