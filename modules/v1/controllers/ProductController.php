<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\RateLimiter;
use yii\rest\Serializer;
use yii\web\UploadedFile;
use yii\filters\auth\HttpBearerAuth;
use app\controllers\Controller;
use app\modules\HttpStatus;
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
            'except' => ['index'],
        ];
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::class,
            'enableRateLimitHeaders' => true,
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
//        $key = "product-list";
//        $products = Yii::$app->cache->get($key);
//        if (!$products) {
//            Yii::$app->cache->set($key, $products, 600);
//        }
        $searchModel = new ProductSearch();
        $products = $searchModel->search(Yii::$app->request->queryParams);
        return $this->json(true, $products);
    }

    public function actionView($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if ($product) {
            return $this->json(true, ["product" => $product]);
        }
        return $this->json(false, [], 'Product not found', HttpStatus::NOT_FOUND);
    }


    public function actionSearch()
    {
        $modelSearch = new ProductSearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->getQueryParams());
        return $this->json(true, ["products" => $dataProvider], "Find successfully");
    }

    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        $productForm = new ProductForm();
        $productForm->load(Yii::$app->request->post());
        if ($productForm->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$productForm->save()) {
                    return $this->json(false, ['error' => $productForm->getErrors()], 'Cant save product', HttpStatus::BAD_REQUEST);
                }
                $productImage = new ProductImage();
                $productImage->product_id = $productForm->id;
                $productImage->imageFiles = UploadedFile::getInstancesByName('images');
                if (!$productImage->validate()) {
                    return $this->json(false, ['error' => $productImage->getErrors()], 'Validate failed', HttpStatus::BAD_REQUEST);
                }
                if (!$productImage->uploadFile()) {
                    return $this->json(false, ['error' => $productImage->getErrors()], "Can't save image product", HttpStatus::BAD_REQUEST);
                }
                $transaction->commit();
                return $this->json(true, ["product" => $productForm], "Create product successfully");
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->json(false, ["errors" => $e->getMessage()], 'Cant create new product');
            }
        }
        return $this->json(false, ["errors" => $productForm->getErrors()], "Validation failed", HttpStatus::BAD_REQUEST);
    }

    public function actionUpdate($id): array
    {
        $productForm = ProductForm::find()->where(["id" => $id])->one();
        if (!$productForm) {
            return $this->json(false, [], 'Product not found', HttpStatus::NOT_FOUND);
        }
        $productForm->load(Yii::$app->request->post());
        if ($productForm->validate() && $productForm->save()) {
            return $this->json(true, ['product' => $productForm], 'Update product successfully');
        }
        return $this->json(false, ['errors' => $productForm->getErrors()], "Can't update product", HttpStatus::BAD_REQUEST);
    }

    public function actionDelete($id)
    {
        $product = Product::find()->where(["id" => $id])->one();
        if (!$product) {
            return $this->json(false, [], "Product not found", HttpStatus::NOT_FOUND);
        }

        $product->status = Product::STATUS_DELETE;
        $product->deleted_at = date('Y-m-d H:i:s');
        if ($product->save()) {
            return $this->json(true, [], 'Delete product successfully');
        }

        return $this->json(false, ['errors' => $product->getErrors()], "Can't delete product", HttpStatus::BAD_REQUEST);
    }
}