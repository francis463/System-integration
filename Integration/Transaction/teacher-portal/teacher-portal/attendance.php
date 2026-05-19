<?php
require_once 'config.php';
requireTeacher();
$pdo = db();

$dateFilter   = $_GET['date']   ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? '';
$export       = $_GET['export'] ?? '';

// Build query
$where  = ["a.date = ?"];
$params = [$dateFilter];
if ($statusFilter) { $where[] = "a.status = ?"; $params[] = $statusFilter; }
$wSql = implode(' AND ', $where);

$records = $pdo->prepare("
    SELECT a.*, s.full_name, s.student_number, s.course, s.section
    FROM attendance a
    JOIN students s ON a.user_id = s.user_id
    WHERE $wSql
    ORDER BY a.time DESC
");
$records->execute($params);
$records = $records->fetchAll();

// CSV export
if ($export === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_' . $dateFilter . '.csv"');
    $out = fopen('php://output','w');
    fputcsv($out, ['ID','Student No.','Name','Course','Section','Date','Day','Time','Status']);
    foreach ($records as $r) {
        fputcsv($out, [
            $r['attendance_id'], $r['student_number'], $r['full_name'],
            $r['course'], $r['section'],
            date('Y-m-d', strtotime($r['date'])),
            date('l', strtotime($r['date'])),
            date('H:i:s', strtotime($r['time'])),
            $r['status']
        ]);
    }
    fclose($out);
    exit;
}

// Stats for selected date
$present = array_sum(array_map(fn($r) => $r['status']==='Present'?1:0, $records));
$late    = array_sum(array_map(fn($r) => $r['status']==='Late'   ?1:0, $records));
$absent  = array_sum(array_map(fn($r) => $r['status']==='Absent' ?1:0, $records));
$total   = count($records);

// Available dates for quick jump
$dates = $pdo->query("SELECT DISTINCT date FROM attendance ORDER BY date DESC LIMIT 30")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle    = 'Attendance';
$pageSubtitle = date('l, F j, Y', strtotime($dateFilter));
$activePage   = 'attendance';
include 'layout.php';
?>

<div class="page-header">
    <div>
        <h2>Attendance Records</h2>
        <p><?php echo date('l, F j, Y', strtotime($dateFilter)); ?> — <?php echo $total; ?> record(s)</p>
    </div>
    <div class="page-actions">
        <a href="?<?php echo http_build_query(['date'=>$dateFilter,'status'=>$statusFilter,'export'=>'csv']); ?>"
           class="btn btn-export">⬇ Export CSV</a>
    </div>
</div>

<!-- Filter bar -->
<div class="card" style="margin-bottom:24px;">
    <form method="GET" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Date</span>
            <input type="date" name="date" class="filter-input" style="min-width:180px;"
                   value="<?php echo $dateFilter; ?>" max="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="status" class="filter-select">
                <option value="">All</option>
                <option value="Present" <?php echo $statusFilter==='Present'?'selected':''; ?>>Present</option>
                <option value="Late"    <?php echo $statusFilter==='Late'   ?'selected':''; ?>>Late</option>
                <option value="Absent"  <?php echo $statusFilter==='Absent' ?'selected':''; ?>>Absent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">View</button>
        <a href="attendance.php" class="btn btn-ghost">Today</a>
    </form>
    <!-- Quick date links -->
    <div style="padding:12px 24px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);">Quick:</span>
        <?php foreach (array_slice($dates,0,10) as $d): ?>
            <a href="?date=<?php echo $d; ?>"
               class="tab-btn <?php echo $d===$dateFilter?'active':''; ?>"
               style="padding:4px 12px;font-size:12px;">
                <?php echo date('M d', strtotime($d)); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Summary stats -->
<div class="stats-row" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card c-blue">
        <div class="stat-num"><?php echo $total; ?></div>
        <div class="stat-label">Total Records</div>
    </div>
    <div class="stat-card c-green">
        <div class="stat-num"><?php echo $present; ?></div>
        <div class="stat-label">Present</div>
    </div>
    <div class="stat-card c-amber">
        <div class="stat-num"><?php echo $late; ?></div>
        <div class="stat-label">Late</div>
    </div>
    <div class="stat-card c-red">
        <div class="stat-num"><?php echo $absent; ?></div>
        <div class="stat-label">Absent</div>
    </div>
</div>

<!-- Records table -->
<div class="card">
    <div class="card-header">
        <h3>Records for <?php echo date('F j, Y', strtotime($dateFilter)); ?></h3>
    </div>
    <?php if (empty($records)): ?>
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <h4>No records for this date</h4>
            <p>No attendance was recorded on <?php echo date('F j, Y', strtotime($dateFilter)); ?>.</p>
        </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Student</th><th>Course</th>
                    <th>Section</th><th>Time</th><th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td class="mono">#<?php echo $r['attendance_id']; ?></td>
                    <td>
                        <strong><?php echo e($r['full_name']); ?></strong>
                        <small><?php echo e($r['student_number']); ?></small>
                    </td>
                    <td style="font-size:12px;"><?php echo e($r['course']); ?></td>
                    <td style="font-size:12px;"><?php echo e($r['section']); ?></td>
                    <td class="mono"><?php echo date('g:i A', strtotime($r['time'])); ?></td>
                    <td><span class="badge <?php echo statusClass($r['status']); ?>"><?php echo $r['status']; ?></span></td>
                    <td><a href="student-record.php?id=<?php echo $r['user_id']; ?>" class="btn btn-ghost btn-sm">Profile</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include 'layout-end.php'; ?>
