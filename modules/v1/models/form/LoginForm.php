<?php

namespace app\modules\v1\models\form;

use app\modules\v1\models\User;

class LoginForm extends User
{
    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
        ];
    }


    public function formName()
    {
        return "";
    }

}