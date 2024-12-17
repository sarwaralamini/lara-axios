/**
 * File Manager Script
 * This script handles the functionality of a file manager interface, including file and folder navigation,
 * creation, deletion, uploading, searching, and pagination.
 * Requires Bootstrap Modals and jQuery for AJAX calls.
 */

document.addEventListener('DOMContentLoaded', function () {
    /**
     * Initialize modal for the file manager.
     */
    const fileManagerModal = new bootstrap.Modal('#fileManagerModal');

    // Default storage path and pagination settings
    let currentPath = '/catalog'; // Default storage path
    let currentPage = 1;
    const itemsPerPage = 12;
    let totalPages = 1;

    // DOM elements used in the script
    const elements = {
        openFileManager: document.getElementById('openFileManager'),
        fileList: document.getElementById('fileList'),
        deleteSelected: document.getElementById('deleteSelected'),
        searchFileInput: document.getElementById('searchFileInput'),
        goBack: document.getElementById('goBack'),
        paginationContainer: document.getElementById('paginationContainer'),
        fileInput: document.getElementById('fileInput'),
        uploadFileInput: document.getElementById('uploadFileInput'),
        createDirectory: document.getElementById('createDirectory'),
        uploadFile: document.getElementById('uploadFile')
    };

    /**
     * Event listener to open the file manager modal.
     */
    elements.openFileManager.addEventListener('click', () => {
        fetchFiles(currentPath, currentPage);
        fileManagerModal.show();
    });

    /**
     * Event listener for live file search.
     */
    let debounceTimer;
    elements.searchFileInput.addEventListener('input', () => {
        // Clear the previous debounce timer
        clearTimeout(debounceTimer);

        // Set a new debounce timer to trigger the search after a delay
        debounceTimer = setTimeout(function() {
            // Hide the delete button when typing in the search field
            elements.deleteSelected.classList.add('d-none');

            // Call the fetchFiles function to perform the search with the current input value
            fetchFiles(currentPath, 1, elements.searchFileInput.value.trim(), true);
        }, 500); // 500ms debounce time
    });


    /**
     * Event listener for deleting selected items.
     */
    elements.deleteSelected.addEventListener('click', deleteSelectedItems);

    /**
     * Event listener for navigating back to the parent directory.
     */
    elements.goBack.addEventListener('click', () => {
        const parentPath = elements.goBack.dataset.path;
        currentPage = 1;
        fetchFiles(parentPath, currentPage);
    });

    /**
     * Event listener for creating a new directory.
     */
    elements.createDirectory.addEventListener('click', () => {
        const dirName = prompt('Enter directory name:');
        if (dirName) createDirectory(dirName);
    });

    /**
     * Event listener for initiating file upload.
     */
    elements.uploadFile.addEventListener('click', () => elements.uploadFileInput.click());

    /**
     * Event listener for handling file uploads.
     */
    elements.uploadFileInput.addEventListener('change', uploadFile);

    /**
     * General event listener for dynamic interactions, including pagination and directory navigation.
     */
    document.addEventListener('click', event => {
        const target = event.target;

        // Handle pagination button clicks
        if (target.classList.contains('pagination-btn')) {
            const page = target.dataset.page;
            if (page >= 1 && page <= totalPages) {
                currentPage = parseInt(page);
                fetchFiles(currentPath, currentPage);
            }
        }

        // Handle directory navigation
        if (target.closest('.directory')) {
            const path = target.closest('.directory').dataset.path;
            currentPage = 1;
            fetchFiles(path, currentPage);
        }

        // Handle file selection
        if (target.closest('.file')) {
            const filePath = target.closest('.file').dataset.path;
            elements.fileInput.value = filePath;
            fileManagerModal.hide();
        }
    });

    /**
     * Event listener for toggling the visibility of the delete button based on item selection.
     */
    document.addEventListener('change', event => {
        if (event.target.classList.contains('select-item')) {
            const selectedItems = document.querySelectorAll('.select-item:checked').length;
            elements.deleteSelected.classList.toggle('d-none', selectedItems === 0);
        }
    });

    /**
     * Fetch files and directories from the server.
     * @param {string} path - The current path to fetch files from.
     * @param {number} page - The current page for pagination.
     * @param {string} [search] - Optional search query.
     * @param {boolean} [isSearch] - Flag to indicate a search operation.
     */
    function fetchFiles(path, page, search = '', isSearch = false) {
        elements.deleteSelected.classList.add('d-none');

        $.ajax({
            url: '/file-manager/files',
            method: 'GET',
            data: { path, page, limit: itemsPerPage, search, is_search: isSearch },
            success: function (response) {
                const { items, totalItems, totalPages: resTotalPages, currentPage: resCurrentPage, parentDirectory } = response;
                currentPath = path;
                totalPages = resTotalPages;
                currentPage = resCurrentPage;

                renderFiles(items);
                renderPagination(totalPages, currentPage, totalItems);
                toggleGoBackButton(parentDirectory);
            }
        });
    }

    /**
     * Render the list of files and directories.
     * @param {Array} items - Array of file and directory paths.
     */
    function renderFiles(items) {
        const hiddenNames = ['thumbnails', 'index.html', 'index.htm', 'index.php', 'index', '.gitignore', 'folder.png'];
        const hiddenPaths = ['catalog/thumbnails/products', 'catalog/thumbnails/categories'];
        const hideInputFor = ['products', 'categories'];

        elements.fileList.innerHTML = items
            .map(item => {
                const name = item.split('/').pop();
                const isDir = !item.includes('.');
                const isPdf = item.endsWith('.pdf');  // Check if the item ends with .pdf
                const icon = isDir ? folderIcon : (isPdf ? pdfIcon : `${storagePath}/${item}`);  // Add pdfIcon if the item is a PDF
                const type = isDir ? 'directory' : 'file';

                const hideFile = hiddenNames.includes(name) ? 'd-none' : 'd-inline-block';
                const hidePath = hiddenPaths.includes(item) ? 'd-none' : 'd-inline-block';
                const hideInput = hideInputFor.includes(name) ? 'd-none' : 'd-inline-block';

                return `
                    <div class="col-6 col-md-4 col-lg-3 mb-3 text-start ${hideFile} ${hidePath}">
                        <div class="${type}" data-path="${item}">
                            <img src="${icon}" class="file-manager-image img-thumbnail" alt="${name}">
                        </div>
                        <input type="checkbox" class="select-item form-check-input ${hideInput}" data-path="${item}">
                        <span class="item-name">${name}</span>
                    </div>`;
            })
            .join('');
    }


    /**
     * Render pagination controls.
     * @param {number} totalPages - Total number of pages.
     * @param {number} currentPage - The current page number.
     * @param {number} totalItems - Total number of items.
     */
    function renderPagination(totalPages, currentPage, totalItems) {
        if (totalPages <= 1) {
            elements.paginationContainer.innerHTML = '';
            return;
        }

        let paginationHtml = '';

        paginationHtml += `
            <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn" data-page="1" ${currentPage === 1 ? 'disabled' : ''}>First</button>
            <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>`;

        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `
                <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }

        paginationHtml += `
            <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
            <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn" data-page="${totalPages}" ${currentPage === totalPages ? 'disabled' : ''}>Last</button>`;

        elements.paginationContainer.innerHTML = paginationHtml;
    }

    /**
     * Toggle the visibility of the 'Go Back' button based on the parent directory.
     * @param {string|null} parentDirectory - Path of the parent
     * directory. If null or invalid, the button is hidden.
     */
    function toggleGoBackButton(parentDirectory) {
        if (parentDirectory && parentDirectory !== "\\" && parentDirectory !== ".") {
            elements.goBack.style.display = 'block';
            elements.goBack.dataset.path = parentDirectory;
        } else {
            elements.goBack.style.display = 'none';
        }
    }

    /**
     * Create a new directory at the current path.
     * @param {string} dirName - Name of the new directory.
     */
    function createDirectory(dirName) {
        $.ajax({
            url: '/file-manager/create-directory',
            method: 'POST',
            data: {
                path: currentPath,
                name: dirName,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                fetchFiles(currentPath);
            }
        });
    }

    /**
     * Upload a file to the current path.
     * Triggered when a file is selected via the file input element.
     */
    function uploadFile() {
        const file = elements.uploadFileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('path', currentPath);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: '/file-manager/upload',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                fetchFiles(currentPath);
            }
        });
    }

    /**
     * Delete selected files and directories.
     * Confirms the action before sending a deletion request to the server.
     */
    function deleteSelectedItems() {
        const selectedPaths = Array.from(document.querySelectorAll('.select-item:checked')).map(
            checkbox => checkbox.dataset.path
        );

        if (selectedPaths.length > 0 && confirm('Are you sure you want to delete the selected items?')) {
            $.ajax({
                url: '/file-manager/delete-multiple',
                method: 'POST',
                data: {
                    paths: selectedPaths,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    fetchFiles(currentPath);
                    elements.deleteSelected.classList.add('d-none'); // Hide the delete button after action
                }
            });
        }
    }
});
