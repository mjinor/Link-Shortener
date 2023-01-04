<?php

namespace Mjinor\MjaShorturl\App\Controller;

use Mjinor\MjaShorturl\App\Model\Token;
use Mjinor\MjaShorturl\App\Model\User;
use Mjinor\MjaShorturl\Service\Validator\Validator;

class AuthController extends Controller {
    /**
     * Author : MJavad Amirbeiki
     * Description : Register new User
     */
    public function register() {
        $errors = Validator::validate([
            'name' => 'required',
            'family' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:5',
            'confirm_password' => 'required|same:password'
        ]);
        if (!empty($errors))
            response_json([
                'errors' => $errors
            ],422);
        $request = $this->request();
        $user = new User();
        $user->name = $request->name;
        $user->family = $request->family;
        $user->username = $request->username;
        $user->password = $request->password;
        getDBService()->saveWithTransAction([$user]);
        response_json([
            'status' => 'Success',
            'message' => 'You Registered Successfully!'
        ]);
    }

    /**
     * Author : MJavad Amirbeiki
     * Description : Login as a user and get Auth Token
     */
    public function login() {
        $errors = Validator::validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        if (!empty($errors))
            response_json([
                'errors' => $errors
            ],422);
        $request = $this->request();
        $user = getDBService()->select('*','users',[
            ['username','=',$request->username]
        ],User::class);
        if (empty($user))
            response_json([
                'status' => 'Failed',
                'message' => 'The username or Password is incorrect!'
            ],422);
        $user = current($user);
        $is_password_currect = password_verify($request->password,$user->password);
        if (!$is_password_currect)
            response_json([
                'status' => 'Failed',
                'message' => 'The username or Password is incorrect!'
            ],422);
        $token = getDBService()->select('*','tokens',[
            ['user_id','=',$user->id]
        ],Token::class);
        if (empty($token)) {
            $token = new Token();
            $token->token = generateRandomString(100);
            $token->user_id = $user->id;
            getDBService()->saveWithTransAction([$token]);
        } else
            $token = current($token);
        response_json([
            'status' => 'Success',
            'message' => 'Welcome!',
            'access_token' => $token->token,
        ]);
    }
}