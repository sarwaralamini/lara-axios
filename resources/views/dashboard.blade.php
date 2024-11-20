<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root {
      --primary-color: #252b36; /* Dark Slate */
      --secondary-color: #03dac6; /* Teal */
      --background-color: #f5f5f5; /* Light Gray */
      --surface-color: #ffffff; /* White */
      --on-primary-color: #ffffff; /* White text on Primary */
      --on-surface-color: #000000; /* Black text on Surface */
      --border-color: #414955; /* Lighter version of Primary for borders */
    }

    body {
      margin: 0;
      background: var(--background-color);
      color: var(--on-surface-color);
      display: flex;
      flex-direction: column;
      height: 100vh;
    }

    .main-content {
      display: flex;
      flex-grow: 1;
      overflow: hidden;
    }

    .sidebar {
      width: 250px;
      background-color: var(--primary-color);
      color: var(--on-primary-color);
      flex-shrink: 0;
      padding: 15px;
      border-right: 1px solid var(--border-color); /* Subtle border */
      transition: transform 0.3s ease;
      height: 100%;
    }

    .sidebar.hidden {
      transform: translateX(-100%);
    }

    .sidebar .nav-link {
      color: var(--on-primary-color);
      margin-bottom: 10px;
      border-radius: 5px;
      padding: 10px;
    }

    .sidebar .nav-link:hover {
      background-color: var(--secondary-color);
      color: var(--on-surface-color);
    }

    .top-nav {
      background-color: var(--primary-color);
      color: var(--on-primary-color);
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--border-color); /* Subtle border */
    }

    .btn-outline-light {
        border: 2px solid var(--secondary-color); /* Teal border */
        color: var(--secondary-color); /* Teal text */
        transition: background-color 0.3s, color 0.3s, border-color 0.3s;
    }

    .btn-outline-light:hover {
        background-color: var(--secondary-color);
        color: var(--on-primary-color); /* White text on hover */
        border-color: var(--secondary-color);
    }

    .menu-toggle {
      display: none;
      cursor: pointer;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: var(--on-primary-color);
    }

    .footer {
      background-color: var(--primary-color);
      color: var(--on-primary-color);
      text-align: center;
      padding: 10px 0;
      border-top: 1px solid var(--border-color); /* Subtle border */
    }

    .table-container {
      flex-grow: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .breadcrumb-container {
      background-color: var(--surface-color); /* Matches table background */
      border: 1px solid var(--border-color); /* Subtle border */
      border-radius: 5px;
      padding: 10px 15px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .breadcrumb {
      margin: 0;
      padding: 0;
      background: none;
    }

    .breadcrumb-item a {
      color: var(--secondary-color); /* Teal links */
      text-decoration: none;
    }

    .breadcrumb-item a:hover {
      text-decoration: underline;
    }

    .card-container {
      margin-bottom: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Card shadow */
    }

    .card-header {
      background-color: var(--primary-color);
      color: var(--on-primary-color);
      border-bottom: 1px solid var(--border-color);
    }

    .loading-text {
    padding: 20px;
    font-size: 1.2rem;
    color: #252b36;
    font-weight: bold;
  }

  .animated-text {
    display: inline-block;
    animation: fadeInOut 1.5s infinite;
  }

  @keyframes fadeInOut {
    0% {
      opacity: 0;
    }
    50% {
      opacity: 1;
    }
    100% {
      opacity: 0;
    }
  }

    @media (max-width: 768px) {
      .menu-toggle {
        display: inline-block;
      }

      .sidebar {
        position: fixed;
        z-index: 1050;
        transform: translateX(-100%);
      }

      .main-content {
        margin-left: 0;
      }

      .sidebar.visible {
        transform: translateX(0);
      }
    }
  </style>
</head>
<body>
  <!-- Top Navigation -->
  <div class="top-nav">
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
    <span>Welcome, <strong>Username</strong></span>
    <button class="btn btn-outline-light btn-sm px-4 rounded-pill">Logout</button>
  </div>

  <!-- Main Content Area -->
  <div class="main-content">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
      <h4 class="text-center">Menu</h4>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="#" class="nav-link active">Dashboard</a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">Profile</a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">Settings</a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">Help</a>
        </li>
      </ul>
    </nav>

    <!-- Table Container -->
    <div class="table-container">
      <!-- Breadcrumb Section -->
      <nav aria-label="breadcrumb" class="breadcrumb-container">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
      </nav>

      <div class="logo text-center mb-4">My Dashboard</div>

      <!-- Card with Table -->
        <div class="card card-container">
            <div class="card-header">
            <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
            <!-- Create Button aligned to the right -->
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-success" id="createButton">
                <i class="bi bi-plus-lg"></i> Create New User
                </button>
            </div>

            <!-- Table with borders -->
            <table class="table table-bordered table-striped table-hover">
                <thead style="background-color: #252b36; color: white;">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                <!-- Loading Text shown while data is being fetched -->
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
  </div>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2024 My Company</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
