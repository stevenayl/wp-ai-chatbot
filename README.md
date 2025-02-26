# WP AI Chatbot

A simple AI-powered chatbot plugin for WordPress, powered by OpenAI's API.

## Features

- Easy to set up and configure
- Powered by OpenAI's GPT models
- Customizable welcome message
- Multiple display options (bottom right, bottom left, or via shortcode)
- Mobile-responsive design
- Real-time conversation with typing indicators
- Automatic updates from GitHub

## Installation

### Manual Installation
1. Download the latest release zip file from the [Releases page](https://github.com/YOUR_GITHUB_USERNAME/wp-ai-chatbot/releases)
2. In your WordPress admin, go to Plugins > Add New > Upload Plugin
3. Choose the zip file and click "Install Now"
4. Activate the plugin

### GitHub Updater Installation
If you have the [GitHub Updater](https://github.com/afragen/github-updater) plugin installed:

1. Go to Settings > GitHub Updater > Install Plugin
2. Enter `YOUR_GITHUB_USERNAME/wp-ai-chatbot` in the GitHub Repository field
3. Click "Install Plugin"
4. Activate the plugin

## Configuration

1. Go to Settings > WP AI Chatbot
2. Enter your OpenAI API key (you can get one from [OpenAI's API keys page](https://platform.openai.com/api-keys))
3. Choose your preferred AI model
4. Customize the welcome message
5. Select where to display the chatbot
6. Save your settings

## Usage

### Automatic Display

If you've selected "Bottom Right" or "Bottom Left" as the chatbot position, the chatbot bubble will automatically appear on all pages of your site.

### Shortcode

You can use the shortcode `[wp_ai_chatbot]` to display the chatbot in specific posts or pages.

Example:

```
This is my support page. Need help? Chat with our AI assistant below:

[wp_ai_chatbot]
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- OpenAI API key

## Updating

The plugin supports automatic updates directly from this GitHub repository. When a new version is released, you'll see an update notification in your WordPress admin dashboard.

## Support

If you need help with this plugin, please open an issue on GitHub.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Created by Claude, powered by OpenAI's API.