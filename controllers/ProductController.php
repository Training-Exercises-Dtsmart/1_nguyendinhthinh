<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use app\models\form\ProductForm;
use app\models\Product;
use app\models\search\ProductSearch;
use app\controllers\Controller;
use function PHPUnit\Framework\isNull;

class ProductController extends Controller
{
    public function actionIndex()
    {
        $query = Product::find();

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 2,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ]
        ]);
        return $this->json(true, ["products" => $provider->getModels()], "Success");
    }


    public function actionCreate()
    {
        $productForm = new ProductForm();
        $productForm->imageFile = UploadedFile::getInstanceByName('imageFile');
        $productForm->load(Yii::$app->request->post());

        if (!$productForm->validate() || !$productForm->uploadImage()) {
            return $this->json(false, ["error" => $productForm->getErrors()], "Can't create product", 400);
        }

        $productForm->image = "uploads/" . $productForm->imageFile->fullPath;
        $productForm->imageFile = null;
        if (!$productForm->save()) {
            return $this->json(false, ["error" => $productForm->getErrors()], "Can't create product", 400);
        }
        return $this->json(true, ["product" => $productForm], "Create product successfully");
    }

    public function actionUpdate($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if (empty($product)) {
            return $this->json(false, [], "Product not found", 404);
        }

        $product->load(Yii::$app->request->post());
        if (!$product->validate() || !$product->save()) {
            return $this->json(false, ["error" => $product->getErrors()], "Can't update product", 400);
        }
        return $this->json(true, ["product" => $product], "Update product successfully");
    }

    public function actionDelete($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if (empty($product)) {
            return $this->json(false, [], "Product not found", 404);
        }
        if (!$product->delete()) {
            return $this->json(false, [], "Can't delete product", 400);
        }
        return $this->json(true, [], 'Delete product successfully');
    }

    public function actionSearch()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->json(true, ["products" => $dataProvider->getModels()], "Find successfully");
    }
}