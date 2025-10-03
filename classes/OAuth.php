<?php
/**
 * OAuth Class
 * Handles social login authentication with various providers
 */

require_once __DIR__ . '/../config/database.php';

class OAuth {
    private $conn;
    private $providers;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->loadProviders();
    }
    
    private function loadProviders() {
        $query = "SELECT * FROM social_login_providers WHERE is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $this->providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProvider($providerName) {
        foreach ($this->providers as $provider) {
            if ($provider['provider_name'] === $providerName) {
                return $provider;
            }
        }
        return null;
    }
    
    public function handleGoogleLogin() {
        $provider = $this->getProvider('google');
        if (!$provider) {
            throw new Exception('Google login is not configured');
        }
        
        // Redirect to Google OAuth
        $redirectUri = urlencode($provider['redirect_uri']);
        $clientId = $provider['client_id'];
        $scope = urlencode('email profile');
        
        $authUrl = "https://accounts.google.com/o/oauth2/auth?client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&response_type=code&access_type=online";
        
        header("Location: {$authUrl}");
        exit;
    }
    
    public function handleFacebookLogin() {
        $provider = $this->getProvider('facebook');
        if (!$provider) {
            throw new Exception('Facebook login is not configured');
        }
        
        // Redirect to Facebook OAuth
        $redirectUri = urlencode($provider['redirect_uri']);
        $clientId = $provider['client_id'];
        $scope = urlencode('email,public_profile');
        
        $authUrl = "https://www.facebook.com/v18.0/dialog/oauth?client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}";
        
        header("Location: {$authUrl}");
        exit;
    }
    
    public function handleTwitterLogin() {
        $provider = $this->getProvider('twitter');
        if (!$provider) {
            throw new Exception('Twitter login is not configured');
        }
        
        // Redirect to Twitter OAuth
        $redirectUri = urlencode($provider['redirect_uri']);
        $clientId = $provider['client_id'];
        
        $authUrl = "https://twitter.com/i/oauth2/authorize?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&scope=users.read%20tweet.read&state=state&code_challenge=challenge&code_challenge_method=plain";
        
        header("Location: {$authUrl}");
        exit;
    }
    
    public function handleYahooLogin() {
        $provider = $this->getProvider('yahoo');
        if (!$provider) {
            throw new Exception('Yahoo login is not configured');
        }
        
        // Redirect to Yahoo OAuth
        $redirectUri = urlencode($provider['redirect_uri']);
        $clientId = $provider['client_id'];
        $scope = urlencode('openid email profile');
        
        $authUrl = "https://api.login.yahoo.com/oauth2/request_auth?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope={$scope}&nonce=" . time();
        
        header("Location: {$authUrl}");
        exit;
    }
    
    public function processGoogleCallback($code) {
        $provider = $this->getProvider('google');
        if (!$provider) {
            throw new Exception('Google login is not configured');
        }
        
        // Exchange code for access token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $data = [
            'client_id' => $provider['client_id'],
            'client_secret' => $provider['client_secret'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => $provider['redirect_uri'],
            'code' => $code
        ];
        
        $response = $this->makePostRequest($tokenUrl, $data);
        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['error'])) {
            throw new Exception('Error getting access token: ' . $tokenData['error_description']);
        }
        
        // Get user info
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $headers = [
            'Authorization: Bearer ' . $tokenData['access_token']
        ];
        
        $userInfoResponse = $this->makeGetRequest($userInfoUrl, $headers);
        $userInfo = json_decode($userInfoResponse, true);
        
        if (isset($userInfo['error'])) {
            throw new Exception('Error getting user info: ' . $userInfo['error']['message']);
        }
        
        return $this->processSocialUser('google', $userInfo, $tokenData);
    }
    
    public function processFacebookCallback($code) {
        $provider = $this->getProvider('facebook');
        if (!$provider) {
            throw new Exception('Facebook login is not configured');
        }
        
        // Exchange code for access token
        $tokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token';
        $data = [
            'client_id' => $provider['client_id'],
            'client_secret' => $provider['client_secret'],
            'redirect_uri' => $provider['redirect_uri'],
            'code' => $code
        ];
        
        $response = $this->makeGetRequest($tokenUrl, [], $data);
        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['error'])) {
            throw new Exception('Error getting access token: ' . $tokenData['error']['message']);
        }
        
        // Get user info
        $userInfoUrl = 'https://graph.facebook.com/v18.0/me?fields=id,name,email,first_name,last_name';
        $headers = [
            'Authorization: Bearer ' . $tokenData['access_token']
        ];
        
        $userInfoResponse = $this->makeGetRequest($userInfoUrl, $headers);
        $userInfo = json_decode($userInfoResponse, true);
        
        if (isset($userInfo['error'])) {
            throw new Exception('Error getting user info: ' . $userInfo['error']['message']);
        }
        
        return $this->processSocialUser('facebook', $userInfo, $tokenData);
    }
    
    public function processTwitterCallback($code) {
        $provider = $this->getProvider('twitter');
        if (!$provider) {
            throw new Exception('Twitter login is not configured');
        }
        
        // Exchange code for access token
        $tokenUrl = 'https://api.twitter.com/2/oauth2/token';
        $data = [
            'client_id' => $provider['client_id'],
            'client_secret' => $provider['client_secret'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => $provider['redirect_uri'],
            'code' => $code,
            'code_verifier' => 'challenge'
        ];
        
        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $response = $this->makePostRequest($tokenUrl, $data, $headers);
        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['error'])) {
            throw new Exception('Error getting access token: ' . $tokenData['error_description']);
        }
        
        // Get user info
        $userInfoUrl = 'https://api.twitter.com/2/users/me';
        $headers = [
            'Authorization: Bearer ' . $tokenData['access_token']
        ];
        
        $userInfoResponse = $this->makeGetRequest($userInfoUrl, $headers);
        $userInfo = json_decode($userInfoResponse, true);
        
        if (isset($userInfo['errors'])) {
            throw new Exception('Error getting user info: ' . $userInfo['errors'][0]['message']);
        }
        
        $userData = [
            'id' => $userInfo['data']['id'],
            'name' => $userInfo['data']['name'],
            'email' => $userInfo['data']['id'] . '@twitter.com' // Twitter doesn't provide email by default
        ];
        
        return $this->processSocialUser('twitter', $userData, $tokenData);
    }
    
    public function processYahooCallback($code) {
        $provider = $this->getProvider('yahoo');
        if (!$provider) {
            throw new Exception('Yahoo login is not configured');
        }
        
        // Exchange code for access token
        $tokenUrl = 'https://api.login.yahoo.com/oauth2/get_token';
        $data = [
            'client_id' => $provider['client_id'],
            'client_secret' => $provider['client_secret'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => $provider['redirect_uri'],
            'code' => $code
        ];
        
        $headers = [
            'Authorization: Basic ' . base64_encode($provider['client_id'] . ':' . $provider['client_secret']),
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $response = $this->makePostRequest($tokenUrl, $data, $headers);
        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['error'])) {
            throw new Exception('Error getting access token: ' . $tokenData['error_description']);
        }
        
        // Get user info
        $userInfoUrl = 'https://api.login.yahoo.com/openid/v1/userinfo';
        $headers = [
            'Authorization: Bearer ' . $tokenData['access_token']
        ];
        
        $userInfoResponse = $this->makeGetRequest($userInfoUrl, $headers);
        $userInfo = json_decode($userInfoResponse, true);
        
        if (isset($userInfo['error'])) {
            throw new Exception('Error getting user info: ' . $userInfo['error']);
        }
        
        return $this->processSocialUser('yahoo', $userInfo, $tokenData);
    }
    
    private function processSocialUser($providerName, $userInfo, $tokenData) {
        // Check if user already exists with this social account
        $query = "SELECT u.*, usa.provider_user_id 
                  FROM users u 
                  JOIN user_social_accounts usa ON u.id = usa.user_id 
                  JOIN social_login_providers slp ON usa.provider_id = slp.id 
                  WHERE slp.provider_name = :provider_name AND usa.provider_user_id = :provider_user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':provider_name', $providerName);
        $stmt->bindParam(':provider_user_id', $userInfo['id']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // User exists, update tokens and login
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->updateSocialAccount($user['id'], $providerName, $tokenData);
            return $user;
        }
        
        // Check if user exists with same email
        if (isset($userInfo['email'])) {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $userInfo['email']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // User exists with same email, link social account
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->linkSocialAccount($user['id'], $providerName, $userInfo['id'], $tokenData);
                return $user;
            }
        }
        
        // Create new user
        $userId = $this->createSocialUser($providerName, $userInfo);
        $this->linkSocialAccount($userId, $providerName, $userInfo['id'], $tokenData);
        
        // Get complete user data
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function createSocialUser($providerName, $userInfo) {
        // Parse name
        $firstName = '';
        $lastName = '';
        
        if (isset($userInfo['name'])) {
            $nameParts = explode(' ', $userInfo['name'], 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
        } elseif (isset($userInfo['first_name']) && isset($userInfo['last_name'])) {
            $firstName = $userInfo['first_name'];
            $lastName = $userInfo['last_name'];
        } else {
            $firstName = $providerName . '_user';
            $lastName = $userInfo['id'];
        }
        
        $email = isset($userInfo['email']) ? $userInfo['email'] : $userInfo['id'] . '@' . $providerName . '.com';
        $username = $this->generateUsername($firstName, $lastName);
        
        $query = "INSERT INTO users (username, email, first_name, last_name, user_type, status, email_verified) 
                  VALUES (:username, :email, :first_name, :last_name, 'customer', 'active', 1)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->execute();
        
        $userId = $this->conn->lastInsertId();
        
        // Create user profile
        $query = "INSERT INTO user_profiles (user_id, email_verified_at) VALUES (:user_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $userId;
    }
    
    private function linkSocialAccount($userId, $providerName, $providerUserId, $tokenData) {
        $query = "SELECT id FROM social_login_providers WHERE provider_name = :provider_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':provider_name', $providerName);
        $stmt->execute();
        $provider = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$provider) {
            throw new Exception("Provider {$providerName} not found");
        }
        
        $query = "INSERT INTO user_social_accounts (user_id, provider_id, provider_user_id, access_token, refresh_token, expires_at) 
                  VALUES (:user_id, :provider_id, :provider_user_id, :access_token, :refresh_token, :expires_at)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':provider_id', $provider['id']);
        $stmt->bindParam(':provider_user_id', $providerUserId);
        $stmt->bindParam(':access_token', $tokenData['access_token'] ?? null);
        $stmt->bindParam(':refresh_token', $tokenData['refresh_token'] ?? null);
        $expiresAt = isset($tokenData['expires_in']) ? date('Y-m-d H:i:s', time() + $tokenData['expires_in']) : null;
        $stmt->bindParam(':expires_at', $expiresAt);
        $stmt->execute();
    }
    
    private function updateSocialAccount($userId, $providerName, $tokenData) {
        $query = "SELECT usa.id FROM user_social_accounts usa 
                  JOIN social_login_providers slp ON usa.provider_id = slp.id 
                  WHERE usa.user_id = :user_id AND slp.provider_name = :provider_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':provider_name', $providerName);
        $stmt->execute();
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($account) {
            $query = "UPDATE user_social_accounts 
                      SET access_token = :access_token, refresh_token = :refresh_token, expires_at = :expires_at, updated_at = NOW()
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $account['id']);
            $stmt->bindParam(':access_token', $tokenData['access_token'] ?? null);
            $stmt->bindParam(':refresh_token', $tokenData['refresh_token'] ?? null);
            $expiresAt = isset($tokenData['expires_in']) ? date('Y-m-d H:i:s', time() + $tokenData['expires_in']) : null;
            $stmt->bindParam(':expires_at', $expiresAt);
            $stmt->execute();
        }
    }
    
    private function generateUsername($firstName, $lastName) {
        $baseUsername = strtolower(substr($firstName, 0, 1) . $lastName);
        $username = $baseUsername;
        $counter = 1;
        
        // Check if username exists and increment if needed
        while ($this->usernameExists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    private function usernameExists($username) {
        $query = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    private function makePostRequest($url, $data, $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    
    private function makeGetRequest($url, $headers = [], $params = []) {
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
}
?>