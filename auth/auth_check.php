<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// Set default values if not in session
if (!isset($_SESSION['company_name'])) {
    $_SESSION['company_name'] = '';
}
if (!isset($_SESSION['first_name'])) {
    $_SESSION['first_name'] = '';
}
if (!isset($_SESSION['logo'])) {
    $_SESSION['logo'] = '';
}

// Load profile photo and company logo from database if not in session
if (!isset($_SESSION['profile_photo']) || empty($_SESSION['profile_photo']) || !isset($_SESSION['company_logo']) || empty($_SESSION['company_logo'])) {
    // Use absolute path to avoid relative path issues
    $db_path = dirname(__DIR__) . '/config/db.php';
    if (file_exists($db_path)) {
        include($db_path);
    }
    if (isset($conn)) {
        $result = mysqli_query($conn, "SELECT profile_photo, company_logo FROM users WHERE id='{$_SESSION['user_id']}'");
        if ($result) {
            $user_data = mysqli_fetch_assoc($result);
            $_SESSION['profile_photo'] = $user_data['profile_photo'] ?? null;
            $_SESSION['company_logo'] = $user_data['company_logo'] ?? null;
            
            // If user doesn't have a company logo, try to get it from company admin
            if (empty($_SESSION['company_logo'])) {
                $company_logo_query = mysqli_query($conn, "SELECT company_logo FROM users WHERE company_name='{$_SESSION['company_name']}' AND role='admin' AND company_logo IS NOT NULL AND company_logo != '' LIMIT 1");
                if ($company_logo_query && mysqli_num_rows($company_logo_query) > 0) {
                    $logo_data = mysqli_fetch_assoc($company_logo_query);
                    $_SESSION['company_logo'] = $logo_data['company_logo'];
                    
                    // Update user's company_logo field for future use
                    mysqli_query($conn, "UPDATE users SET company_logo='{$logo_data['company_logo']}' WHERE id='{$_SESSION['user_id']}'");
                }
            }
        }
    }
}
?>
