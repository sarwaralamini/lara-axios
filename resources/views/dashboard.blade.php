@extends('layouts.app')

@push('css')

@endpush

@section('title', 'Dashboard')

@section('content')
    <!-- Breadcrumb Section -->
    <nav aria-label="breadcrumb" class="breadcrumb-container">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <!-- Body Container -->
    <div class="body-container">
        <div class="card card-container">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Information</h5>
                <a href="{{ route('user.create')}}" class="btn btn-success btn-sm" id="createButton">
                    <i class="bi bi-plus-lg"></i> Create New User
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                <thead style="background-color: #f3f6f9; color: #0A2B3D;">
                    <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                    <td colspan="4">
                        <div class="loading-text text-center">
                        <p class="animated-text">Loading, please wait...</p>
                        </div>
                    </td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
        const sidebar = document.getElementById("sidebar");
        const toggleButton = document.getElementById("menu-toggle");

        // Toggle Sidebar on Button Click
        toggleButton.addEventListener("click", () => {
            sidebar.classList.toggle("visible");
        });

        // Simulate an API call with setTimeout
        const apiData = [
            { id: 1, name: "John Doe", email: "john.doe@example.com", role: "Admin" },
            { id: 2, name: "Jane Smith", email: "jane.smith@example.com", role: "User" },
            { id: 3, name: "Michael Brown", email: "michael.brown@example.com", role: "Moderator" }
        ];

        // Show loading spinner
        const tableBody = document.getElementById("tableBody");

        setTimeout(() => {
            // Clear loading spinner
            tableBody.innerHTML = "";

            // Populate table with data
            apiData.forEach((item, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>${item.email}</td>
                <td>${item.role}</td>
            `;
            tableBody.appendChild(row);
            });
        }, 5000); // Simulated API call delay of 2 seconds
        });
    </script>
@endpush
