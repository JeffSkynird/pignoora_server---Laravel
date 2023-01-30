<?php
namespace App\Http\Services;

use CURLFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImageRemoteService
{
    private $remoteServer;
    private $clientId;
   
    public function __construct()
    {
        $this->remoteServer = 'https://api.imgur.com/3/image';
        $this->clientId = '4e61d1e0e12b313';
     
    }
    public function saveImgur($file)
    {
        $curl = curl_init($this->remoteServer);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => array('image' => $file, 'type' => 'base64', 'name' => 'image.jpg'),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Client-ID '.$this->clientId,
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
            CURLOPT_URL => $this->remoteServer.'/'.$name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Client-ID '.$this->clientId,
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
            CURLOPT_URL => $this->remoteServer.'/'.$name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Client-ID '.$this->clientId,
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