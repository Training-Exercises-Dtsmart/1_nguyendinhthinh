<?php

namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\modules\HTTP_STATUS;
use app\modules\v1\jobs\VerifyMailQueue;
use app\modules\v1\models\form\LoginForm;
use app\modules\v1\models\form\RegisterForm;
use app\modules\v1\models\User;
use Yii;

class AuthController extends Controller
{
    public function actionLogin()
    {
        $loginForm = new LoginForm();
        $loginForm->load(Yii::$app->request->post());

        if (!$loginForm->validate() && !$loginForm->login()) {
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
        $registerForm->generateVerificationToken();
        $registerForm->email_verified = RegisterForm::EMAIL_VERIFY_PENDING;

        if (!$registerForm->save()) {
            return $this->json(false, ['errors' => $registerForm->getErrors()], 'Cant register.', HTTP_STATUS::BAD_REQUEST);
        }

        $this->sendVerificationEmail($registerForm);

        $auth = Yii::$app->authManager;
        $author = $auth->getRole('author');
        $auth->assign($author, $registerForm->id);

        return $this->json(true, ['user' => $registerForm], 'Register Successfully', HTTP_STATUS::OK);
    }

    public function sendVerificationEmail($registerForm)
    {
        $verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['/api/v1/auth/verify-email', 'token' => $registerForm->verification_token]);

        Yii::$app->queue->push(new VerifyMailQueue($registerForm, $verifyLink));
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
}