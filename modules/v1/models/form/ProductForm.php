<?php

namespace app\modules\v1\models\form;

use app\modules\v1\models\Product;


class ProductForm extends Product
{

    public $imageFile;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['name', 'price', 'stock'], 'required'],
            ['name', 'unique'],
            ['name', 'string', 'max' => 255],
        ]);
    }

    public function uploadImage()
    {
        if ($this->imageFile->saveAs("uploads/" . $this->imageFile->baseName . '.' . $this->imageFile->extension)) {
            return true;
        }
        return false;
    }
}