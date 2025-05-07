<?php
// Sidebar for student dashboard
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>MMU Hostel</h2>
        <p>Student Portal</p>
    </div>
    <div class="sidebar-menu">
        <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="room_details.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'room_details.php' ? 'active' : ''; ?>">
            <i class="fas fa-bed"></i> Room Details
        </a>
        <a href="billing.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'billing.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice"></i> Billing
        </a>
        <a href="maintenance.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'maintenance.php' ? 'active' : ''; ?>">
            <i class="fas fa-wrench"></i> Maintenance
        </a>
        <a href="announcements.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active' : ''; ?>">
            <i class="fas fa-bullhorn"></i> Announcements
        </a>
        <a href="profile.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-circle"></i> My Profile
        </a>
        <a href="settings.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
    </div>
</div>