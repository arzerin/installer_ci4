<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installer - Precheck</title>
    <style>
        :root { --ink:#0f172a; --muted:#475569; --line:rgba(15,23,42,.12); --card:rgba(255,255,255,.78); }
        * { box-sizing: border-box; }
        body {
            margin:0; min-height:100vh; display:grid; place-items:center; padding:1rem;
            background: radial-gradient(circle at 15% 20%, #cffafe, transparent 40%),
                        radial-gradient(circle at 85% 80%, #dcfce7, transparent 40%),
                        linear-gradient(135deg, #f8fafc, #ecfeff);
            font-family:"Segoe UI","Helvetica Neue",sans-serif; color:var(--ink);
        }
        .container {
            width:100%; max-width:760px; border:1px solid var(--line); border-radius:20px;
            padding:2rem; background:var(--card); backdrop-filter:blur(10px);
            box-shadow:0 24px 64px rgba(15,23,42,.15);
        }
        h1 { margin:.2rem 0 .2rem; font-size:1.9rem; }
        .sub { color:var(--muted); margin:0 0 1.2rem; }
        .step { display:inline-block; border-radius:999px; background:#e0f2fe; color:#0369a1; padding:.25rem .7rem; font-size:.8rem; font-weight:700; text-transform:uppercase; }
        .list { margin: 1rem 0 0; padding: 0; list-style: none; }
        .item { border:1px solid #e2e8f0; border-radius:14px; padding:.8rem 1rem; margin:.6rem 0; background:#fff; }
        .ok { color:#166534; font-weight:600; }
        .bad { color:#991b1b; font-weight:600; }
        small { color:var(--muted); display:block; margin-top:.2rem; }
        .error { border:1px solid #fecaca; background:#fef2f2; color:#991b1b; padding:.7rem .9rem; border-radius:12px; margin:1rem 0; }
        .actions { margin-top:1.2rem; display:flex; gap:.75rem; align-items:center; }
        button { border:0; border-radius:12px; padding:.75rem 1.1rem; font-weight:700; color:#fff; background:linear-gradient(120deg,#0369a1,#0ea5e9); cursor:pointer; }
        .disabled { opacity:.5; cursor:not-allowed; }
    </style>
</head>
<body>
<div class="container">
    <span class="step">Installer Precheck</span>
    <h1>System Readiness</h1>
    <p class="sub">All required checks must pass before continuing.</p>

    <?php if (! empty($error)): ?>
        <p class="error"><?= esc($error) ?></p>
    <?php endif; ?>

    <ul class="list">
        <?php foreach ($checks as $check): ?>
            <li class="item">
                <span class="<?= $check['pass'] ? 'ok' : 'bad' ?>">
                    <?= $check['pass'] ? 'PASS' : 'FAIL' ?>
                </span>
                - <?= esc($check['label']) ?>
                <small><?= esc($check['detail']) ?></small>
            </li>
        <?php endforeach; ?>
    </ul>

    <form method="post" action="/install/precheck">
        <?= csrf_field() ?>
        <div class="actions">
            <button type="submit" <?= $allPass ? '' : 'class="disabled" disabled' ?>>Continue to Database Setup</button>
        </div>
    </form>
</div>
</body>
</html>
