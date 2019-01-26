<?php

namespace TestRuimin;

class Request
{
    const BASE_URL = 'http://localhost:3080/api/';
    const MAX_TRY_TIMES = 10;
    const POW_BASE = 2;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    private function request($url, $method, $data = null)
    {
        $payload = json_encode($data);
        echo static::BASE_URL . $url;

        $curl = curl_init(static::BASE_URL . $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-type' => 'application/json',
        ]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($curl);
        echo $response;
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        echo $httpCode;
        if ($httpCode >= 400 && $httpCode < 500) {
            throw new ClientSideException();
        }
        return json_decode($response);
    }

    private function requestWithBackOffRetry($url, $method, $data = null)
    {
        $tryTimes = 0;

        while (++$tryTimes <= static::MAX_TRY_TIMES) {
            echo "try $tryTimes times\n";
            if ($tryTimes === static::MAX_TRY_TIMES) {
                return $this->request($url, $method, $data);
            }

            try {
                return $this->request($url, $method, $data);
            } catch (ClientSideException $e) {
                sleep(pow(static::POW_BASE, $tryTimes - 1));
            }
        }

        return null;
    }

    public function __construct()
    {

    }

    public function get($url)
    {
        // TDO
    }

    public function post($url, $data)
    {
        return $this->requestWithBackOffRetry($url, static::METHOD_POST, $data);
    }

    public function put($url, $data)
    {
        return $this->request($url, static::METHOD_PUT, $data);
    }

    public function delete($url, $data = null)
    {
        return $this->request($url, static::METHOD_DELETE, $data);
    }
}
