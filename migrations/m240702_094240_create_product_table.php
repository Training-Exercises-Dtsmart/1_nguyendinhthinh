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
            'name' => $this->string()->notNull()->unique(),
            'price' => $this->double()->notNull(),
            'stock' => $this->integer()->unsigned()->defaultValue(0),
            'description' => $this->text(),
            'thumbnail' => $this->text(),
            'status' => $this->integer(),
            'category_product_id' => $this->integer(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),

        ]);

        $this->addForeignKey('fk-product-category_product_id', 'product', 'category_product_id', 'category_product', 'id', 'CASCADE');

        $this->addForeignKey('fk-product-created_by', 'product', 'created_by', 'user', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product-category_product_id', 'product');
        $this->dropForeignKey('fk-product-category_product_id', 'product');
        $this->dropTable('{{%product}}');
    }
}
