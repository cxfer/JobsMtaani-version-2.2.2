-- Migration 004: Add social login support
-- This migration adds tables and data for social login functionality

-- Create social login providers table
CREATE TABLE IF NOT EXISTS social_login_providers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    provider_name VARCHAR(50) NOT NULL UNIQUE,
    client_id VARCHAR(255) NOT NULL,
    client_secret VARCHAR(255) NOT NULL,
    redirect_uri VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create user social accounts table
CREATE TABLE IF NOT EXISTS user_social_accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    provider_id INT NOT NULL,
    provider_user_id VARCHAR(255) NOT NULL,
    access_token VARCHAR(500),
    refresh_token VARCHAR(500),
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES social_login_providers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_provider (user_id, provider_id),
    UNIQUE KEY unique_provider_user (provider_id, provider_user_id)
);

-- Add social login providers (initial configuration - you'll need to update with real credentials)
INSERT IGNORE INTO social_login_providers (provider_name, client_id, client_secret, redirect_uri, is_active) VALUES
('google', 'YOUR_GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_SECRET', 'http://localhost/login.php?provider=google', 1),
('facebook', 'YOUR_FACEBOOK_APP_ID', 'YOUR_FACEBOOK_APP_SECRET', 'http://localhost/login.php?provider=facebook', 1),
('twitter', 'YOUR_TWITTER_CLIENT_ID', 'YOUR_TWITTER_CLIENT_SECRET', 'http://localhost/login.php?provider=twitter', 1),
('yahoo', 'YOUR_YAHOO_CLIENT_ID', 'YOUR_YAHOO_CLIENT_SECRET', 'http://localhost/login.php?provider=yahoo', 1);

-- Add columns to users table for better login tracking
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS login_count INT DEFAULT 0;

-- Add columns to user_profiles for better verification tracking
ALTER TABLE user_profiles
ADD COLUMN IF NOT EXISTS email_verification_token VARCHAR(255),
ADD COLUMN IF NOT EXISTS email_verified_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS phone_verification_token VARCHAR(255),
ADD COLUMN IF NOT EXISTS phone_verified_at TIMESTAMP NULL;