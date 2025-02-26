<?php

class WP_AI_Chatbot {

    public function run() {
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add shortcode
        add_shortcode('wp_ai_chatbot', array($this, 'chatbot_shortcode'));
        
        // Register AJAX handlers
        add_action('wp_ajax_wp_ai_chatbot_send_message', array($this, 'handle_message'));
        add_action('wp_ajax_nopriv_wp_ai_chatbot_send_message', array($this, 'handle_message'));
    }

    public static function activate() {
        // Activation tasks
    }

    public static function deactivate() {
        // Deactivation tasks
    }

    public function add_admin_menu() {
        add_options_page(
            'WP AI Chatbot Settings',
            'WP AI Chatbot',
            'manage_options',
            'wp-ai-chatbot',
            array($this, 'display_settings_page')
        );
    }

    public function register_settings() {
        register_setting('wp_ai_chatbot_settings', 'wp_ai_chatbot_api_key');
        register_setting('wp_ai_chatbot_settings', 'wp_ai_chatbot_model');
        register_setting('wp_ai_chatbot_settings', 'wp_ai_chatbot_welcome_message');
        register_setting('wp_ai_chatbot_settings', 'wp_ai_chatbot_position');
    }

    public function display_settings_page() {
        require_once WP_AI_CHATBOT_PATH . 'includes/admin-settings.php';
    }

    public function enqueue_scripts() {
        // Enqueue dashicons
        wp_enqueue_style('dashicons');
        
        wp_enqueue_style(
            'wp-ai-chatbot-style',
            WP_AI_CHATBOT_URL . 'assets/css/chatbot.css',
            array('dashicons'),
            WP_AI_CHATBOT_VERSION
        );
        
        wp_enqueue_script(
            'wp-ai-chatbot-script',
            WP_AI_CHATBOT_URL . 'assets/js/chatbot.js',
            array('jquery'),
            WP_AI_CHATBOT_VERSION,
            true
        );
        
        wp_localize_script(
            'wp-ai-chatbot-script',
            'wpAiChatbot',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_ai_chatbot_nonce'),
                'welcome_message' => get_option('wp_ai_chatbot_welcome_message', 'Hi! How can I help you today?'),
                'position' => get_option('wp_ai_chatbot_position', 'bottom-right')
            )
        );
    }

    public function chatbot_shortcode($atts) {
        ob_start();
        include WP_AI_CHATBOT_PATH . 'includes/chatbot-template.php';
        return ob_get_clean();
    }

    public function handle_message() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_ai_chatbot_nonce')) {
            wp_send_json_error('Invalid security token');
        }

        $message = sanitize_text_field($_POST['message']);
        
        // Get API key and model from settings
        $api_key = get_option('wp_ai_chatbot_api_key');
        $model = get_option('wp_ai_chatbot_model', 'gpt-3.5-turbo');
        
        if (empty($api_key)) {
            wp_send_json_error('API key not configured');
        }
        
        // Call OpenAI API
        $response = $this->call_openai_api($message, $api_key, $model);
        
        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        }
        
        wp_send_json_success(array(
            'reply' => $response
        ));
    }

    private function call_openai_api($message, $api_key, $model) {
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $headers = array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        );
        
        $body = array(
            'model' => $model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => 'You are a helpful assistant on a WordPress website.'
                ),
                array(
                    'role' => 'user',
                    'content' => $message
                )
            ),
            'max_tokens' => 150,
            'temperature' => 0.7
        );
        
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 60
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('openai_error', $body['error']['message']);
        }
        
        return $body['choices'][0]['message']['content'];
    }
}