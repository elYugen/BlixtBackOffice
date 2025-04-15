<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blixt Espace Auteur - @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('cropped-favico-192x192.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>
    <script>
        // On page load, apply dark mode if enabled in localStorage
        (function() {
            if (localStorage.getItem('blixt_dark_mode') === '1') {
                document.body.classList.add('dark-mode');
            }
        })();
    </script>
    <style>
        body.dark-mode {
            background: #1b1e26 !important;
            color: #f3f3f3 !important;
        }
        /* NAVBAR */
        body.dark-mode .navbar,
        body.dark-mode .navbar.navbar-dark {
            background: #232635 !important;
            color: #f3f3f3 !important;
        }
        body.dark-mode .navbar .navbar-brand,
        body.dark-mode .navbar .nav-link,
        body.dark-mode .navbar .text-white,
        body.dark-mode .navbar .me-3,
        body.dark-mode .navbar .fw-bold,
        body.dark-mode .navbar .active {
            color: #b3c7f9 !important;
        }
        body.dark-mode .navbar .nav-link.active,
        body.dark-mode .navbar .nav-link:hover {
            color: #fff !important;
            background: none !important;
            box-shadow: none !important;
        }
        body.dark-mode .navbar .dropdown-menu {
            background: #232635 !important;
            color: #f3f3f3 !important;
        }
        body.dark-mode .dropdown-item {
            color: #f3f3f3 !important;
        }
        body.dark-mode .dropdown-item:hover {
            background: #0a549a !important;
            color: #fff !important;
        }
        /* PROFILE HEADER */
        body.dark-mode .profile-header {
            background: linear-gradient(90deg, #232635 60%, #2a3550 100%) !important;
            color: #f3f3f3 !important;
            box-shadow: 0 4px 16px rgba(10,84,154,0.12) !important;
        }
        body.dark-mode .profile-header .profile-avatar {
            border: 4px solid #232635 !important;
            background: #232635 !important;
        }
        body.dark-mode .profile-header .profile-meta,
        body.dark-mode .profile-header .profile-bio {
            color: #b3c7f9 !important;
        }
        body.dark-mode .profile-header .profile-stats span {
            background: rgba(179,199,249,0.13) !important;
            color: #b3c7f9 !important;
        }
        /* CARDS & BOXES */
        body.dark-mode .dashboard-card,
        body.dark-mode .profile-card,
        body.dark-mode .form-section,
        body.dark-mode .card,
        body.dark-mode .stats-card,
        body.dark-mode .description-box,
        body.dark-mode .form-container,
        body.dark-mode .unsplash-container,
        body.dark-mode .image-preview-container,
        body.dark-mode .reader-container,
        body.dark-mode .chapter-content {
            background: #232635 !important;
            color: #f3f3f3 !important;
            border-color: #44485a !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.18) !important;
        }
        body.dark-mode .card-title,
        body.dark-mode .fw-bold,
        body.dark-mode h1,
        body.dark-mode h3,
        body.dark-mode h5,
        body.dark-mode .profile-header .profile-name {
            color: #b3c7f9 !important;
        }
        body.dark-mode .info-label,
        body.dark-mode .text-muted,
        body.dark-mode .form-label,
        body.dark-mode .dashboard-card h5 {
            color: #b3c7f9 !important;
        }
        /* BADGES */
        body.dark-mode .badge.bg-secondary {
            background: #44485a !important;
            color: #b3c7f9 !important;
        }
        body.dark-mode .badge.bg-primary {
            background: #3b82f6 !important;
            color: #fff !important;
        }
        body.dark-mode .badge.bg-info {
            background: #2563eb !important;
            color: #fff !important;
        }
        body.dark-mode .badge.bg-success {
            background: #22c55e !important;
            color: #fff !important;
        }
        body.dark-mode .badge.bg-warning {
            background: #facc15 !important;
            color: #232635 !important;
        }
        body.dark-mode .badge.bg-danger {
            background: #ef4444 !important;
            color: #fff !important;
        }
        /* BUTTONS */
        body.dark-mode .btn-primary,
        body.dark-mode .btn-success,
        body.dark-mode .btn-danger,
        body.dark-mode .btn-info,
        body.dark-mode .btn,
        body.dark-mode .btn-sm,
        body.dark-mode .create-button {
            background: #0a549a !important;
            color: #fff !important;
            border-color: #0a549a !important;
        }
        body.dark-mode .btn-primary:hover,
        body.dark-mode .btn-success:hover,
        body.dark-mode .btn-danger:hover,
        body.dark-mode .btn-info:hover,
        body.dark-mode .btn:hover,
        body.dark-mode .btn-sm:hover,
        body.dark-mode .create-button:hover {
            background: #083d71 !important;
            color: #fff !important;
            border-color: #083d71 !important;
        }
        body.dark-mode .btn-outline-primary {
            color: #b3c7f9 !important;
            border-color: #b3c7f9 !important;
        }
        body.dark-mode .btn-outline-primary:hover {
            background: #3b82f6 !important;
            color: #fff !important;
            border-color: #3b82f6 !important;
        }
        /* ALERTS */
        body.dark-mode .alert-success {
            background: #22303c !important;
            color: #b3c7f9 !important;
            border-color: #22303c !important;
        }
        body.dark-mode .alert-danger {
            background: #3b1e26 !important;
            color: #ffb3b3 !important;
            border-color: #3b1e26 !important;
        }
        /* TABLES & DATATABLES */
        body.dark-mode .table,
        body.dark-mode .table-striped,
        body.dark-mode .table-hover,
        body.dark-mode .prospect-table,
        body.dark-mode .dataTable,
        body.dark-mode .dataTables_wrapper,
        body.dark-mode table.dataTable {
            background: #232635 !important;
            color: #f3f3f3 !important;
        }
        body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #23283a !important;
        }
        body.dark-mode .table thead th,
        body.dark-mode .prospect-table thead th,
        body.dark-mode table.dataTable thead th,
        body.dark-mode table.dataTable tfoot th {
            background-color: #232635 !important;
            color: #b3c7f9 !important;
            border-bottom: 2px solid #44485a !important;
        }
        body.dark-mode .table td,
        body.dark-mode .table th,
        body.dark-mode table.dataTable tbody td {
            border-color: #44485a !important;
            background-color: #232635 !important;
            color: #f3f3f3 !important;
        }
        body.dark-mode .table img,
        body.dark-mode .preview-image {
            box-shadow: 0 2px 4px rgba(0,0,0,0.25);
            border: 2px solid #232635;
        }
        /* PAGINATION & FILTERS */
        body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #b3c7f9 !important;
            background: #232635 !important;
        }
        body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            color: #fff !important;
            background: #0a549a !important;
        }
        body.dark-mode .dataTables_wrapper .dataTables_filter input,
        body.dark-mode .dataTables_wrapper .dataTables_length select {
            /* DARK MODE: ALL INPUTS, TEXTAREAS, SELECTS, INPUT GROUPS */
            body.dark-mode input,
            body.dark-mode textarea,
            body.dark-mode select,
            body.dark-mode .form-control,
            body.dark-mode .form-select,
            body.dark-mode .input-group-text {
                background: #232635 !important;
                color: #f3f3f3 !important;
                border-color: #44485a !important;
            }
            body.dark-mode input:focus,
            body.dark-mode textarea:focus,
            body.dark-mode select:focus,
            body.dark-mode .form-control:focus,
            body.dark-mode .form-select:focus {
                background: #232635 !important;
                color: #f3f3f3 !important;
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 0.2rem rgba(59,130,246,0.15) !important;
            }
            body.dark-mode input::placeholder,
            body.dark-mode textarea::placeholder,
            body.dark-mode .form-control::placeholder {
                color: #b3c7f9 !important;
                opacity: 1;
            }
            body.dark-mode input:disabled,
            body.dark-mode textarea:disabled,
            body.dark-mode select:disabled,
            body.dark-mode .form-control:disabled,
            body.dark-mode .form-select:disabled {
                background: #232635 !important;
                color: #888 !important;
                opacity: 0.7;
            }
            body.dark-mode input.is-invalid,
            body.dark-mode textarea.is-invalid,
            body.dark-mode select.is-invalid,
            body.dark-mode .form-control.is-invalid,
            body.dark-mode .form-select.is-invalid {
                background: #3b1e26 !important;
                color: #ffb3b3 !important;
                border-color: #ef4444 !important;
            }
            /* FORM CONTAINER */
            body.dark-mode .form-container {
                background: #232635 !important;
                color: #f3f3f3 !important;
                border-color: #44485a !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.18) !important;
            }
            /* TINYMCE EDITOR */
            body.dark-mode .tox .tox-edit-area__iframe,
            body.dark-mode .tox .tox-edit-area {
                background: #232635 !important;
                color: #f3f3f3 !important;
            }
            body.dark-mode .tox .tox-toolbar,
            body.dark-mode .tox .tox-toolbar__primary,
            body.dark-mode .tox .tox-toolbar__overflow,
            body.dark-mode .tox .tox-toolbar__group {
                background: #232635 !important;
                color: #f3f3f3 !important;
            }
            /* READER HEADERS */
            body.dark-mode .reader-header {
                background: #232635 !important;
                color: #b3c7f9 !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.18) !important;
                border-bottom: 1px solid #44485a !important;
            }
            body.dark-mode .reader-header .chapter-title,
            body.dark-mode .reader-header h4 {
                color: #b3c7f9 !important;
            }
            /* CHAPTER CONTENT */
            body.dark-mode .chapter-content {
                background: #232635 !important;
                color: #f3f3f3 !important;
                border-color: #44485a !important;
                box-shadow: 0 1px 3px rgba(0,0,0,0.18) !important;
            }
            body.dark-mode .chapter-title {
                color: #b3c7f9 !important;
                border-bottom: 1px solid #44485a !important;
            }
        }
        body.dark-mode .dataTables_wrapper .dataTables_filter input,
        body.dark-mode .dataTables_wrapper .dataTables_length select {
            background: #232635 !important;
            color: #f3f3f3 !important;
            border-color: #44485a !important;
        }
        /* BREADCRUMB */
        body.dark-mode .breadcrumb {
            background: transparent !important;
        }
        body.dark-mode .breadcrumb-item,
        body.dark-mode .breadcrumb-item a {
            color: #b3c7f9 !important;
        }
        /* ICONS & STATS */
        body.dark-mode .icon-wrapper.bg-primary.bg-opacity-10,
        body.dark-mode .icon-wrapper.bg-success.bg-opacity-10,
        body.dark-mode .icon-wrapper.bg-info.bg-opacity-10,
        body.dark-mode .icon-wrapper.bg-danger.bg-opacity-10,
        body.dark-mode .icon-wrapper.bg-warning.bg-opacity-10,
        body.dark-mode .icon-wrapper.bg-purple.bg-opacity-10,
        body.dark-mode .icon-wrapper.bg-purple.bg-opacity-25 {
            background: #232635 !important;
        }
        body.dark-mode .icon-wrapper .text-primary,
        body.dark-mode .icon-wrapper .text-success,
        body.dark-mode .icon-wrapper .text-info,
        body.dark-mode .icon-wrapper .text-danger,
        body.dark-mode .icon-wrapper .text-warning {
            filter: brightness(1.2);
        }
        body.dark-mode .icon-wrapper .bi {
            opacity: 0.85;
        }
        /* LIST GROUPS */
        body.dark-mode .list-group-item {
            background: #232635 !important;
            color: #f3f3f3 !important;
            border-color: #44485a !important;
        }
        /* MODALS */
        body.dark-mode .modal-content {
            background: #232635 !important;
            color: #f3f3f3 !important;
            border-color: #44485a !important;
        }
        body.dark-mode .modal-header,
        body.dark-mode .modal-footer {
            border-color: #44485a !important;
        }
        /* SELECT2 */
        body.dark-mode .select2-container--bootstrap-5 .select2-selection {
            background: #232635 !important;
            color: #f3f3f3 !important;
            border-color: #44485a !important;
        }
        body.dark-mode .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #44485a !important;
            border: 1px solid #b3c7f9 !important;
            color: #b3c7f9 !important;
        }
        body.dark-mode .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #b3c7f9 !important;
        }
        body.dark-mode .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ef4444 !important;
            background-color: transparent !important;
        }
        /* CHAPTER READER */
        body.dark-mode .chapter-title {
            color: #b3c7f9 !important;
            border-bottom: 1px solid #44485a !important;
        }
        body.dark-mode .chapter-navigation {
            background: #232635 !important;
            color: #f3f3f3 !important;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.18) !important;
        }
        /* CAROUSEL (comics reader) */
        body.dark-mode .carousel-container {
            background: #232635 !important;
        }
        body.dark-mode .carousel-caption {
            background: rgba(27,30,38,0.85) !important;
            color: #f3f3f3 !important;
        }
        /* GENERAL */
        body.dark-mode hr {
            border-color: #44485a !important;
        }
        /* Add more dark mode overrides as needed */
    </style>
    @yield('content')

    @yield('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>