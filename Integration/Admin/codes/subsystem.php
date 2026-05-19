<?php
require_once 'config.php';
require_admin();

$entity = $_GET['entity'] ?? '';
if (!isset($SUBSYSTEMS[$entity])) {
    header('Location: dashboard.php');
    exit;
}

$sub = $SUBSYSTEMS[$entity];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo e($sub['label']); ?></title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/nav.css">
  <?php if (!empty($sub['css'])) : ?>
  <link rel="stylesheet" href="<?php echo e($sub['css']); ?>">
  <?php endif; ?>
</head>
<body>
  <div class="app">
    <?php render_sidebar($entity); ?>
    <main class="main">
      <?php render_topbar($sub['label']); ?>
    
    </main>
  </div>
</body>
</html>
