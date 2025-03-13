<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Material Requests</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
        }
        
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        #sidebar {
            width: var(--sidebar-width);
            background: #2c3e50;
            color: white;
            transition: all 0.3s;
            position: fixed;
            height: 100vh;
            z-index: 1000;
        }

        #sidebar.collapsed {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        #content {
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }

        #content.expanded {
            width: 100%;
            margin-left: 0;
        }

        .sidebar-header {
            padding: 20px;
            background: #1a2634;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            display: block;
            transition: all 0.3s;
            cursor: pointer;
        }

        .menu-item:hover {
            background: #34495e;
            color: #fff;
        }

        .menu-item.active {
            background: #3498db;
        }

        .menu-item i {
            margin-right: 10px;
        }

        #sidebarCollapse {
            background: none;
            border: none;
            color: white;
            padding: 10px;
        }

        .content-section {
            display: none;
            padding: 20px;
        }

        .content-section.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #e74c3c;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 12px;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
                margin-left: 0;
            }
        }

        .request-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        
        .request-card:hover {
            transform: translateY(-5px);
        }
        
        .material-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .material-option {
            cursor: pointer;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .material-option:hover {
            background: #f8f9fa;
            transform: scale(1.05);
        }
        
        .material-option.selected {
            background: #e3f2fd;
            border: 2px solid #0d6efd;
        }

        .welcome-card {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            color: white;
            border: none;
            border-radius: 15px;
        }

        .main-action-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .main-action-card:hover {
            transform: translateY(-5px);
        }

        .request-btn {
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
            transition: all 0.3s ease;
        }

        .request-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }

        .stat-card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .timeline-item {
            padding: 15px;
            border-left: 2px solid #e9ecef;
            position: relative;
        }

        .timeline-icon {
            width: 10px;
        }

        .chart-container {
            margin: 20px auto;
        }

        .alert {
            animation: slideIn 0.5s ease-out;
            border-left: 5px solid;
        }

        .alert-success {
            border-left-color: #28a745;
        }

        .alert-danger {
            border-left-color: #dc3545;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4>Material Requests</h4>
            </div>

            <div class="sidebar-menu">
                <a class="menu-item active" data-section="dashboard">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a class="menu-item" data-section="new-request">
                    <i class="fas fa-plus"></i> Nouvelle Demande
                </a>
                <a class="menu-item" data-section="my-requests">
                    <i class="fas fa-list"></i> Mes Demandes
                    <span class="notification-badge">0</span>
                </a>
                <a class="menu-item" data-section="profile">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </nav>

        <!-- Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <button id="sidebarCollapse">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="navbar-brand ms-3">
                        Bienvenue, <?php echo htmlspecialchars($_SESSION['fullname']); ?>
                    </span>
                </div>
            </nav>

            <!-- Dashboard Section -->
            <div id="dashboard-section" class="content-section active">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8 text-center">
                            <!-- Welcome Card -->
                            <div class="card welcome-card mb-5">
                                <div class="card-body">
                                    <h2 class="welcome-title mb-4">
                                        <i class="fas fa-hand-wave"></i> 
                                        Bienvenue, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!
                                    </h2>
                                    <p class="lead text-muted">
                                        Besoin de matériel informatique ? Faites votre demande en quelques clics.
                                    </p>
                                </div>
                            </div>

                            <!-- Main Action Card -->
                            <div class="card main-action-card">
                                <div class="card-body p-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/9748/9748449.png" 
                                         alt="IT Equipment" 
                                         class="img-fluid mb-4" 
                                         style="max-width: 300px;">
                                    
                                    <h3 class="mb-4">Faire une nouvelle demande</h3>
                                    <p class="text-muted mb-4">
                                        Cliquez sur le bouton ci-dessous pour demander du matériel informatique 
                                        (PC, souris, clavier, câbles, etc.)
                                    </p>
                                    
                                    <button class="btn btn-primary btn-lg request-btn" 
                                            onclick="showRequestSection()">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Nouvelle Demande
                                    </button>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="row mt-5">
                                <div class="col-md-4">
                                    <div class="card stat-card">
                                        <div class="card-body">
                                            <i class="fas fa-clock text-primary mb-3" style="font-size: 2rem;"></i>
                                            <?php
                                            $stmt = $conn->prepare("SELECT COUNT(*) FROM material_requests WHERE user_id = :user_id AND status = 'pending'");
                                            $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                            $stmt->execute();
                                            $pending = $stmt->fetchColumn();
                                            ?>
                                            <h4><?php echo $pending; ?></h4>
                                            <p class="text-muted">Demandes en attente</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card stat-card">
                                        <div class="card-body">
                                            <i class="fas fa-check-circle text-success mb-3" style="font-size: 2rem;"></i>
                                            <?php
                                            $stmt = $conn->prepare("SELECT COUNT(*) FROM material_requests WHERE user_id = :user_id AND status = 'approved'");
                                            $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                            $stmt->execute();
                                            $approved = $stmt->fetchColumn();
                                            ?>
                                            <h4><?php echo $approved; ?></h4>
                                            <p class="text-muted">Demandes approuvées</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card stat-card">
                                        <div class="card-body">
                                            <i class="fas fa-list text-info mb-3" style="font-size: 2rem;"></i>
                                            <?php
                                            $stmt = $conn->prepare("SELECT COUNT(*) FROM material_requests WHERE user_id = :user_id");
                                            $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                            $stmt->execute();
                                            $total = $stmt->fetchColumn();
                                            ?>
                                            <h4><?php echo $total; ?></h4>
                                            <p class="text-muted">Total des demandes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Request Section -->
            <div id="new-request-section" class="content-section">
                <div class="container">
                    <h2 class="mb-4"><i class="fas fa-plus-circle"></i> Nouvelle Demande de Matériel</h2>
                    
                    <form action="submit_request.php" method="POST" id="requestForm">
                        <!-- Material Selection -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Sélectionnez le matériel</h5>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="material-option" data-material="souris">
                                            <i class="fas fa-mouse material-icon"></i>
                                            <h6>Souris</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="material-option" data-material="clavier">
                                            <i class="fas fa-keyboard material-icon"></i>
                                            <h6>Clavier</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="material-option" data-material="cable">
                                            <i class="fas fa-plug material-icon"></i>
                                            <h6>Câble</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="material-option" data-material="pc">
                                            <i class="fas fa-laptop material-icon"></i>
                                            <h6>PC</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="material-option" data-material="usb">
                                            <i class="fas fa-usb material-icon"></i>
                                            <h6>Clé USB</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="material-option" data-material="chargeur">
                                            <i class="fas fa-battery-full material-icon"></i>
                                            <h6>Chargeur</h6>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="material_type" id="selectedMaterial" required>
                            </div>
                        </div>

                        <!-- Request Details -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Détails de la demande</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description du besoin</label>
                                    <textarea name="description" class="form-control" rows="4" required 
                                        placeholder="Expliquez pourquoi vous avez besoin de ce matériel..."></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Niveau d'urgence</label>
                                    <select name="urgency" class="form-select">
                                        <option value="normal">Normal</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> Soumettre la demande
                        </button>
                    </form>

                    <!-- Add this right after the form in new-request-section -->
                    <?php if(isset($_SESSION['request_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['request_message']['type']; ?> alert-dismissible fade show mt-3" role="alert">
                            <i class="fas <?php echo $_SESSION['request_message']['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
                            <?php 
                            echo $_SESSION['request_message']['text'];
                            unset($_SESSION['request_message']); // Clear the message after displaying
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- My Requests Section -->
            <div id="my-requests-section" class="content-section">
                <div class="container">
                    <h2 class="mb-4"><i class="fas fa-list"></i> Mes Demandes</h2>
                    
                    <div class="row">
                        <?php
                        try {
                            $stmt = $conn->prepare("SELECT * FROM material_requests WHERE user_id = :user_id ORDER BY created_at DESC");
                            $stmt->bindParam(':user_id', $_SESSION['user_id']);
                            $stmt->execute();
                            
                            while($request = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card request-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title">
                                                    <?php 
                                                    $icons = [
                                                        'souris' => 'fa-mouse',
                                                        'clavier' => 'fa-keyboard',
                                                        'cable' => 'fa-plug',
                                                        'pc' => 'fa-laptop',
                                                        'usb' => 'fa-usb',
                                                        'chargeur' => 'fa-battery-full'
                                                    ];
                                                    $icon = $icons[$request['material_type']] ?? 'fa-box';
                                                    ?>
                                                    <i class="fas <?php echo $icon; ?>"></i>
                                                    <?php echo ucfirst($request['material_type']); ?>
                                                </h5>
                                                <span class="badge <?php echo $request['urgency'] == 'urgent' ? 'bg-danger' : 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($request['urgency']); ?>
                                                </span>
                                            </div>
                                            
                                            <p class="card-text"><?php echo htmlspecialchars($request['description']); ?></p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?>
                                                </small>
                                                <span class="badge <?php 
                                                    echo match($request['status']) {
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-danger',
                                                        default => 'bg-warning'
                                                    };
                                                ?>">
                                                    <?php echo ucfirst($request['status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } catch(PDOException $e) {
                            echo '<div class="alert alert-danger">Erreur: ' . $e->getMessage() . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Profile Section -->
            <div id="profile-section" class="content-section">
                <div class="container">
                    <div class="row">
                        <!-- User Profile Card -->
                        <div class="col-md-4 mb-4">
                            <div class="card profile-card">
                                <div class="card-body text-center">
                                    <div class="profile-avatar mb-4">
                                        <i class="fas fa-user-circle" style="font-size: 5rem; color: #4e54c8;"></i>
                                    </div>
                                    <h3><?php echo htmlspecialchars($_SESSION['fullname']); ?></h3>
                                    <?php
                                    $stmt = $conn->prepare("SELECT email FROM users WHERE id = :user_id");
                                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                    $stmt->execute();
                                    $user = $stmt->fetch();
                                    ?>
                                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Statistiques des demandes</h4>
                                    
                                    <?php
                                    // Get request statistics
                                    $stmt = $conn->prepare("
                                        SELECT 
                                            status, 
                                            COUNT(*) as count 
                                        FROM material_requests 
                                        WHERE user_id = :user_id 
                                        GROUP BY status
                                    ");
                                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                    $stmt->execute();
                                    $stats = $stmt->fetchAll();

                                    // Initialize counters
                                    $pending = 0;
                                    $approved = 0;
                                    $rejected = 0;
                                    $total = 0;

                                    // Calculate statistics
                                    foreach($stats as $stat) {
                                        switch($stat['status']) {
                                            case 'pending': $pending = $stat['count']; break;
                                            case 'approved': $approved = $stat['count']; break;
                                            case 'rejected': $rejected = $stat['count']; break;
                                        }
                                        $total += $stat['count'];
                                    }
                                    ?>

                                    <!-- Donut Chart -->
                                    <div class="chart-container" style="position: relative; height:300px;">
                                        <canvas id="requestsChart"></canvas>
                                    </div>

                                    <!-- Statistics Grid -->
                                    <div class="row mt-4">
                                        <div class="col-md-3 col-6">
                                            <div class="text-center">
                                                <h5><?php echo $total; ?></h5>
                                                <small class="text-muted">Total Demandes</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="text-center">
                                                <h5 class="text-warning"><?php echo $pending; ?></h5>
                                                <small class="text-muted">En Attente</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="text-center">
                                                <h5 class="text-success"><?php echo $approved; ?></h5>
                                                <small class="text-muted">Approuvées</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="text-center">
                                                <h5 class="text-danger"><?php echo $rejected; ?></h5>
                                                <small class="text-muted">Rejetées</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="card mt-4">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Activité Récente</h4>
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT * FROM material_requests 
                                        WHERE user_id = :user_id 
                                        ORDER BY created_at DESC 
                                        LIMIT 5
                                    ");
                                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                    $stmt->execute();
                                    $recent = $stmt->fetchAll();
                                    ?>

                                    <div class="timeline">
                                        <?php foreach($recent as $activity): ?>
                                            <div class="timeline-item">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="timeline-icon me-3">
                                                        <i class="fas fa-circle" style="color: <?php 
                                                            echo match($activity['status']) {
                                                                'approved' => '#28a745',
                                                                'rejected' => '#dc3545',
                                                                default => '#ffc107'
                                                            };
                                                        ?>"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Demande de <?php echo htmlspecialchars($activity['material_type']); ?></h6>
                                                        <small class="text-muted">
                                                            <?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?>
                                                        </small>
                                                    </div>
                                                    <span class="ms-auto badge <?php 
                                                        echo match($activity['status']) {
                                                            'approved' => 'bg-success',
                                                            'rejected' => 'bg-danger',
                                                            default => 'bg-warning'
                                                        };
                                                    ?>">
                                                        <?php echo ucfirst($activity['status']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Sidebar
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const sidebarCollapse = document.getElementById('sidebarCollapse');

            sidebarCollapse.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('expanded');
            });

            // Navigation Menu
            const menuItems = document.querySelectorAll('.menu-item[data-section]');
            const contentSections = document.querySelectorAll('.content-section');

            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all menu items and sections
                    menuItems.forEach(mi => mi.classList.remove('active'));
                    contentSections.forEach(section => section.classList.remove('active'));

                    // Add active class to clicked menu item
                    this.classList.add('active');

                    // Show corresponding section
                    const sectionId = this.dataset.section + '-section';
                    document.getElementById(sectionId).classList.add('active');

                    // On mobile, collapse sidebar after selection
                    if (window.innerWidth <= 768) {
                        sidebar.classList.add('collapsed');
                        content.classList.add('expanded');
                    }
                });
            });

            // Update notification badge
            function updateNotificationBadge() {
                // This is just an example - you would typically get this from your backend
                const pendingRequests = document.querySelectorAll('.status-pending').length;
                const badge = document.querySelector('.notification-badge');
                badge.textContent = pendingRequests;
                badge.style.display = pendingRequests > 0 ? 'inline' : 'none';
            }

            // Call initially and set up periodic updates
            updateNotificationBadge();
            setInterval(updateNotificationBadge, 60000); // Update every minute

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('collapsed');
                    content.classList.remove('expanded');
                }
            });
        });

        // Material Selection
        document.querySelectorAll('.material-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.material-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update hidden input
                document.getElementById('selectedMaterial').value = this.dataset.material;
            });
        });

        // Form Validation
        document.getElementById('requestForm').addEventListener('submit', function(e) {
            if (!document.getElementById('selectedMaterial').value) {
                e.preventDefault();
                alert('Veuillez sélectionner un matériel');
            }
        });

        function showRequestSection() {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show request section
            document.getElementById('new-request-section').classList.add('active');
            
            // Update active menu item
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
                if(item.getAttribute('data-section') === 'new-request') {
                    item.classList.add('active');
                }
            });
        }
    </script>

    <!-- Add Chart.js library before closing body tag -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Add this script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the chart canvas
        const ctx = document.getElementById('requestsChart').getContext('2d');
        
        // Create the donut chart
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['En Attente', 'Approuvées', 'Rejetées'],
                datasets: [{
                    data: [<?php echo "$pending, $approved, $rejected"; ?>],
                    backgroundColor: [
                        '#ffc107', // warning yellow for pending
                        '#28a745', // success green for approved
                        '#dc3545'  // danger red for rejected
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    });
    </script>

    <!-- Add this script to your existing scripts -->
    <script>
    // Auto-hide alert after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.querySelector('.alert');
        if(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
    </script>
</body>
</html>