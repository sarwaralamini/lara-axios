<?php

namespace Sarwar\PopupFileManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    /**
     * Fetch files and directories with optional search and pagination.
     *
     * @param Request $request The incoming request instance.
     * @return \Illuminate\Http\JsonResponse JSON response containing files, directories, and pagination details.
     */
    public function getFiles(Request $request):JsonResponse
    {
        $this->createDefaultFolders();

        $path = $request->input('path', '/');
        $page = (int) $request->input('page', 1);
        $limit = (int) $request->input('limit', 12);
        $search = $request->input('search', '');
        $is_search = $request->is_search;

        // Retrieve items and directories based on search state.
        if ($is_search) {
            $allItems = $search ? Storage::allFiles('/catalog') : Storage::files($path);
            $allDirectories = $search ? Storage::allDirectories('/catalog') : Storage::directories($path);
        } else {
            $allItems = Storage::files($path);
            $allDirectories = Storage::directories($path);
        }

        // Combine files and directories.
        $items = array_merge($allDirectories, $allItems);

        // Filter items by search term if provided.
        if (!empty($search)) {
            $items = array_filter($items, fn($item) => stripos($item, $search) !== false);
        }

        // Pagination logic.
        $totalItems = count($items);
        $paginatedItems = array_slice($items, ($page - 1) * $limit, $limit);
        $totalPages = ceil($totalItems / $limit);

        $parentDirectory = dirname($path);

        return response()->json([
            'items' => $paginatedItems,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'parentDirectory' => $parentDirectory,
        ]);
    }

    /**
     * Create default catalog and thumbnail folders with necessary subfolders.
     *
     * This method ensures that the required catalog and thumbnail folders
     * with subfolders exist in the public storage. If the folders don't
     * exist, they will be created.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDefaultFolders():JsonResponse
    {
        // Define the catalog folder and subfolders
        $catalogFolder = 'catalog';
        $catalogSubfolders = ['categories', 'products'];

        // Create catalog folder if it doesn't exist
        $this->createFolderIfNotExists($catalogFolder);

        // Create subfolders inside the catalog folder
        foreach ($catalogSubfolders as $subfolder) {
            $this->createFolderIfNotExists("{$catalogFolder}/{$subfolder}");
        }

        // Define the thumbnails folder and subfolders
        $thumbnailsFolder = "{$catalogFolder}/thumbnails";
        $thumbnailsSubfolders = ['categories', 'products'];

        // Create thumbnails folder if it doesn't exist
        $this->createFolderIfNotExists($thumbnailsFolder);

        // Create subfolders inside the thumbnails folder
        foreach ($thumbnailsSubfolders as $subfolder) {
            $this->createFolderIfNotExists("{$thumbnailsFolder}/{$subfolder}");
        }

        return response()->json(['message' => 'Catalog and subfolders created successfully!']);
    }

    /**
     * Create a folder if it doesn't already exist in public storage.
     *
     * @param string $folderPath
     * @return void
     */
    protected function createFolderIfNotExists(string $folderPath):void
    {
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }
    }

    /**
     * Handle file upload to a specified path.
     *
     * @param Request $request The incoming request instance.
     * @return \Illuminate\Http\JsonResponse JSON response confirming the file upload.
     */
    public function uploadFile(Request $request):JsonResponse
    {
        $path = $request->input('path', '/');
        $file = $request->file('file');

        // Save the uploaded file with its original name.
        $file->storeAs($path, $file->getClientOriginalName());

        return response()->json(['message' => 'File uploaded successfully']);
    }

    /**
     * Create a new directory within a specified path.
     *
     * @param Request $request The incoming request instance.
     * @return \Illuminate\Http\JsonResponse JSON response confirming directory creation.
     */
    public function createDirectory(Request $request):JsonResponse
    {
        $path = $request->input('path', '/');
        $name = $request->input('name');

        // Create the directory.
        Storage::makeDirectory($path . '/' . $name);

        return response()->json(['message' => 'Directory created successfully']);
    }

    /**
     * Delete multiple files or directories while respecting an ignore list.
     *
     * @param Request $request The incoming request instance.
     * @return \Illuminate\Http\JsonResponse JSON response confirming the deletion.
     */
    public function deleteMultiple(Request $request):JsonResponse
    {
        $paths = $request->input('paths', []);

        // List of files and directories to be ignored.
        $ignoreList = [
            '.gitignore',
            'folder.png',
            'catalog/thumbnails/products',
            'catalog/thumbnails/categories',
            'products',
            'categories',
            'thumbnails',
            'index.html',
            'index.htm',
            'index.php',
            'index',
        ];

        foreach ($paths as $path) {
            $itemName = basename($path);

            // Skip items in the ignore list.
            if (in_array($itemName, $ignoreList)) {
                continue;
            }

            $fullPath = storage_path('app/public/' . $path);

            // Delete directory or file based on its type.
            if (is_dir($fullPath)) {
                File::deleteDirectory($fullPath);
            } else {
                File::delete($fullPath);
            }
        }

        return response()->json(['message' => 'Selected items deleted successfully.']);
    }

}
