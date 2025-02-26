<?php
/**
 * Plugin Name: WP AI Chatbot
 * Description: A simple AI-powered chatbot for WordPress
 * Version: 1.0.0
 * Author: Claude
 * Text Domain: wp-ai-chatbot
 * GitHub Plugin URI: YOUR_GITHUB_USERNAME/wp-ai-chatbot
 * GitHub Branch: main
 * Update URI: YOUR_GITHUB_USERNAME/wp-ai-chatbot
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WP_AI_CHATBOT_VERSION', '1.0.0');
define('WP_AI_CHATBOT_PATH', plugin_dir_path(__FILE__));
define('WP_AI_CHATBOT_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WP_AI_CHATBOT_PATH . 'includes/class-wp-ai-chatbot.php';
require_once WP_AI_CHATBOT_PATH . 'includes/updater/class-wp-ai-chatbot-updater.php';

// Initialize GitHub updater
$github_username = 'YOUR_GITHUB_USERNAME'; // Replace with your GitHub username
$github_repo = 'wp-ai-chatbot'; // Replace with your repository name
$updater = new WP_AI_Chatbot_Updater(__FILE__, $github_username, $github_repo);

// Initialize the plugin
function run_wp_ai_chatbot() {
    $plugin = new WP_AI_Chatbot();
    $plugin->run();
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, array('WP_AI_Chatbot', 'activate'));
register_deactivation_hook(__FILE__, array('WP_AI_Chatbot', 'deactivate'));

// Run the plugin
run_wp_ai_chatbot();