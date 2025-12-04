<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp - Smart Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 400% 400%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: gradientShift 15s ease infinite;
            overflow-x: hidden;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 25s infinite linear;
        }
        
        .shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 15%;
            left: 5%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 70%;
            right: 5%;
            animation-delay: -8s;
        }
        
        .shape:nth-child(3) {
            width: 70px;
            height: 70px;
            bottom: 10%;
            left: 15%;
            animation-delay: -15s;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            33% { transform: translate(50px, 50px) rotate(120deg) scale(1.1); }
            66% { transform: translate(-30px, 80px) rotate(240deg) scale(0.9); }
            100% { transform: translate(0, 0) rotate(360deg) scale(1); }
        }
        
        .signup-container {
            width: 100%;
            max-width: 520px;
            z-index: 1;
        }
        
        .signup-panel {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(25px);
            border-radius: 24px;
            padding: 45px 40px;
            box-shadow: 0 20px 60px rgba(31, 38, 135, .4);
            border: 1px solid rgba(255, 255, 255, 0.25);
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .logo h1 {
            font-size: 2.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, #f093fb 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 4px 20px rgba(0, 0, 0, .2);
            margin-bottom: 8px;
        }
        
        .logo p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
            font-weight: 300;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        .form-group {
            margin-bottom: 22px;
            position: relative;
        }
        
        .form-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            background: rgba(255, 255, 255, .15);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.3s ease;
            z-index: 2;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px 15px 70px;
            background: rgba(255, 255, 255, .2);
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 14px;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group input:focus {
            background: rgba(255, 255, 255, .25);
            border-color: rgba(255, 255, 255, .5);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.15);
        }
        
        .form-group input:focus + i {
            background: rgba(255, 255, 255, .25);
            color: #fff;
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .form-group small {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            margin-top: 5px;
            padding-left: 5px;
            opacity: 0;
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        
        .form-group input:focus + i + small,
        .form-group input:not(:placeholder-shown) + i + small {
            opacity: 1;
            transform: translateY(0);
        }
        
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            background: #ff4757;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .strength-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            margin-top: 5px;
            text-align: right;
        }
        
        .error-message {
            background: rgba(255, 59, 48, .2);
            border: 1px solid rgba(255, 59, 48, .4);
            color: #fff;
            padding: 14px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            display: none;
            animation: shake 0.5s ease;
        }
        
        .success-message {
            background: rgba(52, 199, 89, .2);
            border: 1px solid rgba(52, 199, 89, .4);
            color: #fff;
            padding: 14px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            display: none;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .signup-button {
            width: 100%;
            padding: 17px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .signup-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .signup-button:active {
            transform: translateY(0);
        }
        
        .signup-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .signup-button:active::after {
            width: 300px;
            height: 300px;
        }
        
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            z-index: 2;
        }
        
        .password-toggle:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.95rem;
        }
        
        .login-link a {
            color: #f093fb;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 8px;
        }
        
        .login-link a:hover {
            color: #fff;
            background: rgba(240, 147, 251, 0.2);
        }
        
        .loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .terms {
            margin-top: 20px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            line-height: 1.5;
        }
        
        .terms a {
            color: #f093fb;
            text-decoration: none;
        }
        
        .terms a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .signup-panel {
                padding: 35px 25px;
            }
            
            .logo h1 {
                font-size: 2.3rem;
            }
            
            .form-group input {
                padding: 14px 15px 14px 60px;
            }
        }
        
        @media (max-width: 400px) {
            .signup-panel {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Floating background shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

fetch

    <div class="signup-container">
        <div class="signup-panel">
            <!-- Error/Success Messages -->
            <div class="error-message" id="errorMessage"></div>
            <div class="success-message" id="successMessage"></div>

            <div class="logo">
                <h1>CREATE ACCOUNT</h1>
                <p>Join Smart Home and automate your living space</p>
            </div>

            <form id="signupForm" method="POST">
                <div class="form-group">
                    <i class="fas fa-home"></i>
                    <input type="text" name="housename" id="housename" placeholder="Household Name" required 
                           autocomplete="organization">
                    <small>Your smart home's display name</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="firstname" id="firstname" placeholder="First Name" required 
                               autocomplete="given-name">
                        <small>Your first name</small>
                    </div>

                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="lastname" id="lastname" placeholder="Last Name" required 
                               autocomplete="family-name">
                        <small>Your last name</small>
                    </div>
                </div>

                <div class="form-group">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" name="username" id="username" placeholder="Username" required 
                           autocomplete="username">
                    <small>Choose a unique username</small>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required
                           autocomplete="new-password" oninput="checkPasswordStrength()">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i id="toggleIcon" class="fas fa-eye"></i>
                    </button>
                    <small>At least 8 characters with letters and numbers</small>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Weak</div>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required
                           autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="toggleConfirmPassword()">
                        <i id="confirmToggleIcon" class="fas fa-eye"></i>
                    </button>
                    <small>Re-enter your password</small>
                </div>

                <button type="submit" class="signup-button" id="signupButton">
                    <span id="buttonText">CREATE ACCOUNT</span>
                    <div class="loader" id="loader"></div>
                </button>
            </form>

            <div class="terms">
                By creating an account, you agree to our 
                <a href="terms.php">Terms of Service</a> and 
                <a href="privacy.php">Privacy Policy</a>
            </div>

            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("toggleIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function toggleConfirmPassword() {
            const input = document.getElementById("confirm_password");
            const icon = document.getElementById("confirmToggleIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById("password").value;
            const strengthBar = document.getElementById("strengthBar");
            const strengthText = document.getElementById("strengthText");
            
            let strength = 0;
            let color = "#ff4757"; // Red
            let text = "Weak";
            
            if (password.length >= 8) strength += 1;
            if (/[a-z]/.test(password)) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            switch(strength) {
                case 0:
                case 1:
                    color = "#ff4757";
                    text = "Weak";
                    break;
                case 2:
                    color = "#ffa502";
                    text = "Fair";
                    break;
                case 3:
                    color = "#2ed573";
                    text = "Good";
                    break;
                case 4:
                case 5:
                    color = "#1e90ff";
                    text = "Strong";
                    break;
            }
            
            strengthBar.style.width = `${(strength / 5) * 100}%`;
            strengthBar.style.background = color;
            strengthText.textContent = text;
            strengthText.style.color = color;
        }

        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const button = document.getElementById('signupButton');
            const buttonText = document.getElementById('buttonText');
            const loader = document.getElementById('loader');
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');
            
            // Validate passwords match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                errorMessage.textContent = 'Passwords do not match!';
                errorMessage.style.display = 'block';
                document.getElementById('confirm_password').style.borderColor = '#ff4757';
                return;
            }
            
            // Hide previous messages
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';
            
            // Show loading state
            button.disabled = true;
            buttonText.style.display = 'none';
            loader.style.display = 'block';
            
           try {
    const response = await fetch('../controller/val_con.php', {
        method: 'POST',
        body: formData
    });
    
    // First get the raw text to see if it's valid JSON
    const rawText = await response.text();
    console.log("Raw server response:", rawText);
    
    // Try to parse as JSON
    let data;
    try {
        data = JSON.parse(rawText);
    } catch (jsonError) {
        console.error("Failed to parse JSON:", jsonError);
        console.error("Raw response was:", rawText);
        throw new Error("Server returned invalid response: " + rawText.substring(0, 100));
    }
    
    console.log("Parsed data:", data);
    
    if (data.success) {
        successMessage.textContent = data.message || 'Account created successfully! Redirecting...';
        successMessage.style.display = 'block';
        
        // Reset form
        document.getElementById('signupForm').reset();
        
        // Redirect after 2 seconds
        setTimeout(() => {
            window.location.href = data.redirect || '/view/login.php';
        }, 2000);
    } else {
        errorMessage.textContent = data.message || 'An error occurred. Please try again.';
        errorMessage.style.display = 'block';
        
        // Show debug info if available
        if (data.debug) {
            console.error("Debug info:", data.debug);
        }
        
        button.disabled = false;
    }
} catch (error) {
    console.error("Fetch error details:", {
        error: error,
        name: error.name,
        message: error.message,
        stack: error.stack
    });
    
    errorMessage.textContent = 'Network error: ' + error.message + 
                               '. Please check browser console (F12) for details.';
    errorMessage.style.display = 'block';
    
    buttonText.style.display = 'block';
    loader.style.display = 'none';
    button.disabled = false;
}
        });

        // Add input validation
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = 'rgba(255, 255, 255, .3)';
                document.getElementById('errorMessage').style.display = 'none';
                
                // Clear confirm password error when typing
                if (this.id === 'password' || this.id === 'confirm_password') {
                    document.getElementById('confirm_password').style.borderColor = 'rgba(255, 255, 255, .3)';
                }
            });
        });
    </script>
</body>
</html>