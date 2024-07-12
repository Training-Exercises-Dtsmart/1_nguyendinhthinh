<?php

namespace app\modules\controllers;

use Yii;
use app\modules\models\form\RegisterForm;
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

        if (!$user->validatePassword($loginForm->password)) {
            return $this->json(false, ['errors' => $user->getErrors()], 'Wrong username or password', HTTP_STATUS::UNAUTHORIZED);
        }

        $user->generateAuthKey();
        if (!$user->save()) {
            return $this->json(false, ['errors' => $user->getErrors()], 'Cant login.', HTTP_STATUS::BAD_REQUEST);
        }

        return $this->json(true, ['token' => $user->auth_key], 'Login Successfully', HTTP_STATUS::OK);
    }

    /**
     * @throws \Exception
     */
    public function actionRegister()
    {
        $registerForm = new RegisterForm();
        $registerForm->load(Yii::$app->request->post());

        if (!$registerForm->validate() || !$registerForm->save()) {
            return $this->json(false, ['errors' => $registerForm->getErrors()], 'Cant register user.', HTTP_STATUS::BAD_REQUEST);
        }

        $registerForm->password = Yii::$app->getSecurity()->generatePasswordHash($registerForm->password);
        $registerForm->re_password = $registerForm->password;
        if (!$registerForm->save()) {
            return $this->json(false, ['errors' => $registerForm->getErrors()], 'Cant register.', HTTP_STATUS::BAD_REQUEST);
        }

        $auth = Yii::$app->authManager;
        $author = $auth->getRole('author');
        $auth->assign($author, $registerForm->id);

        return $this->json(true, ['user' => $registerForm], 'Register Successfully', HTTP_STATUS::OK);
    }
}