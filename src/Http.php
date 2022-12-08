<?php

namespace Cometa\KeyCloack;

use Cometa\KeyCloack\Exceptions\CurlException;
use Cometa\KeyCloack\Exceptions\KeycloackHttpException;

class Http
{
    public static function post($url, $data, $header = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => $header,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        if($err) throw new CurlException($err);

        curl_close($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode >= 200 && $statusCode <= 299) return json_decode($response);
        throw new KeycloackHttpException($response, $statusCode);
    }
}
