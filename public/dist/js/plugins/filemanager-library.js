/**
 * FileManager Library
 * A reusable library for managing file operations in a modal interface.
 *
 * Author: Md.Sarwar Alam
 * GitHub: https://github.com/sarwaralamini
 * Version: 1.0.0
 * Created: December 18, 2024
 * Repository: https://github.com/sarwaralamini/file-manager-library
 * Dependencies: jQuery, Bootstrap (for modals)
 */

class FileManager {
    constructor(config) {
        // Default settings for the FileManager
        const defaultSettings = {
            modalSelector: '#fileManagerModal',
            openButtonSelector: '#openFileManager',
            fileListSelector: '#fileList',
            deleteButtonSelector: '#deleteSelected',
            searchInputSelector: '#searchFileInput',
            goBackButtonSelector: '#goBack',
            paginationContainerSelector: '#paginationContainer',
            fileInputSelector: '#fileInput',
            uploadFileInputSelector: '#uploadFileInput',
            createDirectorySelector: '#createDirectory',
            uploadButtonSelector: '#uploadFile',
            itemsPerPage: 12,
            defaultPath: '/catalog',
            storagePath: '',
            folderIcon: '',
            pdfIcon: '',
            hiddenNames: ['thumbnails', 'index.html', 'index.htm', 'index.php', 'index', '.gitignore', 'folder.png'],
            hiddenPaths: ['catalog/thumbnails/products', 'catalog/thumbnails/categories'],
            hideInputFor: ['products', 'categories'],
            endpoints: {
                fetchFiles: '/file-manager/files',
                createDirectory: '/file-manager/create-directory',
                uploadFile: '/file-manager/upload',
                deleteFiles: '/file-manager/delete-multiple',
            },
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            fileInputButtons: [] // Dynamic input-button pairs
        };

        // Merge user config with default settings
        this.settings = { ...defaultSettings, ...config };
        this.currentPath = this.settings.defaultPath;
        this.currentPage = 1;
        this.totalPages = 1;

        // Initialize DOM elements
        this.elements = {
            modal: document.querySelector(this.settings.modalSelector),
            openButton: document.querySelector(this.settings.openButtonSelector),
            fileList: document.querySelector(this.settings.fileListSelector),
            deleteButton: document.querySelector(this.settings.deleteButtonSelector),
            searchInput: document.querySelector(this.settings.searchInputSelector),
            goBackButton: document.querySelector(this.settings.goBackButtonSelector),
            paginationContainer: document.querySelector(this.settings.paginationContainerSelector),
            uploadFileInput: document.querySelector(this.settings.uploadFileInputSelector),
            createDirectory: document.querySelector(this.settings.createDirectorySelector),
            uploadButton: document.querySelector(this.settings.uploadButtonSelector),
        };

        // Initialize the modal from Bootstrap
        this.modal = new bootstrap.Modal(this.elements.modal);

        // Bind events for actions like search, delete, and file selection
        this.bindEvents();
        this.bindInputButtons(); // Bind the dynamic input/button pairs
    }

    /**
     * Add dynamic input and button pairs to be handled by the modal.
     * @param {string} inputSelector - Selector for the input field.
     * @param {string} buttonSelector - Selector for the associated button.
     */
    addFileInputButton(inputSelector, buttonSelector) {
        this.settings.fileInputButtons.push({ input: inputSelector, button: buttonSelector });
    }

    /**
     * Bind click events for each dynamic input-button pair and handle file selection.
     */
    bindInputButtons() {
        this.settings.fileInputButtons.forEach(({ input, button }) => {
            const inputElement = document.querySelector(input);
            const buttonElement = document.querySelector(button);
            buttonElement.addEventListener('click', () => {
                this.elements.fileList.innerHTML = ''; // Clear previous file list
                this.currentPath = this.settings.defaultPath; // Reset to default path
                this.currentPage = 1;

                // Fetch files and open modal
                this.fetchFiles(this.currentPath, this.currentPage);
                this.modal.show();

                // Handle file selection
                const handleFileSelection = (event) => {
                    const target = event.target.closest('.file');
                    if (target) {
                        const selectedPath = target.dataset.path;
                        if (inputElement) inputElement.value = selectedPath; // Set the selected path

                        // Close modal after selection
                        this.modal.hide();
                        document.removeEventListener('click', handleFileSelection);
                    }
                };

                // Attach event listener for file selection
                document.addEventListener('click', handleFileSelection);
            });
        });
    }

    /**
     * Bind events for file manager actions like search, delete, and directory navigation.
     */
    bindEvents() {
        // Handle live search input with debounce
        let debounceTimer;
        this.elements.searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                this.elements.deleteButton.classList.add('d-none'); // Hide delete button during search
                this.fetchFiles(this.currentPath, 1, this.elements.searchInput.value.trim(), true);
            }, 500);
        });

        // Delete selected items
        this.elements.deleteButton.addEventListener('click', () => this.deleteSelectedItems());

        // Navigate to the parent directory
        this.elements.goBackButton.addEventListener('click', () => {
            const parentPath = this.elements.goBackButton.dataset.path;
            this.currentPage = 1;
            this.fetchFiles(parentPath, this.currentPage);
        });

        // Create a new directory
        this.elements.createDirectory.addEventListener('click', () => {
            const dirName = prompt('Enter directory name:');
            if (dirName) this.createDirectory(dirName);
        });

        // File upload trigger
        this.elements.uploadButton.addEventListener('click', () => this.elements.uploadFileInput.click());
        this.elements.uploadFileInput.addEventListener('change', () => this.uploadFile());

        // General event delegation for dynamic actions (pagination, directory navigation)
        document.addEventListener('click', event => this.handleDynamicEvents(event));
        document.addEventListener('change', event => this.toggleDeleteButtonVisibility(event));
    }

    /**
     * Handle pagination and directory navigation based on the clicked target.
     * @param {Event} event - The event triggered by a click.
     */
    handleDynamicEvents(event) {
        const target = event.target;

        // Handle pagination click
        if (target.classList.contains('pagination-btn')) {
            const page = target.dataset.page;
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = parseInt(page);
                this.fetchFiles(this.currentPath, this.currentPage);
            }
        }

        // Handle directory navigation click
        if (target.closest('.directory')) {
            const path = target.closest('.directory').dataset.path;
            this.currentPage = 1;
            this.fetchFiles(path, this.currentPage);
        }
    }

    /**
     * Toggle visibility of the delete button based on selected items.
     * @param {Event} event - The change event triggered by selecting an item.
     */
    toggleDeleteButtonVisibility(event) {
        if (event.target.classList.contains('select-item')) {
            const selectedItems = document.querySelectorAll('.select-item:checked').length;
            this.elements.deleteButton.classList.toggle('d-none', selectedItems === 0);
        }
    }

    /**
     * Fetch files from the server for a given path and page, optionally applying a search filter.
     * @param {string} path - The directory path to fetch files from.
     * @param {number} page - The current page of files to fetch.
     * @param {string} [search=''] - Search query to filter files.
     * @param {boolean} [isSearch=false] - Whether this is a search request.
     */
    fetchFiles(path, page, search = '', isSearch = false) {
        this.elements.deleteButton.classList.add('d-none'); // Hide delete button while fetching

        $.ajax({
            url: this.settings.endpoints.fetchFiles,
            method: 'GET',
            data: { path, page, limit: this.settings.itemsPerPage, search, is_search: isSearch },
            success: response => {
                const { items, totalItems, totalPages, currentPage, parentDirectory } = response;
                this.currentPath = path;
                this.totalPages = totalPages;
                this.currentPage = currentPage;

                this.renderFiles(items); // Render file list
                this.renderPagination(totalPages, currentPage, totalItems); // Render pagination
                this.toggleGoBackButton(parentDirectory); // Toggle back button visibility
            }
        });
    }

    /**
     * Render the list of files as HTML.
     * @param {Array} items - The list of file paths to render.
     */
    renderFiles(items) {
        this.elements.fileList.innerHTML = items
            .map(item => {
                const name = item.split('/').pop();
                const isDir = !item.includes('.');
                const isPdf = item.endsWith('.pdf');
                const type = isDir ? 'directory' : 'file';
                const icon = isDir ? this.settings.folderIcon : (isPdf ? this.settings.pdfIcon : `${this.settings.storagePath}/${item}`);

                // Hide files and paths based on settings
                const hideFile = this.settings.hiddenNames.includes(name) ? 'd-none' : 'd-inline-block';
                const hidePath = this.settings.hiddenPaths.includes(item) ? 'd-none' : 'd-inline-block';
                const hideInput = this.settings.hideInputFor.includes(name) ? 'd-none' : 'd-inline-block';

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
     * Render pagination buttons.
     * @param {number} totalPages - Total number of pages.
     * @param {number} currentPage - The current page.
     * @param {number} totalItems - The total number of items.
     */
    renderPagination(totalPages, currentPage, totalItems) {
        if (totalPages <= 1) {
            this.elements.paginationContainer.innerHTML = ''; // No pagination if only one page
            return;
        }

        let paginationHtml = `
            <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn" data-page="1" ${currentPage === 1 ? 'disabled' : ''}>First</button>
            <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>`;

        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `
                <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }

        paginationHtml += `
            <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
            <button class="btn btn-sm btn-outline-primary mx-1 pagination-btn" data-page="${totalPages}" ${currentPage === totalPages ? 'disabled' : ''}>Last</button>`;

        this.elements.paginationContainer.innerHTML = paginationHtml;
    }

    /**
     * Toggle visibility of the "Go Back" button based on the parent directory.
     * @param {string} parentDirectory - The parent directory path.
     */
    toggleGoBackButton(parentDirectory) {
        if (parentDirectory && parentDirectory !== "\\" && parentDirectory !== ".") {
            this.elements.goBackButton.style.display = 'block';
            this.elements.goBackButton.dataset.path = parentDirectory;
        } else {
            this.elements.goBackButton.style.display = 'none';
        }
    }

    /**
     * Create a new directory on the server.
     * @param {string} dirName - The name of the new directory.
     */
    createDirectory(dirName) {
        $.ajax({
            url: this.settings.endpoints.createDirectory,
            method: 'POST',
            data: {
                path: this.currentPath,
                name: dirName,
                _token: this.settings.csrfToken,
            },
            success: () => this.fetchFiles(this.currentPath), // Refresh file list
        });
    }

    /**
     * Upload a file to the current directory.
     */
    uploadFile() {
        const file = this.elements.uploadFileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('path', this.currentPath);
        formData.append('_token', this.settings.csrfToken);

        $.ajax({
            url: this.settings.endpoints.uploadFile,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: () => this.fetchFiles(this.currentPath), // Refresh file list
        });
    }

    /**
     * Delete the selected files from the server.
     */
    deleteSelectedItems() {
        const selectedPaths = Array.from(document.querySelectorAll('.select-item:checked')).map(checkbox => checkbox.dataset.path);
        if (selectedPaths.length > 0 && confirm('Are you sure you want to delete the selected items?')) {
            $.ajax({
                url: this.settings.endpoints.deleteFiles,
                method: 'POST',
                data: {
                    paths: selectedPaths,
                    _token: this.settings.csrfToken,
                },
                success: () => this.fetchFiles(this.currentPath), // Refresh file list
            });
        }
    }
}
