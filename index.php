<?php
include 'config.php';

// SEO Meta Tags
$seo_title = "CS Test Series Schedule | CS Executive & Professional Test Series";
$seo_description = "Flexible CS Test Series Schedule for December 2025 Exams. Download CS Executive & CS Professional Test Series Schedules starting from August 2025.";
$seo_keywords = "cs test series, cs executive test series, cs professional test series, cs test series schedule, cs exam preparation";

try {
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching courses: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seo_keywords); ?>">
    <meta name="title" content="<?php echo htmlspecialchars($seo_title); ?>">
    <meta name="robots" content="index, follow">
    
    <title><?php echo htmlspecialchars($seo_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #e63e58;
            --primary-light: #f7d9e1;
            --primary-dark: #c5354a;
            --secondary: #4a90e2;
            --text-primary: #333;
            --text-secondary: #666;
            --bg-light: #f8f9fa;
            --border-color: #e9ecef;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
        }
        
        /* Header Styles */
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        /* Section Styles */
        .section-title {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1rem 0;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .section-title h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        /* Plan Card Styles */
        .plan-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            height: 100%;
        }
        
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .plan-header {
            padding: 1.2rem 1.5rem;
            background: linear-gradient(135deg, #f7d9e1 0%, #f5e0e4 100%);
            border-bottom: 1px solid var(--border-color);
        }
        
        .plan-header i {
            color: var(--primary);
            font-size: 1.5rem;
            margin-right: 10px;
        }
        
        .plan-body {
            padding: 1.5rem;
        }
        
        .plan-name {
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
        }
        
        .plan-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0 0 1rem 0;
        }
        
        .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-buy {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-download {
            background: linear-gradient(135deg, #4a90e2 0%, #357ab8 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 62, 88, 0.3);
        }
        
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .row {
                flex-wrap: wrap;
            }
            
            .col-md-3 {
                flex: 1 1 100%;
                max-width: 100%;
                margin-bottom: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 0.8rem 0;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .section-title {
                padding: 0.8rem 0;
            }
            
            .section-title h2 {
                font-size: 1.3rem;
            }
            
            .plan-card {
                margin-bottom: 1rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn-buy, .btn-download {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .plan-header {
                padding: 1rem;
            }
            
            .plan-body {
                padding: 1rem;
            }
        }
        
        /* SEO Optimization */
        .seo-meta {
            display: none;
        }
    </style>
</head>
<body>
    <!-- SEO Meta -->
    <div class="seo-meta">
        <meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>">
        <meta name="keywords" content="<?php echo htmlspecialchars($seo_keywords); ?>">
        <meta name="author" content="CS Test Series">
        <meta name="publisher" content="CS Test Series">
    </div>

    <!-- Header -->
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-graduation-cap"></i> CS Test Series Schedule</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-4">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Course Sections -->
        <?php foreach($courses as $course): ?>
            <div class="mb-5">
                <div class="section-title">
                    <h2>
                        <i class="fas fa-calendar-alt"></i> 
                        <?php echo htmlspecialchars($course['course_name']); ?> Test Series Schedule
                    </h2>
                </div>
                
                <p class="text-muted mb-4" style="font-size: 0.9rem;">
                    This <?php echo htmlspecialchars($course['course_name']); ?> But you may complete the tests at your convenience but atlest one week before exams, regardless of the recommended date & sequence.
                </p>
                
                <div class="row g-4">
                    <?php
                    try {
                        $stmt = $pdo->prepare("SELECT * FROM plans WHERE course_id = ? ORDER BY id ASC");
                        $stmt->execute([$course['id']]);
                        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch(PDOException $e) {
                        echo "<div class='alert alert-danger'>Error fetching plans</div>";
                        continue;
                    }
                    
                    foreach($plans as $plan): ?>
                        <div class="col-md-3">
                            <div class="plan-card">
                                <div class="plan-header d-flex align-items-center">
                                    <i class="fas fa-file-pdf"></i>
                                    <h5 class="mb-0 ms-2" style="font-size: 1rem; font-weight: 600;">
                                        <?php echo htmlspecialchars($plan['plan_name']); ?>
                                    </h5>
                                </div>
                                
                                <div class="plan-body">
                                    <p class="plan-description">
                                        Starting from <?php echo date('dS F Y', strtotime($plan['created_at'])); ?>
                                    </p>
                                    
                                    <div class="btn-group">
                                        <button class="btn btn-buy">Buy Now</button>
                                        <?php if($plan['pdf_file']): ?>
                                            <a href="uploads/<?php echo htmlspecialchars($plan['pdf_file']); ?>" 
                                               class="btn btn-download" 
                                               download>
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fas fa-file-pdf"></i> No PDF
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-copyright"></i> 2025 CS Test Series Schedule. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-info-circle"></i> Flexible scheduling available for all test series
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
