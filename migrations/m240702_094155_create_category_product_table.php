<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category_product}}`.
 */
class m240702_094155_create_category_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category_product}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'status' => $this->integer(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk_category_product_created_by', '{{%category_product}}', 'created_by', '{{%user}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_category_product_created_by', '{{%category_product}}');
        $this->dropTable('{{%category_product}}');
    }
}
