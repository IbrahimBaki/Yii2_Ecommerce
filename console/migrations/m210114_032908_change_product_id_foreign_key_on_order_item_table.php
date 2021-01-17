<?php

use yii\db\Migration;

/**
 * Class m210114_032908_change_product_id_foreign_key_on_order_item_table
 */
class m210114_032908_change_product_id_foreign_key_on_order_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // drops foreign key for table `{{%products}}`
        $this->dropForeignKey(
            '{{%fk-order_items-product_id}}',
            '{{%order_items}}'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210114_032908_change_product_id_foreign_key_on_order_item_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210114_032908_change_product_id_foreign_key_on_order_item_table cannot be reverted.\n";

        return false;
    }
    */
}
