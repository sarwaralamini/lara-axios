<!-- File Manager Modal 1-->
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
