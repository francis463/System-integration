<?php
require_once 'config.php';
require_admin();


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/nav.css">
</head>
<body>
  <div class="app">
    <?php render_sidebar('dashboard'); ?>
    <main class="main">
      <?php render_topbar('Dashboard'); ?>
      <section class="cards">
        <?php foreach ($SUBSYSTEMS as $key => $sub) : ?>
          <a class="card" href="<?php echo e(subsystem_link($key)); ?>">
            <div class="card-title"><?php echo e($sub['label']); ?></div>
            <div class="card-count"><?php echo e($counts[$key] ?? 0); ?></div>
            <div class="card-sub">Records</div>
          </a>
        <?php endforeach; ?>
      </section>
    </main>
  </div>
</body>
</html>
