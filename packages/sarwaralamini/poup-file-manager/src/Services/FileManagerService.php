<?php

namespace Sarwar\PopupFileManager\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Sarwar\PopupFileManager\Exceptions\InvalidConfigurationException;

class FileManagerService
{
    /**
     * Ensure that the default folders specified in the configuration exist.
     *
     * This method checks if the directories defined in the `default_folders` configuration
     * exist on the disk and creates them if they do not.
     *
     * @throws InvalidConfigurationException If the `default_folders` configuration is invalid.
     * @return void
     */

    public function ensureDefaultFoldersExist(): void
    {
        $folders = config('pupup-file-manager.default_folders');

        if (!is_array($folders) || empty($folders)) {
            throw new InvalidConfigurationException('Default folders configuration is invalid.');
        }

        foreach ($folders as $parent => $subfolders) {
            if (!Storage::disk('public')->exists($parent)) {
                Storage::disk('public')->makeDirectory($parent);
            }

            foreach ($subfolders as $subfolder) {
                $folderPath = "{$parent}/{$subfolder}";
                if (!Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->makeDirectory($folderPath);
                }
            }
        }
    }

    /**
     * Get all files from the specified directory path.
     *
     * This method retrieves files based on the search conditions. If a search is enabled,
     * it can filter based on the search term.
     *
     * @param string $path The directory path to search within.
     * @param string $search The search term used to filter the files.
     * @param bool $isSearch Flag indicating whether the search is enabled.
     * @return array A list of file paths.
     */
    public function getAllFiles(string $path, string $search, bool $isSearch): array
    {
        if ($isSearch) {
            return $search ? Storage::allFiles(config('pupup-file-manager.default_path')) : Storage::files($path);
        }

        return Storage::files($path);
    }

     /**
     * Get all directories from the specified directory path.
     *
     * This method retrieves directories based on the search conditions. If a search is enabled,
     * it can filter based on the search term.
     *
     * @param string $path The directory path to search within.
     * @param string $search The search term used to filter the directories.
     * @param bool $isSearch Flag indicating whether the search is enabled.
     * @return array A list of directory paths.
     */
    public function getAllDirectories(string $path, string $search, bool $isSearch): array
    {
        if ($isSearch) {
            return $search ? Storage::allDirectories(config('pupup-file-manager.default_path')) : Storage::directories($path);
        }

        return Storage::directories($path);
    }

     /**
     * Delete multiple files or directories from the specified paths.
     *
     * This method deletes files and directories from the given paths. It will skip any paths
     * that are included in the ignore list.
     *
     * @param array $paths The list of file or directory paths to delete.
     * @param array $ignoreList The list of files or directories to ignore during deletion.
     * @return void
     */
    public function deleteMultiple(array $paths, array $ignoreList): void
    {
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
    }
}
