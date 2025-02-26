(function($) {
    'use strict';

    // Initialize the chatbot when the DOM is ready
    $(document).ready(function() {
        // DOM elements
        const $chatbot = $('#wp-ai-chatbot');
        const $messages = $('#wp-ai-chatbot-messages');
        const $input = $('#wp-ai-chatbot-input');
        const $form = $('#wp-ai-chatbot-form');
        const $openButton = $('#wp-ai-chatbot-toggle-open');
        const $closeButton = $('#wp-ai-chatbot-toggle-close');
        
        // Only initialize if the chatbot container exists
        if ($chatbot.length === 0 || $messages.length === 0) {
            console.log('WP AI Chatbot: Chat container not found on this page');
            return;
        }
        
        // Apply position class
        const position = wpAiChatbot.position || 'bottom-right';
        $('body').addClass('wp-ai-chatbot-position-' + position);
        
        // Show welcome message
        addMessage(wpAiChatbot.welcome_message, 'bot');
        
        // Event listeners
        if ($openButton.length > 0) {
            $openButton.on('click', function() {
                $chatbot.addClass('is-active');
                $openButton.hide();
            });
        }
        
        if ($closeButton.length > 0) {
            $closeButton.on('click', function() {
                $chatbot.removeClass('is-active');
                if ($openButton.length > 0) {
                    $openButton.show();
                }
            });
        }
        
        if ($form.length > 0 && $input.length > 0) {
            $form.on('submit', function(e) {
                e.preventDefault();
                
                const message = $input.val().trim();
                if (!message) return;
            
                // Add user message to chat
                addMessage(message, 'user');
                
                // Clear input
                $input.val('');
                
                // Show typing indicator
                showTypingIndicator();
            
                // Send message to server
                $.ajax({
                    url: wpAiChatbot.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'wp_ai_chatbot_send_message',
                        message: message,
                        nonce: wpAiChatbot.nonce
                    },
                    success: function(response) {
                        // Hide typing indicator
                        hideTypingIndicator();
                        
                        if (response.success) {
                            // Add bot response to chat
                            addMessage(response.data.reply, 'bot');
                        } else {
                            // Show error message
                            addMessage('Sorry, I encountered an error: ' + response.data, 'bot');
                        }
                        
                        // Scroll to bottom
                        scrollToBottom();
                    },
                    error: function() {
                        // Hide typing indicator
                        hideTypingIndicator();
                        
                        // Show error message
                        addMessage('Sorry, there was an error communicating with the server.', 'bot');
                        
                        // Scroll to bottom
                        scrollToBottom();
                    }
                });
        });
        
        // Helper functions
        function addMessage(text, sender) {
            const $message = $('<div class="wp-ai-chatbot-message"></div>')
                .addClass('wp-ai-chatbot-message-' + sender)
                .text(text);
            
            $messages.append($message);
            scrollToBottom();
        }
        
        function scrollToBottom() {
            const $body = $('.wp-ai-chatbot-body');
            if ($body.length > 0 && $body[0]) {
                $body.scrollTop($body[0].scrollHeight);
            }
        }
        
        function showTypingIndicator() {
            const $typing = $('<div class="wp-ai-chatbot-typing wp-ai-chatbot-message-bot"></div>');
            for (let i = 0; i < 3; i++) {
                $typing.append('<span class="wp-ai-chatbot-typing-dot"></span>');
            }
            
            $messages.append($typing);
            scrollToBottom();
        }
        
        function hideTypingIndicator() {
            $('.wp-ai-chatbot-typing').remove();
        }
    });

})(jQuery);