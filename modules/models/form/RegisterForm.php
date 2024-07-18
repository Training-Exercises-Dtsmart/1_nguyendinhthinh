<?php

namespace app\modules\models\form;

use app\modules\models\User;

class RegisterForm extends User
{
    const EMAIL_VERIFY_PENDING = 0;
    public $re_password;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['username', 'trim'],
            [['username', 'password', 'email'], 'required'],
            ['email', 'email'],
        ]);
    }
}