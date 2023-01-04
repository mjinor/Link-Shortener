<?php

namespace Mjinor\MjaShorturl\App\Model;

class User extends Model {
    public $name;
    public $family;
    public $username;
    public $password;

    public static function currentUser() {
        $token = getAuthToken();
        if (empty($token))
            response_json([
                "message" => "Unauthorized"
            ],401);
        $token = getDBService()->select("*","tokens",[
            ["token",'=',$token],
        ],Token::class);
        if (empty($token))
            response_json([
                "message" => "Unauthorized"
            ],401);
        $token = current($token);
        return current(getDBService()->select('*','users',[
            ['id','=',$token->user_id]
        ],User::class));
    }
}