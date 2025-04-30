<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: #f5f7fb;
            font-family: 'Poppins', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #fff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding-top: 20px;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: #6c757d;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            font-size: 16px;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .sidebar .nav-link.active {
            color: #ff6f61;
            background: #f8f9fa;
        }

        .sidebar .nav-link:hover {
            color: #ff6f61;
        }

        /* Header */
        .header {
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px 20px;
            margin-left: 250px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ff6f61;
        }

        .header .btn {
            background: #ff6f61;
            color: #fff;
            border: none;
            padding: 8px 20px;
            font-size: 14px;
        }

        .header .btn:hover {
            background: #e65b50;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .profile-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: url('https://via.placeholder.com/1200x300/4a90e2/ffffff?text=Cover+Image') no-repeat center center;
            background-size: cover;
            height: 150px;
            position: relative;
        }

        .profile-header .btn-upload {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff6f61;
            color: #fff;
            border: none;
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 5px;
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #fff;
            position: absolute;
            bottom: -50px;
            left: 20px;
        }

        .profile-info {
            padding: 60px 20px 20px;
        }

        .profile-info h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-info p {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row label {
            font-weight: 500;
            color: #6c757d;
            font-size: 14px;
        }

        .info-row span {
            color: #343a40;
            font-size: 14px;
        }

        .info-row .btn-edit {
            color: #ff6f61;
            font-size: 14px;
            text-decoration: none;
        }

        .info-row .btn-edit:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    
    <!-- Main Content -->
    <div class="main-content">
        <div class="profile-card">
            <div class="profile-header">
                <button class="btn-upload">Update Cover</button>
                <img src="Selecao/assets/img/working-1.jpg" alt="Profile Picture" class="profile-pic">
            </div>
            <div class="profile-info">
                <h2>Debel Parek</h2>
                <p>1st, Sistem Informasi Bisnis</p>
                <div class="info-row">
                    <label>Personal Meeting ID</label>
                    <span>Sat- A30 S09S85</span>
                    <a href="#" class="btn-edit">Edit</a>
                </div>
                <div class="info-row">
                    <label>Email</label>
                    <span>debelparek@gmail.com</span>
                    <a href="#" class="btn-edit">Edit</a>
                </div>
                <div class="info-row">
                    <label>Subscription Type</label>
                    <span>Basic User (Freeuser)</span>
                    <a href="#" class="btn-edit">Edit</a>
                </div>
                <div class="info-row">
                    <label>Time Zone</label>
                    <span>Indonesia (Jakarta Timezone)</span>
                    <a href="#" class="btn-edit">Edit</a>
                </div>
                <div class="info-row">
                    <label>Language</label>
                    <span>English</span>
                    <a href="#" class="btn-edit">Edit</a>
                </div>
                <div class="info-row">
                    <label>Password</label>
                    <span>••••••••</span>
                    <a href="#" class="btn-edit">Edit</a>
                </div>
                <div class="info-row">
                    <label>Device</label>
                    <span>Sign Out From All Devices</span>
                    <a href="#" class="btn-edit">Edit</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>