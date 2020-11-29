<?php


namespace app\controllers;

use app\core\Controller;
use app\models\LoginModel;
use app\models\RegisterModel;
use Application;

class AuthController extends Controller
{

    public function register($request)
    {
        $registerModel = new RegisterModel();

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $registerModel->loadData($request->getBody());

            if ($registerModel->validate() && $registerModel->register())
            {
                Application::$app->session->setFlash('success', 'Thanks for registering!');
                Application::$app->response->redirect('/login');
                return 0;
            }

            return $this->render('register', [
                'model' => $registerModel
            ]);
        }

        // GET request
        // return register view
        $this->setLayout('auth');
        return $this->render('register', [
            'model' => $registerModel
        ]);
    }

    public function login($request, $response)
    {
        $loginForm = new LoginModel();
        if ($_POST)
        {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login())
            {
                $response->redirect('/');
                return 0;
            }
        }
        // GET request
        // return login view
        $this->setLayout('auth');
        return $this->render('login', [
            'model' => $loginForm
        ]);
    }

    public function logout($request, $response)
    {
        Application::$app->logout();
        $response->redirect('/');
    }

    public function profile($request, $response)
    {
        $loginForm = new LoginModel();
        if ($_POST)
        {
            // From Profile Data Form
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login())
            {
                $response->redirect('/');
                return 0;
            }
        }

        // GET request
        // return profile view
        $_SESSION['spotify_active'] = true;

        $params = array();
        $params['spotify_active'] = false;

        if (isset($_SESSION['spotify_active']) && $_SESSION['spotify_active'] == true)
        {
            $user_data = $this->getSpotifyProfile();
            if (isset($user_data['display_name']))
            {
                $params['spotify_active'] = false;
                $params['spotify_name'] = $user_data['display_name'];
                $params['spotify_followers'] = $user_data['followers']['total'];
                $params['spotify_product'] = ucfirst($user_data['product']);
                $params['spotify_account_url'] = $user_data['external_urls']['spotify'];
                $params['spotify_profile_img'] = $user_data['images'][0]['url'] ?? 'img/profile-picture.png';
            }
        }

        return $this->render('profile', $params);
    }


    // ===== ===== Spotify OAuth2 ===== =====

    public function spotifyAuth($request, $response)
    {
        $url      = "https://accounts.spotify.com/authorize";
        $client   = "client_id=" . Application::$app->config['client_id'];
        $resp     = "response_type=code";
        $redirect = "redirect_uri=" . Application::$app->config['client_redirect'];
        $scope    = "scope=user-read-private%20user-read-email%20user-top-read";
        $state    = "state=34fFs29kd09";

        return $response->redirect("$url?$client&$resp&$redirect&$scope&$state");
    }

    public function spotifyCallback($request, $response)
    {
        if (isset($_GET['error']) && trim($_GET['error']) == 'access_denied')
        {
            $response->redirect('/error');
        }

        $state = $_GET['state'] ?? '';
        if ($state != Application::$app->config['spotify_state'])
        {
            Application::$app->response->redirect('/error');
        }
        // TODO: Check state var
        $code = $_GET['code'] ?? '';
        $redirect = "redirect_uri=" . Application::$app->config['client_redirect'];

        $auth = base64_encode(Application::$app->config['client_id'] . ":" .  Application::$app->config['client_secret']);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://accounts.spotify.com/api/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=authorization_code&code=$code&$redirect",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . $auth,
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        // Save token to session to be available on all pages
        $arr_response = json_decode($response, true);

        if (isset($arr_response['access_token']))
        {
            // Save access token to session
            $_SESSION['access_token'] = $arr_response['access_token'];
            $_SESSION['access_token_time'] = time();
            $_SESSION['refresh_token'] = $arr_response['refresh_token'];
            $_SESSION['active'] = true;
            Application::$app->response->redirect('/spotify-connected');
            return;
            // TODO: On Application check bearer token, add case of logged in user
            // TODO: On logout, remove active session and other tokens
        }
        Application::$app->response->redirect('/error');
    }

    public function spotifyConnected($request, $response)
    {
        if (isset($_SESSION['active']) && isset($_SESSION['refresh_token']))
        {
            var_dump($_SESSION['access_token']);
            return $this->render('spotify-connected');
        }
        return $this->render('error');
    }

    private function getSpotifyProfile()
    {
        Application::$app->checkShopifyToken();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.spotify.com/v1/me',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $_SESSION['access_token']
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

}
