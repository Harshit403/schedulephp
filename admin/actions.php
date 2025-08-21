<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'add_course':
            $course_name = trim($_POST['course_name'] ?? '');
            if (empty($course_name)) {
                throw new Exception('Course name is required');
            }
            
            // Check if course already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE course_name = ?");
            $stmt->execute([$course_name]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Course with this name already exists');
            }
            
            $stmt = $pdo->prepare("INSERT INTO courses (course_name) VALUES (?)");
            $stmt->execute([$course_name]);
            $_SESSION['success'] = 'Course added successfully!';
            break;
            
        case 'edit_course':
            $course_id = $_POST['course_id'] ?? 0;
            $course_name = trim($_POST['course_name'] ?? '');
            
            if (empty($course_name) || empty($course_id)) {
                throw new Exception('Invalid input');
            }
            
            // Check if another course with same name exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE course_name = ? AND id != ?");
            $stmt->execute([$course_name, $course_id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Course with this name already exists');
            }
            
            $stmt = $pdo->prepare("UPDATE courses SET course_name = ? WHERE id = ?");
            $stmt->execute([$course_name, $course_id]);
            $_SESSION['success'] = 'Course updated successfully!';
            break;
            
        case 'delete_course':
            $course_id = $_POST['course_id'] ?? 0;
            
            if (empty($course_id)) {
                throw new Exception('Invalid course ID');
            }
            
            $pdo->beginTransaction();
            try {
                // Delete associated plans first
                $stmt = $pdo->prepare("DELETE FROM plans WHERE course_id = ?");
                $stmt->execute([$course_id]);
                
                // Delete course
                $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
                $stmt->execute([$course_id]);
                
                $pdo->commit();
                $_SESSION['success'] = 'Course and associated plans deleted successfully!';
            } catch(Exception $e) {
                $pdo->rollback();
                throw new Exception('Error deleting course: ' . $e->getMessage());
            }
            break;
            
        case 'add_plan':
            $course_id = $_POST['course_id'] ?? 0;
            $plan_name = trim($_POST['plan_name'] ?? '');
            
            if (empty($course_id) || empty($plan_name)) {
                throw new Exception('Course and plan name are required');
            }
            
            // Check if plan already exists for this course
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM plans WHERE course_id = ? AND plan_name = ?");
            $stmt->execute([$course_id, $plan_name]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Plan with this name already exists for the selected course');
            }
            
            $pdf_file = null;
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                $pdf_file = handleFileUpload($_FILES['pdf_file']);
            }
            
            $stmt = $pdo->prepare("INSERT INTO plans (course_id, plan_name, pdf_file) VALUES (?, ?, ?)");
            $stmt->execute([$course_id, $plan_name, $pdf_file]);
            $_SESSION['success'] = 'Plan added successfully!';
            break;
            
        case 'edit_plan':
            $plan_id = $_POST['plan_id'] ?? 0;
            $course_id = $_POST['course_id'] ?? 0;
            $plan_name = trim($_POST['plan_name'] ?? '');
            
            if (empty($plan_id) || empty($course_id) || empty($plan_name)) {
                throw new Exception('Invalid input');
            }
            
            // Check if another plan with same name exists for this course
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM plans WHERE course_id = ? AND plan_name = ? AND id != ?");
            $stmt->execute([$course_id, $plan_name, $plan_id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Plan with this name already exists for the selected course');
            }
            
            // Get current PDF file
            $stmt = $pdo->prepare("SELECT pdf_file FROM plans WHERE id = ?");
            $stmt->execute([$plan_id]);
            $current_pdf = $stmt->fetchColumn();
            
            $pdf_file = $current_pdf;
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                // Delete old file if exists
                if ($current_pdf && file_exists("../uploads/$current_pdf")) {
                    unlink("../uploads/$current_pdf");
                }
                $pdf_file = handleFileUpload($_FILES['pdf_file']);
            }
            
            $stmt = $pdo->prepare("UPDATE plans SET course_id = ?, plan_name = ?, pdf_file = ? WHERE id = ?");
            $stmt->execute([$course_id, $plan_name, $pdf_file, $plan_id]);
            $_SESSION['success'] = 'Plan updated successfully!';
            break;
            
        case 'delete_plan':
            $plan_id = $_POST['plan_id'] ?? 0;
            
            if (empty($plan_id)) {
                throw new Exception('Invalid plan ID');
            }
            
            // Get PDF file to delete
            $stmt = $pdo->prepare("SELECT pdf_file FROM plans WHERE id = ?");
            $stmt->execute([$plan_id]);
            $pdf_file = $stmt->fetchColumn();
            
            // Delete plan
            $stmt = $pdo->prepare("DELETE FROM plans WHERE id = ?");
            $stmt->execute([$plan_id]);
            
            // Delete PDF file if exists
            if ($pdf_file && file_exists("../uploads/$pdf_file")) {
                unlink("../uploads/$pdf_file");
            }
            
            $_SESSION['success'] = 'Plan deleted successfully!';
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch(Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
exit;

function handleFileUpload($file) {
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['pdf'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        throw new Exception('Only PDF files are allowed');
    }
    
    if ($file['size'] > 10000000) { // 10MB limit
        throw new Exception('File size must be less than 10MB');
    }
    
    $filename = uniqid() . '_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload file. Please try again.');
    }
    
    return $filename;
}
?>