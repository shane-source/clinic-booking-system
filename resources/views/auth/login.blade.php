<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Login — Clinic Booking</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{background:#0f172a;font-family:system-ui,sans-serif;display:flex;
         justify-content:center;align-items:center;min-height:100vh;}
    .card{background:#1e293b;border-radius:16px;padding:40px;width:100%;
          max-width:400px;box-shadow:0 25px 50px rgba(0,0,0,0.5);}
    h1{color:#f1f5f9;font-size:28px;font-weight:800;text-align:center;margin-bottom:6px;}
    .sub{color:#94a3b8;text-align:center;margin-bottom:28px;font-size:14px;}
    .error{background:#450a0a;border:1px solid #dc2626;color:#fca5a5;
           border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:14px;}
    label{display:block;color:#94a3b8;font-size:13px;font-weight:600;margin-bottom:6px;}
    input{width:100%;background:#0f172a;border:1.5px solid #334155;border-radius:8px;
          padding:10px 14px;color:#f1f5f9;font-size:15px;outline:none;
          margin-bottom:18px;font-family:inherit;}
    input:focus{border-color:#2563eb;}
    button{width:100%;background:#2563eb;color:#fff;border:none;border-radius:8px;
           padding:13px;font-size:16px;font-weight:700;cursor:pointer;font-family:inherit;}
    button:hover{background:#1d4ed8;}
    .link{text-align:center;color:#64748b;font-size:14px;margin-top:20px;}
    a{color:#60a5fa;}
    .divider{border-top:1px solid #334155;margin:24px 0;}
    .quick-title{color:#475569;font-size:12px;text-align:center;margin-bottom:10px;}
    .quick-btn{width:100%;background:#0f172a;border:1px solid #334155;color:#94a3b8;
               border-radius:8px;padding:8px;font-size:13px;cursor:pointer;
               margin-bottom:8px;font-family:inherit;}
    .quick-btn:hover{background:#1e293b;color:#f1f5f9;}
  </style>
</head>
<body>
  <div class="card">
    <h1>🏥 Clinic Booking</h1>
    <p class="sub">Sign in to your account</p>

    @if($errors->any())
      <div class="error">❌ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/login" id="loginForm">
      @csrf
      <label>Email</label>
      <input type="email" name="email" id="emailInput"
             value="{{ old('email') }}" placeholder="your@email.com" required/>
      <label>Password</label>
      <input type="password" name="password" id="passInput"
             placeholder="Your password" required/>
      <button type="submit">Sign In</button>
    </form>

    <p class="link">No account? <a href="/register">Register here</a></p>

    <div class="divider"></div>
    <p class="quick-title">Quick test logins:</p>
    <button class="quick-btn" onclick="fill('admin@clinic.com','password123')">
      👑 Admin — admin@clinic.com
    </button>
    <button class="quick-btn" onclick="fill('doctor@clinic.com','password123')">
      🩺 Doctor — doctor@clinic.com
    </button>
    <button class="quick-btn" onclick="fill('patient@clinic.com','password123')">
      👤 Patient — patient@clinic.com
    </button>
  </div>
  <script>
    function fill(email, pass) {
      document.getElementById('emailInput').value = email;
      document.getElementById('passInput').value  = pass;
    }
  </script>
</body>
</html>
