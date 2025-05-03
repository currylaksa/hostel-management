<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMU Hostel Management - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>MMU Hostel</h2>
                <p>Admin Portal</p>
            </div>
            <div class="sidebar-menu">
                <a href="admin_dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                
                <div class="menu-category">Student Management</div>
                <a href="#" class="menu-item">
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
                <a href="admin_profile.php" class="menu-item">
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

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Admin Dashboard</h1>
                <div class="user-info">
                    <img src="https://i.pravatar.cc/150?img=12" alt="Admin Profile">
                    <span class="user-name"><?php echo $_SESSION["user"]; ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-info">
                        <h3>842</h3>
                        <p>Total Residents</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3>92%</h3>
                        <p>Occupancy Rate</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-info">
                        <h3>17</h3>
                        <p>Pending Maintenance</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-info">
                        <h3>RM 24.5k</h3>
                        <p>Monthly Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <!-- Recent Applications -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-area">
                            <div class="card-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h2 class="card-title">Recent Applications</h2>
                        </div>
                        <div class="card-actions">
                            <a href="#">View All</a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1191301382</td>
                                        <td>Amir Bin Razak</td>
                                        <td>Apr 20, 2025</td>
                                        <td><span class="status status-pending">Pending</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-check"></i></a>
                                            <a href="#"><i class="fas fa-times"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>1191302476</td>
                                        <td>Nurul Huda</td>
                                        <td>Apr 19, 2025</td>
                                        <td><span class="status status-pending">Pending</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-check"></i></a>
                                            <a href="#"><i class="fas fa-times"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>1191303539</td>
                                        <td>Liu Wei Ming</td>
                                        <td>Apr 18, 2025</td>
                                        <td><span class="status status-pending">Pending</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-check"></i></a>
                                            <a href="#"><i class="fas fa-times"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Room Status -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-area">
                            <div class="card-icon">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <h2 class="card-title">Room Status</h2>
                        </div>
                        <div class="card-actions">
                            <a href="#">View All</a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Room No.</th>
                                        <th>Block</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>A-101</td>
                                        <td>Cyber Heights A</td>
                                        <td>Single</td>
                                        <td><span class="status status-occupied">Occupied</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>B-203</td>
                                        <td>Cyber Heights B</td>
                                        <td>Twin</td>
                                        <td><span class="status status-occupied">Occupied</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>C-305</td>
                                        <td>Cyber Heights C</td>
                                        <td>Twin</td>
                                        <td><span class="status status-vacant">Vacant</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>D-407</td>
                                        <td>Cyber Heights D</td>
                                        <td>Single</td>
                                        <td><span class="status status-maintenance">Maintenance</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-area">
                            <div class="card-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <h2 class="card-title">Recent Payments</h2>
                        </div>
                        <div class="card-actions">
                            <a href="#">View All</a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1191303672</td>
                                        <td>RM 850.00</td>
                                        <td>Apr 19, 2025</td>
                                        <td><span class="status status-paid">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>1191302156</td>
                                        <td>RM 850.00</td>
                                        <td>Apr 18, 2025</td>
                                        <td><span class="status status-paid">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>1191301943</td>
                                        <td>RM 1,200.00</td>
                                        <td>Apr 17, 2025</td>
                                        <td><span class="status status-paid">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>1191302789</td>
                                        <td>RM 850.00</td>
                                        <td>Apr 01, 2025</td>
                                        <td><span class="status status-overdue">Overdue</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Requests -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-area">
                            <div class="card-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h2 class="card-title">Maintenance Requests</h2>
                        </div>
                        <div class="card-actions">
                            <a href="#">View All</a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Issue</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>B-203</td>
                                        <td>AC repair</td>
                                        <td>Apr 19, 2025</td>
                                        <td><span class="status status-pending">In Progress</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-check"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>A-105</td>
                                        <td>Ceiling light</td>
                                        <td>Apr 18, 2025</td>
                                        <td><span class="status status-pending">Pending</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-check"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>D-407</td>
                                        <td>Water heater</td>
                                        <td>Apr 17, 2025</td>
                                        <td><span class="status status-maintenance">Scheduled</span></td>
                                        <td class="action-buttons">
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-check"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Create Announcement -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-area">
                            <div class="card-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h2 class="card-title">Create Announcement</h2>
                        </div>
                    </div>
                    <div class="card-content">
                        <form>
                            <div class="form-group">
                                <label for="title">Announcement Title</label>
                                <input type="text" class="form-control" id="title" placeholder="Enter announcement title">
                            </div>
                            <div class="form-group" style="margin-top: 12px;">
                                <label for="content">Content</label>
                                <textarea class="form-control" id="content" rows="3" placeholder="Enter announcement content"></textarea>
                            </div>
                            <div class="form-row" style="margin-top: 12px;">
                                <div class="form-group">
                                    <label for="target">Target Audience</label>
                                    <select class="form-control" id="target">
                                        <option>All Students</option>
                                        <option>Block A</option>
                                        <option>Block B</option>
                                        <option>Block C</option>
                                        <option>Block D</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <select class="form-control" id="priority">
                                        <option>Normal</option>
                                        <option>Important</option>
                                        <option>Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
                                <button type="button" class="btn btn-outline" style="margin-right: 8px;">Cancel</button>
                                <button type="submit" class="btn btn-primary">Publish</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-area">
                            <div class="card-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h2 class="card-title">Notifications</h2>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="notification notification-urgent">
                            <div class="notification-title">Block C Water Supply Disruption</div>
                            <p>Emergency maintenance required for Block C water pipes.</p>
                            <div class="notification-time">Today, 09:45 AM</div>
                        </div>
                        
                        <div class="notification">
                            <div class="notification-title">New Student Check-in</div>
                            <p>5 new students checked in today. Room assignments completed.</p>
                            <div class="notification-time">Yesterday, 3:30 PM</div>
                        </div>
                        
                        <div class="notification">
                            <div class="notification-title">Payment Reminder</div>
                            <p>Reminder: 12 students have overdue payments for this month.</p>
                            <div class="notification-time">April 19, 2025</div>
                        </div>
                        
                        <div class="notification">
                            <div class="notification-title">System Update</div>
                            <p>Hostel management system updated to version 2.4.1</p>
                            <div class="notification-time">April 18, 2025</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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