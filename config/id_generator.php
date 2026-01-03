<?php

/**
 * Generate unique employee/HR ID
 * Format: [Company Code (first 2 letters)] + [First 2 letters of first name][First 2 letters of last name] + [Year] + [Serial]
 * Example: OI = Odoo India, JD = John Doe, 2022 = Year, 0001 = Serial
 * Result: OIJODO20220001
 */
function generateEmployeeID($conn, $company_name, $first_name, $last_name, $year = null) {
    if (!$year) {
        $year = date('Y');
    }
    
    // Get first 2 letters of company name (uppercase)
    $company_code = strtoupper(substr($company_name, 0, 2));
    
    // Get first 2 letters of first name (uppercase)
    $fname_code = strtoupper(substr($first_name, 0, 2));
    
    // Get first 2 letters of last name (uppercase)
    $lname_code = strtoupper(substr($last_name, 0, 2));
    
    // Combine parts
    $base_id = $company_code . $fname_code . $lname_code . $year;
    
    // Get the count of employees with same base ID for this year
    $check = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE custom_id LIKE '$base_id%'");
    $result = mysqli_fetch_assoc($check);
    $serial = str_pad($result['cnt'] + 1, 4, '0', STR_PAD_LEFT);
    
    // Generate final ID
    $custom_id = $base_id . $serial;
    
    return $custom_id;
}

?>
