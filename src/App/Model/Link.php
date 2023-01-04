<?php

namespace Mjinor\MjaShorturl\App\Model;

class Link extends Model {
    public $link;
    public $hash;
    public $user_id;
    public $expire_at;
}