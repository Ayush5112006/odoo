<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");

$user_id = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// Get user details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    
    // Handle password change
    $password_updated = false;
    if (!empty($_POST['new_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['profile_error'] = "Current password is incorrect";
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['profile_error'] = "New passwords do not match";
        } elseif (strlen($new_password) < 6) {
            $_SESSION['profile_error'] = "New password must be at least 6 characters";
        } else {
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $password_updated = true;
        }
    }
    
    if (!isset($_SESSION['profile_error'])) {
        $update_query = "UPDATE users SET first_name='$first_name', last_name='$last_name', phone='$phone'";
        
        if ($password_updated) {
            $update_query .= ", password='$new_password_hash'";
        }
        
        $update_query .= " WHERE id='$user_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['first_name'] = $first_name;
            $_SESSION['profile_success'] = "Profile updated successfully!";
            
            // Refresh user data
            $user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
            $user = mysqli_fetch_assoc($user_query);
            
            header("Location: profile.php?action=view");
            exit;
        } else {
            $_SESSION['profile_error'] = "Failed to update profile";
        }
    }
}

$eq = mysqli_query($conn, "SELECT * FROM employee_details WHERE user_id='{$_SESSION['user_id']}'");
$emp = mysqli_fetch_assoc($eq);
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-10">
        <?php if (isset($_SESSION['profile_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['profile_success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['profile_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['profile_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['profile_error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['profile_error']); ?>
        <?php endif; ?>

        <div class="row">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div id="profile-photo-container" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #875A7B 0%, #5E3B5A 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: bold; overflow: hidden; position: relative; cursor: pointer; border: 3px solid #875A7B;" onclick="document.getElementById('photo-input').click();">
                    <?php if (isset($user['profile_photo']) && !empty($user['profile_photo']) && file_exists($user['profile_photo'])): ?>
                        <img id="profile-img" src="<?= htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <span id="initials-text"><?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'] ?? '', 0, 1)); ?></span>
                    <?php endif; ?>
                    <div style="position: absolute; background: rgba(0,0,0,0.5); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;" class="photo-overlay">
                        <i class="bi bi-camera" style="font-size: 2rem; color: white;"></i>
                    </div>
                </div>
                <input type="file" id="photo-input" accept="image/*" style="display: none;">
                <p style="font-size: 0.85rem; color: #666; margin-bottom: 1rem;">Click photo to change</p>
                <h5><?= $user['first_name'] . ' ' . ($user['last_name'] ?? ''); ?></h5>
                <p class="text-muted"><?= ucfirst($user['role']); ?></p>
                <hr>
                <p><strong>Employee ID:</strong><br><span style="font-weight: bold; color: #875A7B; font-size: 1.1rem;"><?= $user['custom_id'] ?? 'Not assigned'; ?></span></p>
                <hr>
                <p><strong>Email:</strong><br><?= $user['email']; ?></p>
                <p><strong>Phone:</strong><br><?= $user['phone'] ?? 'Not provided'; ?></p>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5>Employee Information</h5>
                <table class="table mt-3">
                    <tbody>
                        <tr>
                            <td><strong>Employee ID</strong></td>
                            <td><span style="font-weight: bold; color: #875A7B; font-size: 1rem;"><?= $user['custom_id'] ?? 'Not assigned'; ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Company</strong></td>
                            <td><?= $user['company_name']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Department</strong></td>
                            <td><?= $emp['department'] ?? 'Not assigned'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Designation</strong></td>
                            <td><?= $emp['designation'] ?? 'Not assigned'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Manager</strong></td>
                            <td><?= $emp['manager'] ?? 'Not assigned'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Location</strong></td>
                            <td><?= $emp['location'] ?? 'Not assigned'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date of Joining</strong></td>
                            <td><?= $emp['join_date'] ?? 'Not assigned'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Account Status</strong></td>
                            <td><span class='badge bg-success'><?= ucfirst($user['status']); ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Email Verified</strong></td>
                            <td><?= $user['email_verified'] == 1 ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No</span>'; ?></td>
                        </tr>
                    </tbody>
                </table>

                <?php if ($action === 'view'): ?>
                    <div class="mt-4">
                        <a href="profile.php?action=edit" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Profile
                        </a>
                        <a href="profile.php?action=change-password" class="btn btn-secondary">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                    </div>
                <?php else: ?>
                    <form method="POST" action="profile.php?action=edit">
                        <hr>
                        <h6 class="mb-3"><i class="bi bi-person"></i> Edit Your Information</h6>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>First Name *</strong></label>
                            <input type="text" name="first_name" class="form-control" 
                                   value="<?= htmlspecialchars($user['first_name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Last Name</strong></label>
                            <input type="text" name="last_name" class="form-control" 
                                   value="<?= htmlspecialchars($user['last_name'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Phone</strong></label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?= htmlspecialchars($user['phone'] ?? ''); ?>" 
                                   pattern="[0-9]{10}" placeholder="10 digit phone number">
                        </div>

                        <?php if ($action === 'change-password'): ?>
                            <hr>
                            <h6 class="mb-3"><i class="bi bi-lock"></i> Change Password</h6>
                            
                            <div class="mb-3">
                                <label class="form-label"><strong>Current Password *</strong></label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>New Password *</strong></label>
                                <input type="password" name="new_password" class="form-control" required 
                                       minlength="6" placeholder="Minimum 6 characters">
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Confirm New Password *</strong></label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        <?php else: ?>
                            <hr>
                            <h6 class="mb-3"><i class="bi bi-lock"></i> Change Password (Optional)</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" 
                                       placeholder="Enter your current password">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" 
                                       placeholder="Enter new password (leave blank to keep current)">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" 
                                       placeholder="Confirm new password">
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                            <a href="profile.php?action=view" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>

<script>
// Profile photo upload handling
document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = this.files[0];
    if (!file) return;
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        alert('Only JPG, JPEG, PNG, and GIF files are allowed');
        return;
    }
    
    // Upload file
    const formData = new FormData();
    formData.append('profile_photo', file);
    
    fetch('upload_profile_photo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the profile photo in the UI
            const container = document.getElementById('profile-photo-container');
            const img = document.getElementById('profile-img');
            const initials = document.getElementById('initials-text');
            
            if (img) {
                img.src = data.photo_path + '?t=' + new Date().getTime();
            } else {
                container.innerHTML = '<img id="profile-img" src="' + data.photo_path + '?t=' + new Date().getTime() + '" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;"><div style="position: absolute; background: rgba(0,0,0,0.5); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;" class="photo-overlay"><i class="bi bi-camera" style="font-size: 2rem; color: white;"></i></div>';
                if (initials) initials.remove();
            }
            
            alert('Profile photo updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to upload photo');
    });
});

// Show camera icon on hover
document.getElementById('profile-photo-container').addEventListener('mouseenter', function() {
    const overlay = this.querySelector('.photo-overlay');
    if (overlay) overlay.style.opacity = '1';
});

document.getElementById('profile-photo-container').addEventListener('mouseleave', function() {
    const overlay = this.querySelector('.photo-overlay');
    if (overlay) overlay.style.opacity = '0';
});
</script>
