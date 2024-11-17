<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <style>
    body {
      background: #f8f9fa;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      margin: 0;
    }
    .logo {
      font-size: 2rem;
      font-weight: bold;
      color: #6a11cb;
      margin-bottom: 20px;
    }
    .table-container {
      width: 80%;
      max-width: 900px;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      padding: 20px;
    }
    .table thead {
      background-color: #6a11cb;
      color: #fff;
    }
    .loading-spinner {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 200px;
    }
  </style>
</head>
<body>
  <div class="logo text-center">My Dashboard</div>
  <div class="table-container">
    <!-- Placeholder for loading spinner -->
    <div id="loading" class="loading-spinner">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <!-- Placeholder for table -->
    <table class="table table-striped table-hover d-none" id="dataTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        <!-- Rows will be inserted here -->
      </tbody>
    </table>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Simulate an API call with setTimeout
      const apiData = [
        { id: 1, name: "John Doe", email: "john.doe@example.com", role: "Admin" },
        { id: 2, name: "Jane Smith", email: "jane.smith@example.com", role: "User" },
        { id: 3, name: "Michael Brown", email: "michael.brown@example.com", role: "Moderator" }
      ];

      // Show loading spinner
      const loading = document.getElementById("loading");
      const table = document.getElementById("dataTable");
      const tableBody = document.getElementById("tableBody");

      setTimeout(() => {
        // Remove loading spinner
        loading.classList.add("d-none");

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

        // Show table
        table.classList.remove("d-none");
      }, 2000); // Simulated API call delay of 2 seconds
    });
  </script>
</body>
</html>
