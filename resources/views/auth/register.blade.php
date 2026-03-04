<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Register — Clinic Booking</title>
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
  </style>
</head>
<body>
  <div class="card">
    <h1>🏥 Create Account</h1>
    <p class="sub">Join Clinic Booking today</p>

    @if($errors->any())
      <div class="error">❌ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/register">
      @csrf
      <label>Full Name</label>
      <input type="text" name="name" value="{{ old('name') }}"
             placeholder="Your full name" required/>
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email') }}"
             placeholder="your@email.com" required/>
      <label>Password <span style="color:#475569;font-weight:400">(min 8 characters)</span></label>
      <input type="password" name="password" placeholder="Choose a strong password" required/>
      <button type="submit">Create Account</button>
    </form>

    <p class="link">Already have an account? <a href="/login">Sign in</a></p>
  </div>
</body>
</html>
