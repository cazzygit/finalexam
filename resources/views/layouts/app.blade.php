<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Management')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <!-- <div class="nav-brand">
                <a href="{{ route('colleges.index') }}" class="brand-link">
                    Student Management System
                </a>
            </div> -->
            <div class="nav-buttons">
                <a href="{{ route('colleges.index') }}" class="nav-button @if(request()->routeIs('colleges.*')) active @endif">
                    Colleges
                </a>
                <a href="{{ route('students.index') }}" class="nav-button @if(request()->routeIs('students.*')) active @endif">
                    Students
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="main-content">
            <!-- Success Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <div class="alert-content">
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                    <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            <!-- Error Messages -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <div class="alert-content">
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                    <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            <!-- Warning Messages -->
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible">
                    <div class="alert-content">
                        <strong>Warning!</strong> {{ session('warning') }}
                    </div>
                    <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            <!-- Validation Error Messages -->
            @if($errors->any() && !session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <div class="alert-content">
                        <strong>Please fix the following errors:</strong>
                        <ul class="error-list">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- JavaScript -->
    <script type="module" src="{{ asset('js/app.js') }}"></script>
    <script type="module" src="{{ asset('js/notif.js') }}"></script>

    
    <!-- Basic Alert Functionality -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert.style.display !== 'none') {
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.style.display = 'none';
                        }, 300);
                    }
                }, 5000);
            });
        });

        // Confirmation for delete actions
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[method="POST"] button[type="submit"].btn-danger');
            deleteForms.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>