<?php

namespace Mjinor\MjaShorturl\App\Controller;

class Controller {
    /**
     * Author : MJavad Amirbeiki
     * Description : fetch inputs
     */
    protected function request() {
        return (object) ($_POST + $_GET + $_FILES);
    }
}