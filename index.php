<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sun & Sea Restaurant - Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #0b1325;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Portal Main Container */
        .portal-container {
            text-align: center;
            background: rgba(15, 25, 48, 0.85);
            border: 1px solid rgba(0, 255, 136, 0.3);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            max-width: 450px;
            width: 100%;
        }

        .portal-title {
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .portal-subtitle {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 35px;
        }

        /* Buttons Wrapper */
        .btn-wrapper {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Standard Soft Glowing Buttons */
        .portal-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 24px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        /* Customer Portal Button - Balanced Glow */
        .btn-customer {
            color: #00ff88;
            border: 1.5px solid #00ff88;
            background: rgba(0, 255, 136, 0.05);
        }

        .btn-customer:hover {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 12px rgba(0, 255, 136, 0.4); /* Glow ප්‍රමාණය අඩු කර සුමට කර ඇත */
            transform: translateY(-2px);
        }

        /* Admin Portal Button - Balanced Glow */
        .btn-admin {
            color: #00d2ff;
            border: 1.5px solid #00d2ff;
            background: rgba(0, 210, 255, 0.05);
        }

        .btn-admin:hover {
            background: #00d2ff;
            color: #0b1325;
            box-shadow: 0 0 12px rgba(0, 210, 255, 0.4); /* Glow ප්‍රමාණය අඩු කර සුමට කර ඇත */
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="portal-container">
        <h1 class="portal-title"><i class="fa-solid fa-umbrella-beach"></i> Sun & Sea Restaurant</h1>
        <p class="portal-subtitle">Please select your portal to continue:</p>

        <div class="btn-wrapper">
            <a href="customer/index.php" class="portal-btn btn-customer">
                <i class="fa-solid fa-utensils"></i> Customer Portal
            </a>

            <a href="admin/login.php" class="portal-btn btn-admin">
                <i class="fa-solid fa-user-shield"></i> Admin Portal
            </a>
        </div>
    </div>

</body>
</html>