<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Clinic Booking — @yield('title','Dashboard')</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{background:#0f172a;font-family:system-ui,sans-serif;color:#e2e8f0;}
    a{color:#60a5fa;text-decoration:none;}
    a:hover{text-decoration:underline;}

    /* HEADER */
    .header{background:#1e293b;padding:16px 32px;display:flex;justify-content:space-between;
            align-items:center;border-bottom:1px solid #334155;position:sticky;top:0;z-index:100;}
    .header-left h1{color:#f1f5f9;font-size:20px;font-weight:800;}
    .header-left p{color:#64748b;font-size:13px;margin-top:2px;}
    .header-right{display:flex;gap:10px;align-items:center;}
    .nav-link{color:#94a3b8;font-size:14px;padding:8px 16px;border-radius:8px;
              border:1px solid #334155;cursor:pointer;background:transparent;text-decoration:none;}
    .nav-link:hover{background:#334155;color:#f1f5f9;text-decoration:none;}
    .btn-logout{background:transparent;color:#94a3b8;border:1px solid #334155;
                border-radius:8px;padding:8px 16px;cursor:pointer;font-size:14px;}
    .btn-logout:hover{background:#334155;}

    /* CONTENT */
    .content{padding:32px;max-width:1200px;margin:0 auto;}

    /* ALERTS */
    .alert-success{background:#064e3b;border:1px solid #059669;color:#6ee7b7;
                   border-radius:10px;padding:12px 18px;margin-bottom:20px;}
    .alert-error{background:#450a0a;border:1px solid #dc2626;color:#fca5a5;
                 border-radius:10px;padding:12px 18px;margin-bottom:20px;}

    /* CARDS */
    .card{background:#1e293b;border-radius:12px;border:1px solid #334155;padding:24px;margin-bottom:16px;}
    .card-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;}
    .card-title{color:#f1f5f9;font-size:16px;font-weight:700;}
    .card-sub{color:#64748b;font-size:13px;margin-top:4px;}

    /* BADGES */
    .badge{padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;}
    .badge-pending{background:#78350f22;color:#f59e0b;border:1px solid #f59e0b44;}
    .badge-confirmed{background:#06402022;color:#10b981;border:1px solid #10b98144;}
    .badge-cancelled{background:#7f1d1d22;color:#ef4444;border:1px solid #ef444444;}
    .badge-completed{background:#1e1b4b22;color:#818cf8;border:1px solid #818cf844;}

    /* BUTTONS */
    .btn{padding:9px 18px;border-radius:8px;border:none;cursor:pointer;
         font-size:14px;font-weight:700;display:inline-block;}
    .btn-primary{background:#2563eb;color:#fff;}
    .btn-primary:hover{background:#1d4ed8;}
    .btn-success{background:#059669;color:#fff;}
    .btn-success:hover{background:#047857;}
    .btn-danger{background:#dc2626;color:#fff;}
    .btn-danger:hover{background:#b91c1c;}
    .btn-ghost{background:transparent;color:#94a3b8;border:1px solid #334155;}
    .btn-ghost:hover{background:#334155;}
    .btn-sm{padding:6px 12px;font-size:12px;}

    /* GRID */
    .grid-3{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;}
    .grid-4{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;}

    /* STAT CARDS */
    .stat-card{background:#1e293b;border-radius:12px;padding:20px;border:1px solid #334155;}
    .stat-num{font-size:38px;font-weight:800;margin-bottom:4px;}
    .stat-label{color:#64748b;font-size:13px;}

    /* FORMS */
    .form-group{margin-bottom:18px;}
    .form-label{display:block;color:#94a3b8;font-size:13px;font-weight:600;margin-bottom:6px;}
    .form-input{width:100%;background:#0f172a;border:1.5px solid #334155;border-radius:8px;
                padding:10px 14px;color:#f1f5f9;font-size:15px;outline:none;}
    .form-input:focus{border-color:#2563eb;}
    select.form-input{cursor:pointer;}
    textarea.form-input{resize:vertical;min-height:80px;}

    /* TABLE */
    .table-wrap{overflow-x:auto;}
    table{width:100%;border-collapse:collapse;}
    th{background:#0f172a;color:#64748b;font-size:12px;font-weight:700;
       text-transform:uppercase;padding:10px 14px;text-align:left;}
    td{padding:12px 14px;border-bottom:1px solid #1e293b;color:#cbd5e1;font-size:14px;}
    tr:hover td{background:#1e293b55;}

    /* TABS */
    .tabs{display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap;}
    .tab{padding:8px 20px;border-radius:8px;border:none;cursor:pointer;
         font-weight:700;font-size:14px;background:#1e293b;color:#64748b;}
    .tab.active{background:#2563eb;color:#fff;}

    /* MODAL */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);
                   z-index:999;justify-content:center;align-items:center;}
    .modal-overlay.open{display:flex;}
    .modal{background:#1e293b;border-radius:16px;padding:32px;width:100%;
           max-width:500px;max-height:90vh;overflow-y:auto;}
    .modal-title{color:#f1f5f9;font-size:20px;font-weight:800;margin-bottom:20px;}
    .modal-close{float:right;background:none;border:none;color:#94a3b8;
                 font-size:20px;cursor:pointer;margin-top:-4px;}

    /* STEPS */
    .steps{display:flex;gap:8px;margin-bottom:20px;}
    .step-dot{height:4px;flex:1;border-radius:2px;background:#334155;}
    .step-dot.active{background:#2563eb;}

    /* EMPTY STATE */
    .empty{text-align:center;padding:60px 20px;color:#64748b;}
    .empty h3{font-size:18px;margin-bottom:8px;color:#94a3b8;}

    /* SECTION TITLE */
    .section-title{color:#f1f5f9;font-size:20px;font-weight:800;margin-bottom:16px;
                   padding-bottom:12px;border-bottom:1px solid #334155;}

    @media(max-width:640px){
      .header{padding:12px 16px;}
      .content{padding:16px;}
      .grid-3,.grid-4{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>

@auth
<div class="header">
  <div class="header-left">
    <h1>🏥 Clinic Booking</h1>
    <p>{{ Auth::user()->name }} —
      <strong style="color:#60a5fa">
        {{ Auth::user()->roles->first()?->name ?? 'user' }}
      </strong>
    </p>
  </div>
  <div class="header-right">
    @if(Auth::user()->hasRole('admin'))
      <a href="/admin"     class="nav-link">Admin</a>
      <a href="/dashboard" class="nav-link">Patient View</a>
    @elseif(Auth::user()->hasRole('doctor'))
      <a href="/doctor"    class="nav-link">My Schedule</a>
    @else
      <a href="/dashboard" class="nav-link">My Appointments</a>
      <a href="/book"      class="nav-link">+ Book</a>
    @endif
    <form method="POST" action="/logout" style="display:inline">
      @csrf
      <button class="btn-logout" type="submit">Logout</button>
    </form>
  </div>
</div>
@endauth

<div class="content">
  @if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert-error">❌ {{ $errors->first() }}</div>
  @endif

  @yield('content')
</div>

@yield('scripts')
</body>
</html>
