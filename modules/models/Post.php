<?php

namespace app\modules\models;

use app\models\Post as BasePost;

class Post extends BasePost
{
    const ACTIVE = 1;
    const DISABLED = 0;
    const DELETE = -1;

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

    public function getCreatedBy()
    {
        return isset($this->user_id) ? $this->user_id : null;
    }
}