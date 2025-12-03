<?php
// dashboard.php - Complete integrated solution
session_start();

// Database connection
$con = mysqli_connect("localhost", "Prince", "", "sensors", "3307");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle ALL form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. REMOVE APPLIANCE
    if (isset($_POST['remove'])) {
        $id = mysqli_real_escape_string($con, $_POST['remove']);
        
        // Get appliance name for message
        $query = "SELECT Name FROM appliances WHERE Id = '$id'";
        $result = mysqli_query($con, $query);
        $appliance = mysqli_fetch_assoc($result);
        $applianceName = $appliance['Name'] ?? 'Appliance';
        
        // Delete appliance
        $deleteQuery = "DELETE FROM appliances WHERE Id = '$id'";
        if (mysqli_query($con, $deleteQuery)) {
            $_SESSION['message'] = "✓ '$applianceName' removed successfully!";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "✗ Failed to remove appliance!";
            $_SESSION['message_type'] = 'error';
        }
        
        // Redirect to appliances section
        header("Location: " . $_SERVER['PHP_SELF'] . "#appliances");
        exit();
    }
    
    // 2. ADD NEW APPLIANCE
    elseif (isset($_POST['App_name'])) {
        $name = trim(mysqli_real_escape_string($con, $_POST['App_name']));
        $type = mysqli_real_escape_string($con, $_POST['App_type'] ?? 'Light');
        $power = intval($_POST['App_power'] ?? 100);
        $status = 0; // Default to OFF (0)
        
        if (empty($name)) {
            $_SESSION['message'] = "✗ Appliance name is required!";
            $_SESSION['message_type'] = 'error';
        } else {
            $query = "INSERT INTO appliances (Name, Type, Status, WattUsage) VALUES ('$name', '$type', '$status', '$power')";
            if (mysqli_query($con, $query)) {
                $_SESSION['message'] = "✓ '$name' added successfully!";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "✗ Failed to add appliance!";
                $_SESSION['message_type'] = 'error';
            }
        }
        
        // Redirect to appliances section
        header("Location: " . $_SERVER['PHP_SELF'] . "#appliances");
        exit();
    }
    
    // 3. TURN APPLIANCE ON
    elseif (isset($_POST['turnOn'])) {
        $id = mysqli_real_escape_string($con, $_POST['Id']);
        
        // Get appliance name for activity log
        $query = "SELECT Name FROM appliances WHERE Id = '$id'";
        $result = mysqli_query($con, $query);
        $appliance = mysqli_fetch_assoc($result);
        $applianceName = $appliance['Name'] ?? 'Appliance';
        
        $query = "UPDATE appliances SET Status = 1 WHERE Id = '$id'";
        if (mysqli_query($con, $query)) {
            $_SESSION['message'] = "✓ '$applianceName' turned ON!";
            $_SESSION['message_type'] = 'success';
        }
        // Redirect to appliances section
        header("Location: " . $_SERVER['PHP_SELF'] . "#appliances");
        exit();
    }
    
    // 4. TURN APPLIANCE OFF
    elseif (isset($_POST['turnOff'])) {
        $id = mysqli_real_escape_string($con, $_POST['Id']);
        
        // Get appliance name for activity log
        $query = "SELECT Name FROM appliances WHERE Id = '$id'";
        $result = mysqli_query($con, $query);
        $appliance = mysqli_fetch_assoc($result);
        $applianceName = $appliance['Name'] ?? 'Appliance';
        
        $query = "UPDATE appliances SET Status = 0 WHERE Id = '$id'";
        if (mysqli_query($con, $query)) {
            $_SESSION['message'] = "✓ '$applianceName' turned OFF!";
            $_SESSION['message_type'] = 'success';
        }
        // Redirect to appliances section
        header("Location: " . $_SERVER['PHP_SELF'] . "#appliances");
        exit();
    }
}

// Display message from session
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? 'success';
unset($_SESSION['message'], $_SESSION['message_type']);

// Fetch appliances
$query = "SELECT * FROM appliances ORDER BY Id DESC";
$query_run = mysqli_query($con, $query);

// Check if we should auto-show appliances section after redirect
$showAppliancesSection = false;
if (isset($_GET['section']) && $_GET['section'] === 'appliances') {
    $showAppliancesSection = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- External CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .notification {
            padding: 12px 20px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }
        .notification.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .notification.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-on {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }
        .btn-off {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }
        .btn-remove {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }
        .btn-remove:hover { background: #ff4757; }
        .btn-on:hover { background: #45a049; transform: translateY(-2px); }
        .btn-off:hover { background: #d32f2f; transform: translateY(-2px); }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        .status-on {
            background: #e8f5e9;
            color: #4CAF50;
            border: 1px solid #c8e6c9;
        }
        .status-off {
            background: #f5f5f5;
            color: #9e9e9e;
            border: 1px solid #e0e0e0;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #52acbc;
        }
        table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        table tr:hover {
            background: #f9f9f9;
        }
        .no-data {
            text-align: center;
            padding: 40px !important;
            color: #666;
        }
        .no-data i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo-section">
                <h1><i class="fas fa-home"></i> SMART HOME</h1>
                <p>Control Center</p>
            </div>

            <ul class="nav-menu">
                <li><a href="#" class="nav-link <?php echo !$showAppliancesSection ? 'active' : ''; ?>" data-section="dashboard">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a></li>
                <li><a href="#" class="nav-link <?php echo $showAppliancesSection ? 'active' : ''; ?>" data-section="appliances">
                    <i class="fas fa-lightbulb"></i>
                    <span>Appliances</span>
                </a></li>
                <li><a href="#" class="nav-link" data-section="energy">
                    <i class="fas fa-bolt"></i>
                    <span>Energy Monitor</span>
                </a></li>
                <li><a href="#" class="nav-link" data-section="summary">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Summary</span>
                </a></li>
                <li><a href="#" class="nav-link" data-section="logs">
                    <i class="fas fa-history"></i>
                    <span>Activity Logs</span>
                </a></li>
                <li><a href="#" class="nav-link" data-section="settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a></li>
                <li class="logout-btn"><a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h2 id="page-title"><?php echo $showAppliancesSection ? 'Appliances Control' : 'Dashboard Overview'; ?></h2>
                    <p>Welcome back, User!</p>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <input type="file" id="profile-photo-input" accept="image/*" style="display:none">
                    <div class="profile-box">
                        <div class="profile-avatar" id="profile-avatar" title="Change profile photo">
                            <span class="avatar-initial">U</span>
                        </div>
                        <div class="profile-info">
                            <?php if($message): ?>
                                <strong><?php echo htmlspecialchars($message); ?></strong>
                            <?php endif; ?>
                        </div>
                        <button id="remove-photo-btn" style="display:none;margin-left:8px;">Remove</button>
                    </div>
                </div>
            </div>

            <!-- Dashboard Section -->
            <div class="content-section <?php echo !$showAppliancesSection ? 'active' : ''; ?>" id="dashboard">
                <div class="cards-grid">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-user-circle"></i></div>
                            <div class="card-title">Live PIR</div>
                        </div>
                        <div class="card-value" id="pir-value">245</div>
                        <div class="card-footer">
                            <i class="fas fa-arrow-up"></i> Motion detected
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-sun"></i></div>
                            <div class="card-title">LDR Value</div>
                        </div>
                        <div class="card-value" id="ldr-value">780</div>
                        <div class="card-footer">
                            <i class="fas fa-info-circle"></i> Lux level
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-bolt"></i></div>
                            <div class="card-title">Current</div>
                        </div>
                        <div class="card-value" id="current-value">12.5 A</div>
                        <div class="card-footer">
                            <i class="fas fa-chart-line"></i> Real-time
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-tachometer-alt"></i></div>
                            <div class="card-title">Wattage</div>
                        </div>
                        <div class="card-value" id="watt-value">2.75 kW</div>
                        <div class="card-footer">
                            <i class="fas fa-plug"></i> Total power
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                            <div class="card-title">Daily Cost</div>
                        </div>
                        <div class="card-value" id="cost-day">$8.45</div>
                        <div class="card-footer">
                            <i class="fas fa-calendar-day"></i> Today's usage
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-chart-bar"></i></div>
                            <div class="card-title">Monthly Estimate</div>
                        </div>
                        <div class="card-value" id="cost-month">$253</div>
                        <div class="card-footer">
                            <i class="fas fa-calendar-alt"></i> Projected cost
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appliances Section -->
            <div class="content-section <?php echo $showAppliancesSection ? 'active' : ''; ?>" id="appliances">
                <div class="section-header">
                    <h2><i class="fas fa-lightbulb"></i> Appliances Control</h2>
                    <button class="add-appliance-btn" id="add-appliance-btn">
                        <i class="fas fa-plus"></i> Add Appliance
                    </button>
                </div>

                <?php if($message): ?>
                    <div class="notification <?php echo $message_type; ?>">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- Add Appliance Modal -->
                <div class="modal-overlay" id="add-appliance-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="fas fa-plus-circle"></i> Add New Appliance</h3>
                            <button type="button" class="close-modal">&times;</button>
                        </div>
                        <form method="POST" id="appliance-form">
                            <div class="form-group">
                                <label for="appliance-names">Appliance Name *</label>
                                <input type="text" id="appliance-names" name="App_name" 
                                       placeholder="e.g., Living Room Light" required 
                                       autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="appliance-type">Type of Appliance</label>
                                <select id="appliance-type" name="App_type">
                                    <option value="Light">Light</option>
                                    <option value="Fan">Fan</option>
                                    <option value="Charger">Charger</option>
                                    <option value="TV">TV</option>
                                    <option value="AC">Air Conditioner</option>
                                    <option value="Heater">Heater</option>
                                    <option value="Computer">Computer</option>
                                    <option value="Refrigerator">Refrigerator</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="appliance-power">Power Consumption (Watts)</label>
                                <input type="number" id="appliance-power" name="App_power" 
                                       placeholder="e.g., 150" min="0" step="1" value="100"
                                       required>
                                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 5px;">
                                    Typical values: Light (10-100W), Fan (50-100W), TV (50-200W), AC (1000-3000W)
                                </small>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn">
                                    <i class="fas fa-save"></i> Create Appliance
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancel-modal">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Name</th> 
                            <th>Type</th>
                            <th>Status</th>
                            <th>Power Usage</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($query_run) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query_run)): 
                                // Convert Status (0/1) to text display
                                $status_text = $row['Status'] == 1 ? 'ON' : 'OFF';
                                $status_class = $row['Status'] == 1 ? 'status-on' : 'status-off';
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Type']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <i class="fas fa-power-<?php echo $row['Status'] == 1 ? 'on' : 'off'; ?>"></i>
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['WattUsage']); ?>W</td>
                                    <td>
                                        <div class="action-buttons">
                                            <!-- Remove Button -->
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="remove" value="<?php echo $row['Id']; ?>">
                                                <button type="submit" class="btn-remove" 
                                                        onclick="return confirm('Are you sure you want to remove <?php echo htmlspecialchars($row['Name']); ?>?')">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </form>

                                            <!-- Toggle ON/OFF Buttons -->
                                            <?php if($row['Status'] == 0): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="turnOn" value="1">
                                                    <input type="hidden" name="Id" value="<?php echo $row['Id']; ?>">
                                                    <button type="submit" class="btn-on">
                                                        <i class="fas fa-power-off"></i> Turn ON
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="turnOff" value="0">
                                                    <input type="hidden" name="Id" value="<?php echo $row['Id']; ?>">
                                                    <button type="submit" class="btn-off">
                                                        <i class="fas fa-power-off"></i> Turn OFF
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">
                                    <i class="fas fa-plug"></i>
                                    <h3>No appliances found</h3>
                                    <p>Click "Add Appliance" to create your first appliance</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Energy Monitor Section -->
            <div class="content-section" id="energy">
                <div class="cards-grid">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-bolt"></i></div>
                            <div class="card-title">Current Usage</div>
                        </div>
                        <div class="card-value">2.75 kW</div>
                        <div class="card-footer">
                            <i class="fas fa-clock"></i> Real-time monitoring
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                            <div class="card-title">Peak Today</div>
                        </div>
                        <div class="card-value">3.2 kW</div>
                        <div class="card-footer">
                            <i class="fas fa-arrow-up"></i> At 2:30 PM
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                            <div class="card-title">Today's Cost</div>
                        </div>
                        <div class="card-value">$8.45</div>
                        <div class="card-footer">
                            <i class="fas fa-calendar-day"></i> Daily total
                        </div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 class="chart-title"><i class="fas fa-chart-area"></i> Energy Usage Chart (24 Hours)</h3>
                    <div class="chart-wrapper">
                        <svg class="chart-svg" viewBox="0 0 900 320" id="energy-chart">
                            <path fill="url(#gradient)" d="M0,250 L50,220 L100,240 L150,200 L200,180 L250,160 L300,140 L350,120 L400,110 L450,100 L500,90 L550,80 L600,70 L650,80 L700,90 L750,100 L800,110 L850,120 L900,130 L900,320 L0,320 Z"></path>
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:#52acbc;stop-opacity:0.7"></stop>
                                    <stop offset="100%" style="stop-color:#4CAF50;stop-opacity:0.1"></stop>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 class="chart-title"><i class="fas fa-chart-pie"></i> Usage by Appliance</h3>
                    <div class="chart-wrapper">
                        <canvas id="appliance-donut-canvas" width="500" height="320" style="max-width:100%;"></canvas>
                    </div>
                    <div class="chart-legend" id="appliance-legend"></div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="content-section" id="summary">
                <div class="cards-grid">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-calendar-day"></i></div>
                            <div class="card-title">Today</div>
                        </div>
                        <div class="card-value">8.45 kWh</div>
                        <div class="card-footer">
                            <i class="fas fa-dollar-sign"></i> Cost: $8.45
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-calendar-week"></i></div>
                            <div class="card-title">This Week</div>
                        </div>
                        <div class="card-value">59.2 kWh</div>
                        <div class="card-footer">
                            <i class="fas fa-dollar-sign"></i> Cost: $59.20
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                            <div class="card-title">This Month</div>
                        </div>
                        <div class="card-value">253 kWh</div>
                        <div class="card-footer">
                            <i class="fas fa-dollar-sign"></i> Cost: $253.00
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <h3 style="margin-bottom: 20px; font-size: 1.3rem; font-weight: 600;">Weekly Summary</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Usage (kWh)</th>
                                <th>Cost ($)</th>
                                <th>Peak Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Monday</td><td>8.5</td><td>$8.50</td><td>2:30 PM</td></tr>
                            <tr><td>Tuesday</td><td>8.2</td><td>$8.20</td><td>3:15 PM</td></tr>
                            <tr><td>Wednesday</td><td>9.1</td><td>$9.10</td><td>2:00 PM</td></tr>
                            <tr><td>Thursday</td><td>8.8</td><td>$8.80</td><td>2:45 PM</td></tr>
                            <tr><td>Friday</td><td>9.3</td><td>$9.30</td><td>1:30 PM</td></tr>
                            <tr><td>Saturday</td><td>7.9</td><td>$7.90</td><td>11:00 AM</td></tr>
                            <tr><td>Sunday</td><td>7.4</td><td>$7.40</td><td>10:30 AM</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activity Logs Section -->
            <div class="content-section" id="logs">
                <div class="table-container">
                    <h3 style="margin-bottom: 20px; font-size: 1.3rem; font-weight: 600;">Recent Activity</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Action</th>
                                <th>Appliance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>10:45 AM</td><td>Turned ON</td><td>Living Room Light</td><td><span class="status-badge status-on">ACTIVE</span></td></tr>
                            <tr><td>10:30 AM</td><td>Turned OFF</td><td>Ceiling Fan</td><td><span class="status-badge status-off">INACTIVE</span></td></tr>
                            <tr><td>09:15 AM</td><td>Turned ON</td><td>Air Conditioner</td><td><span class="status-badge status-on">ACTIVE</span></td></tr>
                            <tr><td>08:00 AM</td><td>Turned ON</td><td>Smart TV</td><td><span class="status-badge status-on">ACTIVE</span></td></tr>
                            <tr><td>07:30 AM</td><td>Turned ON</td><td>Computer</td><td><span class="status-badge status-on">ACTIVE</span></td></tr>
                            <tr><td>11:30 PM</td><td>Turned OFF</td><td>Water Heater</td><td><span class="status-badge status-off">INACTIVE</span></td></tr>
                            <tr><td>10:00 PM</td><td>Turned OFF</td><td>Living Room Light</td><td><span class="status-badge status-off">INACTIVE</span></td></tr>
                            <tr><td>09:45 PM</td><td>Auto Mode Enabled</td><td>System</td><td><span class="status-badge status-on">ACTIVE</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Settings Section -->
            <div class="content-section" id="settings">
                <div class="settings-section">
                    <h3><i class="fas fa-user-cog"></i> Account Settings</h3>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="User" readonly>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" placeholder="user@example.com">
                    </div>
                    <div class="form-group">
                        <label>Change Password</label>
                        <input type="password" placeholder="Enter new password">
                    </div>
                    <button class="btn">Save Changes</button>
                </div>

                <div class="settings-section">
                    <h3><i class="fas fa-bell"></i> Notifications</h3>
                    <div class="form-group">
                        <label>Email Notifications</label>
                        <select>
                            <option>Enabled</option>
                            <option>Disabled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>High Usage Alert Threshold (kW)</label>
                        <input type="number" value="3.0" step="0.1" placeholder="3.0">
                    </div>
                    <div class="form-group">
                        <label>Daily Report</label>
                        <select>
                            <option>Enabled</option>
                            <option>Disabled</option>
                        </select>
                    </div>
                    <button class="btn">Save Notifications</button>
                </div>
            </div>
        </div>
    </div>

    <!-- External JavaScript -->
    <script src="script.js"></script>
    <script>
        // Modal functionality for Add Appliance button
        document.addEventListener('DOMContentLoaded', function() {
            const addBtn = document.getElementById('add-appliance-btn');
            const modal = document.getElementById('add-appliance-modal');
            const cancelBtn = document.getElementById('cancel-modal');
            const closeBtn = document.querySelector('.close-modal');
            
            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    modal.classList.add('active');
                    document.getElementById('appliance-names').focus();
                });
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    modal.classList.remove('active');
                });
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.classList.remove('active');
                });
            }
            
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    modal.classList.remove('active');
                }
            });
            
            // Form validation
            const form = document.getElementById('appliance-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const nameInput = document.getElementById('appliance-names');
                    const powerInput = document.getElementById('appliance-power');
                    
                    if (nameInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please enter an appliance name');
                        nameInput.focus();
                        return false;
                    }
                    
                    if (powerInput.value <= 0) {
                        e.preventDefault();
                        alert('Power consumption must be greater than 0');
                        powerInput.focus();
                        return false;
                    }
                    
                    // Show loading state
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                    submitBtn.disabled = true;
                    
                    // Re-enable after 3 seconds if form doesn't submit
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                });
            }

            // Auto-scroll to appliances section if URL has #appliances
            if (window.location.hash === '#appliances') {
                // Activate appliances section
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('data-section') === 'appliances') {
                        link.classList.add('active');
                    }
                });
                
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                    if (section.id === 'appliances') {
                        section.classList.add('active');
                    }
                });
                
                // Update page title
                document.getElementById('page-title').textContent = 'Appliances Control';
                
                // Smooth scroll to appliances section
                setTimeout(() => {
                    const appliancesSection = document.getElementById('appliances');
                    if (appliancesSection) {
                        appliancesSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }, 100);
            }

            // Enhanced navigation for smooth section switching
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all links
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Hide all content sections
                    document.querySelectorAll('.content-section').forEach(section => {
                        section.classList.remove('active');
                    });
                    
                    // Show the selected section
                    const sectionId = this.getAttribute('data-section');
                    const targetSection = document.getElementById(sectionId);
                    if (targetSection) {
                        targetSection.classList.add('active');
                        
                        // Update page title
                        const titles = {
                            'dashboard': 'Dashboard Overview',
                            'appliances': 'Appliances Control',
                            'energy': 'Energy Monitor',
                            'summary': 'Usage Summary',
                            'logs': 'Activity Logs',
                            'settings': 'System Settings'
                        };
                        document.getElementById('page-title').textContent = titles[sectionId] || 'Dashboard';
                        
                        // Update URL without page reload
                        history.pushState(null, null, `?section=${sectionId}#${sectionId}`);
                        
                        // Smooth scroll to top of section
                        setTimeout(() => {
                            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php mysqli_close($con); ?>