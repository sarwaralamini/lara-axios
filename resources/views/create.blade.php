@extends('layouts.app')

@push('css')

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
    </div>
@endsection

@push('js')
<script>
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
