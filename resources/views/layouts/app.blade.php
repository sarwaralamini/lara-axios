<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title')</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
    <!-- Spinner Overlay -->
    <div class="loading-overlay" id="loadingSpinner_body" style="display: none;">
        <div>
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="processing-text mt-3">Logging out...</p>
        </div>
    </div>
  </div>



  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2024 My Company</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="{{ asset('dist/js/bootstrap-notify-3.1.3.min.js') }}"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script src="{{ asset('dist/js/common.js') }}"></script>
  <script>
  </script>
  @stack('js')
</body>
</html>
