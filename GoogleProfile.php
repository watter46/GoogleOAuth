<?php
require_once 'vendor/autoload.php';

//Todo
//g_csrf_token調べる
//setPrompt 調べる
final class GoogleProfile {
    private readonly Google\Client $client;

    final public function __construct()
    {
        $this->client = $this->getClient();
    }

    /* GoogleOAuth2を使用する設定 */
    private function getClient(): Google\Client
    {
        $client = new Google\Client();
        $client->setAuthConfig('./client_secrets.json');
        $client->setRedirectUri('http://localhost/redirect');
        $client->addScope("https://www.googleapis.com/auth/userinfo.email");
        $client->addScope("https://www.googleapis.com/auth/userinfo.profile");
        $state = bin2hex(random_bytes(128/8));
        $client->setState($state);

        return $client;
    }

    /* GoogleUserIdを取得する */
    final public function fetchGoogleUserId()
    {
        if (isset($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $this->client->setAccessToken($token['access_token']);
        
            $id_token = $token['id_token'];
            $payload = $this->client->verifyIdToken($id_token);
        
            /*
            * Document (payload)
            *
            * $payload['sub'] : (Googleユーザー固有の値)
            *
            * https://developers.google.com/identity/openid-connect/openid-connect#php_1
            */
            if ($payload) {
                $google_user_id = $payload['sub'];
        
                // return $google_user_id;
                return $payload;
            }
            
            if (!$payload) {
                $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/login';
                header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
                exit;
            }
        }
        
        if (empty($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            exit;
        }
    }
}