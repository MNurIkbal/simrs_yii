<?php

use yii\db\Migration;

/**
 * Class m230831_144352_migrate_RPP_585_improve_export_laporanpasientb_v
 */
class m230831_144352_migrate_RPP_585_improve_export_laporanpasientb_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpasientb_v";');
        $this->execute('
			CREATE OR REPLACE VIEW public.laporanpasientb_v
        AS  SELECT pendaftaran_t.tgl_pendaftaran AS "Tanggal Pendaftaran",
    pendaftaran_t.no_pendaftaran AS "Nomor Pendaftaran",
    pasien_m.no_rekam_medik AS "No Rekam Medik",
    pasien_m.nama_pasien AS "Nama Pasien",
    jk.lookup_name AS "Janis Kelamin",
        CASE
            WHEN pendaftaran_t.status_pasien::text = \'310\'::text THEN \'Y\'::text
            ELSE \'N\'::text
        END AS "Pasien Baru",
    ruangan_m.kode_ruanganpoli AS "Kode Ruangan",
    ruangan_m.ruangan_nama AS "Nama Ruangan",
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN \'Y\'::text
            ELSE \'N\'::text
        END AS "Rawat Jalan",
    pasien_m.tanggal_lahir AS "Tanggal lahir",
    pendaftaran_t.umur AS "Umur",
    diagnosa_m.diagnosa_kode AS "Kode Diagnosa",
    diagnosa_m.diagnosa_nama AS "Nama Diagnosa",
        CASE
            WHEN pendaftaran_t.kunjungan::text = \'181\'::text THEN \'Kunjungan Lama\'::text
            ELSE \'Kunjungan Baru\'::text
        END AS "Status Kunjungan",
    pasien_m.alamat_pasien AS "Alamat",
    kelurahan_m.kelurahan_nama AS "Kelurahan",
    kecamatan_m.kecamatan_nama AS "Kecamatan",
    kabupaten_m.kabupaten_nama AS "Kabupaten",
    propinsi_m.propinsi_nama AS "Propinsi",
        CASE
            WHEN pasien_m.jenisidentitas::text = \'94\'::text THEN pasien_m.no_identitas_pasien
            ELSE \'-\'::character varying
        END AS "No KTP"
   FROM koreksidiagnosa_t
     JOIN pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
     LEFT JOIN propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
  WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 AND diagnosa_m.diagnosa_nama::text ~~* \'%TUBERCULOSIS%\'::text AND koreksidiagnosa_t.is_deleted = false AND koreksidiagnosa_t.is_active = true AND (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2]))
  ORDER BY pendaftaran_t.tgl_pendaftaran;');
  
  
  $this->execute("
	  INSERT INTO public.konfiglaporan_k ( key_laporan, jenis_laporan, source_view, filter, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer) 
	  VALUES ( 'laporan_kasir', 'Laporan Pasien TB', 'laporanpasientb_v', 'Tanggal Pendaftaran', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, NULL);
	  ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230831_144352_migrate_RPP_585_improve_export_laporanpasientb_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230831_144352_migrate_RPP_585_improve_export_laporanpasientb_v cannot be reverted.\n";

        return false;
    }
    */
}
