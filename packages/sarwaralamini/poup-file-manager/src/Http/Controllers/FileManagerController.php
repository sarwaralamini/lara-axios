<?php

namespace Sarwar\PopupFileManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Sarwar\PopupFileManager\Services\ResponseService;
use Sarwar\PopupFileManager\Services\FileManagerService;
use Sarwar\PopupFileManager\Exceptions\InvalidConfigurationException;

class FileManagerController extends Controller
{
    protected $fileManagerService;
    protected $responseService;

    /**
     * FileManagerController constructor.
     *
     * @param \Sarwar\PopupFileManager\Services\FileManagerService $fileManagerService
     * @param \Sarwar\PopupFileManager\Services\ResponseService $responseService
     */
    public function __construct(
        FileManagerService $fileManagerService,
        ResponseService $responseService,
    )
    {
        $this->fileManagerService = $fileManagerService;
        $this->responseService = $responseService;
    }

    /**
     * Fetch files and directories from the specified path.
     *
     * This method retrieves a list of files and directories, applies pagination,
     * and returns the result as a JSON response. It handles various exceptions and
     * logs errors accordingly.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFiles(Request $request): JsonResponse
    {
        try {
            $this->fileManagerService->ensureDefaultFoldersExist();

            $path = $request->input('path', config('file_manager.default_path'));
            $page = max((int) $request->input('page', 1), 1);
            $limit = max((int) $request->input('limit', 12), 1);
            $search = (string) $request->input('search', '');
            $isSearch = $request->boolean('is_search', false);

            $allItems = $this->fileManagerService->getAllFiles($path, $search, $isSearch);
            $allDirectories = $this->fileManagerService->getAllDirectories($path, $search, $isSearch);

            $items = array_merge($allDirectories, $allItems);

            if (!empty($search)) {
                $items = array_filter($items, fn($item) => stripos($item, $search) !== false);
            }

            $totalItems = count($items);
            $paginatedItems = array_slice($items, ($page - 1) * $limit, $limit);
            $totalPages = (int) ceil($totalItems / $limit);

            return response()->json([
                'is_success' => true,
                'items' => array_values($paginatedItems),
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'parentDirectory' => dirname($path),
            ], 200);
        }
        catch(InvalidConfigurationException $InvalidConfigurationException){
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $InvalidConfigurationException->getMessage(),
                    file: $InvalidConfigurationException->getFile(),
                    line: $InvalidConfigurationException->getLine()
                )
            );

            // Handle case where configaration is invalid.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: $InvalidConfigurationException->getMessage(),
                code: 500
            );
        }
        catch (\Error $error) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $error->getFile(),
                    line: $error->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
        catch (\Exception $exception) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $exception->getMessage(),
                    file: $exception->getFile(),
                    line: $exception->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: 'Failed to fetch files',
                code: 500
            );
        }
    }

    /**
     * Upload a file to the specified path.
     *
     * This method handles the file upload, validates the input, stores the file,
     * and returns a success or error response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
                'path' => 'nullable|string',
            ]);

            $path = $request->input('path', '/');
            $file = $request->file('file');

            $file->storeAs($path, $file->getClientOriginalName(), 'public');

            // Return a successful file upload response with a success message.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: true,
                result: null,
                message: 'File uploaded successfully',
                code: 200
            );
        }
        catch (\Error $error) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $error->getFile(),
                    line: $error->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
        catch (\Exception $exception) {
           // Log the exception details using a custom log message format.
           Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $exception->getMessage(),
                    file: $exception->getFile(),
                    line: $exception->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
    }

    /**
     * Create a new directory in the specified path.
     *
     * This method handles the creation of directories and returns a success or
     * error response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDirectory(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'path' => 'nullable|string',
            ]);

            $path = $request->input('path', '/');
            $name = $request->input('name');

            Storage::disk('public')->makeDirectory("{$path}/{$name}");

            // Return a successful directory creation response with a success message.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: true,
                result: null,
                message: 'Directory created successfully',
                code: 200
            );
        }
        catch (\Error $error) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $error->getFile(),
                    line: $error->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
        catch (\Exception $exception) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $exception->getFile(),
                    line: $exception->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: 'Failed to create directory',
                code: 500
            );
        }
    }

     /**
     * Delete multiple files or directories.
     *
     * This method deletes the selected items from the specified paths.
     * Returns a success or error response based on the operation result.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiple(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'paths' => 'required|array',
            ]);

            $paths = $request->input('paths');
            $ignoreList = config('pupup-file-manager.ignore_list');

            $this->fileManagerService->deleteMultiple($paths, $ignoreList);

             // Return a successful files deletion response with a success message.
             return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: true,
                result: null,
                message: 'Selected items deleted successfully.',
                code: 200
            );
        }
        catch (\Error $error) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $error->getFile(),
                    line: $error->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
        catch (\Exception $exception) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $exception->getMessage(),
                    file: $exception->getFile(),
                    line: $exception->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
    }
}
