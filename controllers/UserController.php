<?php

namespace app\controllers;

use Yii;
use app\models\form\UserForm;
use app\models\User;
use app\controllers\Controller;

class UserController extends Controller
{
    private $username = 'dinhthinh';
    private $password = 'e10adc3949ba59abbe56e057f20f883e'; #123456

    public function actionIndex()
    {
        $users = User::find()->active()->all();
        return $this->json(true, ["now" => date('d/m/Y'), 'users' => $users], 'Success');
    }

    public function actionCreate()
    {
        $userForm = new UserForm();
        $userForm->load(Yii::$app->request->post());
        $hash = Yii::$app->getSecurity()->generatePasswordHash(Yii::$app->request->post('password'));
        $userForm->password = $hash;

        if (!$userForm->validate() || !$userForm->save()) {
            return $this->json(false, ['now' => date('d/m/Y'), 'errors' => $userForm->getErrors()], 'Cant create product', 400);
        }
        return $this->json(true, ['now' => date('d/m/Y'), 'user' => $userForm], 'Success');
    }

    public function actionUpdate($id)
    {
        $user = User::find()->where(['id' => $id])->one();
        if (empty($user)) {
            return $this->json(false, ['now' => date('d/m/Y')], 'Product not found', 404);
        }

        $user->load(Yii::$app->request->post());
        if (!$user->validate() || !$user->save()) {
            return $this->json(false, ['now' => date('d/m/Y')], 'Cant create product', 400);
        }
        return $this->json(true, ['now' => date('d/m/Y'), 'user' => $user], 'Success');
    }

    public function actionDelete($id)
    {
        $user = User::find()->where(['id' => $id])->one();
        if (empty($user)) {
            return $this->json(false, ['now' => date('d/m/Y')], 'Product not found', 404);
        }

        if (!$user->delete()) {
            return $this->json(false, ['now' => date('d/m/Y')], 'Cant delete product', 400);
        };
        return $this->json(true, ['now' => date('d/m/Y'), 'user' => $user], 'Success');
    }


    // public function actionCreate(){
    //     $user = new User();
    //     $data = Yii::$app->request->post();
    //     $username = $data['username'];
    //     $password = $data['password'];

    //     if( User::find()->where(['username' => $username])->one() ){
    //         return [
    //             "status" => false,
    //             "data" => [
    //                 "now" => date("d/m/Y")
    //             ],
    //             "message" => "Username already exists"
    //         ];
    //     }

    //     $user->username = $username;
    //     $user->password = md5($password);
    //     if($user->save()){
    //         return [
    //             "status" => true,
    //             "data" => [
    //                 "now" => date("d/m/Y")
    //             ],
    //             "message" => "Success"
    //         ];
    //     }
    // }

    // public function actionLogin(){
    //     if(Yii::$app->request->method != "POST"){
    //         throw new NotFoundHttpException('The requested resource was not found.',404);
    //     }
    //     $data = Yii::$app->request->post();
    //     $username = $data['username'];
    //     $password = $data['password'];

    //     if($username != $this->username or md5($password) != $this->password){
    //         return [
    //             "status" => false,
    //             "data" => [
    //                 "now" => date("d/m/Y")
    //             ],
    //             "message" => "Username or password wrong"
    //         ];
    //     }

    //     return [
    //         "status" => true,
    //         "data" => [
    //             "now" => date("d/m/Y")
    //         ],
    //         "message" => "Success"
    //     ];
    // }

}

