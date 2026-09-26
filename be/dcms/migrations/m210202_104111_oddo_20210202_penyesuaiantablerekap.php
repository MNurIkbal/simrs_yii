<?php

use yii\db\Migration;

/**
 * Class m210202_104111_oddo_20210202_penyesuaiantablerekap
 */
class m210202_104111_oddo_20210202_penyesuaiantablerekap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    
    $this->execute('ALTER TABLE "public"."pasien_r" DROP COLUMN if exists "sync_respon";');

    $this->execute('ALTER TABLE "public"."pasien_r" ADD COLUMN if not exists "sync_response" text COLLATE "pg_catalog"."default";');

    $this->execute('ALTER TABLE "public"."pasien_r" ADD COLUMN if not exists "sync_payload" text COLLATE "pg_catalog"."default";');



    $this->execute('ALTER TABLE "public"."pendaftaran_r" ADD COLUMN if not exists "id_sync_sercon" text COLLATE "pg_catalog"."default";');

    $this->execute('ALTER TABLE "public"."pendaftaran_r" ADD COLUMN if not exists "sync_response" text COLLATE "pg_catalog"."default";');

    $this->execute('ALTER TABLE "public"."pendaftaran_r" ADD COLUMN if not exists "sync_payload" text COLLATE "pg_catalog"."default";');



    $this->execute('ALTER TABLE "public"."penjualanresep_r" DROP COLUMN if exists "sync_respon";');

    $this->execute('ALTER TABLE "public"."penjualanresep_r" ADD COLUMN if not exists "sync_response" text COLLATE "pg_catalog"."default";');

    $this->execute('ALTER TABLE "public"."penjualanresep_r" ADD COLUMN if not exists "sync_payload" text COLLATE "pg_catalog"."default";');
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210202_104111_oddo_20210202_penyesuaiantablerekap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210202_104111_oddo_20210202_penyesuaiantablerekap cannot be reverted.\n";

        return false;
    }
    */
}
