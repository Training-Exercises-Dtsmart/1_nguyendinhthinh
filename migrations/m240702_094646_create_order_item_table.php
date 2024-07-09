<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_detail}}`.
 */
class m240702_094646_create_order_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_item}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'product_id' => $this->integer(),
            'quantity' => $this->integer()->unsigned(),
            'price' => $this->double(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-order-item-order_id', 'order_item', 'order_id', 'order', 'id', 'CASCADE');

        $this->addForeignKey('fk-order-item-product_id', 'order_item', 'product_id', 'product', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order-item-product_id', 'order_item');

        $this->dropForeignKey('fk-order-item-order_id', 'order_item');

        $this->dropTable('{{%order_item}}');
    }
}
