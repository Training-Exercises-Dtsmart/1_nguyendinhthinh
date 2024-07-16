<?php

use yii\db\Migration;

/**
 * Class m240715_024448_update_user_table
 */
class m240715_024448_update_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'email', $this->string()->after('password'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'email');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240715_024448_update_user_table cannot be reverted.\n";

        return false;
    }
    */
}
