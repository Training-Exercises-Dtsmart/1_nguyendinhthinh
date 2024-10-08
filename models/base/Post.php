<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use \app\models\query\PostQuery;

/**
 * This is the base-model class for table "post".
 *
 * @property integer $id
 * @property string $title
 * @property string $body
 * @property integer $views
 * @property string $publish_date
 * @property integer $user_id
 * @property integer $category_post_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \app\models\CategoryPost $categoryPost
 * @property \app\models\User $user
 */
abstract class Post extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
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
            [['title'], 'required'],
            [['body'], 'string'],
            [['views', 'user_id', 'category_post_id', 'status'], 'integer'],
            [['publish_date'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'unique'],
            [['category_post_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\CategoryPost::class, 'targetAttribute' => ['category_post_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['user_id' => 'id']]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => 'ID',
            'title' => 'Title',
            'body' => 'Body',
            'views' => 'Views',
            'publish_date' => 'Publish Date',
            'user_id' => 'User ID',
            'category_post_id' => 'Category Post ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryPost()
    {
        return $this->hasOne(\app\models\CategoryPost::class, ['id' => 'category_post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostQuery(static::class);
    }
}
