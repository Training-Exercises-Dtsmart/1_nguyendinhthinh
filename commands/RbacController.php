<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\commands\rbac\AuthorRule;

class RbacController extends Controller
{
    /**
     * @throws \Exception
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);

        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update post';
        $auth->add($updatePost);

        $deletePost = $auth->createPermission('deletePost');
        $deletePost->description = 'Delete post';
        $auth->add($deletePost);

        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createPost);

        $rule = new AuthorRule;
        $auth->add($rule);
        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update own post';
        $updateOwnPost->ruleName = $rule->name;
        $auth->add($updateOwnPost);
        $auth->addChild($updateOwnPost, $updatePost);
        $auth->addChild($author, $updateOwnPost);

        $deleteOwnPost = $auth->createPermission('deleteOwnPost');
        $deleteOwnPost->description = 'Delete own post';
        $deleteOwnPost->ruleName = $rule->name;
        $auth->add($deleteOwnPost);
        $auth->addChild($deleteOwnPost, $deletePost);
        $auth->addChild($author, $deleteOwnPost);

        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $auth->addChild($admin, $createPost);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);

        $auth->assign($admin, 1);
        $auth->assign($author, 2);
        $auth->assign($author, 17);
    }
}