<?php

use yii\db\Migration;

/**
 * Class m240723_042934_update_user_table
 */
class m240723_042934_update_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'reset_password_token', $this->string());
        $this->addColumn('user', 'image', $this->string()->after('telephone'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'image');
        $this->dropColumn('user', 'reset_password_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240723_042934_update_user_table cannot be reverted.\n";

        return false;
    }
    */
}
