<?php
// ─── Timezone & Session ────────────────────────────────────────────────────────
date_default_timezone_set('Asia/Manila');
session_start();

// ─── Database Configuration ───────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'attendance_system_db');

// ─── iProgSMS Configuration ───────────────────────────────────────────────────
define('SMS_API_URL',   'https://www.iprogsms.com/api/v1/sms_messages');
define('SMS_API_TOKEN', 'd4180281f11d9eafccf42161820b0bc8a07c8104');

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

// ─── Ensure attendance_logs table exists ──────────────────────────────────────
function ensureLogsTable($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS `attendance_logs` (
            `log_id`       INT(11)                NOT NULL AUTO_INCREMENT,
            `student_id`   VARCHAR(20)            NOT NULL,
            `full_name`    VARCHAR(100)           DEFAULT NULL,
            `schedule_id`  INT(11)                DEFAULT NULL,
            `class_id`     INT(11)                DEFAULT NULL,
            `subject_name` VARCHAR(100)           DEFAULT NULL,
            `section`      VARCHAR(50)            DEFAULT NULL,
            `action`       ENUM('in','out')       NOT NULL,
            `status`       ENUM('on_time','late') DEFAULT 'on_time',
            `sms_sent`     TINYINT(1)             DEFAULT 0,
            `logged_at`    DATETIME               NOT NULL,
            PRIMARY KEY (`log_id`),
            KEY `idx_student_id` (`student_id`),
            KEY `idx_logged_at`  (`logged_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
}

/**
 * Send an SMS notification via iProgSMS API.
 *
 * @param string $toNumber      Recipient phone number (e.g. "09171234567" or "+639171234567")
 * @param string $studentName   Full name of the student
 * @param string $sendTime      Formatted time string to include in the message
 * @param bool   $isLate        Whether the student is marked late
 * @param string $subjectName   Subject/class name for context
 *
 * @return array ['status' => 'success'|'failed', ...]
 */
function sendSmsNotification(
    string $toNumber,
    string $studentName,
    string $sendTime,
    bool   $isLate      = false,
    string $subjectName = ''
): array {
    // Normalize phone: strip leading + so API receives digits only
    $phone = str_replace('+', '', trim($toNumber));
    // Convert local 09xx to international 639xx
    if (str_starts_with($phone, '09')) {
        $phone = '63' . substr($phone, 1);
    }

    // Build message
    $lateNote = $isLate ? ' (arrived late)' : '';
    $classNote = $subjectName ? " for {$subjectName}" : '';
    $message = "SC BSIT Notification: Your child {$studentName} arrived at school{$classNote}. "
             . "Time: {$sendTime}{$lateNote}.";

    $data = [
        'api_token'    => SMS_API_TOKEN,
        'message'      => $message,
        'phone_number' => $phone,
    ];

    $ch = curl_init(SMS_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT,        30);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'failed', 'error' => $error];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300) {
        return ['status' => 'success', 'response' => $decoded];
    }

    return ['status' => 'failed', 'response' => $decoded, 'raw' => $response];
}

/**
 * Parse a schedule time string from the `schedules.time` column.
 * Handles formats like:
 *   "8:00-9:30 AM"          → start has no meridiem, end is AM
 *   "9:30-10:30 AM"         → same pattern
 *   "10:30 AM-12:00 PM"     → both have meridiem
 *   "1:00-2:30 PM"          → start has no meridiem, end is PM
 *   "2:30-4:30 PM"          → same
 *   "5:00-6:30 PM"          → same
 *
 * Returns [DateTime $start, DateTime $end] or null on parse failure.
 */
function parseScheduleTime(string $timeStr, DateTime $baseDate): ?array {
    $timeStr = trim($timeStr);

    if (!preg_match(
        '/^(\d{1,2}:\d{2})\s*(AM|PM)?\s*-\s*(\d{1,2}:\d{2})\s*(AM|PM)$/i',
        $timeStr, $m
    )) {
        return null;
    }

    $startRaw = $m[1];
    $startMer = strtoupper($m[2] ?? '');
    $endRaw   = $m[3];
    $endMer   = strtoupper($m[4]);

    $endDt = DateTime::createFromFormat('g:i A', $endRaw . ' ' . $endMer);
    if (!$endDt) return null;
    $endDt->setDate((int)$baseDate->format('Y'), (int)$baseDate->format('m'), (int)$baseDate->format('d'));

    if ($startMer !== '') {
        $startDt = DateTime::createFromFormat('g:i A', $startRaw . ' ' . $startMer);
    } else {
        $startDt = DateTime::createFromFormat('g:i A', $startRaw . ' ' . $endMer);
        if ($startDt && $startDt > $endDt) {
            $other   = ($endMer === 'PM') ? 'AM' : 'PM';
            $startDt = DateTime::createFromFormat('g:i A', $startRaw . ' ' . $other);
        }
    }

    if (!$startDt) return null;
    $startDt->setDate((int)$baseDate->format('Y'), (int)$baseDate->format('m'), (int)$baseDate->format('d'));

    return [$startDt, $endDt];
}

// ─── AJAX: Handle barcode scan POST ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    header('Content-Type: application/json');

    $scannedId = trim($_POST['student_id']);

    if (strlen($scannedId) < 3) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    $conn = getDB();
    ensureLogsTable($conn);

    $stmt = $conn->prepare("
        SELECT id, student_id, full_name,
               COALESCE(NULLIF(TRIM(parent_contact), ''), NULLIF(TRIM(contact), ''), '') AS guardian_phone
        FROM students
        WHERE student_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('s', $scannedId);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$student) {
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Student not found.', 'not_found' => true]);
        exit;
    }

    $studentPk    = $student['id'];
    $studentId    = $student['student_id'];
    $fullName     = $student['full_name'];
    $guardianPhone = trim($student['guardian_phone'] ?? '');

    $now        = new DateTime('now');
    // Removed day-based filtering to allow Monday schedule to work on any day

    // ── Today's enrolled schedules (removed day filter) ─────────────────────────────
    $stmt = $conn->prepare("
        SELECT
            sc.schedule_id,
            sc.class_code,
            sc.day,
            sc.time,
            sc.room,
            sc.instructor,
            cl.class_id,
            cl.subject_code,
            cl.subject_name,
            cl.section
        FROM student_schedule ss
        INNER JOIN schedules sc ON sc.schedule_id = ss.schedule_id
        INNER JOIN classes   cl ON cl.class_id    = ss.class_id
        WHERE ss.student_id = ?
        ORDER BY sc.time ASC
    ");
    $stmt->bind_param('i', $studentPk);
    $stmt->execute();
    $todayRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($todayRows)) {
        $conn->close();
        echo json_encode([
            'success'        => false,
            'no_class'       => true,
            'message'        => 'No classes enrolled for today.',
            'today_subjects' => [],
            'student_name'   => $fullName,
        ]);
        exit;
    }

    // ── Find the currently active schedule ─────────────────────────────────────
    $activeSchedule = null;
    $scheduleHints  = [];

    foreach ($todayRows as $row) {
        $parsed = parseScheduleTime($row['time'], $now);
        if (!$parsed) continue;

        [$startDt, $endDt] = $parsed;

        $scheduleHints[] = $row['subject_name']
            . ' · ' . $row['section']
            . ' (' . $startDt->format('h:i A')
            . '–'  . $endDt->format('h:i A') . ')'
            . ' · ' . ($row['room'] ?? '');

        if ($now >= $startDt && $now <= $endDt && $activeSchedule === null) {
            $activeSchedule            = $row;
            $activeSchedule['_start'] = $startDt;
            $activeSchedule['_end']   = $endDt;
        }
    }

    if (!$activeSchedule) {
        $conn->close();
        echo json_encode([
            'success'        => false,
            'no_class'       => true,
            'message'        => 'No active class right now.',
            'today_subjects' => $scheduleHints,
            'student_name'   => $fullName,
        ]);
        exit;
    }

    // ── Late check ─────────────────────────────────────────────────────────────
    $lateThresh  = clone $activeSchedule['_start'];
    $lateThresh->modify('+15 minutes');
    $isLate      = ($now > $lateThresh);
    $minutesLate = $isLate
        ? max(1, (int) round(($now->getTimestamp() - $lateThresh->getTimestamp()) / 60))
        : 0;
    $status = $isLate ? 'late' : 'on_time';

    // ── Last log for this student + schedule + class today ─────────────────────
    $todayDate  = $now->format('Y-m-d');
    $scheduleId = $activeSchedule['schedule_id'];
    $classId    = $activeSchedule['class_id'];

    $stmt = $conn->prepare("
        SELECT action FROM attendance_logs
        WHERE student_id  = ?
          AND schedule_id = ?
          AND class_id    = ?
          AND DATE(logged_at) = ?
        ORDER BY log_id DESC
        LIMIT 1
    ");
    $stmt->bind_param('siis', $studentId, $scheduleId, $classId, $todayDate);
    $stmt->execute();
    $lastRow    = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $lastAction = $lastRow['action'] ?? null;
    $action     = (!$lastAction || $lastAction === 'out') ? 'in' : 'out';

    // Late only applies on time-in
    if ($action === 'out') {
        $isLate      = false;
        $minutesLate = 0;
        $status      = 'on_time';
    }

    // ── Send SMS for time-in events (if guardian phone exists) ─────────────────
    $smsSent   = false;
    $smsResult = null;

    if ($action === 'in' && $guardianPhone !== '') {
        $sendTimeDisplay = $now->format('h:i A');
        $smsResult = sendSmsNotification(
            $guardianPhone,
            $fullName,
            $sendTimeDisplay,
            $isLate,
            $activeSchedule['subject_name']
        );
        $smsSent = ($smsResult['status'] === 'success');
    }

    // ── Insert attendance log ──────────────────────────────────────────────────
    $loggedAt    = $now->format('Y-m-d H:i:s');
    $subjectName = $activeSchedule['subject_name'];
    $section     = $activeSchedule['section'];
    $smsSentInt  = $smsSent ? 1 : 0;

    $stmt = $conn->prepare("
        INSERT INTO attendance_logs
            (student_id, full_name, schedule_id, class_id, subject_name, section, action, status, sms_sent, logged_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('ssiiisssls',
        $studentId, $fullName,
        $scheduleId, $classId,
        $subjectName, $section,
        $action, $status,
        $smsSentInt, $loggedAt
    );
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // ── First name extraction (format: "Last, First M.") ──────────────────────
    $nameParts = explode(',', $fullName, 2);
    $firstName = isset($nameParts[1])
        ? trim(explode(' ', trim($nameParts[1]))[0])
        : trim($nameParts[0]);

    if ($action === 'in') {
        $greeting = $isLate
            ? "You're late, {$firstName}! ({$minutesLate} min past grace)"
            : "Welcome, {$firstName}!";
        $icon        = $isLate ? '⏰' : '👋';
        $statusLabel = $isLate ? '● LATE' : '● TIME IN';
    } else {
        $greeting    = "Goodbye, {$firstName}! Take care.";
        $icon        = '🚀';
        $statusLabel = '● TIME OUT';
    }

    echo json_encode([
        'success'      => true,
        'id'           => $studentId,
        'full_name'    => $fullName,
        'first_name'   => $firstName,
        'action'       => $action,
        'status_label' => $statusLabel,
        'is_late'      => $isLate,
        'minutes_late' => $minutesLate,
        'subject_name' => $subjectName,
        'subject_code' => $activeSchedule['subject_code'],
        'section'      => $section,
        'room'         => $activeSchedule['room'] ?? '—',
        'instructor'   => $activeSchedule['instructor'] ?? '—',
        'class_time'   => $activeSchedule['_start']->format('h:i A')
                          . ' – '
                          . $activeSchedule['_end']->format('h:i A'),
        'timeStr'      => $now->format('h:i:s A'),
        'dateStr'      => $now->format('D, M j, Y'),
        'greeting'     => $greeting,
        'icon'         => $icon,
        // SMS feedback (visible in browser DevTools → Network for debugging)
        'sms_sent'     => $smsSent,
        'sms_result'   => $smsResult,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Attendance Scanner</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Exo+2:wght@300;400;600&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --bg: #050d1a;
      --panel: #0a1628;
      --border: #0e2a4a;
      --accent: #00c8ff;
      --accent2: #00ff9d;
      --warn: #ff6b35;
      --late: #f5c518;
      --text: #cce8ff;
      --muted: #4a7a9b;
      --glow: 0 0 20px rgba(0,200,255,0.4);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: var(--bg);
      font-family: 'Exo 2', sans-serif;
      color: var(--text);
      min-height: 100vh;
      display: flex; flex-direction: column; align-items: center;
      overflow-x: hidden;
    }

    body::before {
      content: ''; position: fixed; inset: 0;
      background-image:
        linear-gradient(rgba(0,200,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,200,255,0.03) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none; z-index: 0;
    }

    body::after {
      content: ''; position: fixed; inset: 0;
      background: repeating-linear-gradient(
        0deg, transparent, transparent 2px,
        rgba(0,0,0,0.05) 2px, rgba(0,0,0,0.05) 4px
      );
      pointer-events: none; z-index: 0;
    }

    header {
      position: relative; z-index: 1; width: 100%;
      padding: 28px 40px 20px;
      display: flex; align-items: center; justify-content: space-between;
      border-bottom: 1px solid var(--border);
      background: rgba(5,13,26,0.8); backdrop-filter: blur(10px);
    }

    .logo {
      font-family: 'Orbitron', monospace; font-size: 1.1rem; font-weight: 900;
      letter-spacing: 0.2em; color: var(--accent); text-shadow: var(--glow);
    }
    .logo span { color: var(--accent2); }

    #live-clock {
      font-family: 'Orbitron', monospace; font-size: 1.3rem; font-weight: 700;
      color: var(--accent); text-shadow: var(--glow); letter-spacing: 0.1em;
    }

    #live-date { font-size: 0.78rem; color: var(--muted); text-align: right; letter-spacing: 0.08em; margin-top: 4px; }

    main {
      position: relative; z-index: 1; width: 100%; max-width: 900px;
      padding: 40px 20px; display: flex; flex-direction: column; align-items: center; gap: 32px;
    }

    .scanner-panel {
      width: 100%; background: var(--panel); border: 1px solid var(--border);
      border-radius: 16px; padding: 40px 36px; position: relative; overflow: hidden;
    }

    .scanner-panel::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
      background: linear-gradient(90deg, transparent, var(--accent), var(--accent2), transparent);
      animation: shimmer 3s infinite;
    }

    @keyframes shimmer { 0%,100% { opacity: 0.4; } 50% { opacity: 1; } }

    .scanner-title {
      font-family: 'Orbitron', monospace; font-size: 0.75rem; letter-spacing: 0.3em;
      color: var(--muted); text-transform: uppercase; margin-bottom: 28px;
    }

    .barcode-icon { display: flex; justify-content: center; margin-bottom: 24px; }
    .barcode-svg  { width: 120px; height: 60px; opacity: 0.7; }

    .scan-label {
      font-family: 'Orbitron', monospace; font-size: 1.6rem; font-weight: 700;
      text-align: center; color: var(--text); margin-bottom: 8px;
    }

    .scan-subtitle {
      text-align: center; font-size: 0.88rem; color: var(--muted);
      margin-bottom: 32px; letter-spacing: 0.04em;
    }

    .input-wrapper {
      position: relative; display: flex; align-items: center;
      max-width: 420px; margin: 0 auto;
    }

    .input-wrapper svg {
      position: absolute; left: 16px; color: var(--muted);
      width: 20px; height: 20px; pointer-events: none;
    }

    #barcode-input {
      width: 100%; padding: 16px 16px 16px 50px;
      background: rgba(0,200,255,0.04); border: 1.5px solid var(--border);
      border-radius: 10px; color: var(--accent);
      font-family: 'Orbitron', monospace; font-size: 1.1rem;
      letter-spacing: 0.15em; outline: none;
      transition: border-color 0.3s, box-shadow 0.3s;
      caret-color: var(--accent);
    }

    #barcode-input:focus {
      border-color: var(--accent);
      box-shadow: var(--glow), inset 0 0 12px rgba(0,200,255,0.05);
    }

    #barcode-input::placeholder { color: var(--muted); letter-spacing: 0.08em; font-size: 0.85rem; }

    .scan-hint { text-align: center; font-size: 0.76rem; color: var(--muted); margin-top: 14px; letter-spacing: 0.06em; }
    .scan-hint span { color: var(--accent2); }

    .scan-beam {
      position: absolute; left: 0; right: 0; height: 2px;
      background: linear-gradient(90deg, transparent 0%, var(--accent) 30%, var(--accent2) 50%, var(--accent) 70%, transparent 100%);
      top: 50%; animation: beam 2s ease-in-out infinite;
      opacity: 0; transition: opacity 0.3s; pointer-events: none;
    }
    .input-wrapper.scanning .scan-beam { opacity: 1; }

    @keyframes beam {
      0%   { top: calc(50% - 28px); }
      50%  { top: calc(50% + 28px); }
      100% { top: calc(50% - 28px); }
    }

    /* ── Notification ── */
    #notification {
      width: 100%; border-radius: 16px;
      display: none; position: relative; overflow: hidden;
      animation: slideIn 0.4s cubic-bezier(0.16,1,0.3,1);
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateY(20px) scale(0.97); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    #notification.time-in  { background: linear-gradient(135deg, rgba(0,255,157,0.07), rgba(0,200,255,0.05));  border: 1px solid rgba(0,255,157,0.25); }
    #notification.time-out { background: linear-gradient(135deg, rgba(255,107,53,0.08), rgba(255,200,50,0.04)); border: 1px solid rgba(255,107,53,0.3); }
    #notification.late     { background: linear-gradient(135deg, rgba(245,197,24,0.09), rgba(255,140,0,0.05));  border: 1px solid rgba(245,197,24,0.4); }
    #notification.not-found{ background: linear-gradient(135deg, rgba(255,80,80,0.08),  rgba(180,0,0,0.04));    border: 1px solid rgba(255,80,80,0.35); }
    #notification.no-class { background: linear-gradient(135deg, rgba(100,100,200,0.09),rgba(60,60,160,0.05));  border: 1px solid rgba(120,120,255,0.35); }

    #notification::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    }
    #notification.time-in::before   { background: linear-gradient(90deg, transparent, var(--accent2), transparent); }
    #notification.time-out::before  { background: linear-gradient(90deg, transparent, var(--warn),    transparent); }
    #notification.late::before      { background: linear-gradient(90deg, transparent, var(--late),    transparent); }
    #notification.not-found::before { background: linear-gradient(90deg, transparent, #ff5050,        transparent); }
    #notification.no-class::before  { background: linear-gradient(90deg, transparent, #8888ff,        transparent); }

    .notif-body { padding: 28px 32px; }
    .notif-inner { display: flex; align-items: center; gap: 24px; }

    .notif-icon {
      width: 64px; height: 64px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.8rem; flex-shrink: 0;
    }
    .time-in .notif-icon   { background: rgba(0,255,157,0.12);   border: 1.5px solid rgba(0,255,157,0.3); }
    .time-out .notif-icon  { background: rgba(255,107,53,0.12);  border: 1.5px solid rgba(255,107,53,0.3); }
    .late .notif-icon      { background: rgba(245,197,24,0.12);  border: 1.5px solid rgba(245,197,24,0.4); }
    .not-found .notif-icon { background: rgba(255,80,80,0.12);   border: 1.5px solid rgba(255,80,80,0.3); }
    .no-class .notif-icon  { background: rgba(120,120,255,0.12); border: 1.5px solid rgba(120,120,255,0.3); }

    .notif-status {
      font-family: 'Orbitron', monospace; font-size: 0.7rem;
      letter-spacing: 0.25em; text-transform: uppercase; margin-bottom: 6px;
    }
    .time-in .notif-status   { color: var(--accent2); }
    .time-out .notif-status  { color: var(--warn); }
    .late .notif-status      { color: var(--late); }
    .not-found .notif-status { color: #ff5050; }
    .no-class .notif-status  { color: #8888ff; }

    .notif-greeting { font-size: 1.45rem; font-weight: 600; color: var(--text); margin-bottom: 4px; }

    .notif-id { font-family: 'Orbitron', monospace; font-size: 0.78rem; letter-spacing: 0.08em; margin-bottom: 2px; }
    .time-in .notif-id   { color: var(--accent); }
    .time-out .notif-id  { color: #ffb38a; }
    .late .notif-id      { color: var(--late); }
    .not-found .notif-id { color: #ff9090; }
    .no-class .notif-id  { color: #aaaaff; }

    .notif-time { margin-left: auto; text-align: right; flex-shrink: 0; }
    .notif-timestamp { font-family: 'Orbitron', monospace; font-size: 1.3rem; font-weight: 700; color: var(--text); }
    .notif-datestamp { font-size: 0.75rem; color: var(--muted); margin-top: 4px; letter-spacing: 0.05em; }

    .late-badge {
      display: none; margin-top: 6px;
      padding: 3px 10px;
      background: rgba(245,197,24,0.15); border: 1px solid rgba(245,197,24,0.4);
      border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.12em;
      color: var(--late); font-family: 'Orbitron', monospace; text-transform: uppercase;
    }

    /* ── SMS badge ── */
    .sms-badge {
      display: none; margin-top: 6px; margin-left: 6px;
      padding: 3px 10px;
      border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.1em;
      font-family: 'Orbitron', monospace; text-transform: uppercase;
    }
    .sms-badge.sent   { background: rgba(0,255,157,0.12); border: 1px solid rgba(0,255,157,0.35); color: var(--accent2); }
    .sms-badge.failed { background: rgba(255,80,80,0.10); border: 1px solid rgba(255,80,80,0.35);  color: #ff8080; }

    .badge-row { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; margin-top: 2px; }

    .subject-strip {
      margin-top: 18px; padding-top: 16px;
      border-top: 1px solid rgba(0,200,255,0.1);
      display: none; flex-wrap: wrap; gap: 20px;
    }

    .subject-chip { display: flex; flex-direction: column; gap: 2px; }
    .chip-label { font-size: 0.65rem; letter-spacing: 0.2em; color: var(--muted); text-transform: uppercase; }
    .chip-value { font-family: 'Orbitron', monospace; font-size: 0.82rem; color: var(--text); letter-spacing: 0.04em; }

    .schedule-list {
      margin-top: 18px; padding-top: 16px;
      border-top: 1px solid rgba(120,120,255,0.15);
      display: none; flex-direction: column; gap: 8px;
    }

    .schedule-label { font-size: 0.7rem; letter-spacing: 0.2em; color: var(--muted); text-transform: uppercase; margin-bottom: 4px; }

    .schedule-item {
      font-size: 0.82rem; color: #aaaaee; padding: 6px 12px;
      background: rgba(120,120,255,0.07);
      border-radius: 6px; border: 1px solid rgba(120,120,255,0.15);
      letter-spacing: 0.03em;
    }

    .progress-bar { height: 3px; background: rgba(0,200,255,0.1); overflow: hidden; }

    .progress-fill {
      height: 100%; width: 100%;
      transform-origin: left; animation: drain 6s linear forwards;
    }
    .time-in .progress-fill   { background: var(--accent2); }
    .time-out .progress-fill  { background: var(--warn); }
    .late .progress-fill      { background: var(--late); }
    .not-found .progress-fill { background: #ff5050; }
    .no-class .progress-fill  { background: #8888ff; }

    @keyframes drain { from { transform: scaleX(1); } to { transform: scaleX(0); } }
  </style>
</head>
<body>

<header>
  <div class="logo">SCAN<span>TRACK</span></div>
  <div>
    <div id="live-clock">00:00:00</div>
    <div id="live-date">—</div>
  </div>
</header>

<main>

  <div class="scanner-panel">
    <div class="scanner-title">// BARCODE ATTENDANCE TERMINAL</div>

    <div class="barcode-icon">
      <svg class="barcode-svg" viewBox="0 0 120 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="4"  y="6" width="3"  height="48" fill="#00c8ff" opacity="0.9"/>
        <rect x="10" y="6" width="5"  height="48" fill="#00c8ff" opacity="0.8"/>
        <rect x="18" y="6" width="2"  height="48" fill="#00c8ff" opacity="0.9"/>
        <rect x="23" y="6" width="4"  height="48" fill="#00c8ff" opacity="0.7"/>
        <rect x="30" y="6" width="3"  height="48" fill="#00ff9d" opacity="0.9"/>
        <rect x="36" y="6" width="6"  height="48" fill="#00c8ff" opacity="0.8"/>
        <rect x="45" y="6" width="2"  height="48" fill="#00c8ff" opacity="0.9"/>
        <rect x="50" y="6" width="4"  height="48" fill="#00ff9d" opacity="0.8"/>
        <rect x="57" y="6" width="3"  height="48" fill="#00c8ff" opacity="0.9"/>
        <rect x="63" y="6" width="5"  height="48" fill="#00c8ff" opacity="0.7"/>
        <rect x="71" y="6" width="2"  height="48" fill="#00c8ff" opacity="0.9"/>
        <rect x="76" y="6" width="4"  height="48" fill="#00ff9d" opacity="0.9"/>
        <rect x="83" y="6" width="3"  height="48" fill="#00c8ff" opacity="0.8"/>
        <rect x="89" y="6" width="6"  height="48" fill="#00c8ff" opacity="0.7"/>
        <rect x="98" y="6" width="2"  height="48" fill="#00c8ff" opacity="0.9"/>
        <rect x="103" y="6" width="4" height="48" fill="#00ff9d" opacity="0.8"/>
        <rect x="110" y="6" width="6" height="48" fill="#00c8ff" opacity="0.9"/>
        <rect x="0"   y="29" width="120" height="2" fill="#00c8ff" opacity="0.5"/>
      </svg>
    </div>

    <div class="scan-label">Scan Your ID</div>
    <div class="scan-subtitle">Must be enrolled in an active class to time in — late after 15-minute grace period · SMS sent to guardian on arrival</div>

    <div class="input-wrapper" id="input-wrapper">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <path d="M7 7h2v10H7zM11 7h1v10h-1zM14 7h3v10h-3z" stroke="none" fill="currentColor"/>
      </svg>
      <input
        id="barcode-input"
        type="text"
        inputmode="numeric"
        autocomplete="off"
        placeholder="Scan or enter ID number..."
        autofocus
      />
      <div class="scan-beam"></div>
    </div>

    <p class="scan-hint">Scanner input is auto-detected — or press <span>ENTER</span> after typing</p>
  </div>

  <!-- Notification Card -->
  <div id="notification">
    <div class="notif-body">
      <div class="notif-inner">
        <div class="notif-icon" id="notif-icon">😊</div>
        <div class="notif-text" style="flex:1;min-width:0;">
          <div class="notif-status"   id="notif-status">TIME IN</div>
          <div class="notif-greeting" id="notif-greeting">Welcome!</div>
          <div class="notif-id"       id="notif-id">ID: —</div>
          <div class="badge-row">
            <div id="late-badge" class="late-badge">⏱ MARKED LATE</div>
            <div id="sms-badge"  class="sms-badge">📱 SMS SENT</div>
          </div>
        </div>
        <div class="notif-time">
          <div class="notif-timestamp" id="notif-timestamp">—</div>
          <div class="notif-datestamp" id="notif-datestamp">—</div>
        </div>
      </div>

      <!-- Class info strip (success) -->
      <div class="subject-strip" id="subject-strip">
        <div class="subject-chip">
          <span class="chip-label">Subject</span>
          <span class="chip-value" id="chip-subject">—</span>
        </div>
        <div class="subject-chip">
          <span class="chip-label">Code</span>
          <span class="chip-value" id="chip-code">—</span>
        </div>
        <div class="subject-chip">
          <span class="chip-label">Section</span>
          <span class="chip-value" id="chip-section">—</span>
        </div>
        <div class="subject-chip">
          <span class="chip-label">Schedule</span>
          <span class="chip-value" id="chip-time">—</span>
        </div>
        <div class="subject-chip">
          <span class="chip-label">Room</span>
          <span class="chip-value" id="chip-room">—</span>
        </div>
        <div class="subject-chip">
          <span class="chip-label">Instructor</span>
          <span class="chip-value" id="chip-instructor">—</span>
        </div>
      </div>

      <!-- Today's schedule hint (no-class) -->
      <div class="schedule-list" id="schedule-list">
        <div class="schedule-label">Today's Enrolled Schedule</div>
        <div id="schedule-items"></div>
      </div>
    </div>
    <div class="progress-bar">
      <div class="progress-fill" id="progress-fill"></div>
    </div>
  </div>

</main>

<script>
  // ─── Live Clock ───────────────────────────────────────────────────────────
  function updateClock() {
    const now = new Date();
    document.getElementById('live-clock').textContent =
      now.toLocaleTimeString('en-PH', { hour12: false });
    document.getElementById('live-date').textContent =
      now.toLocaleDateString('en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
  }
  updateClock();
  setInterval(updateClock, 1000);

  // ─── Scanner ──────────────────────────────────────────────────────────────
  const input   = document.getElementById('barcode-input');
  const wrapper = document.getElementById('input-wrapper');
  let clearTimer = null;

  document.addEventListener('click',   () => input.focus());
  document.addEventListener('keydown', () => input.focus());

  input.addEventListener('focus', () => wrapper.classList.add('scanning'));
  input.addEventListener('blur',  () => {
    wrapper.classList.remove('scanning');
    setTimeout(() => input.focus(), 100);
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const val = input.value.trim();
      if (val.length >= 3) submitScan(val);
    }
  });

  function submitScan(id) {
    input.value = '';
    const fd = new FormData();
    fd.append('student_id', id);
    fetch(window.location.href, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(data => {
        if (data.success)        showSuccess(data);
        else if (data.not_found) showNotFound(id);
        else if (data.no_class)  showNoClass(data, id);
      })
      .catch(() => showNotFound(id))
      .finally(() => setTimeout(() => input.focus(), 10));
  }

  // ─── Notification helpers ─────────────────────────────────────────────────
  function resetNotif() {
    if (clearTimer) clearTimeout(clearTimer);
    const notif = document.getElementById('notification');
    notif.className = '';
    notif.style.display = 'none';
    document.getElementById('late-badge').style.display    = 'none';
    document.getElementById('sms-badge').style.display     = 'none';
    document.getElementById('sms-badge').className         = 'sms-badge';
    document.getElementById('subject-strip').style.display = 'none';
    document.getElementById('schedule-list').style.display = 'none';
    document.getElementById('schedule-items').innerHTML    = '';
    const fill = document.getElementById('progress-fill');
    fill.style.animation = 'none';
    void fill.offsetWidth;
    fill.style.animation = '';
  }

  function revealNotif(cls) {
    const notif = document.getElementById('notification');
    notif.classList.add(cls);
    notif.style.display = 'block';
    clearTimer = setTimeout(() => { notif.style.display = 'none'; }, 6000);
  }

  function setCommon(icon, statusText, greeting, idLine, timeStr, dateStr) {
    document.getElementById('notif-icon').textContent      = icon;
    document.getElementById('notif-status').textContent    = statusText;
    document.getElementById('notif-greeting').textContent  = greeting;
    document.getElementById('notif-id').textContent        = idLine;
    document.getElementById('notif-timestamp').textContent = timeStr;
    document.getElementById('notif-datestamp').textContent = dateStr;
  }

  function showSuccess(data) {
    resetNotif();
    const cls = data.action === 'out' ? 'time-out' : (data.is_late ? 'late' : 'time-in');
    setCommon(data.icon, data.status_label, data.greeting,
      'ID: ' + data.id + '  |  ' + data.full_name,
      data.timeStr, data.dateStr);

    if (data.is_late) document.getElementById('late-badge').style.display = 'inline-block';

    // ── SMS badge ──────────────────────────────────────────────────────────
    if (data.action === 'in' && data.sms_result !== null) {
      const smsBadge = document.getElementById('sms-badge');
      smsBadge.style.display = 'inline-block';
      if (data.sms_sent) {
        smsBadge.classList.add('sent');
        smsBadge.textContent = '📱 SMS SENT';
      } else {
        smsBadge.classList.add('failed');
        smsBadge.textContent = '📵 SMS FAILED';
      }
    }

    const strip = document.getElementById('subject-strip');
    strip.style.display = 'flex';
    document.getElementById('chip-subject').textContent    = data.subject_name;
    document.getElementById('chip-code').textContent       = data.subject_code;
    document.getElementById('chip-section').textContent    = data.section;
    document.getElementById('chip-time').textContent       = data.class_time;
    document.getElementById('chip-room').textContent       = data.room;
    document.getElementById('chip-instructor').textContent = data.instructor;

    revealNotif(cls);
  }

  function showNotFound(id) {
    resetNotif();
    const now = new Date();
    setCommon('⚠️', '● NOT FOUND', 'Unrecognized ID',
      'ID: ' + id + ' — not registered in the system',
      now.toLocaleTimeString('en-PH', { hour12: true }),
      now.toLocaleDateString('en-PH', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }));
    revealNotif('not-found');
  }

  function showNoClass(data, id) {
    resetNotif();
    const now  = new Date();
    const name = data.student_name ? ' — ' + data.student_name : '';
    const hasSchedule = data.today_subjects && data.today_subjects.length > 0;

    setCommon('🚫', '● ACCESS DENIED',
      hasSchedule ? 'No active class right now' : 'No classes today',
      'ID: ' + id + name,
      now.toLocaleTimeString('en-PH', { hour12: true }),
      now.toLocaleDateString('en-PH', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }));

    if (hasSchedule) {
      const list  = document.getElementById('schedule-list');
      const items = document.getElementById('schedule-items');
      list.style.display = 'flex';
      data.today_subjects.forEach(s => {
        const div = document.createElement('div');
        div.className = 'schedule-item';
        div.textContent = s;
        items.appendChild(div);
      });
    }

    revealNotif('no-class');
  }

  input.focus();
</script>
</body>
</html>