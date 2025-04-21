<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "student") {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMU Hostel Management - Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Dashboard Specific Styles */
        body {
            display: block;
            height: auto;
            background: #f5f5f5;
            color: #333;
            padding: 0;
            margin: 0;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #6e8efb, #a777e3);
            color: white;
            padding-top: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }

        .menu-item i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid white;
        }

        /* Main Content Styles */
        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e1e1e1;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .user-name {
            font-weight: 500;
            margin-right: 15px;
        }

        .logout-btn {
            color: #777;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logout-btn i {
            margin-right: 5px;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
        }

        .card-content {
            color: #555;
        }

        .announcement-list, .maintenance-list {
            list-style: none;
            padding: 0;
        }

        .announcement-list li, .maintenance-list li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .announcement-list li:last-child, .maintenance-list li:last-child {
            border-bottom: none;
        }

        .announcement-date, .maintenance-status {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }

        .maintenance-status.pending {
            color: #f39c12;
        }

        .maintenance-status.resolved {
            color: #2ecc71;
        }

        .maintenance-status.in-progress {
            color: #3498db;
        }

        .bill-amount {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 15px 0;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .action-btn {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 14px;
        }

        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>MMU Hostel</h2>
                <p>Student Portal</p>
            </div>
            <div class="sidebar-menu">
                <a href="#" class="menu-item active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-bed"></i> Room Details
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-file-invoice"></i> Billing
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-wrench"></i> Maintenance
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-bullhorn"></i> Announcements
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Student Dashboard</h1>
                <div class="user-info">
                    <img src="https://i.pravatar.cc/150?img=11" alt="Student Profile">
                    <span class="user-name"><?php echo $_SESSION["user"]; ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <!-- Room Information -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <h2 class="card-title">Room Information</h2>
                    </div>
                    <div class="card-content">
                        <p><strong>Room Number:</strong> B-203</p>
                        <p><strong>Hostel Block:</strong> Cyber Heights Villa Block B</p>
                        <p><strong>Room Type:</strong> Twin Sharing</p>
                        <p><strong>Roommate:</strong> Ahmad Bin Abdullah</p>
                        <p><strong>Check-in Date:</strong> January 15, 2025</p>
                        <p><strong>Contract End:</strong> December 15, 2025</p>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h2 class="card-title">Billing & Payments</h2>
                    </div>
                    <div class="card-content">
                        <p><strong>Current Status:</strong> <span style="color: green;">Paid</span></p>
                        <p><strong>Next Payment:</strong> May 1, 2025</p>
                        <div class="bill-amount">RM 850.00</div>
                        <div class="quick-actions">
                            <button class="action-btn">Pay Bill</button>
                            <button class="action-btn">Payment History</button>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Requests -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-wrench"></i>
                        </div>
                        <h2 class="card-title">Maintenance Requests</h2>
                    </div>
                    <div class="card-content">
                        <ul class="maintenance-list">
                            <li>
                                <div>Air conditioning repair</div>
                                <div class="maintenance-status in-progress">In Progress</div>
                            </li>
                            <li>
                                <div>Bathroom sink leaking</div>
                                <div class="maintenance-status resolved">Resolved</div>
                            </li>
                        </ul>
                        <div class="quick-actions" style="margin-top: 10px;">
                            <button class="action-btn">New Request</button>
                            <button class="action-btn">View All</button>
                        </div>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h2 class="card-title">Announcements</h2>
                    </div>
                    <div class="card-content">
                        <ul class="announcement-list">
                            <li>
                                <div>Water supply maintenance on April 25, 2025</div>
                                <div class="announcement-date">Posted: April 20, 2025</div>
                            </li>
                            <li>
                                <div>Wi-Fi upgrade scheduled for next weekend</div>
                                <div class="announcement-date">Posted: April 18, 2025</div>
                            </li>
                            <li>
                                <div>Hostel community gathering - April 30</div>
                                <div class="announcement-date">Posted: April 15, 2025</div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Quick Services -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <h2 class="card-title">Campus Services</h2>
                    </div>
                    <div class="card-content">
                        <div class="quick-actions" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 10px;">
                            <button class="action-btn">Cafeteria Menu</button>
                            <button class="action-btn">Laundry Services</button>
                            <button class="action-btn">Shuttle Schedule</button>
                            <button class="action-btn">Sports Facilities</button>
                        </div>
                    </div>
                </div>

                <!-- Internet Usage -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <h2 class="card-title">Internet Usage</h2>
                    </div>
                    <div class="card-content">
                        <p><strong>Current Plan:</strong> Premium Student Package</p>
                        <p><strong>Data Used:</strong> 45.2 GB / 100 GB</p>
                        <div style="background: #eee; height: 10px; border-radius: 5px; margin: 10px 0;">
                            <div style="background: linear-gradient(to right, #6e8efb, #a777e3); width: 45%; height: 100%; border-radius: 5px;"></div>
                        </div>
                        <p><strong>Reset Date:</strong> May 1, 2025</p>
                        <button class="action-btn" style="width: 100%; margin-top: 10px;">Upgrade Plan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add any JavaScript functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // Menu item active state
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>