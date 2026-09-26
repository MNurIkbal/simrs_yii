<?php

use yii\db\Migration;

/**
 * Class m220408_065217_migrate_multypayer_table_obatalkespasien_r
 */
class m220408_065217_migrate_multypayer_table_obatalkespasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."obatalkespasien_r" ADD COLUMN IF NOT EXISTS "is_penjaminutama" bool;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065217_migrate_multypayer_table_obatalkespasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065217_migrate_multypayer_table_obatalkespasien_r cannot be reverted.\n";

        return false;
    }
    */
}
