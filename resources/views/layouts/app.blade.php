<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title')</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('dist/css/style.css')}}">
  @stack('css')
</head>
<body>
  <!-- Top Navigation -->
  @include('layouts.inc.topbar')

  <div class="main-content">
    <!-- Sidebar -->
    @include('layouts.inc.sidebar')


    <!-- Main Content Area -->
    <div class="main-content-right" style="flex-grow: 1;">
        @yield('content')
    </div>
  </div>



  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2024 My Company</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('logout-button').addEventListener('click', async function (event) {

      const button = this;
      const spinner = document.getElementById('spinner');
      const buttonText = document.getElementById('button-text');
      const buttonTextLoggingOut = document.getElementById('button-text-logging-out');

      // Disable inputs and button, show spinner
      button.disabled = true;
      spinner.style.display = 'inline-block';
      buttonTextLoggingOut.style.display = 'inline-block';
      buttonText.style.display = 'none';

      //MOCKING FAKE LOGOUT
      setTimeout(() => {
        alert('You have been logged out successfully. You will be redirected to the login page shortly.')
        window.location.href = '/';
      }, 1000); // Wait 3 seconds before logged out success messsage
    });
  </script>
  @stack('js')
</body>
</html>
