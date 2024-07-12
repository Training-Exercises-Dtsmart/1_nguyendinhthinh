<?php

namespace app\modules\controllers;

use Yii;
use app\modules\HTTP_STATUS;
use app\modules\models\User;
use app\modules\models\form\LoginForm;
use app\modules\models\form\UserForm;
use app\controllers\Controller;

class AuthController extends Controller
{
    public function actionLogin()
    {
        $loginForm = new LoginForm();
        $loginForm->load(Yii::$app->request->post());

        if (!$loginForm->validate()) {
            return $this->json(false, [], 'Validation failed. Please check the input and try again.', HTTP_STATUS::BAD_REQUEST);
        }

        $user = User::findOne(['username' => $loginForm->username]);

        if (empty($user)) {
            return $this->json(false, [], 'Wrong username', HTTP_STATUS::BAD_REQUEST);
        }

        if (!Yii::$app->security->validatePassword($loginForm->password, $user->password)) {
            return $this->json(false, ['errors' => $user->getErrors()], 'Wrong username or password', HTTP_STATUS::UNAUTHORIZED);
        }

        $user->generateAuthKey();
        if (!$user->save()) {
            return $this->json(false, ['errors' => $user->getErrors()], 'Cant login.', HTTP_STATUS::BAD_REQUEST);
        }

        return $this->json(true, ['token' => $user->auth_key], 'Login Successfully', HTTP_STATUS::OK);
    }
}