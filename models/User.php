<?php

namespace app\models;

use \app\models\base\User as BaseUser;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "user".
 */
class User extends BaseUser
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public function formName()
    {
        return "";
    }
}
