<?php
session_start();
include '../includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['username'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sun & Sea Restaurant</title>
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

        /* Main Login Card */
        .login-box {
            background: rgba(15, 25, 48, 0.85);
            border: 1px solid rgba(0, 210, 255, 0.3);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .login-box h2 {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .login-box p.subtitle {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 25px;
        }

        /* Form Controls */
        .input-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            color: #cbd5e1;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            color: #00d2ff;
            font-size: 14px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            background: #060c18;
            border: 1px solid rgba(0, 210, 255, 0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-wrapper input:focus {
            border-color: #00d2ff;
            box-shadow: 0 0 10px rgba(0, 210, 255, 0.3);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: rgba(0, 210, 255, 0.08);
            border: 1.5px solid #00d2ff;
            color: #00d2ff;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: #00d2ff;
            color: #0b1325;
            box-shadow: 0 0 12px rgba(0, 210, 255, 0.4);
            transform: translateY(-2px);
        }

        /* Error Notification */
        .error-msg {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid #ff4757;
            color: #ff4757;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #94a3b8;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #00d2ff;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2><i class="fa-solid fa-user-shield" style="color: #00d2ff;"></i> Admin Login</h2>
        <p class="subtitle">Sun & Sea Restaurant</p>

        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" placeholder="Enter username" required autocomplete="off">
                </div>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
        </form>

        <a href="../index.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Main Portal
        </a>
    </div>

</body>
</html>