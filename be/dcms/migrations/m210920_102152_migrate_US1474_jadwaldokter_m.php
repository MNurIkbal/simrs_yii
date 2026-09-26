<?php

use yii\db\Migration;

/**
 * Class m210920_102152_migrate_US1474_jadwaldokter_m
 */
class m210920_102152_migrate_US1474_jadwaldokter_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."jadwaldokter_m" 
            ADD COLUMN IF NOT EXISTS "is_bersedia" bool DEFAULT false;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210920_102152_migrate_US1474_jadwaldokter_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210920_102152_migrate_US1474_jadwaldokter_m cannot be reverted.\n";

        return false;
    }
    */
}
