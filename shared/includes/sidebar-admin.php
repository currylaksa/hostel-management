<?php
// Sidebar for admin dashboard
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>MMU Hostel</h2>
        <p>Admin Portal</p>
    </div>
    <div class="sidebar-menu">
        <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <div class="menu-category">Student Management</div>
        <a href="students.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-graduate"></i> Students
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-clipboard-list"></i> Applications
        </a>
        
        <div class="menu-category">Accommodation</div>
        <a href="#" class="menu-item">
            <i class="fas fa-building"></i> Hostel Blocks
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-door-open"></i> Rooms
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-bed"></i> Room Allocation
        </a>
        
        <div class="menu-category">Operations</div>
        <a href="#" class="menu-item">
            <i class="fas fa-file-invoice-dollar"></i> Billing
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-hand-holding-usd"></i> Payments
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-tools"></i> Maintenance
        </a>
        
        <div class="menu-category">Communication</div>
        <a href="#" class="menu-item">
            <i class="fas fa-bullhorn"></i> Announcements
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-envelope"></i> Messages
        </a>
        
        <div class="menu-category">Admin</div>
        <a href="profile.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-circle"></i> My Profile
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-user-shield"></i> Staff
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-cog"></i> Settings
        </a>
    </div>
</div>