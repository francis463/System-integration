<?php
// layout.php — Shared header/sidebar partial
// Include AFTER setting $pageTitle, $pageSubtitle, $activePage
// Usage: include 'layout.php';
// Then echo your content, then include 'layout-end.php'

$pageTitle    = $pageTitle    ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? date('l, F j, Y');
$activePage   = $activePage   ?? 'dashboard';

$pending = pendingDisputeCount();
$tName   = $_SESSION['t_name']    ?? 'Teacher';
$tDept   = $_SESSION['t_dept']    ?? '';
$tRole   = $_SESSION['t_role']    ?? 'teacher';
$tInit   = initials($tName);

$nav = [
    ['id'=>'dashboard', 'href'=>'dashboard.php',  'icon'=>'🏠', 'label'=>'Dashboard'],
    ['id'=>'students',  'href'=>'students.php',   'icon'=>'👥', 'label'=>'Students'],
    ['id'=>'attendance','href'=>'attendance.php',  'icon'=>'📅', 'label'=>'Attendance'],
    ['id'=>'disputes',  'href'=>'disputes.php',   'icon'=>'📋', 'label'=>'Disputes', 'badge'=>$pending],
    ['id'=>'reports',   'href'=>'reports.php',    'icon'=>'📊', 'label'=>'Reports & Export'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?php echo csrf(); ?>">
<title><?php echo e($pageTitle); ?> — Teacher Portal · TRMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-shell">

    <!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-badge">Group 7 · TRMS</div>
            <div class="brand-name">Teacher<br>Portal</div>
            <div class="brand-sub">Southland College</div>
        </div>

        <div class="sidebar-teacher">
            <div class="t-avatar"><?php echo $tInit; ?></div>
            <div class="t-info">
                <strong><?php echo e($tName); ?></strong>
                <small><?php echo e($tDept ?: $tRole); ?></small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Navigation</div>
            <?php foreach ($nav as $item): ?>
                <a href="<?php echo $item['href']; ?>"
                   class="nav-item <?php echo $activePage === $item['id'] ? 'active' : ''; ?>">
                    <span class="nav-icon"><?php echo $item['icon']; ?></span>
                    <span><?php echo $item['label']; ?></span>
                    <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                        <span class="nav-badge"><?php echo $item['badge']; ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="logout.php">
                <?php echo csrfField(); ?>
                <button type="submit" class="logout-btn">
                    <span>🚪</span> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- ── Top header ────────────────────────────────────────────────────────── -->
    <header class="app-header">
        <div class="header-left">
            <h1><?php echo e($pageTitle); ?></h1>
            <p><?php echo e($pageSubtitle); ?></p>
        </div>
        <div class="header-right">
            <div class="header-time" id="liveClock"><?php echo date('g:i:s A'); ?></div>
            <?php if ($pending): ?>
                <a href="disputes.php" class="header-alert-btn" title="<?php echo $pending; ?> pending dispute(s)">
                    📋<span class="alert-dot"></span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- ── Main ──────────────────────────────────────────────────────────────── -->
    <main class="app-main">
