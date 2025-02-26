<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_ai_chatbot_settings');
        do_settings_sections('wp_ai_chatbot_settings');
        ?>
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">API Key</th>
                <td>
                    <input type="password" name="wp_ai_chatbot_api_key" value="<?php echo esc_attr(get_option('wp_ai_chatbot_api_key')); ?>" class="regular-text" />
                    <p class="description">Enter your OpenAI API key. You can get one from <a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com/api-keys</a></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">AI Model</th>
                <td>
                    <select name="wp_ai_chatbot_model">
                        <?php
                        $selected_model = get_option('wp_ai_chatbot_model', 'gpt-3.5-turbo');
                        $models = array(
                            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                            'gpt-4' => 'GPT-4',
                            'gpt-4-turbo' => 'GPT-4 Turbo'
                        );
                        
                        foreach ($models as $model_id => $model_name) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($model_id),
                                selected($selected_model, $model_id, false),
                                esc_html($model_name)
                            );
                        }
                        ?>
                    </select>
                    <p class="description">Select the AI model to use for your chatbot.</p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Welcome Message</th>
                <td>
                    <textarea name="wp_ai_chatbot_welcome_message" rows="3" class="regular-text"><?php echo esc_textarea(get_option('wp_ai_chatbot_welcome_message', 'Hi! How can I help you today?')); ?></textarea>
                    <p class="description">The welcome message displayed when the chatbot loads.</p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Chatbot Position</th>
                <td>
                    <select name="wp_ai_chatbot_position">
                        <?php
                        $selected_position = get_option('wp_ai_chatbot_position', 'bottom-right');
                        $positions = array(
                            'bottom-right' => 'Bottom Right',
                            'bottom-left' => 'Bottom Left',
                            'shortcode-only' => 'Shortcode Only'
                        );
                        
                        foreach ($positions as $position_id => $position_name) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($position_id),
                                selected($selected_position, $position_id, false),
                                esc_html($position_name)
                            );
                        }
                        ?>
                    </select>
                    <p class="description">Choose where to display the chatbot. Select "Shortcode Only" to use the [wp_ai_chatbot] shortcode.</p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="wp-ai-chatbot-info">
        <h2>Shortcode Usage</h2>
        <p>You can use the <code>[wp_ai_chatbot]</code> shortcode to display the chatbot in posts or pages.</p>
        
        <h2>Getting Started</h2>
        <ol>
            <li>Enter your OpenAI API key above</li>
            <li>Choose the AI model you want to use</li>
            <li>Customize the welcome message</li>
            <li>Select where to display the chatbot</li>
            <li>Save your settings</li>
        </ol>
    </div>
</div>