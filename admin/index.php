<?php
session_start();
include '../config.php';

// Get counts
try {
    $courseCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $planCount = $pdo->query("SELECT COUNT(*) FROM plans")->fetchColumn();
} catch(PDOException $e) {
    $error = "Error fetching counts: " . $e->getMessage();
}

// Check for messages
$success_message = $_SESSION['success'] ?? '';
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CS Test Series Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --header-bg: #3498db;
            --card-bg: #ffffff;
            --border: #e9ecef;
            --primary: #e63e58;
            --primary-light: #f7d9e1;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #1a2530 100%);
            color: white;
            min-height: 100vh;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--sidebar-hover);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            padding: 20px;
        }
        
        .stat-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            background: linear-gradient(135deg, #f7d9e1 0%, #f5e0e4 100%);
            border: 1px solid var(--border);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
        }
        
        .dropdown-content {
            display: none;
            padding-left: 2rem;
        }
        
        .dropdown-content.show {
            display: block;
        }
        
        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
        }
        
        .form-card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            background: white;
        }
        
        .table-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            background: white;
        }
        
        .upload-progress {
            display: none;
            margin-top: 10px;
        }
        
        .progress-bar {
            transition: width 0.3s ease;
        }
        
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .sidebar .nav-link {
                display: block;
                padding: 10px 15px;
            }
            
            .sidebar .nav-link i {
                margin-right: 8px;
            }
            
            .main-content {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Toast Notifications -->
    <div class="toast-container">
        <?php if($success_message): ?>
            <div class="toast show align-items-center text-white bg-success border-0" role="alert" id="successToast">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if($error_message): ?>
            <div class="toast show align-items-center text-white bg-danger border-0" role="alert" id="errorToast">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4 sidebar p-4">
                <div class="text-center mb-4">
                    <h3><i class="fas fa-cogs"></i> Admin Panel</h3>
                </div>
                
                <div class="stats mb-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-primary p-3 rounded text-center">
                                <div class="stat-icon"><i class="fas fa-book"></i></div>
                                <div class="fs-4 fw-bold"><?php echo $courseCount ?? 0; ?></div>
                                <div class="small">Courses</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success p-3 rounded text-center">
                                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                                <div class="fs-4 fw-bold"><?php echo $planCount ?? 0; ?></div>
                                <div class="small">Plans</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link active" href="#" onclick="showSection('dashboard')">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    
                    <a class="nav-link" href="#" onclick="toggleDropdown('courseDropdown')">
                        <i class="fas fa-book"></i> Courses <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div id="courseDropdown" class="dropdown-content">
                        <a class="nav-link" href="#" onclick="showSection('addCourse')">
                            <i class="fas fa-plus"></i> Add Course
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('courseList')">
                            <i class="fas fa-list"></i> Course List
                        </a>
                    </div>
                    
                    <a class="nav-link" href="#" onclick="toggleDropdown('planDropdown')">
                        <i class="fas fa-file-alt"></i> Plans <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div id="planDropdown" class="dropdown-content">
                        <a class="nav-link" href="#" onclick="showSection('addPlan')">
                            <i class="fas fa-plus"></i> Add Plan
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('planList')">
                            <i class="fas fa-list"></i> Plan List
                        </a>
                    </div>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9 col-md-8 main-content">
                <!-- Dashboard Section -->
                <div id="dashboard" class="content-section active">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                    <div class="row mt-4">
                        <div class="col-md-4 mb-4">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <div class="stat-icon text-primary mb-3">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <h4><?php echo $courseCount ?? 0; ?></h4>
                                    <p class="text-muted">Total Courses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <div class="stat-icon text-success mb-3">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <h4><?php echo $planCount ?? 0; ?></h4>
                                    <p class="text-muted">Total Plans</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <div class="stat-icon text-info mb-3">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <h4>
                                        <?php 
                                        try {
                                            $pdfCount = $pdo->query("SELECT COUNT(*) FROM plans WHERE pdf_file IS NOT NULL")->fetchColumn();
                                            echo $pdfCount ?? 0;
                                        } catch(PDOException $e) {
                                            echo "0";
                                        }
                                        ?>
                                    </h4>
                                    <p class="text-muted">PDF Files</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add Course Section -->
                <div id="addCourse" class="content-section">
                    <h2><i class="fas fa-plus-circle"></i> Add New Course</h2>
                    <div class="card form-card mt-4">
                        <div class="card-body">
                            <form method="POST" action="actions.php" id="addCourseForm">
                                <input type="hidden" name="action" value="add_course">
                                <div class="mb-3">
                                    <label class="form-label">Course Name</label>
                                    <input type="text" class="form-control" name="course_name" required>
                                </div>
                                <button type="submit" class="btn btn-primary" id="addCourseBtn">
                                    <i class="fas fa-save"></i> Add Course
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Course List Section -->
                <div id="courseList" class="content-section">
                    <h2><i class="fas fa-list"></i> Course List</h2>
                    <div class="card table-card mt-4">
                        <div class="card-body">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC");
                                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch(PDOException $e) {
                                echo "<div class='alert alert-danger'>Error fetching courses</div>";
                                $courses = [];
                            }
                            ?>
                            
                            <?php if(empty($courses)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-info-circle text-muted fa-2x"></i>
                                    <p class="text-muted mt-2">No courses found</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Course Name</th>
                                                <th>Created Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($courses as $course): ?>
                                                <tr>
                                                    <td><?php echo $course['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($course['created_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" 
                                                                onclick="editCourse(<?php echo $course['id']; ?>, '<?php echo addslashes($course['course_name']); ?>')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="deleteCourse(<?php echo $course['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Add Plan Section -->
                <div id="addPlan" class="content-section">
                    <h2><i class="fas fa-plus-circle"></i> Add New Plan</h2>
                    <div class="card form-card mt-4">
                        <div class="card-body">
                            <form method="POST" action="actions.php" enctype="multipart/form-data" id="addPlanForm">
                                <input type="hidden" name="action" value="add_plan">
                                <div class="mb-3">
                                    <label class="form-label">Select Course</label>
                                    <select class="form-select" name="course_id" required>
                                        <option value="">Choose a course</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC");
                                            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($courses as $course) {
                                                echo "<option value='{$course['id']}'>{$course['course_name']}</option>";
                                            }
                                        } catch(PDOException $e) {
                                            echo "<option>Error loading courses</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Plan Name</label>
                                    <input type="text" class="form-control" name="plan_name" required>
                                    <div class="form-text">Note: Same plan names can be used for different courses</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload PDF (Optional)</label>
                                    <input type="file" class="form-control" name="pdf_file" id="pdfFile" accept=".pdf">
                                    <div class="upload-progress mt-2">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted">Uploading... <span id="progressPercent">0%</span></small>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" id="addPlanBtn">
                                    <i class="fas fa-save"></i> Add Plan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Plan List Section -->
                <div id="planList" class="content-section">
                    <h2><i class="fas fa-list"></i> Plan List</h2>
                    <div class="card table-card mt-4">
                        <div class="card-body">
                            <?php
                            try {
                                $stmt = $pdo->query("
                                    SELECT p.*, c.course_name 
                                    FROM plans p 
                                    JOIN courses c ON p.course_id = c.id 
                                    ORDER BY p.id ASC
                                ");
                                $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch(PDOException $e) {
                                echo "<div class='alert alert-danger'>Error fetching plans</div>";
                                $plans = [];
                            }
                            ?>
                            
                            <?php if(empty($plans)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-info-circle text-muted fa-2x"></i>
                                    <p class="text-muted mt-2">No plans found</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Course</th>
                                                <th>Plan Name</th>
                                                <th>PDF File</th>
                                                <th>Created Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($plans as $plan): ?>
                                                <tr>
                                                    <td><?php echo $plan['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($plan['course_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($plan['plan_name']); ?></td>
                                                    <td>
                                                        <?php if($plan['pdf_file']): ?>
                                                            <a href="../uploads/<?php echo htmlspecialchars($plan['pdf_file']); ?>" 
                                                               target="_blank" class="text-primary">
                                                                <i class="fas fa-file-pdf"></i> View PDF
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No PDF</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($plan['created_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" 
                                                                onclick="editPlan(<?php echo $plan['id']; ?>, '<?php echo addslashes($plan['plan_name']); ?>', <?php echo $plan['course_id']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="deletePlan(<?php echo $plan['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="actions.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_course">
                        <input type="hidden" name="course_id" id="edit_course_id">
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" name="course_name" id="edit_course_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Plan Modal -->
    <div class="modal fade" id="editPlanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="actions.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_plan">
                        <input type="hidden" name="plan_id" id="edit_plan_id">
                        <div class="mb-3">
                            <label class="form-label">Select Course</label>
                            <select class="form-select" name="course_id" id="edit_plan_course" required>
                                <?php
                                try {
                                    $stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC");
                                    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($courses as $course) {
                                        echo "<option value='{$course['id']}'>{$course['course_name']}</option>";
                                    }
                                } catch(PDOException $e) {
                                    echo "<option>Error loading courses</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Plan Name</label>
                            <input type="text" class="form-control" name="plan_name" id="edit_plan_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload New PDF (Optional)</label>
                            <input type="file" class="form-control" name="pdf_file" accept=".pdf">
                            <small class="text-muted">Leave empty to keep current PDF</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide toasts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var toasts = document.querySelectorAll('.toast');
                toasts.forEach(function(toast) {
                    var bsToast = new bootstrap.Toast(toast);
                    bsToast.hide();
                });
            }, 5000);
        });
        
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Update active nav link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            event.target.closest('.nav-link').classList.add('active');
        }
        
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('show');
        }
        
        function editCourse(id, name) {
            document.getElementById('edit_course_id').value = id;
            document.getElementById('edit_course_name').value = name;
            new bootstrap.Modal(document.getElementById('editCourseModal')).show();
        }
        
        function editPlan(id, name, courseId) {
            document.getElementById('edit_plan_id').value = id;
            document.getElementById('edit_plan_name').value = name;
            document.getElementById('edit_plan_course').value = courseId;
            new bootstrap.Modal(document.getElementById('editPlanModal')).show();
        }
        
        function deleteCourse(id) {
            if(confirm('Are you sure you want to delete this course? All associated plans will also be deleted.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'actions.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_course';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'course_id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deletePlan(id) {
            if(confirm('Are you sure you want to delete this plan?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'actions.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_plan';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'plan_id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Progress bar for PDF upload
        document.getElementById('pdfFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const uploadProgress = document.querySelector('.upload-progress');
                uploadProgress.style.display = 'block';
                
                // Simulate progress (in real implementation, this would be actual upload progress)
                let progress = 0;
                const interval = setInterval(() => {
                    progress += 5;
                    if (progress >= 100) {
                        clearInterval(interval);
                    }
                    document.querySelector('.progress-bar').style.width = progress + '%';
                    document.getElementById('progressPercent').textContent = progress + '%';
                }, 100);
            }
        });
        
        // Form submission with loading state
        document.getElementById('addPlanForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('addPlanBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Plan...';
            submitBtn.disabled = true;
        });
        
        document.getElementById('addCourseForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('addCourseBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Course...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>