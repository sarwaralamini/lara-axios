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
  @stack('js')
</body>
</html>
