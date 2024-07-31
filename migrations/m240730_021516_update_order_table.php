<?php

use yii\db\Migration;

/**
 * Class m240730_021516_update_order_table
 */
class m240730_021516_update_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'payment_method', $this->integer());
        $this->addColumn('order', 'payment_status', $this->integer());
        $this->addColumn('order', 'app_trans_id', $this->string());
        $this->addColumn('order', 'zp_trans_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'payment_method');
        $this->dropColumn('order', 'payment_status');
        $this->dropColumn('order', 'app_trans_id');
        $this->dropColumn('order', 'zp_trans_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240730_021516_update_order_table cannot be reverted.\n";

        return false;
    }
    */
}
