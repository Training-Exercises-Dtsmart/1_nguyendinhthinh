<?php

namespace app\modules\models;

use Yii;
use yii\base\Exception;
use app\models\User as BaseUser;
use yii\web\IdentityInterface;

class User extends BaseUser implements IdentityInterface
{
    const SECRET_KEY = "dinhthinh";

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /**
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    public function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }
}