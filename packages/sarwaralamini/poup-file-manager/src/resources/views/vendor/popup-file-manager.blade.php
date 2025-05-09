@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('dist/css/popup-file-manager.css') }}">
@endpush

@section('title', 'Create User')

@section('content')
   <!-- Breadcrumb Section -->
   <nav aria-label="breadcrumb" class="breadcrumb-container">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">User</li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <!-- Body Container -->
    <div class="body-container">
        <div class="card card-container">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Create New User</h5>
                <a href="{{ route('dashboard') }}" class="btn btn-success btn-sm" id="backButton">
                    <i class="bi bi-arrow-left-short"></i> Back
                </a>
            </div>
        <div class="card-body">
            <div class="form-container">
                <form id="userForm">
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="userName" placeholder="Enter name" required>
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="userEmail" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label">Role</label>
                        <select class="form-select" id="userRole" required>
                            <option value="" disabled selected>Select role</option>
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                            <option value="Moderator">Moderator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" readonly id="fileInput1">
                            <label class="input-group-text" id="openFileManager1">Upload</label>
                          </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" readonly id="fileInput2">
                            <label class="input-group-text" id="openFileManager2">Upload</label>
                          </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" readonly id="fileInput3">
                            <label class="input-group-text" id="openFileManager3">Upload</label>
                          </div>
                    </div>
                    <button type="submit" class="btn btn-success float-end">Create User</button>
                </form>

                <!-- Spinner Overlay -->
                <div class="loading-overlay" id="loadingSpinner" style="display: none;">
                    <div>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="processing-text mt-3">Processing...</p>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <x-file-manager-modal />

    </div>
@endsection

@push('js')
<script src="{{ asset('dist/js/popup-file-manager.js') }}"></script>
<script>
    const fileManager = new FileManager({
        modalSelector: '#fileManagerModal',
        fileListSelector: '#fileList',
        deleteButtonSelector: '#deleteSelected',
        searchInputSelector: '#searchFileInput',
        goBackButtonSelector: '#goBack',
        paginationContainerSelector: '#paginationContainer',
        uploadFileInputSelector: '#uploadFileInput',
        createDirectorySelector: '#createDirectory',
        uploadButtonSelector: '#uploadFile',
        defaultPath: "{{ config('pupup-file-manager.default_path') }}",
        storagePath: "{{ config('pupup-file-manager.storage_path') }}",
        folderIcon: "{{ asset('dist/img/folder.png') }}",
        pdf_icon: "{{ asset('dist/img/pdf.png') }}",
        hiddenNames: ['thumbnails', 'index.html', 'index.htm', 'index.php', 'index', '.gitignore', 'folder.png'],
        hiddenPaths: ['catalog/thumbnails/products', 'catalog/thumbnails/categories'],
        hiddenDeleteButtonItems: ['products', 'categories'],
        fileInputButtons: []
    });

    // // Dynamically push file input buttons
    fileManager.settings.fileInputButtons.push( { input: '#fileInput1', button: '#openFileManager1' } );
    fileManager.settings.fileInputButtons.push( { input: '#fileInput2', button: '#openFileManager2' } );
    fileManager.settings.fileInputButtons.push( { input: '#fileInput3', button: '#openFileManager3' } );

    // // Rebind the buttons after adding new input/button pairs
    fileManager.bindInputButtons(); // This ensures the new input-button pairs are correctly bound

    document.addEventListener('DOMContentLoaded', () => {
        const userForm = document.getElementById('userForm');
        const loadingSpinner = document.getElementById('loadingSpinner');

        userForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent form from submitting normally

            // Show spinner and disable form
            loadingSpinner.style.display = 'flex';
            Array.from(userForm.elements).forEach((element) => {
                element.disabled = true; // Disable all form inputs
            });

            // Simulate a delay for form submission (e.g., an API call)
            setTimeout(() => {
                // Hide spinner and re-enable form
                loadingSpinner.style.display = 'none';
                Array.from(userForm.elements).forEach((element) => {
                    element.disabled = false; // Re-enable all form inputs
                });

                // Optionally reset form or provide feedback
                alert('Form submitted successfully!');
                userForm.reset();
            }, 11111); // Simulate 3 seconds delay
        });
    });
</script>
@endpush
