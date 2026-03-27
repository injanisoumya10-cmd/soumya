<?php
require_once 'config.php';

$search = sanitize_input($_GET['search'] ?? '');
$level = sanitize_input($_GET['level'] ?? '');

// Build query
$query = "SELECT * FROM courses WHERE 1=1";
$params = array();
$types = '';

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR description LIKE ?)";
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

if (!empty($level)) {
    $query .= " AND level = ?";
    $params[] = $level;
    $types .= 's';
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Courses - E-Learning Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/courses.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo">📚 ELearning</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="courses.php">Courses</a></li>
                <li><a href="index.php#about">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (is_logged_in()): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php" class="btn-primary">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-primary">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>All Courses</h1>
            <p>Choose from our wide range of professional courses</p>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="section">
        <div class="container">
            <!-- Search and Filter -->
            <div class="search-filter">
                <form method="GET" action="" class="search-form">
                    <input type="text" name="search" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>">
                    <select name="level">
                        <option value="">All Levels</option>
                        <option value="Beginner" <?php echo $level === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                        <option value="Intermediate" <?php echo $level === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                        <option value="Advanced" <?php echo $level === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                    <button type="submit" class="btn-search">Search</button>
                </form>
            </div>

            <!-- Courses Grid -->
            <div class="courses-grid">
                <?php
                if ($result->num_rows > 0) {
                    while ($course = $result->fetch_assoc()) {
                        echo '
                        <div class="course-card">
                            <div class="course-image">📖</div>
                            <div class="course-content">
                                <span class="course-level">' . htmlspecialchars($course['level']) . '</span>
                                <h3>' . htmlspecialchars($course['title']) . '</h3>
                                <p>Instructor: ' . htmlspecialchars($course['instructor_name']) . '</p>
                                <p>' . htmlspecialchars(substr($course['description'], 0, 100)) . '...</p>
                                <div class="course-meta">
                                    <span class="course-duration">⏱️ ' . htmlspecialchars($course['duration']) . '</span>
                                    <span class="course-price">₹' . htmlspecialchars($course['price']) . '</span>
                                </div>
                                <button class="btn-primary enroll-btn" data-course-id="' . $course['id'] . '">Enroll Now</button>
                            </div>
                        </div>
                        ';
                    }
                } else {
                    echo '<div class="no-courses">No courses found matching your search.</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2026 E-Learning Portal. All rights reserved.</p>
            <p>📞 Phone: <a href="tel:9964169781" style="color: white;">9964169781</a> | 📧 Email: <a href="mailto:prajwalbahaddurbandi@gmail.com" style="color: white;">prajwalbahaddurbandi@gmail.com</a></p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
