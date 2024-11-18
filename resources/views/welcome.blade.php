<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .form-control:focus {
      border-color: #6a11cb;
      box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
    }
    .btn-primary {
      background-color: #6a11cb;
      border-color: #6a11cb;
    }
    .btn-primary:hover {
      background-color: #2575fc;
      border-color: #2575fc;
    }
    #spinner {
      display: none;
    }
    #button-text-logging{
        display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card p-4">
          <div id="alert-container"></div>
          <h3 class="text-center mb-4">Login</h3>
          <form id="login-form" method="POST">
            <input type="hidden" name="device_name" value="mi9tpro">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
              <span id="button-text">Login</span>
              <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              <span id="button-text-logging">Logging...</span>
            </button>
            <div class="text-center mt-3">
              <a href="#" class="text-decoration-none">Forgot Password?</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script>
    document.getElementById('login-form').addEventListener('submit', async function (event) {
      event.preventDefault();

      const usernameField = document.getElementById('username');
      const passwordField = document.getElementById('password');
      const button = event.target.querySelector('button');
      const spinner = document.getElementById('spinner');
      const buttonText = document.getElementById('button-text');
      const buttonTextLogging = document.getElementById('button-text-logging');

      const alertContainer = document.getElementById('alert-container');

      // Disable inputs and button, show spinner
      usernameField.disabled = true;
      passwordField.disabled = true;
      button.disabled = true;
      spinner.style.display = 'inline-block';
      buttonTextLogging.style.display = 'inline-block';
      buttonText.style.display = 'none';

      //MOCKING FAKE LOGIN
      setTimeout(() => {
        showAlert('success', 'Login successful! Redirecting to dashboard...');
            setTimeout(() => {
            window.location.href = '/dashboard'; // Redirect after 2 seconds
        }, 2000);
      }, 3000); // Wait 3 seconds before login success messsage


    //   try {
    //     const response = await axios.post('/api/login', {
    //       username: usernameField.value,
    //       password: passwordField.value,
    //       device_name: 'mi9tpro'
    //     });

    //     // Handle success
    //     if (response.status === 200) {
    //       showAlert('success', 'Login successful! Redirecting to dashboard...');
    //       setTimeout(() => {
    //         window.location.href = '/dashboard'; // Redirect after 2 seconds
    //       }, 2000);
    //     }
    //   } catch (error) {
    //     showAlert('danger', 'Login failed. Please check your credentials.');
    //   } finally {
    //     // Re-enable inputs and button, hide spinner
    //     usernameField.disabled = false;
    //     passwordField.disabled = false;
    //     button.disabled = false;
    //     spinner.style.display = 'none';
    //     buttonText.style.display = 'inline';
    //   }

      function showAlert(type, message) {
        alertContainer.innerHTML = `
          <div class="alert alert-${type} alert-dismissible fade show text-center" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `;
      }
    });
  </script>
</body>
</html>
