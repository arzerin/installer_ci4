<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installer - Step 2</title>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --accent:#0f766e; --line:rgba(15,23,42,.12); --card:rgba(255,255,255,.78); }
        * { box-sizing: border-box; }
        body {
            margin:0; min-height:100vh; display:grid; place-items:center; padding:1rem;
            background: radial-gradient(circle at 0% 0%, #dbeafe, transparent 40%), radial-gradient(circle at 100% 100%, #dcfce7, transparent 40%), linear-gradient(135deg, #f8fafc, #f0fdfa);
            font-family:"Segoe UI","Helvetica Neue",sans-serif; color:var(--ink);
        }
        .container { width:100%; max-width:720px; border:1px solid var(--line); border-radius:20px; padding:2rem; background:var(--card); backdrop-filter:blur(10px); box-shadow:0 24px 64px rgba(15,23,42,.15);}
        h1 { margin:.2rem 0 .2rem; font-size:1.9rem; }
        .sub { color:var(--muted); margin:0 0 1.5rem; }
        .step { display:inline-block; border-radius:999px; background:#ccfbf1; color:#0f766e; padding:.25rem .7rem; font-size:.8rem; font-weight:700; text-transform:uppercase; }
        label { display:block; margin-top:1rem; font-weight:600; }
        input { width:100%; padding:.75rem .9rem; margin-top:.4rem; border-radius:12px; border:1px solid #cbd5e1; font-size:.98rem; }
        input:focus { outline:2px solid #99f6e4; border-color:#14b8a6; }
        .actions { margin-top:1.4rem; display:flex; gap:.75rem; align-items:center; }
        .btn { text-decoration:none; border:1px solid #cbd5e1; border-radius:12px; padding:.72rem 1rem; color:#1e293b; font-weight:600; }
        button { border:0; border-radius:12px; padding:.75rem 1.1rem; font-weight:700; color:#fff; background:linear-gradient(120deg,#0f766e,#0ea5e9); cursor:pointer; }
        .error { border:1px solid #fecaca; background:#fef2f2; color:#991b1b; padding:.7rem .9rem; border-radius:12px; margin-bottom:1rem; }
    </style>
</head>
<body>
<div class="container">
    <span class="step">Step 2 of 4</span>
    <h1>Application URL</h1>
    <p class="sub">Set your public base URL. Installer will write root <code>.env</code> from <code>env</code> template.</p>

    <?php if (! empty($error)): ?>
        <p class="error"><?= esc($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/install/app">
        <?= csrf_field() ?>
        <label for="app_base_url">app.baseURL</label>
        <input
            id="app_base_url"
            name="app_base_url"
            type="url"
            placeholder="https://example.com/"
            value="<?= esc($appBase) ?>"
            required
        >

        <div class="actions">
            <a class="btn" href="/install/database">Back</a>
            <button type="submit">Write .env and Continue</button>
        </div>
    </form>
</div>
</body>
</html>
