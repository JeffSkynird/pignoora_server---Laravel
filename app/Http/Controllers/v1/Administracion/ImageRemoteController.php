<?php

namespace App\Http\Controllers\v1\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageRemoteController extends Controller
{
    public function saveImgur($file)
    {
        $curl = curl_init("https://api.imgur.com/3/image");
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => array('image' => $file, 'type' => 'base64', 'name' => 'image.jpg'),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Client-ID 07c723a1c44ff65',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
    public function getRemoteImage($name)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.imgur.com/3/image/' . $name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Client-ID 07c723a1c44ff65',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }
    public function deleteRemoteImage($name)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.imgur.com/3/image/' . $name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Client-ID 07c723a1c44ff65',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    public function validateFile($data)
    {
        if (base64_encode(base64_decode($data, true)) === $data) {
            return true;
        } else {
            return false;
        }
    }
}
