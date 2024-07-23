<?php

namespace app\modules\v1\models\form;

use app\modules\v1\models\User;

class ResetPasswordForm extends User
{
    public function rules(): array
    {
        return [
            ['password', 'validatePasswordStrength'],
            ['password', 'required']

        ];
    }

    public function validatePasswordStrength($attribute): bool
    {
        if (strlen($this->password) < 6) {
            $this->addError($attribute,
                'Password must contain at least 6 characters, one uppercase letter, and special characters');
            return true;
        }
        if (!preg_match('/[A-Z]/', $this->password)) {
            $this->addError($attribute,
                'Password must contain at least one uppercase letter, and special characters');
            return true;
        }
        if (!preg_match('/[\W_]/', $this->password)) {
            $this->addError($attribute, 'Password must contain at least one special character.');
        }
        return false;
    }
}