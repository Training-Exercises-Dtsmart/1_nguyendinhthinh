<?php

use yii\db\Migration;

/**
 * Class m240715_064436_update_user_table
 */
class m240715_064436_update_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'allowance', $this->integer()->defaultValue(100));
        $this->addColumn('{{%user}}', 'allowance_updated_at', $this->integer()->defaultValue(time()));
        $this->addColumn('{{%user}}', 'email_verified', $this->integer());
        $this->addColumn('{{%user}}', 'verification_token', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'allowance');
        $this->dropColumn('{{%user}}', 'allowance_updated_at');
        $this->dropColumn('{{%user}}', 'email_verified');
        $this->dropColumn('{{%user}}', 'verification_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240715_064436_update_user_table cannot be reverted.\n";

        return false;
    }
    */
}
