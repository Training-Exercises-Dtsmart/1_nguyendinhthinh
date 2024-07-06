<?php

namespace app\modules\controllers;

use app\controllers\Controller;
use app\modules\models\search\ProductSearch;
use app\modules\models\Product;
use app\models\form\ProductForm;
use Yii;
use yii\data\ActiveDataProvider;

class ProductController extends Controller{
    public function actionIndex(){
        $product = Product::find()->all();
        return $this->json(true, ["products" => $product]);
    }

    public function actionView($id){
        $product = Product::find()->where(["id" => $id])->one();
        
        $product->load(Yii::$app->request->post());
        return $this->json(true, ["products" => $product]);
    }


    public function actionSearch(){
        $modelSearch = new ProductSearch();
        $dataProvider = $modelSearch->search(Yii::$app->request->getQueryParams());

        if($dataProvider->getCount() == 0){
            return $this->json(false, [], "Not found");
        }
        return $this->json(true, ["products" => $dataProvider->getModels()], "Find successfully");
    }

    public function actionCreate(){
        $productForm = new ProductForm();
        $productForm->load(Yii::$app->request->post());

        if(!$productForm->validate() || !$productForm->save()){
            return $this->json(false, ["errors" => $productForm->getErrors()], "Can't create new product");
        }

        return $this->json(true, ["product" => $productForm], "Create product successfully");
    }

    public function actionUpdate($id){
        $product = Product::find()->where(["id" => $id])->one();
        
        $product->load(Yii::$app->request->post());

        if(!$product->validate() || !$product->save()){
            return $this->json(false, ['errors' => $product->getErrors()], "Can't update product");
        }
        
        return $this->json(true, ['product' => $product], 'Update product successfully');
    }

    public function actionDelete($id){
        $product = Product::find()->where(["id" => $id])->one();
        
        if(!$product){
            return $this->json(false, [], "Product not found");
        }
        
        $product->load(Yii::$app->request->post());

        if(!$product->delete()){
            return $this->json(false, ['errors' => $product->getErrors()], "Can't delete product");
        }   
        
        return $this->json(true,[], 'Delete product successfully');

    }
}