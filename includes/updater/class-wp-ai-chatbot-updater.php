<?php
/**
 * Plugin Updater Class
 *
 * Handle plugin updates from GitHub.
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class WP_AI_Chatbot_Updater {

    /**
     * GitHub username
     * 
     * @var string
     */
    private $username;

    /**
     * GitHub repository name
     * 
     * @var string
     */
    private $repository;

    /**
     * GitHub access token
     * 
     * @var string
     */
    private $access_token;

    /**
     * Plugin data
     * 
     * @var array
     */
    private $plugin_data;

    /**
     * Plugin file
     * 
     * @var string
     */
    private $plugin_file;

    /**
     * Plugin slug
     * 
     * @var string
     */
    private $plugin_slug;

    /**
     * Constructor
     * 
     * @param string $plugin_file Main plugin file path
     * @param string $github_username GitHub username
     * @param string $github_repository GitHub repository name
     * @param string $access_token GitHub access token (optional)
     */
    public function __construct($plugin_file, $github_username, $github_repository, $access_token = '') {
        $this->plugin_file = $plugin_file;
        $this->username = $github_username;
        $this->repository = $github_repository;
        $this->access_token = $access_token;

        // Get plugin data
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $this->plugin_data = get_plugin_data($this->plugin_file);
        $this->plugin_slug = plugin_basename($this->plugin_file);

        // Hook into the update check
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'post_install'), 10, 3);
    }

    /**
     * Check for plugin updates
     * 
     * @param object $transient Update transient
     * @return object Modified update transient
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        // Get latest release from GitHub
        $latest_release = $this->get_latest_release();
        if (empty($latest_release)) {
            return $transient;
        }

        // Compare versions
        $current_version = $this->plugin_data['Version'];
        $latest_version = ltrim($latest_release['tag_name'], 'v');

        if (version_compare($latest_version, $current_version, '>')) {
            $download_url = $this->get_download_url($latest_release);
            
            if ($download_url) {
                $obj = new stdClass();
                $obj->slug = $this->plugin_slug;
                $obj->new_version = $latest_version;
                $obj->url = $this->plugin_data['PluginURI'];
                $obj->package = $download_url;
                $obj->tested = isset($latest_release['tested']) ? $latest_release['tested'] : '';
                $obj->requires_php = isset($latest_release['requires_php']) ? $latest_release['requires_php'] : '';
                $obj->icons = array(
                    '1x' => isset($this->plugin_data['icons']['1x']) ? $this->plugin_data['icons']['1x'] : '',
                    '2x' => isset($this->plugin_data['icons']['2x']) ? $this->plugin_data['icons']['2x'] : '',
                );
                
                $transient->response[$this->plugin_slug] = $obj;
            }
        }

        return $transient;
    }

    /**
     * Get plugin information for the update information screen
     * 
     * @param object $result Result object
     * @param string $action Action being performed
     * @param object $args Arguments passed to the function
     * @return object Plugin information
     */
    public function plugin_info($result, $action, $args) {
        // Check if this is our plugin
        if ('plugin_information' !== $action || !isset($args->slug) || $args->slug !== dirname($this->plugin_slug)) {
            return $result;
        }

        // Get latest release from GitHub
        $latest_release = $this->get_latest_release();
        if (empty($latest_release)) {
            return $result;
        }

        $plugin_info = new stdClass();
        $plugin_info->name = $this->plugin_data['Name'];
        $plugin_info->slug = dirname($this->plugin_slug);
        $plugin_info->version = ltrim($latest_release['tag_name'], 'v');
        $plugin_info->author = $this->plugin_data['Author'];
        $plugin_info->homepage = $this->plugin_data['PluginURI'];
        $plugin_info->requires = isset($latest_release['requires']) ? $latest_release['requires'] : '';
        $plugin_info->tested = isset($latest_release['tested']) ? $latest_release['tested'] : '';
        $plugin_info->requires_php = isset($latest_release['requires_php']) ? $latest_release['requires_php'] : '';
        $plugin_info->downloaded = 0;
        $plugin_info->last_updated = isset($latest_release['published_at']) ? date('Y-m-d', strtotime($latest_release['published_at'])) : '';
        $plugin_info->sections = array(
            'description' => $this->plugin_data['Description'],
            'changelog' => isset($latest_release['body']) ? $this->format_markdown($latest_release['body']) : '',
        );
        $plugin_info->download_link = $this->get_download_url($latest_release);

        return $plugin_info;
    }

    /**
     * Post-installation actions
     * 
     * @param bool $true Always true
     * @param array $hook_extra Extra arguments passed to the function
     * @param array $result Installation result data
     * @return array Modified installation result data
     */
    public function post_install($true, $hook_extra, $result) {
        // Get plugin directory
        $plugin_dir = WP_PLUGIN_DIR . '/' . dirname($this->plugin_slug);
        
        // Move files to the plugin directory
        $this->move_directory($result['destination'], $plugin_dir);
        
        // Update result destination
        $result['destination'] = $plugin_dir;
        
        // Activate the plugin
        if (is_plugin_inactive($this->plugin_slug)) {
            $activate = activate_plugin($this->plugin_slug);
        }
        
        return $result;
    }

    /**
     * Get the latest release from GitHub
     * 
     * @return array|bool Latest release data or false on failure
     */
    private function get_latest_release() {
        // Build the API URL
        $url = "https://api.github.com/repos/{$this->username}/{$this->repository}/releases/latest";
        
        // Add access token if available
        $options = array();
        if (!empty($this->access_token)) {
            $options['headers'] = array(
                'Authorization' => "token {$this->access_token}",
            );
        }
        
        // Make the request
        $response = wp_remote_get($url, $options);
        
        // Check for errors
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return false;
        }
        
        // Decode the response
        $release_data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (empty($release_data)) {
            return false;
        }
        
        return $release_data;
    }

    /**
     * Get the download URL for the latest release
     * 
     * @param array $release_data Release data
     * @return string|bool Download URL or false on failure
     */
    private function get_download_url($release_data) {
        if (empty($release_data) || empty($release_data['assets'])) {
            return false;
        }
        
        // Look for a ZIP file in the assets
        foreach ($release_data['assets'] as $asset) {
            if (isset($asset['content_type']) && 'application/zip' === $asset['content_type']) {
                return $asset['browser_download_url'];
            }
        }
        
        // If no ZIP found in assets, use the source code ZIP
        if (isset($release_data['zipball_url'])) {
            return $release_data['zipball_url'];
        }
        
        return false;
    }

    /**
     * Format markdown to HTML
     * 
     * @param string $markdown Markdown text
     * @return string HTML
     */
    private function format_markdown($markdown) {
        if (!class_exists('Parsedown')) {
            // Simple transformation for changelog
            $html = nl2br(esc_html($markdown));
            $html = preg_replace('/\*\*(.*?)\*\*/i', '<strong>$1</strong>', $html);
            $html = preg_replace('/\*(.*?)\*/i', '<em>$1</em>', $html);
            $html = preg_replace('/`(.*?)`/i', '<code>$1</code>', $html);
            return $html;
        } else {
            $parsedown = new Parsedown();
            return $parsedown->text($markdown);
        }
    }

    /**
     * Move a directory
     * 
     * @param string $source Source directory
     * @param string $destination Destination directory
     * @return bool Success or failure
     */
    private function move_directory($source, $destination) {
        // Check if the source directory exists
        if (!is_dir($source)) {
            return false;
        }
        
        // Create the destination directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        // Get all files and directories from the source
        $files = scandir($source);
        
        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }
            
            $src = $source . '/' . $file;
            $dest = $destination . '/' . $file;
            
            if (is_dir($src)) {
                // Recursively move directories
                $this->move_directory($src, $dest);
            } else {
                // Move files
                copy($src, $dest);
            }
        }
        
        // Remove the source directory
        $this->remove_directory($source);
        
        return true;
    }

    /**
     * Remove a directory
     * 
     * @param string $directory Directory to remove
     * @return bool Success or failure
     */
    private function remove_directory($directory) {
        if (!is_dir($directory)) {
            return false;
        }
        
        $files = scandir($directory);
        
        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }
            
            $path = $directory . '/' . $file;
            
            if (is_dir($path)) {
                $this->remove_directory($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($directory);
        
        return true;
    }
}