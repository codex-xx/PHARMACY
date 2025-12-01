	<?php $currentTheme = session()->get('theme') ?? 'light'; ?>
	<style>
		/* Dark theme styles */
		[data-bs-theme="dark"] {
			--bs-body-color: #fff;
			--bs-body-bg: #212529;
		}
		[data-bs-theme="dark"] .sidebar {
			background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 100%);
		}
		[data-bs-theme="dark"] .main-content {
			background-color: #1a1a1a;
		}
		[data-bs-theme="dark"] .top-navbar {
			background-color: #343a40 !important;
			border-bottom-color: #495057 !important;
		}
		[data-bs-theme="dark"] .card {
			background-color: #343a40;
			border-color: #495057;
			color: #fff;
		}
		[data-bs-theme="dark"] .card-header {
			background-color: #495057;
			border-bottom-color: #6c757d;
		}
		[data-bs-theme="dark"] .table {
			color: #fff;
		}
		[data-bs-theme="dark"] .table th,
		[data-bs-theme="dark"] .table td {
			border-color: #495057;
		}
		[data-bs-theme="dark"] .form-control,
		[data-bs-theme="dark"] .form-select {
			background-color: #343a40;
			border-color: #495057;
			color: #fff;
		}
		[data-bs-theme="dark"] .form-control:focus,
		[data-bs-theme="dark"] .form-select:focus {
			background-color: #343a40;
			color: #fff;
		}
		[data-bs-theme="dark"] .text-muted {
			color: #adb5bd !important;
		}
		[data-bs-theme="dark"] .alert {
			border-color: transparent;
		}
    .sidebar {
        min-height: 100vh;
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        z-index: 100;
        padding: 20px 0;
        transition: background 0.3s ease;
    }
    .sidebar .sidebar-header {
        text-align: center;
        padding: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        margin-bottom: 20px;
    }
    .sidebar .sidebar-header h4 {
        color: white;
        margin: 0;
    }
    .sidebar .nav-link {
        color: rgba(255,255,255,0.8);
        padding: 12px 20px;
        margin: 5px 10px;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    .sidebar .nav-link:hover {
        background-color: rgba(255,255,255,0.1);
        color: white;
        transform: translateX(5px);
    }
    .sidebar .nav-link.active {
        background-color: rgba(255,255,255,0.2);
        color: white;
    }
    .sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
    }
    .main-content {
        margin-left: 250px;
        padding: 20px;
    }
    .top-navbar {
        margin-left: 250px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 10px 20px;
        position: sticky;
        top: 0;
        z-index: 99;
    }
    .top-navbar .navbar-brand {
        color: #3498db !important;
        font-weight: bold;
    }
    .pharmacy-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .pharmacy-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    .btn-pharmacy {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        border: none;
        color: white;
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-pharmacy:hover {
        background: linear-gradient(135deg, #229954 0%, #28a745 100%);
        transform: scale(1.05);
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-pills"></i> Pharmacy POS</h4>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo ($currentPage ?? '') == 'dashboard' ? 'active' : ''; ?>" href="<?php echo site_url('dashboard'); ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a class="nav-link <?php echo ($currentPage ?? '') == 'inventory' ? 'active' : ''; ?>" href="<?php echo site_url('inventory'); ?>">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <a class="nav-link <?php echo ($currentPage ?? '') == 'products' ? 'active' : ''; ?>" href="<?php echo site_url('products'); ?>">
            <i class="fas fa-flask"></i> Products
        </a>
        <a class="nav-link <?php echo ($currentPage ?? '') == 'sales' ? 'active' : ''; ?>" href="<?php echo site_url('sales'); ?>">
            <i class="fas fa-shopping-cart"></i> Sales
        </a>
        <?php if ($role === 'admin'): ?>
        <a class="nav-link <?php echo ($currentPage ?? '') == 'reports' ? 'active' : ''; ?>" href="<?php echo site_url('reports'); ?>">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>
        <a class="nav-link <?php echo ($currentPage ?? '') == 'users' ? 'active' : ''; ?>" href="<?php echo site_url('users'); ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a class="nav-link <?php echo ($currentPage ?? '') == 'settings' ? 'active' : ''; ?>" href="<?php echo site_url('settings'); ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
        <?php endif; ?>
        <hr style="border-color: rgba(255,255,255,0.3); margin: 20px 10px;">
        <a class="nav-link" href="<?php echo site_url('logout'); ?>" style="color: rgba(255,255,255,0.8);">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light top-navbar">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Welcome, <?php echo esc($username); ?></span>
        <div class="d-flex">
            <small class="text-muted align-self-center me-3">
                <i class="fas fa-calendar-alt me-1"></i><?php
                date_default_timezone_set('Asia/Taipei');
                echo date('l, F j, Y');
                ?>
            </small>
        </div>
    </div>
</nav>
