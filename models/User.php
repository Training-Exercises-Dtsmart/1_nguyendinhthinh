<?php

namespace app\models;

use Yii;
use \app\models\base\User as BaseUser;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\filters\auth\HttpBasicAuth;

/**
 * This is the model class for table "user".
 */
class User extends BaseUser
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = 5;
    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;

    public function formName()
    {
        return "";
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['id']);
        unset($fields['password']);

        return $fields;
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}
