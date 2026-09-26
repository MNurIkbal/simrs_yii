<?php

use yii\db\Migration;

/**
 * Class m211025_102021_migrate_pasien_indolab
 */
class m211025_102021_migrate_pasien_indolab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
  ADD COLUMN if not exists "is_indolab" bool DEFAULT false,
  ADD COLUMN if not exists "additional_indolab" text COLLATE "pg_catalog"."default";');

        $this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."is_indolab" IS \'khusus pasien indolab\';');

        $this->execute('DROP VIEW if exists "public"."infopasienindolab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienindolab_v\" AS  SELECT pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang AS no_lab,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.additional_indolab AS ref_pasien,
    (pendaftaran_t.additional_indolab::json ->> 'nama_pasien_indolab'::text)::character varying AS ref_nama_pasien,
    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
           FROM ( SELECT daftartindakan_m.daftartindakan_nama
                   FROM tindakanpelayanan_t
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false) x) AS nama_pemeriksaan
   FROM pendaftaran_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_masukpenunjang
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.jeniskelamin,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE pendaftaran_t.is_indolab = true;");

      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211025_102021_migrate_pasien_indolab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211025_102021_migrate_pasien_indolab cannot be reverted.\n";

        return false;
    }
    */
}
