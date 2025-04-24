<?php
/**
 * Template Name: Custom Login
 */

// Process form submission before headers are sent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    // Verify nonce
    if (!isset($_POST['login_nonce']) || !wp_verify_nonce($_POST['login_nonce'], 'login_action')) {
        $error_message = 'Security verification failed. Please try again.';
    } else {
        // Get credentials
        $username = sanitize_text_field($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;
        
        // Attempt to log in
        $creds = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember
        );
        
        $user = wp_signon($creds, is_ssl());
        
        if (is_wp_error($user)) {
            $error_message = $user->get_error_message();
        } else {
            // Redirect to dashboard page
            wp_redirect(site_url('/dashboard'));
            exit;
        }
    }
}

// Redirect if user is already logged in
if (is_user_logged_in()) {
    wp_redirect(site_url('/dashboard'));
    exit;
}

get_header();
?>

<div class="container">
    <div class="form-header">
        <h1>Login to Your Account</h1>
    </div>
    
    <?php if (isset($error_message)): ?>
        <div class="error-message" style="display: block;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <div id="js-error-message" class="error-message"></div>
    
    <form id="login-form" method="post" action="">
        <?php wp_nonce_field('login_action', 'login_nonce'); ?>
        
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="checkbox-group">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Remember me</label>
        </div>
        
        <button type="submit" name="login_submit" class="btn">Login</button>
        
        <div class="form-footer">
            <p>Don't have an account? <a href="<?php echo site_url('/register'); ?>">Sign Up</a></p>
            <p><a href="<?php echo wp_lostpassword_url(); ?>">Forgot Password?</a></p>
        </div>
    </form>
</div>

<style>
    /* CSS Styles */
    :root {
        --primary-color: #4a6bef;
        --error-color: #e74c3c;
        --success-color: #2ecc71;
        --text-color: #333;
        --light-gray: #f5f5f5;
        --border-color: #ddd;
    }
    
    .container {
        max-width: 400px;
        margin: 50px auto;
        padding: 30px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .form-header {
        text-align: center;
        margin-bottom: 25px;
    }
    
    .form-header h1 {
        margin: 0;
        color: var(--primary-color);
        font-size: 28px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    input[type="text"],
    input[type="password"],
    input[type="email"] {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 16px;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }
    
    input[type="text"]:focus,
    input[type="password"]:focus,
    input[type="email"]:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(74, 107, 239, 0.2);
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .checkbox-group input {
        margin-right: 10px;
    }
    
    .btn {
        display: inline-block;
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s;
    }
    
    .btn:hover {
        background-color: #3a5bd9;
    }
    
    .error-message {
        color: var(--error-color);
        background-color: rgba(231, 76, 60, 0.1);
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
        display: none;
    }
    
    .form-footer {
        text-align: center;
        margin-top: 25px;
        font-size: 15px;
    }
    
    .form-footer a {
        color: var(--primary-color);
        text-decoration: none;
    }
    
    .form-footer a:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 480px) {
        .container {
            margin: 20px;
            padding: 20px;
        }
    }
</style>

<script>
    // JavaScript for form validation
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const errorMessage = document.getElementById('js-error-message');
        
        loginForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            // Reset error message
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            
            // Basic validation
            if (username === '') {
                e.preventDefault();
                errorMessage.textContent = 'Please enter your username or email.';
                errorMessage.style.display = 'block';
                return;
            }
            
            if (password === '') {
                e.preventDefault();
                errorMessage.textContent = 'Please enter your password.';
                errorMessage.style.display = 'block';
                return;
            }
        });
    });
</script>

<?php get_footer(); ?>