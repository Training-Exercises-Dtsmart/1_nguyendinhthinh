<?php

namespace app\controllers;

use app\models\form\ProductForm;
use app\models\Product;
// use yii\rest\Controller;
use app\models\search\ProductSearch;
use Yii;
use app\controllers\Controller;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class ProductController extends Controller{
    public function actionIndex(){
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
        return $this->json(true,[
            "products" => $provider->getModels()
        ],"Success");
    }


    public function actionCreate(){
        $productForm = new ProductForm();
        $productForm->imageFile = UploadedFile::getInstance($productForm, 'imageFile');
        $productForm->load(Yii::$app->request->post());
        if (!$productForm->validate() || !$productForm->uploadImage()) {
            return $this->json(false,
                [
                    "error" => $productForm->getErrors()
                ],
                "Can't create product"
            );
        }

        $productForm->image = "uploads/". $productForm->imageFile->fullPath;
        $productForm->imageFile = null;
        if(!$productForm->save()){
            return $this->json(false, ["error" => $productForm->getErrors()], "Can't create product");
        }
        return $this->json(true, ["product"=>$productForm], "Create product successfully");
    }

    public function actionUpdate($id){
        $product = Product::find()->where(["id"=>$id])->one();

        $product->load(Yii::$app->request->post());
        if(!$product->validate()){
            return $product->getErrors();
        }
        $product->update();

        return [
            'status' => true,
            'data' => [
                'now' => date('d/m/Y'),
                'product' => $product
            ],
            'message' => 'Create product success'
        ];

    }

    public function actionDelete($id){
        $product = Product::find()->where(["id"=>$id])->one();

        if(!$product){

        }

        return $product->delete();
    }

    public function actionSearch(){
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $dataProvider;
    }
}