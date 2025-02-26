<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wp-ai-chatbot-container" id="wp-ai-chatbot">
    <div class="wp-ai-chatbot-header">
        <h3 class="wp-ai-chatbot-title">Chat Support</h3>
        <button type="button" class="wp-ai-chatbot-toggle" id="wp-ai-chatbot-toggle-close">
            <span class="dashicons dashicons-no"></span>
        </button>
    </div>
    
    <div class="wp-ai-chatbot-body">
        <div class="wp-ai-chatbot-messages" id="wp-ai-chatbot-messages">
            <!-- Messages will be displayed here -->
        </div>
    </div>
    
    <div class="wp-ai-chatbot-footer">
        <form id="wp-ai-chatbot-form">
            <input type="text" id="wp-ai-chatbot-input" placeholder="Type your message..." />
            <button type="submit" id="wp-ai-chatbot-submit">
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </button>
        </form>
    </div>
</div>

<?php if (get_option('wp_ai_chatbot_position') !== 'shortcode-only'): ?>
<button type="button" class="wp-ai-chatbot-bubble" id="wp-ai-chatbot-toggle-open">
    <span class="dashicons dashicons-format-chat"></span>
</button>
<?php endif; ?>