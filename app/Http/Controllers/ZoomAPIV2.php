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
        ];
    }

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
            $responseBodyAsString = $response->getBody()->getContents();
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
            $responseBodyAsString = $response->getBody()->getContents();
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
            $responseBodyAsString = $response->getBody()->getContents();
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
            $responseBodyAsString = $response->getBody()->getContents();
        }
        return $response;
    }
}
