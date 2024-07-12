<?php

namespace app\modules\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use app\controllers\Controller;
use app\modules\HTTP_STATUS;
use app\modules\models\User;
use app\modules\models\Product;
use app\modules\models\form\ProductForm;
use app\modules\models\search\ProductSearch;

class ProductController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['index', 'view'],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $products = Product::find()->all();
        return $this->json(true, ["products" => $products]);
    }

    public function actionView($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if (empty($product)) {
            return $this->json(false, [], 'Product not found', HTTP_STATUS::NOT_FOUND);
        }
        return $this->json(true, ["product" => $product]);
    }


    public function actionSearch()
    {
        $modelSearch = new ProductSearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->getQueryParams());

        return $this->json(true, ["products" => $dataProvider->getModels()], "Find successfully");
    }

    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        $productForm = new ProductForm();
        $productForm->load(Yii::$app->request->post());
        $productForm->created_by = $user->id;

        if (!$productForm->validate() || !$productForm->save()) {
            return $this->json(false, ["errors" => $productForm->getErrors()], "Can't create new product", HTTP_STATUS::BAD_REQUEST);
        }
        return $this->json(true, ["product" => $productForm], "Create product successfully");
    }

    public function actionUpdate($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if (empty($product)) {
            return $this->json(false, [], 'Product not found', HTTP_STATUS::NOT_FOUND);
        }

        $product->load(Yii::$app->request->post());
        if (!$product->validate() || !$product->save()) {
            return $this->json(false, ['errors' => $product->getErrors()], "Can't update product", HTTP_STATUS::BAD_REQUEST);
        }
        return $this->json(true, ['product' => $product], 'Update product successfully');
    }

    public function actionDelete($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if (empty($product)) {
            return $this->json(false, [], "Product not found", HTTP_STATUS::NOT_FOUND);
        }

        if (!$product->delete()) {
            return $this->json(false, ['errors' => $product->getErrors()], "Can't delete product", HTTP_STATUS::BAD_REQUEST);
        }
        return $this->json(true, [], 'Delete product successfully');
    }
}