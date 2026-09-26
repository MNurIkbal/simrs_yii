<?php

use yii\db\Migration;

/**
 * Class m210624_070736_mmigrate_improve_perubahanschematable
 */
class m210624_070736_mmigrate_improve_perubahanschematable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" DROP COLUMN if exists "resiko_jatuh";');
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN if not exists "pupil_os_text" text COLLATE "pg_catalog"."default";');
        $this->execute('
ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN if not exists "pupil_od_text" text COLLATE "pg_catalog"."default";');

        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."nama_dpjp_melayani" IS \'Untuk nampung str dari bpjs agar tidak hit ws bpjs\';');
        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."nama_ppk_perujuk" IS \'Untuk nampung str dari bpjs agar tidak hit ws bpjs\';');
        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."kode_ppk_perujuk" IS \'Untuk nampung kode dari bpjs agar tidak hit ws bpjs\';');

        $this->execute('ALTER TABLE "public"."hasilpemeriksaanlab_integrasi_t" DROP CONSTRAINT if exists "hasilpemeriksaanlab_integrasi_t_pkey";');

        $this->execute('ALTER TABLE "public"."kamarruangan_m" ADD COLUMN if not exists "is_rekapkinerjaprofesi" bool DEFAULT true;');

        $this->execute('ALTER TABLE "public"."konfigsystem_k" ALTER COLUMN "dash_kamarheader" DROP NOT NULL;');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ALTER COLUMN "is_nourut" SET NOT NULL;');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ALTER COLUMN "is_nourut" SET DEFAULT false;');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN if not exists "support_multipayer" bool DEFAULT false;');

        $this->execute('ALTER TABLE "public"."pasien_m" ADD COLUMN if not exists "pasienubahdata_id" int4;');

        $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN if not exists "hakkelas_id" int4;');
        $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN if not exists "kelaspermintaan_id" int4;');
        $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN if not exists "dokterpengirim_id" int4;');
        $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN if not exists "dokterkonsul_id" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."pasienkirimkeunitlain_t" ADD COLUMN if not exists "pemakaian_implant" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."pasienkirimkeunitlain_t" ADD COLUMN if not exists "sewa_vendor" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."pasienkirimkeunitlain_t" ADD COLUMN if not exists "sewa_alat_rs" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."pasienkirimkeunitlain_t" ADD COLUMN if not exists "jenis_operasi_cito" bool;');
        $this->execute('ALTER TABLE "public"."pasienkirimkeunitlain_t" ADD COLUMN if not exists "jenis_operasi_elektif" bool;');
        $this->execute('ALTER TABLE "public"."pasienkirimkeunitlain_t" ADD COLUMN if not exists "jenis_operasi_odc" bool;');

        $this->execute('COMMENT ON COLUMN "public"."pasienpulang_t"."dpjp_id" IS \'diisi saat pulang ranap\';');

        $this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."is_bsl" IS \'status bsl\';');
        $this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."dokterpengganti_id" IS \'keperluan sty\';');

        $this->execute('COMMENT ON COLUMN "public"."penerimaanobatdetail_t"."harga" IS \'harga 1 qty sesuai satuan input\';');

        $this->execute('ALTER TABLE "public"."riwayathasipemeriksaanlab_r" ADD COLUMN if not exists "data" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."riwayathasipemeriksaanlab_r" ADD COLUMN if not exists "sub_data" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."riwayathasipemeriksaanlab_r" ADD COLUMN if not exists "satuan" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."riwayatsoap_r" ADD COLUMN if not exists "tindak_lanjut" varchar(100) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."riwayatsoap_r" ADD COLUMN if not exists "perawat" varchar(100) COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."riwayatsoap_r" ADD COLUMN if not exists "bagian" varchar(100) COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."rujukan_t" ADD COLUMN if not exists "diagnosa_text" json;');
        $this->execute('ALTER TABLE "public"."stokopnamedetail_t" ALTER COLUMN "volume_fisik" DROP NOT NULL;
');
        $this->execute('ALTER TABLE "public"."stokopnamedetail_t" ALTER COLUMN "volume_sistem" DROP NOT NULL;');
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210624_070736_mmigrate_improve_perubahanschematable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210624_070736_mmigrate_improve_perubahanschematable cannot be reverted.\n";

        return false;
    }
    */
}
