<?php 
$CI =& get_instance();
$app_settings = $CI->db->get('settings')->row(); 
$theme_header_bg = isset($app_settings->header_color) && !empty($app_settings->header_color) ? $app_settings->header_color : '#ffffff';
$theme_sidebar_bg = isset($app_settings->sidebar_color) && !empty($app_settings->sidebar_color) ? $app_settings->sidebar_color : '#ffffff';
$theme_footer_bg = isset($app_settings->footer_color) && !empty($app_settings->footer_color) ? $app_settings->footer_color : '#f8f9fa';
$theme_menu_active = isset($app_settings->menu_active_color) && !empty($app_settings->menu_active_color) ? $app_settings->menu_active_color : '#2563eb';

if (!function_exists('get_contrast_color')) {
    function get_contrast_color($hexcolor) {
        if (strpos($hexcolor, '#') === 0) { $hexcolor = substr($hexcolor, 1); }
        if (strlen($hexcolor) === 3) { $hexcolor = $hexcolor[0].$hexcolor[0].$hexcolor[1].$hexcolor[1].$hexcolor[2].$hexcolor[2]; }
        if (strlen($hexcolor) !== 6) { return '#111418'; }
        $r = hexdec(substr($hexcolor, 0, 2));
        $g = hexdec(substr($hexcolor, 2, 2));
        $b = hexdec(substr($hexcolor, 4, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return ($yiq >= 128) ? '#111418' : '#ffffff';
    }
}

$theme_sidebar_text = get_contrast_color($theme_sidebar_bg);
$theme_sidebar_muted = ($theme_sidebar_text === '#ffffff') ? '#94a3b8' : '#617589';
$theme_sidebar_hover = ($theme_sidebar_text === '#ffffff') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
?>
<!DOCTYPE html>

<html class="light" lang="id-ID"><head>
<style>
    :root {
        --theme-header-bg: <?= $theme_header_bg ?>;
        --theme-sidebar-bg: <?= $theme_sidebar_bg ?>;
        --theme-footer-bg: <?= $theme_footer_bg ?>;
        --theme-menu-active: <?= $theme_menu_active ?>;
        --theme-sidebar-text: <?= $theme_sidebar_text ?>;
        --theme-sidebar-muted: <?= $theme_sidebar_muted ?>;
        --theme-sidebar-hover: <?= $theme_sidebar_hover ?>;
    }
</style>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta name="turbo-cache-control" content="no-cache">
<title>Moiz Care HRIS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "var(--theme-menu-active)",
                        "sidebar": "var(--theme-sidebar-bg)",
                        "sidebar-text": "var(--theme-sidebar-text)",
                        "sidebar-muted": "var(--theme-sidebar-muted)",
                        "sidebar-hover": "var(--theme-sidebar-hover)",
                        "header": "var(--theme-header-bg)",
                        "footer": "var(--theme-footer-bg)",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<!-- Global Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    /* DataTables Corporate Compact Style */
    
    /* Wrapper Spacing */
    .dataTables_wrapper { 
        padding: 1rem; 
        font-family: 'Inter', sans-serif; 
    }

    /* Top Control Controls (Search & Length) */
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_length select {
        padding: 0.35rem 2rem 0.35rem 0.75rem;
        border-radius: 0.375rem;
        border-color: #d1d5db;
        font-size: 0.875rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.35rem 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        font-size: 0.875rem;
        width: 200px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #1173d4;
        box-shadow: 0 0 0 2px rgba(17, 115, 212, 0.1);
        outline: none;
    }

    /* Main Table Styling */
    table.dataTable { 
        width: 100% !important; 
        border-collapse: separate; 
        border-spacing: 0;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    
    /* Header Styling - Force Visibility */
    table.dataTable thead th {
        background-color: #f3f4f6; 
        color: #374151; 
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem;
        border-bottom: 2px solid #e5e7eb;
        text-align: left;
    }
    
    /* Row Styling */
    table.dataTable tbody td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.875rem;
        color: #4b5563;
        vertical-align: middle;
    }
    
    /* Striped & Hover Effect */
    table.dataTable tbody tr:nth-child(even) { background-color: #f9fafb; }
    table.dataTable tbody tr:hover { background-color: #f0f9ff; }
    
    /* No Footer Border */
    table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb !important; }

    /* Pagination Styling */
    .dataTables_wrapper .dataTables_paginate { margin-top: 1rem; padding-top: 0.5rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3rem 0.75rem !important;
        margin: 0 0.1rem;
        border-radius: 0.375rem !important;
        border: 1px solid #e5e7eb !important;
        background: white !important;
        color: #4b5563 !important;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f3f4f6 !important;
        border-color: #d1d5db !important;
        color: #111827 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #1173d4 !important;
        color: white !important;
        border-color: #1173d4 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #0c62b8 !important;
        color: white !important;
    }
    
    /* Info Text */
    .dataTables_wrapper .dataTables_info {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 1.25rem;
    }

    /* Dark Mode Overrides */
    .dark .dataTables_wrapper .dataTables_length select,
    .dark .dataTables_wrapper .dataTables_filter input { background-color: #1f2937; border-color: #374151; color: white; }
    .dark table.dataTable { border-color: #374151; }
    .dark table.dataTable thead th { background-color: #1f2937; color: #d1d5db; border-bottom-color: #374151; }
    .dark table.dataTable tbody td { border-bottom-color: #374151; color: #d1d5db; }
    .dark table.dataTable tbody tr:nth-child(even) { background-color: #1a242d; }
    .dark table.dataTable tbody tr:hover { background-color: #2d3a4b; }
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button { background-color: #1f2937 !important; border-color: #374151 !important; color: #9ca3af !important; }
</style>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<!-- Upgrade to SPA -> Turbo Drive intercepts links to speed up app -->
<script type="module">
  import turbo from 'https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/+esm';
</script>
<script>
    // Global Helper for SweetAlert Toast
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
</script>
<body class="bg-background-light dark:bg-background-dark font-display text-[#111418] dark:text-white">
<div class="flex min-h-screen">
<!-- Include Sidebar -->
<?php $this->load->view('layout/sidebar'); ?>

<!-- Main Content -->
<main id="main-content" class="flex-1 md:ml-64 flex flex-col transition-all duration-300">
<!-- Include Topbar -->
<?php $this->load->view('layout/topbar'); ?>

<div class="p-8 space-y-8">
