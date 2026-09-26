<?php

use yii\db\Migration;

/**
 * Class m220621_051952_migrate_skema_fisioterapi_MHG2639_kelompokpemeriksaanfisio_m
 */
class m220621_051952_migrate_skema_fisioterapi_MHG2639_kelompokpemeriksaanfisio_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."kelompokpemeriksaanfisio_m" 
            ADD COLUMN IF NOT EXISTS "kelompoktindakan_id" int4;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220621_051952_migrate_skema_fisioterapi_MHG2639_kelompokpemeriksaanfisio_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220621_051952_migrate_skema_fisioterapi_MHG2639_kelompokpemeriksaanfisio_m cannot be reverted.\n";

        return false;
    }
    */
}
