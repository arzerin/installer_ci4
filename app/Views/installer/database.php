<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installer - Step 1</title>
    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --bg-a: #e0f2fe;
            --bg-b: #dcfce7;
            --card: rgba(255, 255, 255, 0.78);
            --line: rgba(15, 23, 42, 0.12);
            --accent: #0f766e;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at top left, var(--bg-a), transparent 45%),
                        radial-gradient(circle at bottom right, var(--bg-b), transparent 45%),
                        linear-gradient(135deg, #f8fafc, #ecfeff);
            font-family: "Segoe UI", "Helvetica Neue", sans-serif;
            color: var(--ink);
            padding: 1rem;
        }
        .container {
            width: 100%;
            max-width: 720px;
            border: 1px solid var(--line);
            background: var(--card);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 24px 64px rgba(15, 23, 42, 0.15);
        }
        h1 { margin: 0 0 .2rem; font-size: 1.9rem; }
        .sub { color: var(--muted); margin: 0 0 1.6rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input {
            width: 100%;
            padding: 0.75rem 0.9rem;
            margin-top: 0.4rem;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            font-size: 0.98rem;
        }
        input:focus { outline: 2px solid #99f6e4; border-color: #14b8a6; }
        .actions { margin-top: 1.4rem; display:flex; gap:.75rem; align-items:center; }
        .btn {
            text-decoration:none;
            border:1px solid #cbd5e1;
            border-radius:12px;
            padding:.72rem 1rem;
            color:#1e293b;
            font-weight:600;
            background:#fff;
        }
        button {
            border: 0;
            border-radius: 12px;
            padding: .75rem 1.1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(120deg, #0f766e, #0ea5e9);
            cursor: pointer;
        }
        .secondary {
            background: linear-gradient(120deg, #334155, #64748b);
        }
        .error {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
            padding: .7rem .9rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .ok {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            padding: .7rem .9rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .step {
            display: inline-block;
            background: #ccfbf1;
            color: #0f766e;
            border-radius: 999px;
            padding: .25rem .7rem;
            font-size: .8rem;
            margin-bottom: .8rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="container">
    <span class="step">Step 1 of 4</span>
    <h1>Database Setup</h1>
    <p class="sub">Provide your database credentials. These values will be saved into root <code>.env</code>.</p>

    <?php if (! empty($error)): ?>
        <p class="error"><?= esc($error) ?></p>
    <?php endif; ?>
    <?php if (! empty($success)): ?>
        <p class="ok"><?= esc($success) ?></p>
    <?php endif; ?>

    <form method="post" action="/install/database">
        <?= csrf_field() ?>
        <label for="hostname">Database Host</label>
        <input id="hostname" name="hostname" type="text" value="<?= esc($values['hostname'] ?? '') ?>" required>

        <label for="database">Database Name</label>
        <input id="database" name="database" type="text" value="<?= esc($values['database'] ?? '') ?>" required>

        <label for="username">Database User</label>
        <input id="username" name="username" type="text" value="<?= esc($values['username'] ?? '') ?>" required>

        <label for="password">Database Password</label>
        <input id="password" name="password" type="password" value="<?= esc($values['password'] ?? '') ?>">

        <label for="port">Database Port</label>
        <input id="port" name="port" type="number" min="1" value="<?= esc((string) ($values['port'] ?? 3306)) ?>" required>

        <div class="actions">
            <a class="btn" href="/install/precheck">Back</a>
            <button class="secondary" type="submit" name="action" value="test">Test Connection</button>
            <button type="submit" name="action" value="continue">Save and Continue</button>
        </div>
    </form>
</div>
</body>
</html>
