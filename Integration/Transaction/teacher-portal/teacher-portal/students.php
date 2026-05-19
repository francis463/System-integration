<?php
require_once 'config.php';
requireTeacher();
$pdo = db();

$search  = trim($_GET['q']       ?? '');
$course  = $_GET['course']       ?? '';
$section = $_GET['section']      ?? '';
$sort    = $_GET['sort']         ?? 'name';
$riskOnly = !empty($_GET['risk']);

// Build WHERE
$where = []; $params = [];
if ($search)  { $where[] = "(s.full_name LIKE ? OR s.student_number LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($course)  { $where[] = "s.course = ?";   $params[] = $course; }
if ($section) { $where[] = "s.section = ?";  $params[] = $section; }
$wSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$orderMap = ['name'=>'s.full_name','number'=>'s.student_number','rate'=>'att_rate DESC','absent'=>'absent_cnt DESC'];
$orderBy  = $orderMap[$sort] ?? 's.full_name';

$students = $pdo->prepare("
    SELECT s.*,
           COUNT(a.attendance_id)                                as total_days,
           SUM(a.status='Present')                               as present_cnt,
           SUM(a.status='Late')                                  as late_cnt,
           SUM(a.status='Absent')                                as absent_cnt,
           ROUND(SUM(a.status='Present')/NULLIF(COUNT(*),0)*100,1) as att_rate,
           MAX(a.date)                                           as last_date,
           (SELECT a2.status FROM attendance a2 WHERE a2.user_id=s.user_id AND a2.date=CURDATE() LIMIT 1) as today_status
    FROM students s
    LEFT JOIN attendance a ON s.user_id=a.user_id
    $wSql
    GROUP BY s.user_id
    " . ($riskOnly ? "HAVING att_rate < 70 OR att_rate IS NULL" : "") . "
    ORDER BY $orderBy
");
$params ? $stmt = $students : $stmt = $students;
$students->execute($params);
$students = $students->fetchAll();

// Filter dropdowns
$courses  = $pdo->query("SELECT DISTINCT course  FROM students WHERE course  IS NOT NULL ORDER BY course")->fetchAll(PDO::FETCH_COLUMN);
$sections = $pdo->query("SELECT DISTINCT section FROM students WHERE section IS NOT NULL ORDER BY section")->fetchAll(PDO::FETCH_COLUMN);
$total    = count($students);

$pageTitle = 'Students'; $pageSubtitle = "$total student(s) found"; $activePage = 'students';
include 'layout.php';
?>

<div class="page-header">
    <div>
        <h2>Student Roster</h2>
        <p><?php echo $total; ?> student(s) — use filters to narrow down</p>
    </div>
    <div class="page-actions">
        <a href="reports.php" class="btn btn-export">⬇ Export</a>
    </div>
</div>

<!-- Filter bar -->
<div class="card" style="margin-bottom:24px;">
    <form method="GET" class="filter-bar">
        <div class="filter-group" style="flex:2;">
            <span class="filter-label">Search</span>
            <input type="text" name="q" class="filter-input"
                   placeholder="Name or student number…"
                   value="<?php echo e($search); ?>">
        </div>
        <div class="filter-group">
            <span class="filter-label">Course</span>
            <select name="course" class="filter-select">
                <option value="">All</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?php echo e($c); ?>" <?php echo $course===$c?'selected':''; ?>><?php echo e($c); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Section</span>
            <select name="section" class="filter-select">
                <option value="">All</option>
                <?php foreach ($sections as $s): ?>
                    <option value="<?php echo e($s); ?>" <?php echo $section===$s?'selected':''; ?>><?php echo e($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Sort</span>
            <select name="sort" class="filter-select">
                <option value="name"   <?php echo $sort==='name'  ?'selected':''; ?>>Name</option>
                <option value="number" <?php echo $sort==='number'?'selected':''; ?>>Student No.</option>
                <option value="rate"   <?php echo $sort==='rate'  ?'selected':''; ?>>Rate ↓</option>
                <option value="absent" <?php echo $sort==='absent'?'selected':''; ?>>Most Absent</option>
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Filter</span>
            <label style="display:flex;align-items:center;gap:6px;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;background:white;cursor:pointer;font-size:13px;<?php echo $riskOnly?'border-color:var(--danger);background:#fee2e2;':'' ?>">
                <input type="checkbox" name="risk" value="1" <?php echo $riskOnly?'checked':''; ?>>
                At-Risk Only
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="students.php" class="btn btn-ghost">Reset</a>
    </form>
</div>

<!-- Table -->
<div class="card">
    <?php if (empty($students)): ?>
        <div class="empty-state">
            <div class="empty-icon">👥</div>
            <h4>No students found</h4>
            <p>Try adjusting your search filters.</p>
        </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Course / Section</th>
                    <th>Today</th>
                    <th>Present</th>
                    <th>Late</th>
                    <th>Absent</th>
                    <th>Rate</th>
                    <th>Last Record</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $i => $s):
                $rate = (float)($s['att_rate'] ?? 0);
            ?>
                <tr>
                    <td class="mono" style="color:var(--muted);"><?php echo $i+1; ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,var(--accent),var(--accent-light));color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;font-family:var(--mono);">
                                <?php echo initials($s['full_name']); ?>
                            </div>
                            <div>
                                <strong><?php echo e($s['full_name']); ?></strong>
                                <small><?php echo e($s['student_number']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:12px;font-weight:600;color:var(--slate);"><?php echo e($s['course']); ?></span>
                        <small><?php echo e($s['section']); ?></small>
                    </td>
                    <td>
                        <?php if ($s['today_status']): ?>
                            <span class="badge <?php echo statusClass($s['today_status']); ?>"><?php echo $s['today_status']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-default">Not Yet</span>
                        <?php endif; ?>
                    </td>
                    <td style="color:var(--success);font-weight:700;"><?php echo (int)$s['present_cnt']; ?></td>
                    <td style="color:var(--warning);font-weight:700;"><?php echo (int)$s['late_cnt']; ?></td>
                    <td style="color:var(--danger);font-weight:700;"><?php echo (int)$s['absent_cnt']; ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="badge <?php echo rateClass($rate); ?>"><?php echo $rate; ?>%</span>
                            <div class="mini-bar"><div class="mini-bar-fill" style="width:<?php echo $rate; ?>%;background:<?php echo $rate>=80?'var(--success)':($rate>=60?'var(--warning)':'var(--danger)'); ?>;"></div></div>
                        </div>
                    </td>
                    <td class="mono" style="font-size:12px;color:var(--muted);">
                        <?php echo $s['last_date'] ? date('M d, Y', strtotime($s['last_date'])) : '—'; ?>
                    </td>
                    <td>
                        <a href="student-record.php?id=<?php echo $s['user_id']; ?>" class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include 'layout-end.php'; ?>
