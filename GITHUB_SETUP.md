# GitHub Setup Instructions

Follow these steps to set up your GitHub repository for automatic updates.

## 1. Create a GitHub repository

1. Go to [GitHub](https://github.com/) and log in to your account
2. Click the "+" button in the top right corner and select "New repository"
3. Name your repository `wp-ai-chatbot`
4. Add a description (optional)
5. Choose "Public" visibility
6. Initialize the repository with a README file
7. Click "Create repository"

## 2. Update plugin files

1. Files are already set up with your GitHub username (stevenayl)

2. Make sure the repository settings match this configuration:
   ```php
   // GitHub Plugin URI: stevenayl/wp-ai-chatbot
   // GitHub Branch: main
   // Update URI: stevenayl/wp-ai-chatbot
   ```
   
   ```php
   // Initialize GitHub updater
   $github_username = 'stevenayl'; // Your GitHub username
   $github_repo = 'wp-ai-chatbot'; // Your repository name
   ```

## 3. Push the code to GitHub

1. Open your terminal and navigate to the plugin directory
2. Initialize Git (if not already done): `git init`
3. Add the remote repository: `git remote add origin https://github.com/stevenayl/wp-ai-chatbot.git`
4. Add all files: `git add .`
5. Create your first commit: `git commit -m "Initial commit"`
6. Push to GitHub: `git push -u origin main`

## 4. Create your first release

1. Go to your repository on GitHub
2. Click on "Releases" in the right sidebar
3. Click "Create a new release"
4. Enter tag version: `v1.0.0` (must match the plugin version)
5. Enter release title: `WP AI Chatbot 1.0.0`
6. Add release notes (description of the initial release)
7. Publish the release

GitHub Actions will automatically create a zip file for your release that will be used for plugin updates.

## 5. Future updates

To release a new version:

1. Update the version number in `wp-ai-chatbot.php` in both the plugin header and the constant definition
2. Make your code changes
3. Commit and push to GitHub
4. Create a new GitHub release with a tag that matches your new version number (e.g., `v1.0.1`)

WordPress sites with your plugin installed will receive update notifications when new versions are released.