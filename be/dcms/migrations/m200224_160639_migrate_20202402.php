<?php

use yii\db\Migration;

/**
 * Class m200224_160639_migrate_20202402
 */
class m200224_160639_migrate_20202402 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."penjualanresep_t" ADD COLUMN "pembatalanresep_id" int4;');

         $this->execute('ALTER TABLE "public"."stokobatalkes_t" ADD COLUMN "pembatalanresep_id" int4;');

         
         

         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200224_160639_migrate_20202402 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200224_160639_migrate_20202402 cannot be reverted.\n";

        return false;
    }
    */
}
