<?php

namespace App\Services;
use GuzzleHttp\Client;

class FCMService
{


     // Public property to store the service account key
     public $serviceAccountKey = [
            // "type"=>"service_account",
            // "project_id"=> "astro-deb9c",
            // "private_key_id"=> "424295cd451bd1049e555d4ed3c21d94c1142414",
            // "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDMGcqFXdDkbJyB\nB4SNp/s2Yfa3J7Uskk9dEpjA5hFcr7+WGsL3ERAhVJyrTTR7Zj4U1svUiZlBlQve\nrYovzMoTJ97SyerFbZH12mFzG2F1MyoyXNiSJ7m4b8jtiwUHZG6wnsQEmvGr3iNq\nU4PT98/fG66qWVKQ+LnC1ctOm1uSHHNP1T0/kPnPb/uiHK5nLFZ/hGcNV+hpVZSr\nBKWz7ig0vx/u2JRVoLVVwhuRCnbV3yuNA8hIiz8TRdOIoqakdwtLjYMXehdnbJGg\nXiAOQ4EGXy9c6o3dviaUdNp1Af+m2QWtOROmd12uHwIpwXID9A+vtuOxzDU31Pxx\nB2VGDdGbAgMBAAECggEAB7U1o8G+QNIlOu44wEzGvPAILDSQsAU6gA5SvH4zy6dL\nefS6xi/bxxZ18DYzNDHRSGIwQm+L3SEu2JL/M/zs6LpJvPYpKpmPuJQtt/YhwRZU\nkqBVAXgEmIWINghZAUV2tK7aQj5DwDTca4iJ4vhp3kgiWYaUCquKAGSoj+hoFeVx\n4nrTQyXUOuumHdhuFyYrgXzV1k2YwTNVr+ZRsEKmNbIwQUsaE/GvzdjL85cyUNk5\nm2P3eCFJGNDoGE1OY6oZZtMrVGmTVpeXCycflltU45eDurGWMI35m09BPaIaUY6X\nWzq2Vi/Tx5kaH3wRsrcJV2WRvCq+eVkgO+RtnZB6yQKBgQDtPj2JtPKFoHguPp0O\n9DlbyLvcHTBnvKMWPftXv/acqyDOePV5/tf3ROKzJA/zQTl2tR9X1vEQIDx/htxm\n6gkKFsUf8jqRCSzjNB7gkozG9te09cD2FkTu7UOA5pbWncqB96sPfCb7njat042Z\n+R4irKOFgujTXTGGMxiIVpY1TwKBgQDcPME0huLI7A73vuDBG9Sa+B5cEWpQs7i2\nJx003KjIbLgUEveLY1L07zHDM3Qplo59cTnj+EyVi7SFbU38mjYKNntqiFkZVL1+\nxCYj64jhRSt95fC3T+LpHfMg/UPBvVoiSugnCHlYtYSlyEVOL8oGaXwiHXYxVXTb\nMSRjtg0j9QKBgQCntAKeMHnredbaoco2Qu+08P3TCBMqkY8bbu8YRYtXjiSfr6Oe\n/EW/9kLmbUyVIPs07a3dUwSO6Kq7XyK34SJIrnXkMj+yPLEtXq+hAhdfoigzNDGW\nA4Hw/2ljWh0wUtRO8TtLs/B+l4WBdPU47X+e9TiWbUaV+t/CRKZH4iVGQwKBgQCX\n9yAFg5FWvHOzhTUGapKv/8bLFoU6fFhW7/RaNmwMJ+B4kYHX30gtlCLNI2LjE2zs\np8UfRYRqqAR/EKYAR9REBcXxA3YfYXaD0wrSPEKt1hGlhJUtl+Tln/HUcI1hUKux\n/+fijxlUGaQW1HLl+Vm4RO8Phy636dSBLo6CcursRQKBgAxzEm6FiAczA8DZeKJI\n9QYFHkJeVd/7+HMtxrW1gZKfN7p6RyFm3iCrtUPpTiwLZiCa9vlVg5wmXphLqdHQ\nDgAKnrd7JRkyRxBqZw+EGOddjwSpkv85/vN5Q2c2XZaiXBeASrVc6y02c9LIdaAs\nWxLniR39ZlFkpCJ8KZuniwfl\n-----END PRIVATE KEY-----\n",
            // "client_email"=> "firebase-adminsdk-nnrom@astroway-diploy.iam.gserviceaccount.com",
            // "client_id"=> "118243690510425066312",
            // "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
            // "token_uri"=> "https://oauth2.googleapis.com/token",
            // "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
            // "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-nnrom%40astroway-diploy.iam.gserviceaccount.com",
            // "universe_domain"=> "googleapis.com"

            "type" => "service_account",
            "project_id" => "astro-deb9c",
            "private_key_id" => "afd92242e26623665b6368ab91481f9b612b0e03",
            "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEuwIBADANBgkqhkiG9w0BAQEFAASCBKUwggShAgEAAoIBAQDmUzaJ30QLY54p\nCq8OH4+rgYkuNIUnWt6KumwTM1/LbGu10zc0ude0ZkJrh5lovMrjuZYeNqrGQb4V\n0b4Iiu1E+lsv7O+yEUO73LYXlSi3dMZyluqhexwbrUlfK0t2IFwuZjaab4FlYysC\nzBS/uu2kkSgWoHGW2NdjqOJIC7j9NdybO/Pbt4BI+BaPhyQgJFKuwF902GIUe6or\n3VlRWH7qQP0HO2cJZ7/LBDVZslqptVbayhwHd5qx2Z0SON31F5OOTSl5ltmZh09q\ntOIGVX63BkhYt57aXMivewE6syj3TQR5j9/hdmPae1UlQ1WMspY+csr8A+HwcGvp\nSgT6Gpd5AgMBAAECggEAA1famJ/n63yVOsNHHnvoP8aBkW0JAcCBXVlo1Uc9/LjT\nQCTCK0p7pZn5S2HplTucqqu4Z+w6fOY3ZNmvQqTY3kdykTbK2yrTiCOjRtFx/SBx\n4+CkL/Pca9vgySDaqQIa4BGKStCqzT9mKSckd2nNvZ6ArbwgpCRBnwURoNz/D39F\n9F7H5PE4JY/sXduFgMayL+Hm87IVKtNMThDDf2/x0TLBSTHwmg/UeVk9jZLEbueT\nUXfwG4a2F6WJC8Rvp0btGK5aVbxFXjYoIZemMYa3FFtACH854iInWt4+8hYmqKp7\nh32UlS25NGpAVlQaGQSbmS3fkHbDfMsec3w7s/fyAQKBgQD+omYwA1FVkUctC9tT\nizPFL5We7biazulILaPU+JzMHHtHHc2wdbYs+VKZfPD+gO5mXgP/ZlgcE2wq2oT0\nPDhYOWWXrRYJHYdhcSzn/LlIkHK9YYr7Dc5VMni0ax8ZflWFQyweKfqeapSCdr/3\nUEFzPSuFp7nxYh3yfvp4ZUod7QKBgQDnj3A2tXUKwHrEJGLizSe7Fx5YEsjXwub4\nuI4Jxcz5vOJmXZmoYwoKejxHC0AJgeyzurkpZb8oKWPdgyUYrIhUfUXgjUURYc2v\nN2/2AKf8wupvVuJot7PTBIE5inz19L7IqcLCJgfzDRy8muvEZLeHBo7mRBpLliJu\n1KV+CziOPQKBgBFVIpmQEEA/1FV3oiVMq2h3tbqMue0num9kU+uSRLIMGkQb/j1N\ntenXysyR2aGpSxECw0jmQYcft1Qvba9mTfuLZKPvbzmNngJ+/qyH4mDTHExR0HEI\nXowkFF1LkWFd3RpmGSGwf9O6s4Mx7B08hEgA2O6PHTQWFq3EAO6jDml9An9zx0RU\nFmZu+c5oQ8ktnWZAJU8Gul2xg8H4mk5qlHvoYojQpfwuNVbP5k49N2LNXij7tGDg\nUf9CFW/ZUbk6nQmNNRW7MOdXyY8ODKnuVmWn+8073jVWJoPC/n2eR6AzWiwKKNc4\nO8u9utCjfNJlX/ZjjOPmPibnpCmTQL9+Rh4BAoGBAIR6v5VFjyu9TPHfyOGX4iD2\nfWzpGb2q6lbqhLo25EFDkXWEBL/fxWM7SQklaCTX/jAz0qS7mUUgnORGI1tQSSzw\ndKtg+6VYTcPENnEPVStSXajmaL56edQNA9haqKFL5Cu/ZfV4nisvDMnyKjGt/Q3x\nSVGysEIhPGWdtKj1W9nt\n-----END PRIVATE KEY-----\n",
            "client_email" => "firebase-adminsdk-b5d51@astro-deb9c.iam.gserviceaccount.com",
            "client_id" => "114748951579114296658",
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-b5d51%40astro-deb9c.iam.gserviceaccount.com",
            "universe_domain" => "googleapis.com"
    ];


public static function send($userDeviceDetail, $notification)
{
    $fcmService = new self();
    $projectId = 'astro-deb9c';
    $serverApiKey = env('FCM_SERVER_KEY');
    $accessToken = $fcmService->getAccessToken($serverApiKey);

    $endpoint = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';

    $responses = []; // Array to store individual responses

    foreach ($userDeviceDetail->pluck('fcmToken')->all() as $token) {
        $notificationType = isset($notification['body']['notificationType']) ? (string) $notification['body']['notificationType'] : null;


        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $notification['title'],
                    'body' => $notification['body']['description'],
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'body' => json_encode($notification['body']),

                ],
                'android' => [
                    'priority' => 'high',
                ],
            ],
        ];


        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $responses[] = json_decode($response, true);
    }

    return $responses;
}


    private function getAccessToken($serverApiKey)
    {
        $url = 'https://www.googleapis.com/oauth2/v4/token';
        $data = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->generateJwtAssertion($serverApiKey),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $body = json_decode($response, true);

        return $body['access_token'];
    }


    private function generateJwtAssertion($serverApiKey)
{
    $now = time();
    $exp = $now + 3600; // Token expires in 1 hour

    $jwtClaims = [
        'iss' => $this->serviceAccountKey['client_email'],
        'sub' => $this->serviceAccountKey['client_email'],
        'aud' => 'https://www.googleapis.com/oauth2/v4/token',
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'iat' => $now,
        'exp' => $exp,
    ];

    $jwtHeader = [
        'alg' => 'RS256',
        'typ' => 'JWT',
    ];

    $base64UrlEncodedHeader = $this->base64UrlEncode(json_encode($jwtHeader));
    $base64UrlEncodedClaims = $this->base64UrlEncode(json_encode($jwtClaims));

    $signatureInput = $base64UrlEncodedHeader.'.'.$base64UrlEncodedClaims;

    $privateKey = openssl_pkey_get_private($this->serviceAccountKey['private_key']);
    openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    openssl_free_key($privateKey);

    $base64UrlEncodedSignature = $this->base64UrlEncode($signature);

    return $signatureInput.'.'.$base64UrlEncodedSignature;
}



    private function base64UrlEncode($input)
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }



    // public static function send($userDeviceDetail, $notification)
    // {
    //     $serverApiKey = env('FCM_SERVER_KEY');
    //     $payload = [
    //         "notification" => [
    //             "title" => $notification['title'],
    //             "body" => $notification['body']['description'],
    //         ],
    //         "data" => [
    //             "click_action" => "FLUTTER_NOTIFICATION_CLICK",
    //             "body" => $notification['body'],

    //         ],
    //         "android" => [
    //             "priority" => 'high',
    //         ],
    //         "registration_ids" => $userDeviceDetail->pluck('fcmToken')->all(),
    //     ];
    //     $dataString = json_encode($payload);
    //     $headers = [
    //         'Authorization: key=' . $serverApiKey,
    //         'Content-Type: application/json',
    //     ];
    //     $ch = curl_init();

    //     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    //     return curl_exec($ch);

	// 	curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
	// 	curl_setopt($ch, CURLOPT_POST, true);
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
	// 	// Set a short timeout to make the request asynchronous
	// 	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	// 	 // Execute the request in the background
	// 	curl_exec($ch);
	// 	// Close the cURL handle
	// 	curl_close($ch);
	// 	return true;
    // }
}
