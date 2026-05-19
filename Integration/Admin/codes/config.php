<?php
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'modular_admin';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    die('Database connection failed.');
}
$mysqli->set_charset('utf8mb4');

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function require_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function stmt_bind($stmt, $types, $params) {
    $bind = [];
    $bind[] = $types;
    foreach ($params as $key => $value) {
        $bind[] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind);
}

function db_execute($mysqli, $sql, $types = '', $params = []) {
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Prepare failed.');
    }
    if ($types !== '') {
        stmt_bind($stmt, $types, $params);
    }
    $stmt->execute();
    return $stmt;
}

function db_fetch_all($mysqli, $sql, $types = '', $params = []) {
    $stmt = db_execute($mysqli, $sql, $types, $params);
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function db_fetch_one($mysqli, $sql, $types = '', $params = []) {
    $stmt = db_execute($mysqli, $sql, $types, $params);
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

$ROLE_OPTIONS = ['Super Admin', 'Admin', 'Teacher', 'Student'];
$STATUS_OPTIONS = ['active', 'inactive'];

$SUBSYSTEMS = [
    'users' => [
        'label' => 'User & Identity Management Subsystem',
        'table' => 'users',
        'path' => 'users.php',
        'css' => 'assets/users.css',
        'list_fields' => [
            ['name' => 'id', 'label' => 'ID'],
            ['name' => 'full_name', 'label' => 'Full Name'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'role', 'label' => 'Role'],
            ['name' => 'status', 'label' => 'Status'],
            ['name' => 'created_at', 'label' => 'Created']
        ],
        'form_fields' => [
            ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => $ROLE_OPTIONS, 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => $STATUS_OPTIONS, 'required_on_add' => true, 'required_on_edit' => true],
        ],
    ],
    'notifications' => [
        'label' => 'Notification & Communication Subsystem',
        'table' => 'notifications',
        'path' => 'notifications.php',
        'css' => 'assets/notifications.css',
        'list_fields' => [
            ['name' => 'id', 'label' => 'ID'],
            ['name' => 'title', 'label' => 'Title'],
            ['name' => 'channel', 'label' => 'Channel'],
            ['name' => 'status', 'label' => 'Status'],
            ['name' => 'created_at', 'label' => 'Created']
        ],
        'form_fields' => [
            ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'channel', 'label' => 'Channel', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => $STATUS_OPTIONS, 'required_on_add' => true, 'required_on_edit' => true],
        ],
    ],
    'profiles' => [
        'label' => 'Records / Profile Management Subsystem',
        'table' => 'profiles',
        'path' => 'profiles.php',
        'css' => 'assets/profiles.css',
        'list_fields' => [
            ['name' => 'id', 'label' => 'ID'],
            ['name' => 'user_id', 'label' => 'User ID'],
            ['name' => 'profile_name', 'label' => 'Profile Name'],
            ['name' => 'status', 'label' => 'Status'],
            ['name' => 'created_at', 'label' => 'Created']
        ],
        'form_fields' => [
            ['name' => 'user_id', 'label' => 'User ID', 'type' => 'number', 'bind' => 'i', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'profile_name', 'label' => 'Profile Name', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'details', 'label' => 'Details', 'type' => 'textarea', 'required_on_add' => false, 'required_on_edit' => false],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => $STATUS_OPTIONS, 'required_on_add' => true, 'required_on_edit' => true],
        ],
    ],
    'schedules' => [
        'label' => 'Scheduling & Resource Management Subsystem',
        'table' => 'schedules',
        'path' => 'schedules.php',
        'css' => 'assets/schedules.css',
        'list_fields' => [
            ['name' => 'id', 'label' => 'ID'],
            ['name' => 'resource_name', 'label' => 'Resource'],
            ['name' => 'scheduled_at', 'label' => 'Scheduled At'],
            ['name' => 'duration_minutes', 'label' => 'Duration (min)'],
            ['name' => 'status', 'label' => 'Status'],
            ['name' => 'created_at', 'label' => 'Created']
        ],
        'form_fields' => [
            ['name' => 'resource_name', 'label' => 'Resource', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'scheduled_at', 'label' => 'Scheduled At', 'type' => 'datetime-local', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'duration_minutes', 'label' => 'Duration (minutes)', 'type' => 'number', 'bind' => 'i', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => $STATUS_OPTIONS, 'required_on_add' => true, 'required_on_edit' => true],
        ],
    ],
    'reports' => [
        'label' => 'Reports & Analytics Subsystem',
        'table' => 'reports',
        'path' => 'reports.php',
        'css' => 'assets/reports.css',
        'list_fields' => [
            ['name' => 'id', 'label' => 'ID'],
            ['name' => 'title', 'label' => 'Title'],
            ['name' => 'report_type', 'label' => 'Type'],
            ['name' => 'period', 'label' => 'Period'],
            ['name' => 'status', 'label' => 'Status'],
            ['name' => 'created_at', 'label' => 'Created']
        ],
        'form_fields' => [
            ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'report_type', 'label' => 'Type', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'period', 'label' => 'Period', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => $STATUS_OPTIONS, 'required_on_add' => true, 'required_on_edit' => true],
        ],
    ],
    'transactions' => [
        'label' => 'Transaction / Request Management Subsystem',
        'table' => 'transactions',
        'path' => 'transactions.php',
        'css' => 'assets/transactions.css',
        'list_fields' => [
            ['name' => 'id', 'label' => 'ID'],
            ['name' => 'request_ref', 'label' => 'Request Ref'],
            ['name' => 'amount', 'label' => 'Amount'],
            ['name' => 'status', 'label' => 'Status'],
            ['name' => 'created_at', 'label' => 'Created']
        ],
        'form_fields' => [
            ['name' => 'request_ref', 'label' => 'Request Ref', 'type' => 'text', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'required_on_add' => false, 'required_on_edit' => false],
            ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'bind' => 'd', 'required_on_add' => true, 'required_on_edit' => true],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => $STATUS_OPTIONS, 'required_on_add' => true, 'required_on_edit' => true],
        ],
    ],
];

function subsystem_link($key) {
    global $SUBSYSTEMS;
    if (!isset($SUBSYSTEMS[$key])) {
        return 'dashboard.php';
    }
    $sub = $SUBSYSTEMS[$key];
    if (!empty($sub['path'])) {
        return $sub['path'];
    }
    return 'subsystem.php?entity=' . urlencode($key);
}

function render_sidebar($active = '') {
    global $SUBSYSTEMS;
    echo '<aside class="sidebar">';
    echo '<div class="sidebar-brand">Student Attendance Monitoring System</div>';//nav header
    echo '<nav class="nav">';
    $dashboardClass = $active === 'dashboard' ? 'active' : '';
    echo '<a class="nav-link ' . $dashboardClass . '" href="dashboard.php">Dashboard</a>';
    foreach ($SUBSYSTEMS as $key => $sub) {
        $class = $active === $key ? 'active' : '';
        $href = subsystem_link($key);
        echo '<a class="nav-link ' . $class . '" href="' . e($href) . '">' . e($sub['label']) . '</a>';
    }
    echo '</nav>';
    echo '</aside>';
}

function render_topbar($title) {
    $username = isset($_SESSION['admin_username']) ? e($_SESSION['admin_username']) : 'Admin';
    echo '<div class="topbar">';
    echo '<div class="topbar-title">' . e($title) . '</div>';
    echo '<div class="topbar-actions">';
    echo '<span class="topbar-user">Signed in as ' . $username . '</span>';
    echo '<a class="btn btn-secondary" href="logout.php">Logout</a>';
    echo '</div>';
    echo '</div>';
}
?>



