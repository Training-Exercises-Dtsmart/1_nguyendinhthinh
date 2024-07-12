<?php

namespace app\modules\models\form;

use app\modules\models\User;

class RegisterForm extends User
{
    public $re_password;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['username', 'trim'],
            [['username', 'password', 're_password'], 'required'],
        ]);
    }
}