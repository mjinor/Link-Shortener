<?php

namespace Mjinor\MjaShorturl\App\Controller;

use Mjinor\MjaShorturl\App\Model\Link;
use Mjinor\MjaShorturl\App\Model\User;
use Mjinor\MjaShorturl\Service\Validator\Validator;

class LinkController extends Controller {
    /**
     * Author : MJavad Amirbeiki
     * Description : redirect root route to url that previously defined
     * @param $hash
     */
    public function redirect($hash) {
        $cache = getRedisService()->get("shorturl_$hash");
        if (empty($cache)) {
            $link = getDBService()->select('*', 'links', [
                ['hash', '=', $hash],
                ['expire_at', '>=', strtotime('now')]
            ], Link::class);
            if (empty($link))
                response_json([
                    'status' => 'Failed',
                    'message' => 'Link Not Found!'
                ], 404);
            $link = current($link);
            getRedisService()->set("shorturl_$hash",json_encode($link),60 * 30); // cache for half hour
        } else
            $link = (object) json_decode($cache,true);
        redirect($link->link);
    }

    /**
     * Author : MJavad Amirbeiki
     * Description : Fetch Current user defined short link to manage
     */
    public function fetchAll() {
        $user = User::currentUser();
        $links = getDBService()->select('*','links',[
            ['expire_at','>=',strtotime('now')],
            ['user_id','=',$user->id]
        ],Link::class);
        $links = array_map(function ($item) {
            return [
                "id" => $item->id,
                "link" => $item->link,
                "shorted_link" => "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/$item->hash"
            ];
        },$links);
        response_json([
            'status' => 'Success',
            'message' => 'List of Links!',
            'linls' => $links,
        ]);
    }

    /**
     * Author : MJavad Amirbeiki
     * Description : define new short link
     */
    public function create() {
        $errors = Validator::validate([
            'link' => 'required|url'
        ]);
        if (!empty($errors))
            response_json([
                'errors' => $errors
            ],422);
        $request = $this->request();
        $link = getDBService()->select('*','links',[
            ['link','=',$request->link],
            ['expire_at','>=',strtotime('now')]
        ],Link::class);
        if (empty($link)) {
            $link = new Link();
            $link->link = $request->link;
            do {
                $link->hash = generateRandomString();
                $is_hash_exists = getDBService()->select('*','links',[
                    ['hash','=',$link->hash]
                ]);
            } while(sizeof($is_hash_exists) > 0);
            $user = User::currentUser();
            $link->user_id = $user->id;
            $link->expire_at = strtotime("+1 day");
            getDBService()->saveWithTransAction([$link]);
        } else
            $link = current($link);
        response_json([
            'status' => 'Success',
            'message' => 'Short Link Generated!',
            'link' => $link->link,
            'shorted_link' => "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/$link->hash",
        ]);
    }

    /**
     * Author : MJavad Amirbeiki
     * Description : edit a short link
     */
    public function edit() {
        $errors = Validator::validate([
            'link_id' => 'required',
            'new_link' => 'required|url'
        ]);
        if (!empty($errors))
            response_json([
                'errors' => $errors
            ],422);
        $request = $this->request();
        $link = getDBService()->select('*','links',[
            ['id','=',$request->link_id],
            ['expire_at','>=',strtotime('now')]
        ],Link::class);
        if (empty($link))
            response_json([
                'status' => 'Failed',
                'message' => 'Link Not Found!'
            ],404);
        $link = current($link);
        $link->link = $request->new_link;
        getDBService()->saveWithTransAction([$link]);
        response_json([
            'status' => 'Success',
            'message' => 'Link updated!'
        ]);
    }

    /**
     * Author : MJavad Amirbeiki
     * Description : delete a specific short link that defined by current user
     */
    public function delete() {
        $errors = Validator::validate([
            'link_id' => 'required'
        ]);
        if (!empty($errors))
            response_json([
                'errors' => $errors
            ],422);
        $request = $this->request();
        $user = User::currentUser();
        $link = getDBService()->select('*','links',[
            ['id','=',$request->link_id],
            ['user_id','=',$user->id],
            ['expire_at','>=',strtotime('now')]
        ],Link::class);
        if (empty($link))
            response_json([
                'status' => 'Failed',
                'message' => 'Link Not Found!'
            ],404);
        $link = current($link);
        $link->delete();
        response_json([
            'status' => 'Success',
            'message' => 'Link Deleted!'
        ]);
    }
}