<?php

defined('ABSPATH') || exit;

class B2BTrelloCurl
{
    private $key = "";
    private $token = "";

    public function __construct()
    {
        $this->key = getenv("TRELLO_KEY");
        $this->token = getenv("TRELLO_TOKEN");
    }

    /**
     * Curl the Trello API.
     *
     * @param string $type
     * @param string $path
     *
     * @return bool|string
     */
    public function setupCurl(string $type, string $path)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.trello.com/1" . $path . "?key=" . $this->key . "&token=" . $this->token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * Curl the Trello API explicitly with PUT
     *
     * @param string $path
     * @param array $data
     *
     * @return array
     */
    public function setupCurlPut(string $path, array $data): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.trello.com/1" . $path . "?key=" . $this->key . "&token=" . $this->token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Content-Type: application/json"
        ),
        ));
        $d['path'] = $path;
        $d['data'] = json_encode($data);
        $d['response'] = curl_exec($curl);
        $d['error'] = curl_error($curl);
        curl_close($curl);
        return $d;
    }
}