<?php

namespace app\modules\models;

use app\models\Post as BasePost;

class Post extends BasePost
{

    public function fields()
    {
        return array_merge(parent::fields(), [
            "category_name" => "categoryName",
        ]);
    }

    public function getCategoryName()
    {
        return isset($this->categoryPost) ? $this->categoryPost->name : null;
    }
}