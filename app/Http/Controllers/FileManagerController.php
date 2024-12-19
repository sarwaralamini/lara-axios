<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class FileManagerController extends Controller
{

    /**
     * Fetch files and directories with optional search and pagination.
     *
     * This method retrieves files and directories based on the provided parameters. It applies search, pagination, and
     * merges the results. Default folders are checked and created if necessary before fetching items.
     *
     * @param Request $request The incoming request instance, containing the path, page, limit, search term, and search flag.
     * @return JsonResponse JSON response containing files, directories, pagination details, and parent directory path.
     */
    public function getFiles(Request $request): JsonResponse
    {
        // Ensure default folders exist
        $this->ensureDefaultFoldersExist();

        // Get the path, page, limit, and search parameters from the request
        $path = $request->input('path', config('file_manager.default_path')); // Use config for default path
        $page = max((int) $request->input('page', 1), 1);
        $limit = max((int) $request->input('limit', 12), 1);

        // Ensure search is a string (empty string if not provided)
        $search = (string) $request->input('search', '');

        $isSearch = $request->boolean('is_search', false);

        // Fetch files and directories
        $allItems = $this->getAllFiles($path, $search, $isSearch);
        $allDirectories = $this->getAllDirectories($path, $search, $isSearch);

        // Merge directories and files
        $items = array_merge($allDirectories, $allItems);

        // Filter items based on search query
        if (!empty($search)) {
            $items = array_filter($items, fn($item) => stripos($item, $search) !== false);
        }

        // Apply pagination
        $totalItems = count($items);
        $paginatedItems = array_slice($items, ($page - 1) * $limit, $limit);
        $totalPages = (int) ceil($totalItems / $limit);

        return response()->json([
            'items' => array_values($paginatedItems),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'parentDirectory' => dirname($path),
        ]);
    }


    /**
     * Ensure that the default folders exist.
     *
     * This method checks if the default folders (catalog and thumbnails) and their subfolders exist in the storage.
     * If any of these folders do not exist, they are created.
     *
     * @return void
     */
    protected function ensureDefaultFoldersExist(): void
    {
        // Retrieve folder structure from the config
        $folders = config('pupup-file-manager.default_folders');

        // Ensure $folders is an array before using foreach
        if (is_array($folders) && !empty($folders)) {
            foreach ($folders as $parent => $subfolders) {
                // Check if the parent folder exists, if not create it
                if (!Storage::disk('public')->exists($parent)) {
                    Storage::disk('public')->makeDirectory($parent);
                }

                // Loop through the subfolders and create them if they do not exist
                foreach ($subfolders as $subfolder) {
                    $folderPath = "{$parent}/{$subfolder}";
                    if (!Storage::disk('public')->exists($folderPath)) {
                        Storage::disk('public')->makeDirectory($folderPath);
                    }
                }
            }
        } else {
            // Handle the case where the folders config is not valid
            Log::warning('default_folders configuration is missing or invalid.');
        }
    }

    /**
     * Fetch all files from the given path with optional search.
     *
     * This method retrieves all files from the specified path. If a search term is provided, it filters the files accordingly.
     *
     * @param string $path The path from which to retrieve files.
     * @param string $search The search term used to filter the files.
     * @param bool $isSearch Flag to indicate whether the search should be applied.
     * @return array An array of file paths matching the search criteria.
     */
    protected function getAllFiles(string $path, string $search, bool $isSearch): array
    {
        if ($isSearch) {
            return $search ? Storage::allFiles(config('file_manager.default_path')) : Storage::files($path);
        }

        return Storage::files($path);
    }

    /**
     * Fetch all directories from the given path with optional search.
     *
     * This method retrieves all directories from the specified path. If a search term is provided, it filters the directories accordingly.
     *
     * @param string $path The path from which to retrieve directories.
     * @param string $search The search term used to filter the directories.
     * @param bool $isSearch Flag to indicate whether the search should be applied.
     * @return array An array of directory paths matching the search criteria.
     */
    protected function getAllDirectories(string $path, string $search, bool $isSearch): array
    {
        if ($isSearch) {
            return $search ? Storage::allDirectories(config('file_manager.default_path')) : Storage::directories($path);
        }

        return Storage::directories($path);
    }


    /**
     * Handle file upload to a specified path.
     *
     * @param Request $request The incoming request instance.
     * @return JsonResponse JSON response confirming the file upload.
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file',
            'path' => 'nullable|string',
        ]);

        $path = $request->input('path', '/');
        $file = $request->file('file');

        $file->storeAs($path, $file->getClientOriginalName(), 'public');

        return response()->json(['message' => 'File uploaded successfully']);
    }

    /**
     * Create a new directory within a specified path.
     *
     * @param Request $request The incoming request instance.
     * @return JsonResponse JSON response confirming directory creation.
     */
    public function createDirectory(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'path' => 'nullable|string',
        ]);

        $path = $request->input('path', '/');
        $name = $request->input('name');

        Storage::disk('public')->makeDirectory("{$path}/{$name}");

        return response()->json(['message' => 'Directory created successfully']);
    }

    /**
     * Delete multiple files or directories while respecting an ignore list.
     *
     * @param Request $request The incoming request instance.
     * @return JsonResponse JSON response confirming the deletion.
     */
    public function deleteMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'paths' => 'required|array',
        ]);

        $paths = $request->input('paths');

        $ignoreList = config('pupup-file-manager.ignore_list');

        foreach ($paths as $path) {
            if (in_array(basename($path), $ignoreList)) {
                continue;
            }

            $fullPath = storage_path('app/public/' . $path);

            if (is_dir($fullPath)) {
                File::deleteDirectory($fullPath);
            } else {
                File::delete($fullPath);
            }
        }

        return response()->json(['message' => 'Selected items deleted successfully.']);
    }
}
