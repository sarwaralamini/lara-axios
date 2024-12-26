<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

    #button-text-Logging-out{
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
              <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" value="admin">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" value="password">
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script>
    const app_url = "{{ url('/') }}";
  </script>
  <script src="{{ asset('dist/js/common.js') }}"></script>
  <script>
        // Retrieve the current URL (or fallback to '/dashboard' if not set)
        @if (session('current_url'))
            const current_url = "{{ session('current_url') }}";
        @else
            const current_url = "/dashboard";
        @endif
  </script>
</body>
</html>
