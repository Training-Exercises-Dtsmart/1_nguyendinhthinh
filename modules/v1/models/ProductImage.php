<?php

namespace app\modules\v1\models;

use app\models\ProductImage as BaseProductImage;
use Yii;

class ProductImage extends BaseProductImage
{
    public $imageFiles;

    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 10],
        ];
    }

    public function uploadFile()
    {
        $uploadPath = Yii::getAlias('@app/web/uploads/product/');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        foreach ($this->imageFiles as $file) {
            $fileName = uniqid() . '_' . $file->baseName . '.' . $file->extension;
            $filePath = $uploadPath . $fileName;
            if ($file->saveAs($filePath)) {
                $productImage = new BaseProductImage();
                $productImage->name = $fileName;
                $productImage->product_id = $this->product_id;
                if (!$productImage->save()) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }
}