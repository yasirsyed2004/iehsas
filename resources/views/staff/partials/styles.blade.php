{{-- File: resources/views/staff/partials/styles.blade.php --}}
{{-- Staff Panel Styles - Based on admin styles with teal/green gradient --}}

<style>
    .sidebar {
        height: 100vh;
        background: linear-gradient(180deg, #1a3a4a 0%, #1e4d5e 100%);
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

    .sidebar .user-info {
        color: #ecf0f1;
        padding: 25px 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.1);
    }

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
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
    }

    .sidebar .nav-link i { width: 20px; text-align: center; }

    .sidebar .nav-link.text-danger-light { color: #ff7675; }
    .sidebar .nav-link.text-danger-light:hover { background-color: rgba(255, 118, 117, 0.2); color: #ff7675; }

    .card-modern { background: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); border: none; overflow: hidden; transition: all 0.3s ease; }
    .card-modern:hover { transform: translateY(-3px); box-shadow: 0 10px 35px rgba(0,0,0,0.12); }

    .stats-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); transition: all 0.3s ease; border: none; }
    .stats-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }

    .card-header-gradient { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 15px 20px; border: none; font-weight: 600; }

    .form-control, .form-select { border-radius: 8px; border: 2px solid #e9ecef; padding: 10px 15px; transition: all 0.3s; }
    .form-control:focus, .form-select:focus { border-color: #0ea5e9; box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25); }

    .btn { border-radius: 8px; padding: 10px 20px; font-weight: 500; transition: all 0.3s; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .btn-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; }
    .btn-primary:hover { background: linear-gradient(135deg, #0891d6 0%, #0273af 100%); }

    .table { border-radius: 10px; overflow: hidden; }
    .table thead { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; }
    .table thead th { border: none; padding: 15px; font-weight: 600; }
    .table tbody tr { transition: all 0.2s; }
    .table tbody tr:hover { background-color: rgba(14, 165, 233, 0.05); }

    .badge { padding: 6px 12px; border-radius: 20px; font-weight: 500; }
    .breadcrumb { background: transparent; padding: 0; margin: 0; }
    .breadcrumb-item a { color: #0ea5e9; text-decoration: none; }
    .breadcrumb-item a:hover { color: #0284c7; }
    .page-header { margin-bottom: 25px; }
    .page-header h2 { color: #1a3a4a; font-weight: 600; }
    .alert { border-radius: 10px; border: none; }

    .sidebar::-webkit-scrollbar { width: 6px; }
    .sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); }
    .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 3px; }
    .sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.5); }

    @media (max-width: 768px) {
        .sidebar { width: 100%; position: relative; min-height: auto; }
        .main-content { margin-left: 0; padding: 15px; }
    }
</style>
