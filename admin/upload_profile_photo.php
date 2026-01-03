<?php
include("../config/db.php");
include("../auth/auth_check.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$upload_dir = "../uploads/profile_photos/";

// Create uploads directory if it doesn't exist
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Check if file was uploaded
if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['profile_photo'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Validate file type
$allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($file_ext, $allowed_exts)) {
    echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG, and GIF files are allowed']);
    exit;
}

// Validate file size (max 5MB)
if ($file_size > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
    exit;
}

// Generate unique filename
$new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
$upload_path = $upload_dir . $new_filename;

// Delete old profile photo if exists
$user_result = mysqli_query($conn, "SELECT profile_photo FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user_result);
if ($user_data && $user_data['profile_photo'] && file_exists($user_data['profile_photo'])) {
    unlink($user_data['profile_photo']);
}

// Move uploaded file
if (move_uploaded_file($file_tmp, $upload_path)) {
    // Update database
    $update = mysqli_query($conn, "UPDATE users SET profile_photo='$upload_path' WHERE id='$user_id'");
    
    if ($update) {
        $_SESSION['profile_photo'] = $upload_path;
        echo json_encode(['success' => true, 'message' => 'Profile photo uploaded successfully', 'photo_path' => $upload_path]);
        exit;
    } else {
        unlink($upload_path);
        echo json_encode(['success' => false, 'message' => 'Failed to save to database']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    exit;
}
?>
