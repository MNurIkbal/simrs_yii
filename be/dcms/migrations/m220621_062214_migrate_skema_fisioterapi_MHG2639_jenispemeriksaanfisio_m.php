<?php

use yii\db\Migration;

/**
 * Class m220621_062214_migrate_skema_fisioterapi_MHG2639_jenispemeriksaanfisio_m
 */
class m220621_062214_migrate_skema_fisioterapi_MHG2639_jenispemeriksaanfisio_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."jenispemeriksaanfisio_m" 
            ADD COLUMN IF NOT EXISTS "is_nonpaket" bool DEFAULT false;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220621_062214_migrate_skema_fisioterapi_MHG2639_jenispemeriksaanfisio_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220621_062214_migrate_skema_fisioterapi_MHG2639_jenispemeriksaanfisio_m cannot be reverted.\n";

        return false;
    }
    */
}
