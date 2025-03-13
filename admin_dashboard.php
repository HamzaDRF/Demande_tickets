<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

require_once 'config.php';

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-nav {
            background: #343a40;
            padding: 15px;
        }
        .stats-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .search-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .request-card {
            transition: transform 0.2s;
            margin-bottom: 15px;
        }
        .request-card:hover {
            transform: translateY(-3px);
        }
        .activity-timeline {
            max-height: 350px;
            overflow-y: auto;
        }
        .activity-item {
            transition: background-color 0.3s ease;
        }
        .activity-item:hover {
            background-color: #f8f9fa;
        }
        .activity-icon {
            width: 10px;
        }
        /* Custom Scrollbar for Activity Timeline */
        .activity-timeline::-webkit-scrollbar {
            width: 6px;
        }
        .activity-timeline::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .activity-timeline::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        .activity-timeline::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <nav class="admin-nav text-white">
        <div class="container d-flex justify-content-between align-items-center">
            <h4>Admin Dashboard</h4>
            <a href="admin_logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Search Bar -->
        <div class="search-container">
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Rechercher par nom, type de matériel, status..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                </div>
            </form>
        </div>

        <div class="row">
            <!-- Charts Column -->
            <div class="col-md-6">
                <!-- Status Distribution Chart -->
                <div class="chart-container">
                    <h5>Distribution des Demandes</h5>
                    <canvas id="statusChart"></canvas>
                </div>
                
                <!-- Material Types Chart -->
                <div class="chart-container">
                    <h5>Types de Matériel Demandés</h5>
                    <canvas id="materialChart"></canvas>
                </div>
            </div>

            <!-- Statistics Column -->
            <div class="col-md-6">
                <div class="row">
                    <?php
                    // Get statistics
                    $stats = [
                        'total' => $conn->query("SELECT COUNT(*) FROM material_requests")->fetchColumn(),
                        'pending' => $conn->query("SELECT COUNT(*) FROM material_requests WHERE status='pending'")->fetchColumn(),
                        'approved' => $conn->query("SELECT COUNT(*) FROM material_requests WHERE status='approved'")->fetchColumn(),
                        'urgent' => $conn->query("SELECT COUNT(*) FROM material_requests WHERE urgency='urgent'")->fetchColumn()
                    ];
                    
                    $statCards = [
                        ['Total Demandes', 'fa-list', 'primary', $stats['total']],
                        ['En Attente', 'fa-clock', 'warning', $stats['pending']],
                        ['Approuvées', 'fa-check', 'success', $stats['approved']],
                        ['Urgentes', 'fa-exclamation', 'danger', $stats['urgent']]
                    ];

                    foreach($statCards as $card): ?>
                        <div class="col-md-6 mb-4">
                            <div class="stats-card text-center">
                                <i class="fas <?php echo $card[1]; ?> text-<?php echo $card[2]; ?> fa-2x mb-3"></i>
                                <h3><?php echo $card[3]; ?></h3>
                                <p class="text-muted mb-0"><?php echo $card[0]; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Add this after the statistics cards and before the requests list -->
        <div class="row mb-4">
            <!-- Latest Activity Card -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Activité Récente</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-timeline">
                            <?php
                            $stmt = $conn->query("
                                SELECT mr.*, u.fullname 
                                FROM material_requests mr 
                                JOIN users u ON mr.user_id = u.id 
                                ORDER BY mr.created_at DESC 
                                LIMIT 5
                            ");
                            while($activity = $stmt->fetch()): ?>
                                <div class="activity-item p-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="activity-icon me-3">
                                            <i class="fas fa-circle text-<?php 
                                                echo match($activity['status']) {
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    default => 'warning'
                                                };
                                            ?>"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">
                                                <strong><?php echo htmlspecialchars($activity['fullname']); ?></strong> 
                                                a demandé un(e) 
                                                <strong><?php echo htmlspecialchars($activity['material_type']); ?></strong>
                                            </p>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <div class="card">
            <div class="card-header">
                <h5>Demandes de Matériel</h5>
            </div>
            <div class="card-body">
                <?php
                $searchQuery = "";
                $params = [];
                
                if(!empty($search)) {
                    $searchQuery = "WHERE u.fullname LIKE :search 
                                  OR mr.material_type LIKE :search 
                                  OR mr.status LIKE :search
                                  OR mr.description LIKE :search";
                    $params[':search'] = "%$search%";
                }

                $sql = "SELECT mr.*, u.fullname 
                        FROM material_requests mr 
                        JOIN users u ON mr.user_id = u.id 
                        $searchQuery 
                        ORDER BY mr.created_at DESC";
                
                $stmt = $conn->prepare($sql);
                if(!empty($params)) {
                    $stmt->execute($params);
                } else {
                    $stmt->execute();
                }
                
                while($request = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="request-card card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6><?php echo htmlspecialchars($request['material_type']); ?></h6>
                                <span class="badge <?php echo $request['urgency'] == 'urgent' ? 'bg-danger' : 'bg-secondary'; ?>">
                                    <?php echo ucfirst($request['urgency']); ?>
                                </span>
                            </div>
                            <p class="mb-1">Demandé par: <?php echo htmlspecialchars($request['fullname']); ?></p>
                            <p class="mb-2"><?php echo htmlspecialchars($request['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?>
                                </small>
                                <div class="btn-group">
                                    <form action="update_request.php" method="POST" class="d-inline">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" name="status" value="approved" 
                                                class="btn btn-sm btn-success me-1">
                                            Approuver
                                        </button>
                                        <button type="submit" name="status" value="rejected" 
                                                class="btn btn-sm btn-danger">
                                            Rejeter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        // Get chart data
        <?php
        // Status distribution data
        $statusData = $conn->query("SELECT status, COUNT(*) as count FROM material_requests GROUP BY status")->fetchAll();
        $statusLabels = [];
        $statusCounts = [];
        foreach($statusData as $data) {
            $statusLabels[] = ucfirst($data['status']);
            $statusCounts[] = $data['count'];
        }

        // Material types data
        $materialData = $conn->query("SELECT material_type, COUNT(*) as count FROM material_requests GROUP BY material_type")->fetchAll();
        $materialLabels = [];
        $materialCounts = [];
        foreach($materialData as $data) {
            $materialLabels[] = ucfirst($data['material_type']);
            $materialCounts[] = $data['count'];
        }
        ?>

        // Status Chart
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($statusLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($statusCounts); ?>,
                    backgroundColor: ['#ffc107', '#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Material Chart
        new Chart(document.getElementById('materialChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($materialLabels); ?>,
                datasets: [{
                    label: 'Nombre de demandes',
                    data: <?php echo json_encode($materialCounts); ?>,
                    backgroundColor: '#4e73df'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Add notification sound for new requests
        let lastRequestCount = document.querySelectorAll('.request-card').length;
        setInterval(() => {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newCount = doc.querySelectorAll('.request-card').length;
                    
                    if(newCount > lastRequestCount) {
                        // Play notification sound
                        const audio = new Audio('notification.mp3');
                        audio.play();
                        
                        // Show browser notification
                        if(Notification.permission === "granted") {
                            new Notification("Nouvelle demande!", {
                                body: "Une nouvelle demande de matériel a été reçue.",
                                icon: "/path/to/icon.png"
                            });
                        }
                        
                        lastRequestCount = newCount;
                    }
                });
        }, 30000); // Check every 30 seconds
    </script>
</body>
</html> 