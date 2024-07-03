<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use \app\models\query\CategoryProductQuery;

/**
 * This is the base-model class for table "category_product".
 *
 * @property integer $category_id
 * @property string $category_name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \app\models\Product[] $products
 */
abstract class CategoryProduct extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_product';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'value' => (new \DateTime())->format('Y-m-d H:i:s'),
                        ];
        
    return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        return ArrayHelper::merge($parentRules, [
            [['category_name'], 'required'],
            [['category_name'], 'string', 'max' => 255],
            [['category_name'], 'unique']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'category_id' => 'Category ID',
            'category_name' => 'Category Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(\app\models\Product::class, ['category_id' => 'category_id']);
    }

    /**
     * @inheritdoc
     * @return CategoryProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryProductQuery(static::class);
    }
}
