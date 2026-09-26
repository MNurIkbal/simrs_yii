<?php

use yii\db\Migration;

/**
 * Class m201020_035752_oddo_penyesuaiantabel_20201020
 */
class m201020_035752_oddo_penyesuaiantabel_20201020 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."daftartindakan_m" ADD IF NOT EXISTS "servicecategory_id" int4;');
        $this->execute('ALTER TABLE "public"."daftartindakan_m" ADD IF NOT EXISTS "servicegroup_id" int4;');
        $this->execute('COMMENT ON COLUMN "public"."daftartindakan_m"."servicecategory_id" IS \'kebutuhan ODDO]\';');
        $this->execute('COMMENT ON COLUMN "public"."daftartindakan_m"."servicegroup_id" IS \'kebutuhan ODDO\';');

        $this->execute('ALTER TABLE "public"."jenisnontunai_m" ADD IF NOT EXISTS "tipe_pembayaran" int4;');
        $this->execute('COMMENT ON COLUMN "public"."jenisnontunai_m"."tipe_pembayaran" IS \'lookup_type = tipe_pembayaran\';');

        $this->execute('ALTER TABLE "public"."jenisobatalkes_m" ADD IF NOT EXISTS "servicecategory_id" int4;');
        $this->execute('ALTER TABLE "public"."jenisobatalkes_m" ADD IF NOT EXISTS "servicegroup_id" int4;');
        $this->execute('COMMENT ON COLUMN "public"."jenisobatalkes_m"."servicecategory_id" IS \'kebutuhan ODDO\';');
        $this->execute('COMMENT ON COLUMN "public"."jenisobatalkes_m"."servicegroup_id" IS \'kebutuhan ODDO\';');

        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD IF NOT EXISTS "no_obatalkespasien" varchar(255) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD IF NOT EXISTS "tarif_dijamin" float8 DEFAULT 0;');
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD IF NOT EXISTS "tarif_dibayarkan" float8 DEFAULT 0;');

        $this->execute('ALTER TABLE "public"."pegawai_m" ADD IF NOT EXISTS "spesialis_id" int4;');

        $this->execute('ALTER TABLE "public"."pembayaranmetode_t" ADD IF NOT EXISTS "pendaftaran_id" int4;');
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD IF NOT EXISTS "no_tindakanpelayanan" varchar(255) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD IF NOT EXISTS "tarif_dijamin" float8 DEFAULT 0;');
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD IF NOT EXISTS "tarif_dibayarkan" float8 DEFAULT 0;');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201020_035752_oddo_penyesuaiantabel_20201020 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_035752_oddo_penyesuaiantabel_20201020 cannot be reverted.\n";

        return false;
    }
    */
}
