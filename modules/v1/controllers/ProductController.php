<?php

namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\modules\HTTP_STATUS;
use app\modules\v1\models\form\ProductForm;
use app\modules\v1\models\Product;
use app\modules\v1\models\search\ProductSearch;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UploadedFile;

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
        $cache = Yii::$app->cache;
        $key = 'product-list';
        $products = $cache->get($key);
        if ($products == false) {
            $products = Product::find()->all();
            $cache->set($key, $products, 6000);
        }

        return $this->json(true, ["products" => $products]);
    }

    public function actionView($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if ($product) {
            return $this->json(true, ["product" => $product]);
        }
        return $this->json(false, [], 'Product not found', HTTP_STATUS::NOT_FOUND);
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

        if ($productForm->validate()) {
            $productForm->thumbnail = UploadedFile::getInstancesByName('thumbnail');
            foreach ($productForm->thumbnail as $file) {
                var_dump($file);
            }
            die;
            $productForm->created_by = $user->id;

            if ($productForm->save()) {
                return $this->json(true, ["product" => $productForm], "Create product successfully");
            }
            return $this->json(false, ["errors" => $productForm->getErrors()], "Can't create new product", HTTP_STATUS::BAD_REQUEST);
        }
        return $this->json(false, ["errors" => $productForm->getErrors()], "Validation failed", HTTP_STATUS::BAD_REQUEST);
    }

    public function actionUpdate($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if (!$product) {
            return $this->json(false, [], 'Product not found', HTTP_STATUS::NOT_FOUND);
        }

        $product->load(Yii::$app->request->post());
        if ($product->validate() && $product->save()) {
            return $this->json(true, ['product' => $product], 'Update product successfully');
        }
        return $this->json(false, ['errors' => $product->getErrors()], "Can't update product", HTTP_STATUS::BAD_REQUEST);
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