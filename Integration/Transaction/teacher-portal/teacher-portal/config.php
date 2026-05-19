<?php
// ============================================================
// Teacher Portal — config.php
// Transaction / Request Management Subsystem · Group 7
// Southland College
// TEACHER ACCESS ONLY — Students are blocked entirely.
// ============================================================

// ── Database ──────────────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'transaction_request');
define('DB_USER', 'root');
define('DB_PASS', '');

// ── Attendance windows ────────────────────────────────────────────────────────
define('CLASS_START_TIME',       '08:00:00');
define('PRESENT_WINDOW_MINUTES', 0);
define('LATE_WINDOW_MINUTES',    15);

// ── Base URL — auto-detected from server, no trailing slash ───────────────────
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST']
    . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

// ── Session bootstrap ─────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_name('TEACHER_PORTAL_SESSION');
    session_start();
}

// ── CSRF ──────────────────────────────────────────────────────────────────────
function csrf(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function verifyCsrf(string $token): bool {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
function csrfField(): string {
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrf()) . '">';
}

// ── Auth guards ───────────────────────────────────────────────────────────────
function isTeacher(): bool {
    return !empty($_SESSION['teacher_auth']) && $_SESSION['teacher_auth'] === true;
}

/** Call at the top of every protected page */
function requireTeacher(): void {
    if (!isTeacher()) {
        header('Location: ' . BASE_URL . '/teacher-login.php?reason=auth');
        exit;
    }
}

// ── Teacher login ─────────────────────────────────────────────────────────────
function teacherLogin(string $username, string $password): bool {
    try {
        $pdo  = db();
        $stmt = $pdo->prepare(
            "SELECT * FROM teachers WHERE username = ? AND is_active = 1 LIMIT 1"
        );
        $stmt->execute([$username]);
        $t = $stmt->fetch();

        if ($t && password_verify($password, $t['password'])) {
            $pdo->prepare("UPDATE teachers SET last_login = NOW() WHERE teacher_id = ?")
                ->execute([$t['teacher_id']]);

            $_SESSION['teacher_auth']   = true;
            $_SESSION['t_id']           = $t['teacher_id'];
            $_SESSION['t_name']         = $t['full_name'];
            $_SESSION['t_number']       = $t['teacher_number'];
            $_SESSION['t_dept']         = $t['department'];
            $_SESSION['t_role']         = $t['role'];
            $_SESSION['t_username']     = $t['username'];
            $_SESSION['t_login_time']   = time();
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log('[TeacherPortal] Login error: ' . $e->getMessage());
        return false;
    }
}

// ── DB singleton ──────────────────────────────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:40px;max-width:520px;margin:60px auto;
                background:#fee2e2;border-radius:12px;border:1px solid #fca5a5;">
                <h3 style="color:#991b1b;margin:0 0 10px;">⚠ Database Connection Failed</h3>
                <p style="color:#7f1d1d;font-size:14px;">Check DB_HOST / DB_NAME / DB_USER / DB_PASS in config.php</p>
                </div>');
        }
    }
    return $pdo;
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function statusClass(string $s): string {
    return match($s) {
        'Present'      => 'badge-present',
        'Late'         => 'badge-late',
        'Absent'       => 'badge-absent',
        'Approved'     => 'badge-approved',
        'Rejected'     => 'badge-rejected',
        'Pending'      => 'badge-pending',
        'Under Review' => 'badge-review',
        default        => 'badge-default'
    };
}

function rateClass(float $r): string {
    if ($r >= 80) return 'rate-good';
    if ($r >= 60) return 'rate-warn';
    return 'rate-bad';
}

function timeAgo(string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60)    return 'just now';
    if ($diff < 3600)  return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}

function pendingDisputeCount(): int {
    try {
        return (int) db()->query(
            "SELECT COUNT(*) FROM dispute_requests WHERE status IN ('Pending','Under Review')"
        )->fetchColumn();
    } catch (PDOException) { return 0; }
}

function initials(string $name): string {
    $parts = explode(' ', trim($name));
    $out   = '';
    foreach ($parts as $p) $out .= strtoupper(substr($p, 0, 1));
    return substr($out, 0, 2);
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }