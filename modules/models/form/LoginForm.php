<?php

namespace app\modules\models\form;

use app\modules\models\User;

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