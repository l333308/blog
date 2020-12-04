<?php

namespace App\Support;


use Illuminate\Support\Facades\Log;

class RequestUtil
{
    public static function sendRequest($url, $method = 'get', array $data = [], array $options = [], $encode = true)
    {
        $headers = [];
        $method = strtoupper($method);
        if ($encode && ($method === 'POST' || $method === 'PUT')) {
            $data = $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : "{}";
            $headers = ['Content-Type' => 'application/json'];
        }
        try {
            Log::warning("请求接口....", [$url, is_string($data) ? json_decode($data, true) : $data]);
            $resp = \Requests::request($url, $headers, $data, $method, ['timeout' => 10])->body;
            Log::warning("请求接口响应....", [$url, json_decode($resp, true)]);
        } catch (\Requests_Exception $e) {
            Log::error("请求接口异常....", [$e->getMessage()]);
            return ['code' => "1", "msg" => "系统异常:" . $e->getMessage()];
        }
        return json_decode($resp, true) ?: ['code' => "1", "msg" => "系统异常:" . $resp];
    }

    // 是否成功响应
    public static function isSuccess($result)
    {
        return isset($result['code']) && $result['code'] == 0;
    }
}
