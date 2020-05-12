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
        $response = $this->client->request('GET', 'users', [
            'headers' => $this->headers
        ]);
        return $response;
    }

    public function listwebinars() {
        $url = 'users/' . request()->route()->parameter('userId') . '/webinars';
        $response = $this->client->request('GET', $url , [
            'headers' => $this->headers
        ]);
        return $reponse;
    }

    public function getwebinars() {
        $url = 'webinars/' . request()->route()->parameter('webinarId');
        $response = $this->client->request('GET', $url , [
            'headers' => $this->headers
        ]);
        return $response;
    }

    public function getwebinarparticipants() {
        $url = 'metrics/webinars/' . request()->route()->parameter('webinarId') . '/participants';
        $response = $this->client->request('GET', $url , [
            'headers' => $this->headers
        ]);
        return $response;
    }
}
