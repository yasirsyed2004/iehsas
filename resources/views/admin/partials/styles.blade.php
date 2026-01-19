{{-- File: resources/views/admin/partials/styles.blade.php --}}
{{-- 
    Shared Admin Styles Component
    Usage: @include('admin.partials.styles')
    
    This includes the sidebar styles with nice gradient UI.
    Include this in the <head> section of your admin blade files.
--}}

<style>
    /* ========================================
       ADMIN SIDEBAR STYLES (Enhanced Gradient UI)
       ======================================== */
    
    .sidebar {
        min-height: 100vh;
        background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        z-index: 1000;
        box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        overflow-y: auto;
    }
    
    .main-content {
        margin-left: 250px;
        padding: 30px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        min-height: 100vh;
    }
    
    /* User Info Section */
    .sidebar .user-info {
        color: #ecf0f1;
        padding: 25px 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.1);
    }
    
    /* Navigation Links */
    .sidebar .nav-link {
        color: #ecf0f1;
        border-radius: 8px;
        margin: 3px 10px;
        padding: 12px 15px;
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .sidebar .nav-link:hover {
        background-color: rgba(255,255,255,0.15);
        color: white;
        transform: translateX(5px);
    }
    
    .sidebar .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .sidebar .nav-link i {
        width: 20px;
        text-align: center;
    }
    
    /* Logout Link Special Style */
    .sidebar .nav-link.text-danger-light {
        color: #ff7675;
    }
    
    .sidebar .nav-link.text-danger-light:hover {
        background-color: rgba(255, 118, 117, 0.2);
        color: #ff7675;
    }
    
    /* ========================================
       COMMON ADMIN COMPONENT STYLES
       ======================================== */
    
    /* Cards */
    .card-modern {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 35px rgba(0,0,0,0.12);
    }
    
    /* Stats Cards */
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: none;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    /* Gradient Header for Cards */
    .card-header-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border: none;
        font-weight: 600;
    }
    
    /* Form Styles */
    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 10px 15px;
        transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    /* Buttons */
    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
    }
    
    /* Tables */
    .table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table thead th {
        border: none;
        padding: 15px;
        font-weight: 600;
    }
    
    .table tbody tr {
        transition: all 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }
    
    /* Badges */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    /* Breadcrumb */
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }
    
    .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        color: #764ba2;
    }
    
    /* Page Header */
    .page-header {
        margin-bottom: 25px;
    }
    
    .page-header h2 {
        color: #2c3e50;
        font-weight: 600;
    }
    
    /* Alert Styles */
    .alert {
        border-radius: 10px;
        border: none;
    }
    
    /* Scrollbar for Sidebar */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.1);
    }
    
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.3);
        border-radius: 3px;
    }
    
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.5);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            position: relative;
            min-height: auto;
        }
        
        .main-content {
            margin-left: 0;
            padding: 15px;
        }
    }
</style>