<?php

namespace app\commands\rbac;

use Yii;
use yii\rbac\Rule;

class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    public function execute($user, $item, $params): bool
    {
        if (!Yii::$app->user->isGuest) {
            $group = Yii::$app->user->identity->group;
            if ($item->name == 'admin') {
                return $group == 1;
            } elseif ($item->name == 'author') {
                return $group == 1 || $group == 2;
            }
        }
        return false;
    }


}