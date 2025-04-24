<?php
/**
 * Template Name: Custom Registration
 */

// Process form submission before headers are sent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
    // Verify nonce
    if (!isset($_POST['register_nonce']) || !wp_verify_nonce($_POST['register_nonce'], 'register_action')) {
        $registration_error = 'Security verification failed. Please try again.';
    } else {
        // Get form data
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        $error = false;
        
        // Validate username
        if (username_exists($username)) {
            $error = true;
            $username_error = 'This username is already taken.';
        }
        
        // Validate email
        if (!is_email($email)) {
            $error = true;
            $email_error = 'Please enter a valid email address.';
        } elseif (email_exists($email)) {
            $error = true;
            $email_error = 'This email is already registered.';
        }
        
        // Validate password
        if (strlen($password) < 8) {
            $error = true;
            $password_error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirm_password) {
            $error = true;
            $confirm_password_error = 'Passwords do not match.';
        }
        
        // If no errors, create the user
        if (!$error) {
            $user_id = wp_create_user($username, $password, $email);
            
            if (is_wp_error($user_id)) {
                $registration_error = $user_id->get_error_message();
            } else {
                // Set user role
                $user = new WP_User($user_id);
                $user->set_role('subscriber');
                
                // Auto login
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                
                // Redirect to dashboard
                wp_redirect(site_url('/dashboard'));
                exit;
            }
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
        <h1>Create an Account</h1>
    </div>
    
    <?php if (isset($registration_error)): ?>
        <div class="error-message" style="display: block;">
            <?php echo $registration_error; ?>
        </div>
    <?php endif; ?>
    
    <div id="js-error-message" class="error-message"></div>
    
    <form id="register-form" method="post" action="">
        <?php wp_nonce_field('register_action', 'register_nonce'); ?>
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo isset($username) ? esc_attr($username) : ''; ?>" required
                class="<?php echo isset($username_error) ? 'input-error' : ''; ?>">
            <?php if (isset($username_error)): ?>
                <div class="error-text"><?php echo $username_error; ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo isset($email) ? esc_attr($email) : ''; ?>" required
                class="<?php echo isset($email_error) ? 'input-error' : ''; ?>">
            <?php if (isset($email_error)): ?>
                <div class="error-text"><?php echo $email_error; ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required
                class="<?php echo isset($password_error) ? 'input-error' : ''; ?>">
            <div class="password-requirements">Password must be at least 8 characters long</div>
            <?php if (isset($password_error)): ?>
                <div class="error-text"><?php echo $password_error; ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required
                class="<?php echo isset($confirm_password_error) ? 'input-error' : ''; ?>">
            <?php if (isset($confirm_password_error)): ?>
                <div class="error-text"><?php echo $confirm_password_error; ?></div>
            <?php endif; ?>
        </div>
        
        <button type="submit" name="register_submit" class="btn">Create Account</button>
        
        <div class="form-footer">
            <p>Already have an account? <a href="<?php echo site_url('/login'); ?>">Login</a></p>
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
        position: relative;
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
    
    .input-error {
        border-color: var(--error-color) !important;
    }
    
    .error-text {
        color: var(--error-color);
        font-size: 14px;
        margin-top: 5px;
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
    
    .password-requirements {
        font-size: 14px;
        color: #666;
        margin-top: 5px;
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
        const registerForm = document.getElementById('register-form');
        const errorMessage = document.getElementById('js-error-message');
        
        registerForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Reset error message
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            
            // Basic validation
            if (username === '') {
                e.preventDefault();
                errorMessage.textContent = 'Please enter a username.';
                errorMessage.style.display = 'block';
                return;
            }
            
            if (email === '') {
                e.preventDefault();
                errorMessage.textContent = 'Please enter your email address.';
                errorMessage.style.display = 'block';
                return;
            }
            
            // Email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                errorMessage.textContent = 'Please enter a valid email address.';
                errorMessage.style.display = 'block';
                return;
            }
            
            if (password === '') {
                e.preventDefault();
                errorMessage.textContent = 'Please enter a password.';
                errorMessage.style.display = 'block';
                return;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                errorMessage.textContent = 'Password must be at least 8 characters long.';
                errorMessage.style.display = 'block';
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                errorMessage.textContent = 'Passwords do not match.';
                errorMessage.style.display = 'block';
                return;
            }
        });
    });
</script>

<?php get_footer(); ?>