<?php session_start();
require 'db.php';
$idParent = $_SESSION['parent_id'];
$fname=$_SESSION['f_name'] ;
$lname=$_SESSION['l_name'] ;

$sql = "SELECT * FROM enfant WHERE parent_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idParent]);
$enfants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT SUM(solde) AS total_solde FROM enfant WHERE parent_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idParent]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$totalSolde = $row['total_solde'] ;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Parent - FamilyPay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
        }
        
        /* Sidebar */
        .dashboard {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background-color: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            position: fixed;
            height: 100%;
            transition: all 0.3s;
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-logo i {
            color: var(--primary);
            margin-right: 0.5rem;
            font-size: 1.8rem;
        }
        
        .sidebar-menu {
            padding: 1.5rem 0;
        }
        
        .menu-title {
            padding: 0 1.5rem 0.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .menu-item i {
            margin-right: 1rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .menu-item:hover {
            background-color: rgba(67, 97, 238, 0.05);
            color: var(--primary);
        }
        
        .menu-item.active {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            border-left-color: var(--primary);
        }
        
        .menu-badge {
            margin-left: auto;
            background-color: var(--primary);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 50px;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: all 0.3s;
        }
        
        /* Top Navigation */
        .top-nav {
            background-color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 90;
        }
        
        .search-bar {
            position: relative;
            width: 300px;
        }
        
        .search-bar input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .search-bar i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
        }
        
        .notification-btn {
            position: relative;
            margin-right: 1.5rem;
            color: #495057;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.8rem;
        }
        
        .user-name {
            font-weight: 500;
            margin-right: 0.5rem;
        }
        
        /* Content Area */
        .content-wrapper {
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title h1 {
            font-size: 1.8rem;
            color: var(--dark);
        }
        
        .page-title p {
            color: #6c757d;
            margin-top: 0.5rem;
        }
        
        .btn {
            padding: 0.7rem 1.5rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            border: none;
            cursor: pointer;
        }
        
        .btn i {
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(113, 26, 200, 0.3);
        }
        

        .card {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-size: 1rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .card-icon.primary {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .card-icon.success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .card-icon.warning {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning);
        }
        
        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .card-footer {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        /* Children List */
        .children-section {
            margin-top: 2rem;
        }
        
        .section-title {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 0.8rem;
            color: var(--primary);
        }
        
        .children-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .child-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        
        .child-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .child-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 1.5rem;
            color: black;
            display: flex;
            align-items: center;
        }
        
        .child-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.3);
            margin-right: 1rem;
        }
        
        .child-info h3 {
            font-size: 1.2rem;
            margin-bottom: 0.2rem;
        }
        
        .child-info p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .child-body {
            padding: 1.5rem;
        }
        
        .child-stat {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .stat-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .progress-container {
            margin-bottom: 1.5rem;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .progress-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 4px;
            width: 65%;
        }
        
        .child-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .child-btn {
            flex: 1;
            padding: 0.6rem;
            border-radius: 5px;
            font-size: 0.9rem;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .child-btn i {
            margin-right: 0.3rem;
        }
        
        .btn-recharge {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-recharge:hover {
            background-color: var(--secondary);
        }
        
        .btn-details {
            background-color: #e9ecef;
            color: var(--dark);
        }
        
        .btn-details:hover {
            background-color: #dee2e6;
        }
        
        /* Recent Transactions */
        .transactions-section {
            margin-top: 3rem;
        }
        
        .transaction-table {
            width: 100%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .table-header {
            display: flex;
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #eee;
        }
        
        .table-row {
            display: flex;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #eee;
            transition: all 0.3s;
        }
        
        .table-row:last-child {
            border-bottom: none;
        }
        
        .table-row:hover {
            background-color: #f8f9fa;
        }
        
        .col-1 { width: 20%; }
        .col-2 { width: 25%; }
        .col-3 { width: 20%; }
        .col-4 { width: 15%; }
        .col-5 { width: 20%; }
        
        .transaction-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }
        
        .transaction-user {
            display: flex;
            align-items: center;
        }
        
        .transaction-amount {
            font-weight: 600;
        }
        
        .transaction-amount.negative {
            color: var(--danger);
        }
        
        .transaction-amount.positive {
            color: var(--success);
        }
        
        .transaction-status {
            padding: 0.3rem 0.6rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-completed {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .status-pending {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning);
        }
        
        .view-all {
            text-align: center;
            padding: 1rem;
        }
        
        .view-all a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }
        
        .view-all a i {
            margin-left: 0.5rem;
            transition: transform 0.3s;
        }
        
        .view-all a:hover i {
            transform: translateX(3px);
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .sidebar {
                width: 250px;
            }
            
            .main-content {
                margin-left: 250px;
            }
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
                margin-right: 1rem;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: #495057;
                cursor: pointer;
            }
        }
        
        @media (max-width: 768px) {
            .search-bar {
                width: 200px;
            }
            
            .user-name {
                display: none;
            }
            
            .table-header {
                display: none;
            }
            
            .table-row {
                flex-direction: column;
                padding: 1.5rem;
            }
            
            .col-1, .col-2, .col-3, .col-4, .col-5 {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .col-5 {
                margin-bottom: 0;
            }
        }

        /* Styles pour le formulaire d'ajout */
        .form-container {
            display: none;
            background-color: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-container.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .form-title {
            font-size: 1.5rem;
            color: var(--dark);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6c757d;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
        }

        .avatar-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #f1f3f5;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .avatar-upload-btn {
            background-color: var(--light);
            color: var(--dark);
            border: 1px solid #ddd;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .recharge-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .recharge-modal.active {
            display: flex;
        }
        
        .recharge-content {
            background-color: white;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .recharge-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .recharge-title {
            font-size: 1.5rem;
            color: #4361ee;
            display: flex;
            align-items: center;
        }
        
        .recharge-title i {
            margin-right: 0.8rem;
        }
        
        .close-recharge {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6c757d;
            cursor: pointer;
        }
        
        .recharge-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .recharge-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .recharge-form input, 
        .recharge-form select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .recharge-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .montant-rapide {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.8rem;
            margin: 1.5rem 0;
        }
        
        .btn-montant {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-montant:hover {
            border-color: #4361ee;
            color: #4361ee;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .child-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            padding: 20px;
            width: 300px;
            transition: transform 0.2s ease-in-out;
        }

        .child-card:hover {
            transform: translateY(-5px);
        }

        .child-header {
            margin-bottom: 15px;
        }

        .child-info h3 {
            margin: 0;
            color:rgb(11, 11, 10);
            font-size: 20px;
        }

        .child-stat {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 13px;
            color: #888;
        }

        .stat-value {
            font-weight: bold;
            color:rgb(20, 23, 189);
        }

        .progress-container {
            margin: 10px 0;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .progress-bar {
            background-color: #e0e0e0;
            border-radius: 10px;
            height: 8px;
        }

        .progress-fill {
            height: 8px;
            border-radius: 10px;
            background-color:rgb(219, 52, 52);
            width: 0%;
        }

        .child-actions {
            margin-top: 10px;
            text-align: center;
        }

        .child-actions input[type="number"] {
            width: 80px;
            padding: 5px;
            margin-right: 5px;
        }

        .child-actions button {
            background-color: #27ae60;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .child-actions button:hover {
            background-color: #219150;
        }
        table {
    width: 90%;
    margin: 30px auto;
    border-collapse: collapse;
    font-family: 'Segoe UI', sans-serif;
    background-color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    overflow: hidden;
}

h2 {
    text-align: center;
    color: #2c3e50;
}

table th, table td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #f0f0f0;
}

table th {
    background-color: #3498db;
    color: white;
    font-weight: normal;
    font-size: 16px;
}

table tr:hover {
    background-color: #f9f9f9;
}

table td {
    font-size: 14px;
    color: #333;
}
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="parent-dashboard.html" class="sidebar-logo">
                    <i class="fas fa-wallet"></i>
                    <span>SafetyPay</span>
                </a>
            </div>
            
            <nav class="sidebar-menu">
                <p class="menu-title">Menu Principal</p>
                <a href="parent-dashboard.html" class="menu-item active">
                    <i class="fas fa-home"></i>
                    <span>Tableau de bord</span>
                </a>
                
                <a href="espace_enfant.php" class="menu-item">
                    <i class="fas fa-child"></i>
                    <span>Mes enfants</span>
                </a>
                
                <a href="transaction.php" class="menu-item">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transaction</span>
                    <span class="menu-badge">3</span>
                </a>
                
                
                <p class="menu-title">Paramètres</p>
                <a href="parent-notifications.html" class="menu-item">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                    <span class="menu-badge">5</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <nav class="top-nav">
                
                
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                
                <div class="user-menu">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge"></span>
                    </button>
                    
                    <div class="user-profile">
                        <span class="user-name"><?= $fname . ' ' . $lname?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </nav>
            
            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-title">
                        <h1>Tableau de bord</h1>
                        <p>Bienvenue dans votre espace parent FamilyPay</p>
                    </div>
                    
                    <button id="addChildBtn" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un enfant
                    </button>
                </div>
                
                <!-- Formulaire d'ajout d'enfant -->
                <div id="childFormContainer" class="form-container">
                    <div class="form-header">
                        <h3 class="form-title">
                            <i class="fas fa-user-plus"></i> Ajouter un enfant
                        </h3>
                        <button class="close-btn" id="closeFormBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="addChildForm" action="ajouter.php" method="POST">    
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName" class="form-label">Prénom</label>
                                <input type="text" id="firstName" name="firstname" class="form-control" placeholder="Prénom de l'enfant" required>
                            </div>
                            
                        </div>
                        
                        <div class="form-group">
                            <label for="birthDate" class="form-label">Age</label>
                            <input type="number"  name="age" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Numéro de tel</label>
                            <input type="number"  name="numtel" class="form-control">
                        </div>
                        <div class="form-group">
                            <label  class="form-label">votre mot de passe</label>
                            <input type="password"  name="mdp" class="form-control">
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" id="cancelBtn">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Dashboard Cards -->
                <div class="cards-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Solde total</h3>
                            <div class="card-icon primary">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <div class="card-value"><?=$totalSolde?></div>
                        <div class="card-footer">Sur les comptes des enfants</div>
                    </div>
                    
                <h1>Mes Enfants</h1>
            <div class="container">
                <?php foreach ($enfants as $enfant): ?>
                <div class="child-card">
                <div class="child-header">
            <div class="child-info">
                <h3><?= htmlspecialchars($enfant['name']) ?></h3>
            </div>
        </div>

        <div class="child-body">
            <div class="child-stat">
                <div>
                    <div class="stat-label">Solde actuel</div>
                    <div class="stat-value"><?= number_format($enfant['solde'], 2, ',', ' ') ?> DT</div>
                </div>
                <div>
                    <div class="stat-label">Dépenses 7j</div>
                    <div class="stat-value">€0,00</div>
                </div>
            </div>

            <div class="progress-container">
                <div class="progress-label">
                    <span>Budget mensuel</span>
                    <span>--% utilisé</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
            </div>

            <div class="child-actions">
                <form method="POST" action="">
                    <input type="hidden" name="id_enfant" value="<?= $enfant['id_enfant'] ?>">
                    <input type="number" name="montant" step="0.01" min="0" placeholder="Montant">
                    <button type="submit">Recharger</button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php
$sql = "
    SELECT recharge.*, enfant.name 
    FROM recharge 
    JOIN enfant  ON recharge.enfant_id = enfant.id_enfant
    WHERE enfant.parent_id = ?
    ORDER BY recharge.date_recharge DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idParent]);
$recharges = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Dernières Recharges</h1>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Enfant</th>
        <th>Montant</th>
        <th>Date de recharge</th>
    </tr>
    <?php foreach ($recharges as $recharge): ?>
    <tr>
        <td><?= htmlspecialchars($recharge['name']) ?></td>
        <td><?= number_format($recharge['montant'], 2, ',', ' ') ?> DT</td>
        <td><?= date('d/m/Y H:i', strtotime($recharge['date_recharge'])) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<div class="view-all">
                            <a href="transaction.php">
                                Voir toutes les recharges
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

            </div>
        </main>
    </div>
    <script>
        // Gestion de la recharge
        document.addEventListener('DOMContentLoaded', function() {
            const rechargeModal = document.getElementById('rechargeModal');
            const rechargeForm = document.getElementById('rechargeForm');
            const closeRecharge = document.getElementById('closeRecharge');
            const cancelRecharge = document.getElementById('cancelRecharge');
            const childNameInput = document.getElementById('childName');
            const childIdInput = document.getElementById('childId');
            const rechargeAmountInput = document.getElementById('rechargeAmount');
            
            // Écouteurs pour les boutons de recharge
            document.querySelectorAll('.btn-recharge').forEach(btn => {
                btn.addEventListener('click', function() {
                    const childId = this.getAttribute('data-child-id');
                    const childName = this.getAttribute('data-child-name');
                    
                    // Remplir le formulaire
                    childIdInput.value = childId;
                    childNameInput.value = childName;
                    
                    // Afficher le modal
                    rechargeModal.classList.add('active');
                });
            });
            
            // Boutons montant rapide
            document.querySelectorAll('.btn-montant').forEach(btn => {
                btn.addEventListener('click', function() {
                    const amount = this.getAttribute('data-amount');
                    rechargeAmountInput.value = amount;
                });
            });
            
            // Fermer le modal
            function closeRechargeModal() {
                rechargeModal.classList.remove('active');
                rechargeForm.reset();
            }
            
            closeRecharge.addEventListener('click', closeRechargeModal);
            cancelRecharge.addEventListener('click', closeRechargeModal);
            
            // Soumission du formulaire
            rechargeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const rechargeData = {
                    childId: childIdInput.value,
                    amount: parseFloat(rechargeAmountInput.value),
                    paymentMethod: document.getElementById('paymentMethod').value
                };
                
                console.log('Recharge en cours:', rechargeData);
                
                // Ici vous enverriez les données au serveur
                // Simulation de succès après 1 seconde
                setTimeout(() => {
                    alert(`Vous avez rechargé ${rechargeData.amount}€ pour ${childNameInput.value}`);
                    closeRechargeModal();
                    
                    // Ici vous pourriez actualiser le solde affiché
                }, 1000);
            });
            
            // Fermer en cliquant à l'extérieur
            rechargeModal.addEventListener('click', function(e) {
                if (e.target === rechargeModal) {
                    closeRechargeModal();
                }
            });
        });
    

        document.addEventListener('DOMContentLoaded', function() {
            // Menu mobile toggle
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
            
            // Gestion du formulaire d'ajout d'enfant
            const addChildBtn = document.getElementById('addChildBtn');
            const formContainer = document.getElementById('childFormContainer');
            const closeFormBtn = document.getElementById('closeFormBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const addChildForm = document.getElementById('addChildForm');
            
            // Afficher le formulaire
            addChildBtn.addEventListener('click', function() {
                formContainer.classList.add('active');
            });
            
            // Cacher le formulaire
            function hideForm() {
                formContainer.classList.remove('active');
                addChildForm.reset();
                
                // Réinitialiser l'avatar
                const avatarImage = document.getElementById('avatarImage');
                const avatarPreviewIcon = document.querySelector('#avatarPreview i');
                avatarImage.style.display = 'none';
                avatarPreviewIcon.style.display = 'block';
            }
            
            // Fermer avec les boutons
            closeFormBtn.addEventListener('click', hideForm);
            cancelBtn.addEventListener('click', hideForm);
            
            // Avatar image preview
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreview = document.getElementById('avatarPreview');
            const avatarImage = document.getElementById('avatarImage');
            
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        avatarImage.src = event.target.result;
                        avatarImage.style.display = 'block';
                        avatarPreview.querySelector('i').style.display = 'none';
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Soumission du formulaire
            addChildForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Collecter les données du formulaire
                const formData = {
                    firstName: document.getElementById('firstName').value,
                    lastName: document.getElementById('lastName').value,
                    birthDate: document.getElementById('birthDate').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value,
                    avatar: avatarInput.files[0] || null
                };
                console.log('Enfant ajouté:', formData);
                            
                            // Ici vous enverriez les données à votre backend
                            // Simulons une réponse réussie
                            setTimeout(() => {
                                // Cacher le formulaire après soumission
                                hideForm();
                                
                                // Afficher un message de confirmation
                                alert(`${formData.firstName} ${formData.lastName} a été ajouté(e) avec succès!`);
                                
                                // Ici vous pourriez actualiser la liste des enfants
                            }, 500);
                        });
                    });
                </script>
            </body>
            </html>