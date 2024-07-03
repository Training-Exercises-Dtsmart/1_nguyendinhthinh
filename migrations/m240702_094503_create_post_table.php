<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m240702_094503_create_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%post}}', [
           'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->unique(),
            'body' => $this->text(),
            'publish_date' => $this->dateTime(),
            'user_id' => $this->integer(),
            'category_id' => $this->integer(),
            'status' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-post-category_id', 'post', 'category_id', 'category_post','category_id', 'CASCADE');

        $this->addForeignKey('fk-post-user_id', 'post', 'user_id', 'user','id', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-post-category_id', 'post');

        $this->dropForeignKey('fk-post-user_id', 'post');
        
        $this->dropTable('{{%post}}');
    }
}
