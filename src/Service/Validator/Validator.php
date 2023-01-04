<?php

namespace Mjinor\MjaShorturl\Service\Validator;

use Rakit\Validation\Validator as Valid;

class Validator {
    public static function validate($rules) {
        $validator = new Valid();
        $pdo = getDBService()->getDatabase()->getConnection();
        $validator->addValidator('unique', new UniqueRule($pdo));
        $validation = $validator->validate($_POST + $_GET + $_FILES,$rules);
        if ($validation->fails())
            return $validation->errors()->all();
        return [];
    }
}