<?php
// Check if this is a login form submission (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    session_start();
    header('Content-Type: application/json');
    
    // Login validation logic
    $host = 'localhost';
    $dbname = 'sensors';
    $db_username = 'Prince';  
    $db_password = '';
    $db_port = 3307;
    
    try {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) throw new Exception("Username and password required.");
        
        $dsn = "mysql:host=$host;port=$db_port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $db_username, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        $stmt = $pdo->prepare("SELECT passWord, userName FROM users WHERE userName = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['passWord'])) {
            throw new Exception("Invalid username or password.");
        }
        
        $_SESSION['user'] = $user['userName'];
        echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => 'dashboard.php']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%); background-size: 400% 400%; display: flex; align-items: center; justify-content: center; padding: 20px; animation: gradientShift 15s ease infinite; }
        @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .floating-shapes { position: fixed; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
        .shape { position: absolute; background: rgba(255, 255, 255, 0.1); border-radius: 50%; animation: float 20s infinite linear; }
        .shape:nth-child(1) { width: 80px; height: 80px; top: 10%; left: 10%; }
        .shape:nth-child(2) { width: 120px; height: 120px; top: 60%; right: 10%; animation-delay: -5s; }
        .shape:nth-child(3) { width: 60px; height: 60px; bottom: 20%; left: 20%; animation-delay: -10s; }
        @keyframes float { 0% { transform: translate(0, 0) rotate(0deg); } 100% { transform: translate(100px, 100px) rotate(360deg); } }
        .login-container { width: 100%; max-width: 480px; z-index: 1; }
        .login-panel { background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(25px); border-radius: 24px; padding: 45px 40px; box-shadow: 0 20px 60px rgba(31, 38, 135, .4); border: 1px solid rgba(255, 255, 255, 0.25); animation: slideUp 0.8s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .logo { text-align: center; margin-bottom: 40px; }
        .logo h1 { font-size: 2.8rem; font-weight: 700; background: linear-gradient(135deg, #fff 0%, #f093fb 100%); -webkit-background-clip: text; background-clip: text; color: transparent; text-shadow: 0 4px 20px rgba(0, 0, 0, .2); margin-bottom: 10px; }
        .logo p { color: rgba(255, 255, 255, 0.8); font-size: 0.95rem; font-weight: 300; }
        .form-group { margin-bottom: 28px; position: relative; }
        .form-group i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.9); font-size: 1.3rem; background: rgba(255, 255, 255, .15); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 10px; transition: all 0.3s ease; }
        .form-group input { width: 100%; padding: 16px 20px 16px 72px; background: rgba(255, 255, 255, .2); border: 1px solid rgba(255, 255, 255, .3); border-radius: 14px; color: #fff; font-size: 1rem; outline: none; transition: all 0.3s ease; }
        .form-group input:focus { background: rgba(255, 255, 255, .25); border-color: rgba(255, 255, 255, .5); box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.15); }
        .form-group input:focus + i { background: rgba(255, 255, 255, .25); color: #fff; }
        .form-group input::placeholder { color: rgba(255, 255, 255, 0.7); }
        .error-message { background: rgba(255, 59, 48, .2); border: 1px solid rgba(255, 59, 48, .4); color: #fff; padding: 14px; border-radius: 10px; text-align: center; margin-bottom: 20px; display: none; animation: shake 0.5s ease; }
        .success-message { background: rgba(52, 199, 89, .2); border: 1px solid rgba(52, 199, 89, .4); color: #fff; padding: 14px; border-radius: 10px; text-align: center; margin-bottom: 20px; display: none; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 75% { transform: translateX(10px); } }
        .login-button { width: 100%; padding: 17px; background: linear-gradient(135deg, #667eea, #764ba2); border: none; border-radius: 14px; color: #fff; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden; }
        .login-button:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4); }
        .login-button:active { transform: translateY(0); }
        .login-button::after { content: ''; position: absolute; top: 50%; left: 50%; width: 0; height: 0; border-radius: 50%; background: rgba(255, 255, 255, 0.3); transform: translate(-50%, -50%); transition: width 0.6s, height 0.6s; }
        .login-button:active::after { width: 300px; height: 300px; }
        .password-toggle { position: absolute; right: 18px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: rgba(255, 255, 255, 0.8); font-size: 1.2rem; cursor: pointer; padding: 8px; border-radius: 8px; transition: all 0.3s ease; }
        .password-toggle:hover { color: #fff; background: rgba(255, 255, 255, 0.1); }
        .signup-link { text-align: center; margin-top: 25px; color: rgba(255, 255, 255, 0.8); font-size: 0.95rem; }
        .signup-link a { color: #f093fb; text-decoration: none; font-weight: 600; transition: all 0.3s ease; padding: 5px 10px; border-radius: 8px; }
        .signup-link a:hover { color: #fff; background: rgba(240, 147, 251, 0.2); }
        .forgot-password { text-align: center; margin-top: 15px; }
        .forgot-password a { color: rgba(255, 255, 255, 0.7); text-decoration: none; font-size: 0.9rem; transition: color 0.3s ease; }
        .forgot-password a:hover { color: #fff; }
        .loader { display: none; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 1s ease-in-out infinite; margin: 0 auto; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 480px) { .login-panel { padding: 35px 25px; } .logo h1 { font-size: 2.2rem; } }
    </style>
</head>

<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container">
        <div class="login-panel">
            <div class="error-message" id="errorMessage"></div>
            <div class="success-message" id="successMessage"></div>

            <div class="logo">
                <h1>SMART HOME</h1>
                <p>Welcome back to your intelligent home system</p>
            </div>

            <form id="loginForm" method="POST">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" id="username" placeholder="Username" required autocomplete="username">
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i id="toggleIcon" class="fas fa-eye"></i>
                    </button>
                </div>

                <button type="submit" class="login-button" id="loginButton">
                    <span id="buttonText">LOGIN</span>
                    <div class="loader" id="loader"></div>
                </button>
            </form>

            <div class="signup-link">
                Don't have an account? <a href="signup.php">Sign up here</a>
            </div>
            
            <div class="forgot-password">
                <a href="forgot-password.php">Forgot your password?</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("toggleIcon");
            input.type = input.type === "password" ? "text" : "password";
            icon.classList.toggle("fa-eye");
            icon.classList.toggle("fa-eye-slash");
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const button = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');
            const loader = document.getElementById('loader');
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');
            
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';
            button.disabled = true;
            buttonText.style.display = 'none';
            loader.style.display = 'block';
            
            try {
                const response = await fetch('', {  // Submit to same page
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    successMessage.textContent = data.message;
                    successMessage.style.display = 'block';
                    setTimeout(() => { window.location.href = data.redirect || 'dashboard.php'; }, 1500);
                } else {
                    errorMessage.textContent = data.message;
                    errorMessage.style.display = 'block';
                }
            } catch (error) {
                errorMessage.textContent = 'Network error. Check your connection.';
                errorMessage.style.display = 'block';
                console.error('Login error:', error);
            } finally {
                buttonText.style.display = 'block';
                loader.style.display = 'none';
                button.disabled = false;
            }
        });
    </script>
</body>
</html>