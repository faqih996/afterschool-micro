<?php

use Illuminate\Support\Facades\Http;

// mengambil data user
function getUser($userId)
{
    $url = env('SERVICE_USER_URL') . 'users/' . $userId;

    try {
        // memanggil ke service user dengan timeout
        $response = Http::timeout(10)->get($url);
        // mengambil response nya         
        $data = $response->json();
        // inject http
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        // pesan jika server down   
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service user unavailable'
        ];
    }
}


// mengambil data user dengan id tertentu
function getUserByIds($userIds = [])
{
    $url = env('SERVICE_USER_URL') . 'users/';

    try {
        // jika data user kosong
        if (count($userIds) === 0) {
            return [
                'status' => 'success',
                'http_code' => 200,
                'data' => []
            ];
        }

        // mengambil data dari service user
        $response = Http::timeout(10)->get($url, ['user_ids[]' => $userIds]);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        // pesan jika server down   
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service user unavailable'
        ];
    }
}

function postOrder($params)
{
    $url = env('SERVICE_ORDER_PAYMENT_URL') . 'api/orders';
    try {
        $response = Http::post($url, $params);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service order payment unavailable'
        ];
    }
}