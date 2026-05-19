<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'db.php'; 

$action = $_POST['action'] ?? $_GET['action'] ?? '';

header('Content-Type: application/json');

try {
    switch ($action) {

        // --- GET STUDENTS BY SUBJECT (with day & search) ---
        case 'get_students_by_subject':
            $subject_code = $_GET['subject'] ?? 'all';
            $search = $_GET['search'] ?? '';
            $day = $_GET['day'] ?? '';

            $sql = "SELECT 
                        s.id AS student_db_id, s.student_id, s.full_name, s.gender, s.course, s.year_level, s.contact, s.email, s.parent_name, s.parent_contact,
                        c.class_id, c.class_code, c.section, c.subject_code, c.subject_name,
                        sch.schedule_id, sch.day, sch.time, sch.room, sch.instructor
                    FROM student_schedule ss
                    JOIN students s ON ss.student_id = s.id
                    JOIN classes c ON ss.class_id = c.class_id
                    JOIN schedules sch ON ss.schedule_id = sch.schedule_id
                    WHERE 1=1
                    AND c.class_code != 'A28'";

            $params = [];
            if ($subject_code !== 'all') {
                $sql .= " AND c.subject_code = ?";
                $params[] = $subject_code;
            }
            if ($search !== '') {
                $sql .= " AND s.full_name LIKE ?";
                $params[] = "%$search%";
            }
            if ($day !== '' && $day !== 'all') {
                $sql .= " AND sch.day = ?";
                $params[] = $day;
            }
            $sql .= " ORDER BY c.subject_code, s.full_name ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();

            $grouped_data = [];
            foreach ($results as $row) {
                $key = $row['subject_name'];
                if (!isset($grouped_data[$key])) $grouped_data[$key] = [];
                $grouped_data[$key][] = $row;
            }
            echo json_encode($grouped_data);
            break;

        // --- GET CLASSES (for dropdown in add student) ---
        case 'get_classes':
            $stmt = $pdo->query("SELECT c.class_id, c.subject_code, c.subject_name, c.section, c.class_code, 
                                        sch.schedule_id, sch.day, sch.time, sch.instructor
                                 FROM classes c
                                 JOIN schedules sch ON c.class_code = sch.class_code
                                 WHERE c.class_code != 'A28'
                                 ORDER BY c.subject_name, sch.day");
            $classes = $stmt->fetchAll();
            echo json_encode($classes);
            break;

        // --- GET TEACHERS LIST (for dropdown) ---
        case 'get_teachers_list':
            $stmt = $pdo->query("SELECT id, name, subject FROM teachers ORDER BY name");
            echo json_encode($stmt->fetchAll());
            break;

        // --- ADD STUDENT (with class & schedule linking) ---
        case 'add_student':
            // Validate student fields
            $required = ['student_id','full_name','gender','course','year_level','contact','email','parent_name','parent_contact','class_id','schedule_id'];
            foreach ($required as $f) {
                if (empty($_POST[$f])) {
                    echo json_encode(['status' => 'error', 'message' => "Field '$f' is required"]);
                    exit;
                }
            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
                exit;
            }
            
            // Start transaction
            $pdo->beginTransaction();
            try {
                // Insert into students
                $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, gender, course, year_level, contact, email, parent_name, parent_contact) 
                                        VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->execute([
                    $_POST['student_id'], $_POST['full_name'], $_POST['gender'], $_POST['course'],
                    $_POST['year_level'], $_POST['contact'], $_POST['email'],
                    $_POST['parent_name'], $_POST['parent_contact']
                ]);
                $student_id = $pdo->lastInsertId();
                
                // Insert into student_schedule
                $stmt2 = $pdo->prepare("INSERT INTO student_schedule (student_id, schedule_id, class_id) VALUES (?, ?, ?)");
                $stmt2->execute([$student_id, $_POST['schedule_id'], $_POST['class_id']]);
                
                $pdo->commit();
                echo json_encode(['status' => 'success', 'message' => 'Student added successfully']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        // --- GET SINGLE STUDENT PROFILE ---
        case 'get_profile':
            $id = $_GET['id'] ?? 0;
            if (!$id) { echo json_encode(['status'=>'error','message'=>'Invalid ID']); exit; }
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            if ($data) {
                $data['status'] = 'success';
                echo json_encode($data);
            } else {
                echo json_encode(['status'=>'error','message'=>'Student not found']);
            }
            break;

        // --- UPDATE STUDENT PROFILE ---
        case 'update_profile':
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE students SET full_name=?, gender=?, course=?, year_level=?, contact=?, email=?, parent_name=?, parent_contact=? WHERE id=?");
            $stmt->execute([
                $_POST['full_name'], $_POST['gender'], $_POST['course'], $_POST['year_level'],
                $_POST['contact'], $_POST['email'], $_POST['parent_name'], $_POST['parent_contact'], $id
            ]);
            echo json_encode(['status'=>'success']);
            break;

        // --- TEACHER ACTIONS (with age & address) ---
        case 'get_teachers':
            $stmt = $pdo->query("SELECT id, name, subject, email, contact, profile_picture, bio, age, address FROM teachers ORDER BY name");
            echo json_encode($stmt->fetchAll());
            break;

        case 'get_teacher':
            $id = $_GET['id'] ?? 0;
            if (!$id) { echo json_encode(['status'=>'error','message'=>'Invalid teacher ID']); exit; }
            $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
            $stmt->execute([$id]);
            $teacher = $stmt->fetch();
            if ($teacher) echo json_encode(['status'=>'success', 'data'=>$teacher]);
            else echo json_encode(['status'=>'error','message'=>'Teacher not found']);
            break;

        case 'add_teacher':
            if (empty($_POST['name']) || empty($_POST['subject'])) {
                echo json_encode(['status'=>'error','message'=>'Name and Subject are required']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO teachers (name, subject, email, contact, profile_picture, bio, age, address) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $_POST['name'], $_POST['subject'], $_POST['email'] ?? '', $_POST['contact'] ?? '',
                $_POST['profile_picture'] ?? '', $_POST['bio'] ?? '',
                $_POST['age'] ?: null, $_POST['address'] ?? ''
            ]);
            echo json_encode(['status'=>'success','message'=>'Teacher added']);
            break;

        case 'update_teacher':
            $id = $_POST['id'] ?? 0;
            if (!$id) { echo json_encode(['status'=>'error','message'=>'Teacher ID required']); exit; }
            $stmt = $pdo->prepare("UPDATE teachers SET name=?, subject=?, email=?, contact=?, profile_picture=?, bio=?, age=?, address=? WHERE id=?");
            $stmt->execute([
                $_POST['name'], $_POST['subject'], $_POST['email'] ?? '', $_POST['contact'] ?? '',
                $_POST['profile_picture'] ?? '', $_POST['bio'] ?? '',
                $_POST['age'] ?: null, $_POST['address'] ?? '', $id
            ]);
            echo json_encode(['status'=>'success','message'=>'Teacher updated']);
            break;

        case 'delete_teacher':
            $id = $_POST['id'] ?? 0;
            if (!$id) { echo json_encode(['status'=>'error','message'=>'ID required']); exit; }
            $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status'=>'success','message'=>'Teacher deleted']);
            break;

        default:
            echo json_encode(['status'=>'error','message'=>'Invalid Action']);
    }
} catch (Exception $e) {
    error_log("CRUD Error: " . $e->getMessage());
    echo json_encode(['status'=>'error','message'=>'Server error: '.$e->getMessage()]);
}
?>