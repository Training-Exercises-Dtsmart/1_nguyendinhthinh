<?php

namespace app\models\form;
use app\modules\models\Product;


class ProductForm extends Product{

    public $imageFile;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ]
        );
    }

    public function uploadImage()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs("uploads/".$this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
}