<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installer - Step 3</title>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --line:rgba(15,23,42,.12); --card:rgba(255,255,255,.78); }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; display:grid; place-items:center; padding:1rem; background:radial-gradient(circle at 0% 0%, #dbeafe, transparent 40%), radial-gradient(circle at 100% 100%, #fef3c7, transparent 40%), linear-gradient(135deg, #f8fafc, #ecfeff); font-family:"Segoe UI","Helvetica Neue",sans-serif; color:var(--ink);}
        .container { width:100%; max-width:720px; border:1px solid var(--line); border-radius:20px; padding:2rem; background:var(--card); backdrop-filter:blur(10px); box-shadow:0 24px 64px rgba(15,23,42,.15); }
        h1 { margin:.2rem 0 .2rem; font-size:1.9rem; }
        .sub { color:var(--muted); margin:0 0 1.5rem; }
        .step { display:inline-block; border-radius:999px; background:#dbeafe; color:#1d4ed8; padding:.25rem .7rem; font-size:.8rem; font-weight:700; text-transform:uppercase; }
        .actions { margin-top:1.4rem; display:flex; gap:.75rem; align-items:center; }
        .btn { text-decoration:none; border:1px solid #cbd5e1; border-radius:12px; padding:.72rem 1rem; color:#1e293b; font-weight:600; }
        button { border:0; border-radius:12px; padding:.75rem 1.1rem; font-weight:700; color:#fff; background:linear-gradient(120deg,#1d4ed8,#0ea5e9); cursor:pointer; }
        .error { border:1px solid #fecaca; background:#fef2f2; color:#991b1b; padding:.7rem .9rem; border-radius:12px; margin-bottom:1rem; }
        .ok { border:1px solid #bbf7d0; background:#f0fdf4; color:#166534; padding:.7rem .9rem; border-radius:12px; margin-bottom:1rem; }
    </style>
</head>
<body>
<div class="container">
    <span class="step">Step 3 of 4</span>
    <h1>Run Migrations</h1>
    <p class="sub">Run all pending migrations using the generated <code>.env</code> configuration.</p>

    <?php if (! empty($error)): ?>
        <p class="error"><?= esc($error) ?></p>
    <?php endif; ?>

    <?php if (! empty($success)): ?>
        <p class="ok"><?= esc($message) ?></p>
        <div class="actions">
            <a class="btn" href="/install/cleanup">Continue to Cleanup</a>
        </div>
    <?php else: ?>
        <form method="post" action="/install/migrate">
            <?= csrf_field() ?>
            <div class="actions">
                <a class="btn" href="/install/app">Back</a>
                <button type="submit">Run Migrations</button>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
