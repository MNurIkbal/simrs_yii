<?php

use yii\db\Migration;

/**
 * Class m220214_115713_migrate_APD114_asuransipenjamin_pengajuanklaim_cetakanpdf
 */
class m220214_115713_migrate_APD114_asuransipenjamin_pengajuanklaim_cetakanpdf extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pengajuanklaim_t" 
            ADD COLUMN IF NOT EXISTS "tgl_keluardari" timestamp(0),
            ADD COLUMN IF NOT EXISTS "tgl_keluarsampai" timestamp(0);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220214_115713_migrate_APD114_asuransipenjamin_pengajuanklaim_cetakanpdf cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220214_115713_migrate_APD114_asuransipenjamin_pengajuanklaim_cetakanpdf cannot be reverted.\n";

        return false;
    }
    */
}
