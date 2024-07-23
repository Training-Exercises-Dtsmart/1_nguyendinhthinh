<?php

namespace app\modules\v1\models\form;

use app\modules\HttpStatus;
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

    public function login()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = User::find()->where(['username' => $this->username])->one();
        if ($user && $user->validatePassword($this->password)) {
            $user->generateAuthKey();
            if ($user->save()) {
                $this->auth_key = $user->getAuthKey();
                return true;
            }
            $this->addError('user', $user->getErrors());
            return false;
        }
        $this->addError('Username', 'Username or password is incorrect');
        return false;
    }
}