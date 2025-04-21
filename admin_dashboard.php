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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Dashboard Specific Styles */
        body {
            display: block;
            height: 100%;
            background: #f5f5f5;
            color: #333;
            padding: 0;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 200px;
            background: linear-gradient(180deg, #6e8efb, #a777e3);
            color: white;
            padding-top: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 15px 0;
        }

        .menu-item {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 14px;
        }

        .menu-item i {
            margin-right: 10px;
            width: 18px;
            text-align: center;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid white;
        }

        .menu-category {
            font-size: 11px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            padding: 15px 15px 8px;
            letter-spacing: 1px;
        }

        /* Main Content Styles */
        .main-content {
            flex-grow: 1;
            margin-left: 200px;
            padding: 20px;
            overflow-x: hidden;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e1e1e1;
        }

        .header h1 {
            font-size: 22px;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 35px;
            height: 35px;
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
            font-size: 14px;
        }

        .logout-btn i {
            margin-right: 5px;
        }

        /* Dashboard Cards & Stats */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            padding: 15px;
            display: flex;
            align-items: center;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .stat-info h3 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 3px;
            margin-top: 0;
        }

        .stat-info p {
            color: #777;
            margin: 0;
            font-size: 13px;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            padding: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .card-title-area {
            display: flex;
            align-items: center;
        }

        .card-icon {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .card-actions a {
            color: #777;
            margin-left: 10px;
            text-decoration: none;
            font-size: 13px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        .card-content {
            color: #555;
            font-size: 14px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: fixed;
        }

        .data-table th {
            background-color: #f8f9fa;
            text-align: left;
            padding: 10px 8px;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid #e1e1e1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e1e1e1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: #f8f9fa;
        }

        .status {
            padding: 4px 8px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 500;
            white-space: nowrap;
        }

        .status-occupied {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .status-vacant {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .status-maintenance {
            background-color: rgba(241, 196, 15, 0.1);
            color: #f39c12;
        }

        .status-pending {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        .status-paid {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .status-overdue {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .action-buttons a {
            margin-right: 8px;
            color: #6e8efb;
            text-decoration: none;
        }

        /* Forms */
        .form-row {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 13px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #e1e1e1;
            border-radius: 4px;
            font-size: 13px;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .form-control:focus {
            outline: none;
            border-color: #6e8efb;
            box-shadow: 0 0 0 2px rgba(110, 142, 251, 0.2);
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(to right, #6e8efb, #a777e3);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #6e8efb;
            color: #6e8efb;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Notifications */
        .notification {
            padding: 10px;
            background-color: #fff;
            border-left: 4px solid #6e8efb;
            margin-bottom: 8px;
            border-radius: 4px;
        }

        .notification-urgent {
            border-left-color: #e74c3c;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .notification p {
            margin: 4px 0;
            font-size: 12px;
        }

        .notification-time {
            font-size: 11px;
            color: #777;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stat-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }

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
                padding: 15px;
            }
            
            .stat-cards {
                grid-template-columns: 1fr;
            }

            .form-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .user-info {
                margin-top: 10px;
                width: 100%;
                justify-content: space-between;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
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
                <p>Admin Portal</p>
            </div>
            <div class="sidebar-menu">
                <a href="#" class="menu-item active">
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