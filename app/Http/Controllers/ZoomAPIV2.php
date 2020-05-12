<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use \Firebase\JWT\JWT;

class ZoomAPIV2 extends Controller
{
    public function __construct(Request $request) {
        //generate a 60 second JWT token on the fly
        $api_secret = $_ENV['ZOOM_SECRET_KEY'];
        $api_key = $_ENV['ZOOM_API_KEY'];
        $payload = array(
        'iss' => $api_key,
        'exp' => (time() + 60)
        );
        $this->jwt = JWT::encode($payload, $api_secret);

        //create global guzzle client
        $this->client = new \GuzzleHttp\Client(['base_uri' => 'http://api.zoom.us/v2/']);
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->jwt,
            'Accept'        => 'application/json',
            'content-type' => 'application/json'
        ];
    }

      //------------------//
     //  User API ROUTES //
    //------------------//

    //List all the meetings that were scheduled for a user (meeting host).
    public function users() {
        try {
            $response = $this->client->request('GET', 'users', [
                'headers' => $this->headers
            ]);
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
        }
        return $response;
    }

      //----------------------//
     //  Webinar API ROUTES  //
    //----------------------//

    //Use this API to list all the webinars that are scheduled by or on-behalf a user (Webinar host).
    public function listwebinars() {
        try {
            $url = 'users/' . request()->route()->parameter('userId') . '/webinars';
            $response = $this->client->request('GET', $url , [
                'headers' => $this->headers
            ]);
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

    //Use this API to get details of a scheduled webinar
    public function getwebinars() {
        try {
            $url = 'webinars/' . request()->route()->parameter('webinarId');
            $response = $this->client->request('GET', $url , [
                'headers' => $this->headers
            ]);
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

    //Retrieve details on participants from live or past webinars.
    public function getwebinarparticipants() {
        try {
            $url = 'metrics/webinars/' . request()->route()->parameter('webinarId') . '/participants';
            $response = $this->client->request('GET', $url , [
            'headers' => $this->headers
        ]);
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

    //List all the live or past webinars from a specified period of time.
    public function listallwebinars() {
        $url = 'metrics/webinars';
        try {
            $response = $this->client->request('GET', $url , [
                'headers' => $this->headers
            ]);
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

      //----------------------//
     // Meeting API ROUTES   //
    //----------------------//

    //list users meetings
    public function listmeetings() {
        try {
            $url = 'users/' . request()->route()->parameter('userId') . '/meetings';
            $response = $this->client->request('GET', $url , [
            'headers' => $this->headers
        ]);
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

    //Create a meeting
    public function createmeeting() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.zoom.us/v2/users/". request()->route()->parameter('userId') . "/meetings",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => '{"topic": "hello", "type": 2, "schedule_for": "QTk1g81ES_-8FMCOV9Q7bg", "start_time": "2020-05-31T12:00:00Z", "duration": 30}',
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IkJ4bjBDRVpLUTFPa3Q5Z29zY3ltQ3ciLCJleHAiOjE1ODkyODgzMzgsImlhdCI6MTU4OTI4Mjk0MH0.3wZcm1N0dSBtwjj2ICnSvVo1X_X68938YeLeRASueMw",
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        return $err;
        } else {
        return $response;
        }
    }
}
