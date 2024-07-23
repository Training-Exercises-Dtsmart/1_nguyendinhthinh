<?php

namespace app\modules\v1\models\form;

use app\modules\v1\models\User;

class UserForm extends User
{
    public function rules()
    {
        return [
            ['status', 'required']
        ];
    }
}