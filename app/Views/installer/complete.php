<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installation Complete</title>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --line:rgba(15,23,42,.12); --card:rgba(255,255,255,.78); }
        * { box-sizing: border-box; }
        body {
            margin:0; min-height:100vh; display:grid; place-items:center; padding:1rem;
            background: radial-gradient(circle at 15% 15%, #d1fae5, transparent 40%),
                        radial-gradient(circle at 85% 85%, #dbeafe, transparent 40%),
                        linear-gradient(135deg, #f8fafc, #ecfeff);
            font-family:"Segoe UI","Helvetica Neue",sans-serif; color:var(--ink);
        }
        .container {
            width:100%; max-width:680px; border:1px solid var(--line); border-radius:20px;
            padding:2rem; background:var(--card); backdrop-filter:blur(10px);
            box-shadow:0 24px 64px rgba(15,23,42,.15); text-align:center;
        }
        h1 { margin:.3rem 0 .4rem; font-size:2rem; }
        p { color:var(--muted); }
        .btn {
            display:inline-block; margin-top:.8rem; text-decoration:none; border-radius:12px;
            padding:.75rem 1.1rem; font-weight:700; color:#fff;
            background:linear-gradient(120deg,#0f766e,#0ea5e9);
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Installation Complete</h1>
    <p>Your application is configured and ready.</p>
    <p><a class="btn" href="/">Go to Home</a></p>
</div>
</body>
</html>
