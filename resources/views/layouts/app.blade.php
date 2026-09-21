<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | EBS - University of Batangas</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- Select2 CSS & Bootstrap 5 Theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --ub-maroon: #7B1113;
            --ub-maroon-dark: #580B0D;
            --ub-maroon-light: #991B1E;
            --ub-gold: #F5B800;
            --ub-gold-dark: #D49B00;
            --ub-gold-light: #FFF8E6;
            --ub-bg: #F8FAFC;
            --ub-card: #FFFFFF;
            --ub-text-main: #0F172A;
            --ub-text-muted: #64748B;
            --ub-border: #E2E8F0;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--ub-bg);
            color: var(--ub-text-main);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Layout Architecture */
        .app-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #6B0E10 0%, #4A0809 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand {
            padding: 20px 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-decoration: none;
            color: #ffffff;
        }

        .sidebar-brand:hover {
            color: #ffffff;
        }

        .brand-icon-box {
            width: 42px;
            height: 42px;
            background: rgba(245, 184, 0, 0.18);
            border: 2px solid var(--ub-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ub-gold);
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .brand-text-title {
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
            line-height: 1.1;
            color: #ffffff;
        }

        .brand-badge {
            background: var(--ub-gold);
            color: var(--ub-maroon-dark);
            font-size: 10px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 4px;
            vertical-align: middle;
        }

        .brand-text-subtitle {
            font-size: 11px;
            font-weight: 400;
            opacity: 0.8;
            letter-spacing: 0.2px;
        }

        /* User Profile in Sidebar */
        .sidebar-user {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0, 0, 0, 0.15);
        }

        .user-avatar-circle {
            width: 40px;
            height: 40px;
            background: var(--ub-gold);
            color: var(--ub-maroon-dark);
            font-weight: 800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        }

        .user-meta {
            overflow: hidden;
            line-height: 1.25;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .user-role-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            color: var(--ub-gold);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Navigation Links */
        .sidebar-nav {
            padding: 18px 12px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.45);
            font-weight: 700;
            padding: 8px 12px 6px;
            margin-top: 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: rgba(255, 255, 255, 0.82);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            transition: color 0.2s;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link:hover i {
            color: var(--ub-gold);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: rgba(245, 184, 0, 0.18);
            border-left: 3.5px solid var(--ub-gold);
            font-weight: 700;
        }

        .sidebar-link.active i {
            color: var(--ub-gold);
        }

        .sidebar-footer {
            padding: 16px 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
        }

        /* Main Content Panel */
        .main-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - var(--sidebar-width));
        }

        /* When user is not authenticated (guest pages) */
        .main-wrapper-full {
            margin-left: 0 !important;
            width: 100% !important;
        }

        /* Top Header Bar */
        .topbar {
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid var(--ub-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .topbar-toggle-btn {
            background: transparent;
            border: 1px solid var(--ub-border);
            color: var(--ub-maroon);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 16px;
            cursor: pointer;
            display: none;
        }

        .topbar-brand-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--ub-maroon);
            margin: 0;
        }

        /* Main Body Area */
        .content-body {
            flex-grow: 1;
            padding: 24px;
        }

        /* Footer */
        .app-footer {
            background: #ffffff;
            border-top: 1px solid var(--ub-border);
            padding: 14px 24px;
            font-size: 12px;
            color: var(--ub-text-muted);
        }

        /* Sidebar Backdrop Overlay on Mobile */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 1030;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .topbar-toggle-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .content-body {
                padding: 16px;
            }
        }

        /* Common Custom Cards & Buttons */
        .card-ub {
            background: #ffffff;
            border: 1px solid var(--ub-border);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-ub:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }

        .card-header-ub {
            background: #ffffff;
            border-bottom: 1px solid var(--ub-border);
            padding: 16px 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            font-weight: 700;
            color: var(--ub-maroon);
        }

        .btn-ub-primary {
            background-color: var(--ub-maroon);
            border-color: var(--ub-maroon);
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 18px;
            transition: all 0.2s;
        }

        .btn-ub-primary:hover,
        .btn-ub-primary:focus {
            background-color: var(--ub-maroon-dark);
            border-color: var(--ub-maroon-dark);
            color: var(--ub-gold);
            transform: translateY(-1px);
        }

        .btn-ub-gold {
            background-color: var(--ub-gold);
            border-color: var(--ub-gold);
            color: var(--ub-maroon-dark);
            font-weight: 700;
            border-radius: 8px;
            padding: 8px 18px;
        }

        .btn-ub-gold:hover {
            background-color: var(--ub-gold-dark);
            border-color: var(--ub-gold-dark);
            color: #000;
        }

        /* Status Badges */
        .badge-status-ongoing {
            background-color: #FEF3C7;
            color: #92400E;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            border: 1px solid #FDE68A;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-returned {
            background-color: #DCFCE7;
            color: #166534;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            border: 1px solid #BBF7D0;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-overdue {
            background-color: #FEE2E2;
            color: #991B1B;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            border: 1px solid #FECACA;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(220, 38, 38, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }

        /* Modern DataTables and Table UI Styling */
        .dataTables_wrapper {
            padding: 0;
        }

        .dataTables_wrapper .row:first-child {
            padding: 14px 18px 12px 18px;
            align-items: center;
            margin: 0;
        }

        .dataTables_wrapper .row:last-child {
            padding: 12px 18px;
            align-items: center;
            border-top: 1px solid var(--ub-border);
            background: #fafafa;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            margin: 0;
        }

        .dataTables_wrapper .dataTables_length label {
            font-size: 13px;
            color: #475569;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px 24px 4px 8px;
            font-size: 13px;
            color: #334155;
            background-color: #ffffff;
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_filter label {
            font-size: 13px;
            color: #475569;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 13px;
            color: #1e293b;
            background-color: #ffffff;
            min-width: 220px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--ub-maroon);
            box-shadow: 0 0 0 3px rgba(123, 17, 19, 0.12);
            outline: none;
        }

        .dataTables_info {
            font-size: 12.5px;
            color: #64748b;
            font-weight: 500;
            padding-top: 4px !important;
        }

        .dataTables_paginate {
            display: flex;
            justify-content: flex-end;
        }

        .dataTables_paginate .pagination {
            margin: 0;
            gap: 4px;
        }

        .dataTables_paginate .page-item .page-link {
            border-radius: 6px !important;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            transition: all 0.2s;
            background: #ffffff;
        }

        .dataTables_paginate .page-item .page-link:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--ub-maroon);
        }

        .dataTables_paginate .page-item.active .page-link {
            background-color: var(--ub-maroon) !important;
            border-color: var(--ub-maroon) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(123, 17, 19, 0.25);
        }

        .dataTables_paginate .page-item.disabled .page-link {
            background-color: #f8fafc;
            color: #94a3b8;
            border-color: #e2e8f0;
        }

        /* Clean Modern Table Headers & Rows */
        table.dataTable {
            margin: 0 !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }

        table.dataTable thead th {
            background: #f8fafc !important;
            color: #475569 !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            padding: 13px 16px !important;
            border-top: 1px solid #e2e8f0 !important;
            border-bottom: 2px solid #e2e8f0 !important;
            white-space: nowrap !important;
            position: relative;
        }

        table.dataTable tbody td {
            padding: 13px 16px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #1e293b;
            font-size: 13px;
        }

        table.dataTable tbody tr:hover {
            background-color: #fcfdfe !important;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid var(--ub-border) !important;
        }

        /* Select2 Custom UB Clean White Styling */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            min-height: 42px;
            display: flex;
            align-items: center;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: var(--ub-maroon);
            box-shadow: 0 0 0 3px rgba(123, 17, 19, 0.12);
            background-color: #ffffff;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            background-color: #ffffff;
            overflow: hidden;
            z-index: 1060;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown {
            padding: 8px 10px;
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13.5px;
            background-color: #ffffff;
            color: #1e293b;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--ub-maroon);
            box-shadow: 0 0 0 3px rgba(123, 17, 19, 0.1);
            outline: none;
        }

        .select2-container--bootstrap-5 .select2-results__options {
            background-color: #ffffff;
            padding: 4px;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            background-color: #ffffff;
            color: #1e293b;
            padding: 8px 10px;
            border-radius: 6px;
            margin-bottom: 2px;
            transition: all 0.15s ease;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: #fff1f2 !important;
            color: #7B1113 !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #ffe4e6 !important;
            color: #7B1113 !important;
            font-weight: 700;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 12px;
            padding-right: 28px;
            color: #1e293b;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--ub-maroon);
            box-shadow: 0 0 0 0.2rem rgba(123, 17, 19, 0.15);
        }

        .is-invalid {
            border-color: #dc3545 !important;
            background-image: none !important;
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #dc3545;
            margin-top: 4px;
        }

        /* Clean Fullscreen Split Auth Layout (UB Reference Style) */
        .auth-clean-body {
            background-color: #ffffff !important;
            color: #1e293b;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .auth-fullscreen-container {
            min-height: 100vh;
            width: 100%;
            display: flex;
            position: relative;
            background-color: #ffffff;
        }

        .auth-top-left-seal {
            position: absolute;
            top: 28px;
            left: 36px;
            z-index: 20;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .ub-seal-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff8e6;
            border: 2px solid var(--ub-gold);
            color: var(--ub-maroon);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(245, 184, 0, 0.25);
        }

        .ub-seal-icon-small {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff8e6;
            border: 2px solid var(--ub-gold);
            color: var(--ub-maroon);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 auto 12px;
            box-shadow: 0 4px 12px rgba(245, 184, 0, 0.2);
        }

        .auth-split-left {
            flex: 1 1 52%;
            position: relative;
            min-height: 100vh;
            background-image: url('{{ asset('images/login-background.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
        }

        .auth-split-left-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.3) 0%, rgba(0, 0, 0, 0) 35%, rgba(15, 23, 42, 0.8) 100%);
            z-index: 1;
            pointer-events: none;
        }

        .auth-split-left-content {
            position: relative;
            z-index: 2;
            color: #ffffff;
        }

        .auth-hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 7px 16px;
            border-radius: 50px;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .auth-split-right {
            flex: 1 1 48%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px 40px;
            background-color: #ffffff;
        }

        .auth-form-panel {
            width: 100%;
            max-width: 400px;
        }

        .auth-input-clean {
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            color: #1e293b;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .auth-input-clean:focus {
            border-color: #7B1113;
            box-shadow: 0 0 0 3px rgba(123, 17, 19, 0.12);
            background-color: #ffffff;
        }

        .btn-auth-primary-clean {
            background-color: #7B1113;
            border: 1.5px solid #7B1113;
            color: #ffffff;
            font-weight: 700;
            font-size: 14.5px;
            padding: 10px 16px;
            border-radius: 8px;
            width: 100%;
            transition: all 0.2s ease;
        }

        .btn-auth-primary-clean:hover {
            background-color: #580B0D;
            border-color: #580B0D;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(123, 17, 19, 0.25);
        }

        .btn-auth-outline-clean {
            background-color: transparent;
            border: 1.5px solid #7B1113;
            color: #7B1113;
            font-weight: 700;
            font-size: 14.5px;
            padding: 9px 16px;
            border-radius: 8px;
            width: 100%;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: all 0.2s ease;
        }

        .btn-auth-outline-clean:hover {
            background-color: #7B1113;
            color: #ffffff;
        }

        .password-toggle-btn-clean {
            background-color: transparent;
            border: 1.5px solid #cbd5e1;
            border-left: none;
            color: #94a3b8;
            padding: 0 12px;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .password-toggle-btn-clean:hover {
            color: #7B1113;
        }

        /* Demo Credentials Quick Pill Styling */
        .demo-chip-btn {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 10px;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }

        .demo-chip-btn:hover {
            border-color: var(--ub-maroon);
            background: #fff8f8;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(123, 17, 19, 0.12);
        }

        /* Responsive: Hide Left Campus Image Showcase on Mobile & Tablets, show ONLY the Form */
        @media (max-width: 991.98px) {
            .auth-fullscreen-container {
                flex-direction: column;
                min-height: 100vh;
                width: 100%;
            }

            .auth-split-left {
                display: none !important;
            }

            .auth-split-right {
                flex: 1 1 100% !important;
                width: 100% !important;
                min-height: 100vh;
                display: flex !important;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 36px 20px !important;
            }

            .auth-form-panel {
                width: 100% !important;
                max-width: 440px !important;
                margin: 0 auto;
            }

            .auth-top-left-seal {
                top: 16px;
                left: 20px;
            }
        }

        @media (max-width: 576px) {
            .auth-split-right {
                padding: 24px 16px !important;
            }

            .auth-form-panel {
                max-width: 100% !important;
            }
        }
    </style>

    @stack('styles')
</head>

@php
    $isAuthPage = !Auth::check() || request()->routeIs('verification.notice');
@endphp

<body class="{{ $isAuthPage ? 'auth-clean-body' : '' }}">

    @if ($isAuthPage)
        <!-- Fullscreen Clean Auth Template (Matches UB Reference) -->
        <main class="auth-fullscreen-container">
            @yield('content')
        </main>
    @else
        <div class="app-container">
            @auth
                <!-- Mobile Sidebar Backdrop -->
                <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

                <!-- Left Sidebar Navigation -->
                <aside class="sidebar" id="ebsSidebar">
                    <!-- Sidebar Brand Header (No ub-logo.png, clean modern badge) -->
                    <a href="{{ url('/') }}" class="sidebar-brand">
                        <div class="brand-icon-box">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <div class="brand-text-title">
                                EBS <span class="brand-badge">UB</span>
                            </div>
                            <div class="brand-text-subtitle">Equipment Borrowing System</div>
                        </div>
                    </a>

                    <!-- Logged-in User Profile Box -->
                    <div class="sidebar-user">
                        <div class="user-avatar-circle">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="user-meta">
                            <div class="user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                            <div class="user-role-badge">
                                {{ ucfirst(Auth::user()->role) }} @if (Auth::user()->department)
                                    <br> {{ Auth::user()->department }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Menu Items -->
                    <div class="sidebar-nav">
                        <div class="nav-section-title">Main Navigation</div>

                        @if (Auth::user()->isAdmin())
                            <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Dashboard</span>
                            </a>
                            <a class="sidebar-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}"
                                href="{{ route('admin.staff.index') }}">
                                <i class="fa-solid fa-user-shield"></i>
                                <span>Staff Management</span>
                            </a>
                            <a class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}"
                                href="{{ route('admin.students.index') }}">
                                <i class="fa-solid fa-user-graduate"></i>
                                <span>Student Management</span>
                            </a>
                            <a class="sidebar-link {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}"
                                href="{{ route('admin.borrowings.index') }}">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <span>Borrowing Logs</span>
                            </a>
                            <a class="sidebar-link {{ request()->routeIs('admin.penalties.*') ? 'active' : '' }}"
                                href="{{ route('admin.penalties.index') }}">
                                <i class="fa-solid fa-receipt"></i>
                                <span>Penalties</span>
                            </a>
                        @elseif(Auth::user()->isStaff())
                            <a class="sidebar-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}"
                                href="{{ route('staff.dashboard') }}">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Dashboard</span>
                            </a>
                            <a class="sidebar-link {{ request()->routeIs('staff.scanner.*') ? 'active' : '' }}"
                                href="{{ route('staff.scanner.index') }}">
                                <i class="fa-solid fa-qrcode"></i>
                                <span>Scan Student QR</span>
                            </a>
                            <a class="sidebar-link {{ request()->routeIs('staff.borrowings.create') ? 'active' : '' }}"
                                href="{{ route('staff.borrowings.create') }}">
                                <i class="fa-solid fa-plus-circle"></i>
                                <span>New Borrowing</span>
                            </a>
                        @elseif(Auth::user()->isStudent())
                            <a class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
                                href="{{ route('student.dashboard') }}">
                                <i class="fa-solid fa-qrcode"></i>
                                <span>My QR & Dashboard</span>
                            </a>
                            <a class="sidebar-link {{ request()->routeIs('student.history*') ? 'active' : '' }}"
                                href="{{ route('student.history') }}">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Borrowing History</span>
                            </a>
                        @endif
                    </div>
                </aside>
            @endauth

            <!-- Main Content Wrapper -->
            <div class="main-wrapper {{ !Auth::check() ? 'main-wrapper-full' : '' }}">
                <!-- Topbar Header -->
                <header class="topbar">
                    <div class="d-flex align-items-center gap-3">
                        @auth
                            <button type="button" class="topbar-toggle-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
                                <i class="fa-solid fa-bars-staggered"></i>
                            </button>
                        @endauth

                        @guest
                            <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
                                <div class="brand-icon-box" style="width: 34px; height: 34px; font-size: 16px;">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                                <span class="topbar-brand-title">University of Batangas &bull; EBS</span>
                            </a>
                        @else
                            <div class="topbar-brand-title d-none d-sm-block">
                                University of Batangas
                            </div>
                        @endguest
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @guest
                            <a href="{{ route('login') }}"
                                class="btn btn-sm {{ request()->routeIs('login') ? 'btn-ub-gold fw-bold shadow-sm' : 'btn-outline-light' }}">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                            </a>
                            <a href="{{ route('register') }}"
                                class="btn btn-sm {{ request()->routeIs('register') ? 'btn-ub-gold fw-bold shadow-sm' : 'btn-outline-light' }}">
                                <i class="fa-solid fa-user-plus me-1"></i> Register (@ub.edu.ph)
                            </a>
                        @else
                            <span class="badge bg-light text-dark border d-none d-md-inline-block px-3 py-2">
                                <i class="fa-regular fa-calendar me-1 text-muted"></i> {{ date('l, F d, Y') }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="m-0 d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Logout">
                                    <i class="fa-solid fa-power-off"></i>
                                </button>
                            </form>
                        @endguest
                    </div>
                </header>

                <!-- Main Body Content -->
                <main class="content-body">
                    @yield('content')
                </main>

                <!-- Application Footer -->
                <footer class="app-footer">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <strong>University of Batangas</strong>
                        </div>
                        <div>
                            &copy; {{ date('Y') }} EBS. All rights reserved.
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    @endif

    <!-- Scripts: JQuery, Bootstrap 5, DataTables, SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Mobile Sidebar Toggle Handling
        $('#sidebarToggle, #sidebarBackdrop').on('click', function() {
            $('#ebsSidebar').toggleClass('show');
            $('#sidebarBackdrop').toggleClass('show');
        });

        // SweetAlert2 Toast configuration for Top-Right Alerts in English
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4500,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#1e293b',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        @if (session('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{{ session('warning') }}"
            });
        @endif

        @if (session('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ session('info') }}"
            });
        @endif

        // Initialize DataTables automatically on any .datatable class (in English)
        $(document).ready(function() {
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    responsive: false,
                    autoWidth: false,
                    columnDefs: [
                        { orderable: false, targets: -1 }
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search records...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No records available",
                        zeroRecords: "No matching records found",
                        paginate: {
                            first: '<i class="fa-solid fa-angles-left"></i>',
                            previous: '<i class="fa-solid fa-chevron-left"></i>',
                            next: '<i class="fa-solid fa-chevron-right"></i>',
                            last: '<i class="fa-solid fa-angles-right"></i>'
                        }
                    },
                    order: []
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
