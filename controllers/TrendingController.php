<?php


namespace app\controllers;


use app\core\Controller;

class TrendingController extends Controller
{
    public function trending()
    {

        \Application::$app->checkShopifyToken();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.spotify.com/v1/playlists/37i9dQZEVXbKj23U1GF4IR/tracks?limits=20',
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
        $decoded = json_decode($response, true);


       // echo "<pre>";
       // var_dump($decoded);
       // echo "</pre>";




        $params = [
           'songs' => $decoded

        ];
       return $this->render('trending', $params);
    }

    public function pasthits()
    {
        \Application::$app->checkShopifyToken();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.spotify.com/v1/playlists/37i9dQZF1DX8Uebhn9wzrS/tracks?limits=10',
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
        $decoded = json_decode($response, true);


       // echo "<pre>";
       // var_dump($decoded);
       // echo "</pre>";

        $curl1 = curl_init();

        curl_setopt_array($curl1, array(
            CURLOPT_URL => 'https://api.spotify.com/v1/playlists/37i9dQZF1DWZeKCadgRdKQ/tracks?limits=10',
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

        $response = curl_exec($curl1);
        curl_close($curl1);
        $decoded1 = json_decode($response, true);



        $curl2 = curl_init();

        curl_setopt_array($curl2, array(
            CURLOPT_URL => 'https://api.spotify.com/v1/playlists/37i9dQZF1DX692WcMwL2yW/tracks?limits=10',
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

        $response = curl_exec($curl2);
        curl_close($curl2);
        $decoded2 = json_decode($response, true);

        $curl3 = curl_init();

        curl_setopt_array($curl3, array(
            CURLOPT_URL => 'https://api.spotify.com/v1/playlists/37i9dQZF1DWXLeA8Omikj7/tracks?limits=10',
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

        $response = curl_exec($curl3);
        curl_close($curl3);
        $decoded3 = json_decode($response, true);


        $params = array();
        $params[] = array('title' => 'Lofi Study Beats',
                           'order' => '1',
                           'songs' => $decoded);
        $params[] = array('title' => 'Deep Focus Music',
            'order' => '2',
            'songs' => $decoded1);
        $params[] = array('title' => 'All Nighter Music',
            'order' => '3',
            'songs' => $decoded2);
        $params[] = array('title' => 'Brain Food',
            'order' => '4',
            'songs' => $decoded3);


//        $params = [
//            'songs' => $decoded, //LOFI STUDY BEATS
//            'songs1' => $decoded1, //DEEP FOCUS MUSIC
//            'songs2' => $decoded2, //ALL NIGHTER MUSIC
//            'songs3' => $decoded3 // BRAIN FOOD
//        ];

        return $this->render('pasthits', $params);
    }
}