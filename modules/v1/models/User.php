<?php

namespace app\modules\v1\models;

use app\models\User as BaseUser;
use Yii;
use yii\base\Exception;
use yii\filters\RateLimitInterface;
use yii\web\IdentityInterface;

class User extends BaseUser implements IdentityInterface, RateLimitInterface
{
    public $rateLimit = 10;

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

    public static function findByEmail($email)
    {
        return static::find()->where(['email' => $email])->one();
    }

    public function generateVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public static function findByVerificationToken($token)
    {
        if (!static::isVerificationTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'verification_token' => $token,
            'email_verified' => 0,
        ]);
    }

    public static function isVerificationTokenValid($token)
    {
        $expire = Yii::$app->params['user.verificationTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    public function verifyEmail()
    {
        $this->email_verified = 1;
        $this->verification_token = null;
    }

    public static function findByResetPasswordToken($token)
    {
        return static::find()->select('id')->where(['reset_password_token' => $token])->one();
    }

    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, 60]; // $rateLimit requests per second
    }

    public function loadAllowance($request, $action)
    {
        return [$this->allowance, $this->allowance_updated_at];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save(false);
    }
}