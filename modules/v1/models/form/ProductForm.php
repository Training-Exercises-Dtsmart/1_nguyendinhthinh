<?php

namespace app\modules\v1\models\form;

use app\modules\v1\models\Product;


class ProductForm extends Product
{

    public $imageFile;

//    public function rules()
//    {
//        return array_merge(parent::rules(), [
//                [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
//            ]
//        );
//    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['name', 'price', 'stock'], 'required'],
        ]);
    }

    public function uploadImage()
    {
//        if ($this->validate()) {
//            $this->imageFile->saveAs("uploads/".$this->imageFile->baseName . '.' . $this->imageFile->extension);
//            return true;
//        } else {
//            return false;
//        }
        if ($this->imageFile->saveAs("uploads/" . $this->imageFile->baseName . '.' . $this->imageFile->extension)) {
            return true;
        }
        return false;
    }
}