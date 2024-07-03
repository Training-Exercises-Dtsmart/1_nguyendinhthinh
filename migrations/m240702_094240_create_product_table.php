<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m240702_094240_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'product_name' => $this->string()->notNull()->unique(),
            'price' => $this->double()->notNull(),
            'description' => $this->text(),
            'image' => $this->text(),
            'stock'=> $this->integer()->defaultValue(0),
            'status' => $this->integer()->defaultValue(0),
            'category_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            
        ]);

        $this->addForeignKey('fk-product-category_id', 'product', 'category_id', 'category_product', 'category_id', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropForeignKey('fk-product-category_id', 'product');

        $this->dropTable('{{%product}}');
    }
}
