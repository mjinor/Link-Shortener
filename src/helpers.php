<?php

use Mjinor\MjaShorturl\Service\Container;

/**
 * Author : MJavad Amirbeiki
 * Description : get configs of app
 * @param null $key
 * @return mixed
 */
function get_config($key = null) {
    $configs = include "Config.php";
    if (empty($key)) return $configs;
    return $configs[$key];
}

/**
 * Author : MJavad Amirbeiki
 * Description : response as json
 * @param $data
 * @param int $status_code
 */
function response_json($data,$status_code = 200) {
    header('Content-Type: application/json; charset=utf-8');
    $json = json_encode($data);
    if ($json === false) {
        // Avoid echo of empty string (which is invalid JSON), and
        // JSONify the error message instead:
        $json = json_encode(["jsonError" => json_last_error_msg()]);
        if ($json === false) {
            // This should not happen, but we go all the way now:
            $json = '{"jsonError":"unknown"}';
        }
        // Set HTTP response status code to: 500 - Internal Server Error
        http_response_code(500);
    }
    http_response_code($status_code);
    echo $json;
    die();
}

/**
 * Author : MJavad Amirbeiki
 * Description : generate random string for auth token and short link
 * @param int $length
 * @return string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#%&^';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Author : MJavad Amirbeiki
 * Description : get MJ token from header of request :D
 * @return mixed|null
 */
function getAuthToken() {
    $token = null;
    $headers = apache_request_headers();
    if(isset($headers['Authorization'])){
        $matches = array();
        preg_match('/MJ (.*)/', $headers['Authorization'], $matches);
        if(isset($matches[1])){
            $token = $matches[1];
        }
    }
    return $token;
}

/**
 * Author : MJavad Amirbeiki
 * Description : redis service that is inside of container
 * @return mixed
 * @throws Exception
 */
function getRedisService() {
    return Container::i()->get("redis")->getService();
}

/**
 * Author : MJavad Amirbeiki
 * Description : database adaptor service that is inside of container
 * @return mixed
 * @throws Exception
 */
function getDBService() {
    return Container::i()->get("db")->getService();
}

/**
 * Author : MJavad Amirbeiki
 * Description : redirect to a new url
 * @param $url
 * @param int $statusCode
 */
function redirect($url, $statusCode = 303) {
    header('Location: ' . $url, true, $statusCode);
    die();
}