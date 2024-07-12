<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category_post}}`.
 */
class m240702_094416_create_category_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category_post}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique(),
            'status' => $this->integer(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk_category_post_created_by', '{{%category_post}}', 'created_by', '{{%user}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_category_post_created_by', '{{%category_post}}');
        $this->dropTable('{{%category_post}}');
    }
}
