<?php

namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\modules\HttpStatus;
use app\modules\v1\jobs\VerifyMailQueue;
use app\modules\v1\models\form\LoginForm;
use app\modules\v1\models\form\PasswordResetRequestForm;
use app\modules\v1\models\form\RegisterForm;
use app\modules\v1\models\form\ResetPasswordForm;
use app\modules\v1\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['index', 'view', 'register', 'login', 'verify-email', 'forgot-password', 'reset-password'],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['view', 'search', 'create', 'update', 'delete'],
                    'roles' => ['author']
                ],
                [
                    'allow' => true,
                    'actions' => ['login', 'register', 'verify-email', 'forgot-password', 'reset-password'],
                    'roles' => ['?'],
                ],
                [
                    'allow' => true,
                    'actions' => ['logout'],
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionLogin()
    {
        $loginForm = new LoginForm();
        $loginForm->load(Yii::$app->request->post());
        if ($loginForm->login()) {
            return $this->json(true, ['token' => $loginForm->getAuthKey()], 'Successfully logged in');
        }

        return $this->json(false, ['errors' => $loginForm->getErrors()], 'Cant login.', HttpStatus::BAD_REQUEST);
    }

    /**
     * @throws \Exception
     */
    public function actionRegister()
    {
        $registerForm = new RegisterForm();
        $registerForm->load(Yii::$app->request->post());

        if ($registerForm->register()) {
            return $this->json(true, ['user' => $registerForm], 'Register successfully', HttpStatus::OK);
        }

        return $this->json(false, ['error' => $registerForm->getErrors()], "Can't register", HttpStatus::BAD_REQUEST);

    }

    public function actionLogout()
    {
        if (Yii::$app->user->logout()) {
            return $this->json(true, [], 'Logged out successfully', HttpStatus::OK);
        }
        return $this->json(false, [], 'Failed to logout', HttpStatus::BAD_REQUEST);
    }

    public function actionVerifyEmail($token)
    {
        try {
            $user = User::findByVerificationToken($token);
            if (!$user) {
                return $this->json(false, [], 'Verification token is invalid or expired. Please request again.');
            }
            $user->verifyEmail();
            $user->save(false);
            return $this->json(true, [], 'Your email has been confirmed!');
        } catch (\RuntimeException $e) {
            return $this->json(false, [], $e->getMessage());
        }
    }

    public function actionForgotPassword($email)
    {
        $model = PasswordResetRequestForm::findByEmail($email);
        if (!$model) {
            return $this->json(false, [], 'User not found', HttpStatus::NOT_FOUND);
        }

        if ($model->sendMailResetPassword()) {
            return $this->json(true, [], 'Check your email for reset password');
        }
        return $this->json(false, [], 'Failed to send reset password email.', HttpStatus::BAD_REQUEST);
    }

    public function actionResetPassword($token)
    {
        $model = ResetPasswordForm::findByResetPasswordToken($token);
        if (!$model) {
            return $this->json(false, [], 'Reset password token is invalid or expired. Please request again.');
        }
        $model->load(Yii::$app->request->post());
        if (!$model->validate()) {
            return $this->json(false, ['errors' => $model->getErrors()], 'Failed to validate reset password', HttpStatus::BAD_REQUEST);
        }

        $model->password = Yii::$app->getSecurity()->generatePasswordHash($model->password);
        $model->reset_password_token = null;
        if ($model->save()) {
            return $this->json(true, [], 'Password reset successfully');
        }

        return $this->json(false, ['error' => $model->getErrors()], 'Failed to reset password', HttpStatus::BAD_REQUEST);
    }
}