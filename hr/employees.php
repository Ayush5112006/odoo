<?php
include("../config/db.php");
include("../auth/auth_check.php");
include("../includes/header.php");

// HR can view employees from their company only
$employees = mysqli_query($conn, "
    SELECT u.*, 
    IF(a.status='present','present','absent') as today_status
    FROM users u
    LEFT JOIN attendance a 
    ON u.id = a.user_id AND a.date = CURDATE()
    WHERE u.role = 'employee' AND u.company_name = '{$_SESSION['company_name']}'
    ORDER BY u.created_at DESC
");
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Employee Directory</h3>
        <div>
            <input type="text" id="searchInput" class="form-control" placeholder="Search employees..." style="max-width: 300px;">
        </div>
    </div>

    <div class="row" id="employeeList">
        <?php 
        if(mysqli_num_rows($employees) > 0) {
            while($row = mysqli_fetch_assoc($employees)) {
                $initials = strtoupper(substr($row['first_name'], 0, 1));
                echo "
                <div class='col-md-6 col-lg-4 mb-4 employee-card' data-name='{$row['first_name']} {$row['last_name']}'>
                    <div class='card h-100 shadow-sm'>
                        <div class='card-body'>
                            <div class='d-flex align-items-center mb-3'>
                                <div style='width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #875A7B 0%, #5E3B5A 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem;'>
                                    {$initials}
                                </div>
                                <div class='ms-3'>
                                    <h6 class='mb-0'>{$row['first_name']} {$row['last_name']}</h6>
                                    <small class='text-muted'>" . ucfirst($row['role']) . "</small>
                                </div>
                                <div class='ms-auto'>
                                    <span class='badge bg-" . ($row['today_status'] == 'present' ? 'success' : 'warning') . "'>
                                        " . ucfirst($row['today_status']) . "
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <p class='mb-2'><small><strong>Email:</strong><br><code>{$row['email']}</code></small></p>
                            <p class='mb-2'><small><strong>Status:</strong><br><span class='badge bg-" . ($row['status'] == 'active' ? 'success' : 'danger') . "'>" . ucfirst($row['status']) . "</span></small></p>
                            <p class='mb-2'><small><strong>Member Since:</strong><br>" . date('d M Y', strtotime($row['created_at'])) . "</small></p>
                            <div class='mt-3'>
                                <a href='employee_view.php?id={$row['id']}' class='btn btn-sm btn-primary'>View</a>
                                <a href='employee_view.php?id={$row['id']}#edit' class='btn btn-sm btn-warning'>Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info'>No employees found</div></div>";
        }
        ?>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let searchTerm = this.value.toLowerCase();
    let cards = document.querySelectorAll('.employee-card');
    
    cards.forEach(card => {
        let name = card.getAttribute('data-name').toLowerCase();
        if (name.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

</main>
<?php include("../includes/footer.php"); ?>
