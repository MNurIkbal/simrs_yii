<?php

use yii\db\Migration;

/**
 * Class m211228_111155_migrate_US2524_move_category_id
 */
class m211228_111155_migrate_US2524_move_category_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		 $this->execute('ALTER TABLE "public"."basecalro_r"  ADD COLUMN IF NOT EXISTS move_category_id int4;');
		 
		 $this->execute('ALTER TABLE "public"."purchasereqdetail_t"  ADD COLUMN IF NOT EXISTS move_category_id int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211228_111155_migrate_US2524_move_category_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211228_111155_migrate_US2524_move_category_id cannot be reverted.\n";

        return false;
    }
    */
}
