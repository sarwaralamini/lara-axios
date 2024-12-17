<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title')</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('dist/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('dist/css/filemanager.css') }}">
  @stack('css')
</head>
<body>
  <!-- Top Navigation -->
  @include('layouts.inc.topbar')

  <div class="main-content">
    <!-- Sidebar -->
    @include('layouts.inc.sidebar')


    <!-- Main Content Area -->
    <div class="main-content-right" style="flex-grow: 1;">
        @yield('content')
    </div>
    <!-- Spinner Overlay -->
    <div class="loading-overlay" id="loadingSpinner_body" style="display: none;">
        <div>
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="processing-text mt-3">Logging out...</p>
        </div>
    </div>
  </div>



  <!-- Footer -->
  <footer class="footer">
    <!-- File Manager Modal -->
    <div class="modal fade" id="fileManagerModal" tabindex="-1" aria-labelledby="fileManagerLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileManagerLabel">File Manager</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="fileManager">
                        <div class="file-manager-controls mb-3">
                            <div class="left-controls">
                                <button class="btn btn-sm btn-success" id="createDirectory">
                                    <i class="bi bi-folder-plus"></i>
                                </button>
                                <input type="file" id="uploadFileInput" class="d-none">
                                <button class="btn btn-sm btn-primary" id="uploadFile">
                                    <i class="bi bi-cloud-upload"></i>
                                </button>
                                <button class="btn btn-sm btn-danger d-none" id="deleteSelected">
                                    <i class="bi bi-trash"></i> Delete Selected
                                </button>
                                <button class="btn btn-sm btn-secondary" id="goBack" style="display:none;">
                                    <i class="bi bi-arrow-left"></i>
                                </button>
                            </div>
                            <div class="right-controls">
                                <input type="text" id="searchFileInput" class="form-control" placeholder="Search files...">
                                <i class="bi bi-search search-icon"></i>
                            </div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="row" id="fileList"></div>
                        </div>
                        <div class="d-flex justify-content-center mt-3" id="paginationContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p>&copy; 2024 My Company</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="{{ asset('dist/js/bootstrap-notify-3.1.3.min.js') }}"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script src="{{ asset('dist/js/common.js') }}"></script>

  <script>
    const storagePath = "{{ asset('storage/') }}";
    const folderIcon = "{{ asset('dist/img/folder.png') }}";
    const pdfIcon = "{{ asset('dist/img/pdf.png') }}";
  </script>
  <script src="{{ asset('dist/js/filemanager.js') }}"></script>
  @stack('js')
</body>
</html>
