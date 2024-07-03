<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_detail}}`.
 */
class m240702_094646_create_order_detail_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_detail}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'product_id' => $this->integer(),
            'quantity' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-order-detail-order_id', 'order_detail', 'order_id', 'order', 'id', 'CASCADE');

        $this->addForeignKey('fk-order-detail-product_id', 'order_detail', 'product_id', 'product', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order-detail-order_id', 'order_detail');

        $this->dropForeignKey('fk-order-detail-product_id', 'order_detail');

        $this->dropTable('{{%order_detail}}');
    }
}
