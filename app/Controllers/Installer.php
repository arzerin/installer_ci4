<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Database;
use Config\Services;
use Throwable;

class Installer extends BaseController
{
    private const LOCK_FILE = WRITEPATH . 'installer/installed.lock';

    public function index()
    {
        $this->guardInstallerAccess();

        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        return redirect()->to('/install/precheck');
    }

    public function precheck()
    {
        $this->guardInstallerAccess();

        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        $session  = session();
        $checks   = $this->runPrechecks();
        $allPass  = $this->allPrechecksPass($checks);
        $error    = null;

        if ($this->request->getMethod(true) === 'POST') {
            if (! $allPass) {
                $error = 'Please fix failed requirements before continuing.';
            } else {
                $session->set('installer.precheck', true);

                return redirect()->to('/install/database');
            }
        }

        return view('installer/precheck', [
            'checks'  => $checks,
            'allPass' => $allPass,
            'error'   => $error,
        ]);
    }

    public function database()
    {
        $this->guardInstallerAccess();

        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        $session = session();
        $error   = null;
        $success = null;
        $values  = (array) ($session->get('installer.db') ?? []);
        if (! $session->get('installer.precheck')) {
            return redirect()->to('/install/precheck');
        }

        if ($this->request->getMethod(true) === 'POST') {
            $action = (string) $this->request->getPost('action');
            $values = [
                'hostname' => trim((string) $this->request->getPost('hostname')),
                'database' => trim((string) $this->request->getPost('database')),
                'username' => trim((string) $this->request->getPost('username')),
                'password' => (string) $this->request->getPost('password'),
                'port'     => (int) ($this->request->getPost('port') ?: 3306),
            ];

            if (
                $values['hostname'] === ''
                || $values['database'] === ''
                || $values['username'] === ''
                || $values['port'] <= 0
            ) {
                $error = 'Database host, name, username, and valid port are required.';
            } else {
                if ($action === 'test') {
                    if ($this->testDatabaseConnection($values, $error)) {
                        $success = 'Database connection successful.';
                    }

                    return view('installer/database', [
                        'step'    => 1,
                        'values'  => $values,
                        'error'   => $error,
                        'success' => $success,
                    ]);
                }

                $session->set('installer.db', $values);
                $session->set('installer.step1', true);

                return redirect()->to('/install/app');
            }
        }

        return view('installer/database', [
            'step'    => 1,
            'values'  => $values,
            'error'   => $error,
            'success' => $success,
        ]);
    }

    public function app()
    {
        $this->guardInstallerAccess();

        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        $session = session();
        if (! $session->get('installer.step1')) {
            return redirect()->to('/install/database');
        }

        $error    = null;
        $appBase  = (string) ($session->get('installer.appBaseURL') ?? '');

        if ($this->request->getMethod(true) === 'POST') {
            $appBase = trim((string) $this->request->getPost('app_base_url'));

            if ($appBase === '' || filter_var($appBase, FILTER_VALIDATE_URL) === false) {
                $error = 'Please provide a valid app.baseURL.';
            } else {
                $normalized = rtrim($appBase, '/') . '/';

                try {
                    $this->writeEnvFile(
                        (array) $session->get('installer.db'),
                        $normalized
                    );
                } catch (Throwable $e) {
                    $error = 'Unable to write .env file: ' . $e->getMessage();
                }

                if ($error === null) {
                    $session->set('installer.appBaseURL', $normalized);
                    $session->set('installer.step2', true);

                    return redirect()->to('/install/migrate');
                }
            }
        }

        return view('installer/app', [
            'step'     => 2,
            'appBase'  => $appBase,
            'error'    => $error,
        ]);
    }

    public function migrate()
    {
        $this->guardInstallerAccess();

        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        $session = session();
        if (! $session->get('installer.step2')) {
            return redirect()->to('/install/app');
        }

        $error   = null;
        $success = false;
        $message = null;

        if ($this->request->getMethod(true) === 'POST') {
            try {
                $db = (array) ($session->get('installer.db') ?? []);
                if (! $this->testDatabaseConnection($db, $connectionError)) {
                    $error = 'Migration cannot run because database connection failed. '
                        . $connectionError
                        . ' Tip: use `127.0.0.1` instead of `localhost` for local MySQL.';

                    return view('installer/migrate', [
                        'step'    => 3,
                        'error'   => $error,
                        'success' => false,
                        'message' => null,
                    ]);
                }

                $runner = Services::migrations();
                $runner->setNamespace(null);
                $runner->latest();
                $session->set('installer.step3', true);
                $success = true;
                $message = 'Migrations completed successfully.';
            } catch (Throwable $e) {
                $error = 'Migration failed: ' . $e->getMessage();
            }
        }

        return view('installer/migrate', [
            'step'    => 3,
            'error'   => $error,
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function cleanup()
    {
        $this->guardInstallerAccess();

        if ($this->isInstalled()) {
            return redirect()->to('/install/complete');
        }

        $session = session();
        if (! $session->get('installer.step3')) {
            return redirect()->to('/install/migrate');
        }

        $error   = null;
        $success = false;

        if ($this->request->getMethod(true) === 'POST') {
            try {
                $this->cleanupPublicTemp();
                $this->markInstalled();
                $session->remove([
                    'installer.precheck',
                    'installer.db',
                    'installer.appBaseURL',
                    'installer.step1',
                    'installer.step2',
                    'installer.step3',
                ]);
                $success = true;
            } catch (Throwable $e) {
                $error = 'Cleanup failed: ' . $e->getMessage();
            }
        }

        return view('installer/cleanup', [
            'step'    => 4,
            'error'   => $error,
            'success' => $success,
        ]);
    }

    public function complete()
    {
        $this->guardInstallerAccess();

        if (! $this->isInstalled()) {
            return redirect()->to('/install');
        }

        return view('installer/complete');
    }

    private function isInstalled(): bool
    {
        return is_file(self::LOCK_FILE);
    }

    private function guardInstallerAccess(): void
    {
        $session = session();
        $inProgress = (bool) (
            $session->get('installer.precheck')
            || $session->get('installer.step1')
            || $session->get('installer.step2')
            || $session->get('installer.step3')
        );

        $alreadyInstalled = $this->isInstalled();
        $freshInstall = ! is_file(ROOTPATH . '.env');
        $enabled = $freshInstall
            || $alreadyInstalled
            || $inProgress
            || ENVIRONMENT === 'development'
            || $this->isTruthy((string) env('INSTALLER_ENABLED', 'false'));

        if (! $enabled) {
            throw PageNotFoundException::forPageNotFound('Installer is disabled.');
        }
    }

    private function isTruthy(string $value): bool
    {
        $normalized = strtolower(trim($value));

        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }

    private function runPrechecks(): array
    {
        $templatePath = ROOTPATH . 'env';
        $envPath      = ROOTPATH . '.env';
        $tempPath     = FCPATH . 'temp';

        $tempWritable = is_dir($tempPath)
            ? is_writable($tempPath)
            : is_writable(FCPATH);

        return [
            [
                'label'  => 'Root env template exists (env)',
                'pass'   => is_file($templatePath),
                'detail' => $templatePath,
            ],
            [
                'label'  => 'Root .env writable (or root directory writable)',
                'pass'   => is_file($envPath) ? is_writable($envPath) : is_writable(ROOTPATH),
                'detail' => is_file($envPath) ? $envPath : ROOTPATH,
            ],
            [
                'label'  => 'writable directory is writable',
                'pass'   => is_dir(WRITEPATH) && is_writable(WRITEPATH),
                'detail' => WRITEPATH,
            ],
            [
                'label'  => 'public/temp is writable (or public is writable to create it)',
                'pass'   => $tempWritable,
                'detail' => is_dir($tempPath) ? $tempPath : FCPATH,
            ],
        ];
    }

    private function allPrechecksPass(array $checks): bool
    {
        foreach ($checks as $check) {
            if (! ($check['pass'] ?? false)) {
                return false;
            }
        }

        return true;
    }

    private function testDatabaseConnection(array $db, ?string &$error): bool
    {
        try {
            $connection = Database::connect([
                'DSN'      => '',
                'hostname' => $db['hostname'],
                'username' => $db['username'],
                'password' => $db['password'],
                'database' => $db['database'],
                'DBDriver' => 'MySQLi',
                'DBDebug'  => true,
                'charset'  => 'utf8mb4',
                'DBCollat' => 'utf8mb4_general_ci',
                'port'     => (int) ($db['port'] ?? 3306),
                'pConnect' => false,
            ], false);

            $connection->initialize();
            $connection->close();

            return true;
        } catch (Throwable $e) {
            $error = 'Database connection failed: ' . $e->getMessage();

            return false;
        }
    }

    private function writeEnvFile(array $db, string $appBaseUrl): void
    {
        $templatePath = ROOTPATH . 'env';
        $envPath      = ROOTPATH . '.env';

        if (! is_file($templatePath)) {
            throw new \RuntimeException('Root env template file is missing.');
        }

        if (is_file($envPath)) {
            $backupPath = ROOTPATH . '.env.backup.' . date('YmdHis');
            if (! copy($envPath, $backupPath)) {
                throw new \RuntimeException('Failed to back up existing .env file.');
            }
        }

        $content = (string) file_get_contents($templatePath);

        $updates = [
            'database.default.hostname' => $db['hostname'],
            'database.default.database' => $db['database'],
            'database.default.username' => $db['username'],
            'database.default.password' => $db['password'],
            'database.default.port'     => (string) ($db['port'] ?? 3306),
            'app.baseURL'               => $appBaseUrl,
        ];

        foreach ($updates as $key => $value) {
            $content = $this->setEnvValue($content, $key, $value);
        }

        if (file_put_contents($envPath, $content) === false) {
            throw new \RuntimeException('Failed to write .env file.');
        }
    }

    private function setEnvValue(string $content, string $key, string $value): string
    {
        $encoded = '"' . addcslashes($value, "\\\"") . '"';
        $line    = $key . ' = ' . $encoded;
        $pattern = '/^\s*#?\s*' . preg_quote($key, '/') . '\s*=.*$/m';

        if (preg_match($pattern, $content) === 1) {
            return (string) preg_replace($pattern, $line, $content, 1);
        }

        return rtrim($content, "\n") . PHP_EOL . $line . PHP_EOL;
    }

    private function cleanupPublicTemp(): void
    {
        $tempPath = FCPATH . 'temp';

        if (! is_dir($tempPath)) {
            return;
        }

        $items = array_diff(scandir($tempPath) ?: [], ['.', '..']);
        foreach ($items as $item) {
            $this->removePath($tempPath . DIRECTORY_SEPARATOR . $item);
        }
    }

    private function removePath(string $path): void
    {
        if (is_dir($path) && ! is_link($path)) {
            $items = array_diff(scandir($path) ?: [], ['.', '..']);
            foreach ($items as $item) {
                $this->removePath($path . DIRECTORY_SEPARATOR . $item);
            }

            if (! rmdir($path)) {
                throw new \RuntimeException('Failed to remove directory: ' . $path);
            }

            return;
        }

        if (is_file($path) || is_link($path)) {
            if (! unlink($path)) {
                throw new \RuntimeException('Failed to remove file: ' . $path);
            }
        }
    }

    private function markInstalled(): void
    {
        $directory = dirname(self::LOCK_FILE);
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException('Failed to create installer directory.');
        }

        if (file_put_contents(self::LOCK_FILE, date(DATE_ATOM)) === false) {
            throw new \RuntimeException('Failed to write installer lock file.');
        }
    }
}
