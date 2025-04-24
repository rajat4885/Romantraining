<?php
/**
 * Template Name: Course Dashboard
 */

// Redirect if user is not logged in
if (!is_user_logged_in()) {
    wp_redirect(site_url('/login'));
    exit;
}

get_header();
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo esc_html(wp_get_current_user()->display_name); ?>!</h1>
        <p>Browse available courses below</p>
    </div>

    <div class="courses-container">
        <?php
// Add this code temporarily to debug API issues
$response = wp_remote_post('https://api.videotilehost.com/courses', array(
    'headers' => array(
        'Content-Type' => 'application/json',
    ),
    'body' => json_encode(array(
        'vendor_id' => 'romantrainingandjobs',
    )),
    'timeout' => 30, // Increase timeout for slow connections
));

if (is_wp_error($response)) {
    // Handle API error
    echo '<div class="api-error">';
    echo '<h3>Error fetching courses</h3>';
    echo '<p>' . esc_html($response->get_error_message()) . '</p>';
    echo '</div>';
} else {
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Check if we have valid data
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '<div class="api-error">';
        echo '<h3>Error parsing course data</h3>';
        echo '<p>The API returned invalid data. Please try again later.</p>';
        echo '</div>';
    } else if (empty($data)) {
        echo '<div class="api-error">';
        echo '<h3>No courses available</h3>';
        echo '<p>There are currently no courses available. Please check back later.</p>';
        echo '</div>';
    } else {
        // Display courses in card format
        foreach ($data as $course) {
            ?>
            <div class="course-card">
                <div class="course-content">
                    <h3 class="course-title"><?php echo esc_html($course['name'] ?? 'Untitled Course'); ?></h3>
                    
                    <?php if (isset($course['description']) && !empty($course['description'])) : ?>
                        <div class="course-description">
                            <?php echo wp_trim_words(esc_html($course['description']), 20, '...'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($course['duration']) && !empty($course['duration'])) : ?>
                        <div class="course-duration">
                            <span class="duration-label">Duration:</span> <?php echo esc_html($course['duration']) . ' minutes'; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($course['rrp']) && !empty($course['rrp'])) : ?>
                        <div class="course-price">
                            <span class="price-label">Price:</span> £<?php echo esc_html($course['rrp']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
}
?>
    </div>
</div>

<style>
    /* Dashboard Styles */
    .dashboard-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .dashboard-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .dashboard-header h1 {
        color: #4a6bef;
        margin-bottom: 10px;
    }
    
    .courses-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }
    
    .course-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    
    .course-image {
        height: 180px;
        overflow: hidden;
        background-color: #f5f5f5;
    }
    
    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .course-image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #eaeaea;
    }
    
    .placeholder-text {
        color: #888;
        font-style: italic;
    }
    
    .course-content {
        padding: 20px;
    }
    
    .course-title {
        margin-top: 0;
        margin-bottom: 15px;
        color: #333;
        font-size: 18px;
        line-height: 1.4;
    }
    
    .course-description {
        color: #666;
        margin-bottom: 15px;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .course-duration, .course-price {
        font-size: 14px;
        color: #555;
        margin-bottom: 8px;
    }
    
    .duration-label, .price-label {
        font-weight: 600;
    }
    
    .course-button {
        display: inline-block;
        background-color: #4a6bef;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 4px;
        margin-top: 15px;
        font-size: 14px;
        font-weight: 500;
        transition: background-color 0.3s;
    }
    
    .course-button:hover {
        background-color: #3a5bd9;
        color: white;
    }
    
    .api-error {
        grid-column: 1 / -1;
        background-color: rgba(231, 76, 60, 0.1);
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }
    
    .api-error h3 {
        color: #e74c3c;
        margin-top: 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .courses-container {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }
    
    @media (max-width: 480px) {
        .courses-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php get_footer(); ?>