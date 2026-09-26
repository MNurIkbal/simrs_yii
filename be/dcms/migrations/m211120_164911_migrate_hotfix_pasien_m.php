<?php

use yii\db\Migration;

/**
 * Class m211120_164911_migrate_hotfix_pasien_m
 */
class m211120_164911_migrate_hotfix_pasien_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasien_m" 
          ADD COLUMN IF NOT EXISTS "departemen" varchar(50) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "posisi_bagian" varchar(50) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "nama_perusahaan" varchar(50) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "nomorindukpegawai" varchar(30) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "no_kartu_keluarga" varchar(100) COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211120_164911_migrate_hotfix_pasien_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211120_164911_migrate_hotfix_pasien_m cannot be reverted.\n";

        return false;
    }
    */
}
