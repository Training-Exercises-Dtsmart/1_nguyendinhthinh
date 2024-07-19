<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\filters\auth\HttpBearerAuth;
use app\controllers\Controller;
use app\modules\HTTP_STATUS;
use app\modules\v1\models\Product;
use app\modules\v1\models\ProductImage;
use app\modules\v1\models\form\ProductForm;
use app\modules\v1\models\search\ProductSearch;

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
            $searchModel = new ProductSearch();
            $products = $searchModel->search(Yii::$app->request->queryParams);
            $cache->set($key, $products, 10);
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
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $productForm->created_by = $user->id;
                $productForm->save();
                $productImage = new ProductImage();
                $productImage->product_id = $productForm->id;
                $productImage->imageFiles = UploadedFile::getInstancesByName('images');
                if (!$productImage->validate()) {
                    throw new Exception($productImage->getFirstError('imageFiles'));
                }

                if (!$productImage->uploadFile()) {
                    throw new Exception("Cant upload image file");
                };

                $transaction->commit();
                return $this->json(true, ["product" => $productForm], "Create product successfully");
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->json(false, ["errors" => $e->getMessage()], 'Cant create new product');
            }
        }
        return $this->json(false, ["errors" => $productForm->getErrors()], "Validation failed", HTTP_STATUS::BAD_REQUEST);
    }

    public function actionUpdate($id): array
    {
        $productForm = ProductForm::find()->where(["id" => $id])->one();
        if (!$productForm) {
            return $this->json(false, [], 'Product not found', HTTP_STATUS::NOT_FOUND);
        }
        $productForm->load(Yii::$app->request->post());
        if (!$productForm->validate() || !$productForm->save()) {
            return $this->json(false, ['errors' => $productForm->getErrors()], "Can't update product", HTTP_STATUS::BAD_REQUEST);
        }
        return $this->json(true, ['product' => $productForm], 'Update product successfully');
    }

    public function actionDelete($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if (!$product) {
            return $this->json(false, [], "Product not found", HTTP_STATUS::NOT_FOUND);
        }

        $product->status = Product::STATUS_DELETE;
        $product->deleted_at = date('Y-m-d H:i:s');
        if ($product->save()) {
            return $this->json(true, [], 'Delete product successfully');
        }

        return $this->json(false, ['errors' => $product->getErrors()], "Can't delete product", HTTP_STATUS::BAD_REQUEST);
    }
}