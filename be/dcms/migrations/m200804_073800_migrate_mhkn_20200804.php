<?php

use yii\db\Migration;

/**
 * Class m200804_073800_migrate_mhkn_20200804
 */
class m200804_073800_migrate_mhkn_20200804 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."pasienmasukpenunjang_t" ALTER COLUMN "is_bayar" SET DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200804_073800_migrate_mhkn_20200804 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200804_073800_migrate_mhkn_20200804 cannot be reverted.\n";

        return false;
    }
    */
}
