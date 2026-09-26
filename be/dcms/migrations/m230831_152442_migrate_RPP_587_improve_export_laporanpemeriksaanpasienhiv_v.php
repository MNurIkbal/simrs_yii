<?php

use yii\db\Migration;

/**
 * Class m230831_152442_migrate_RPP_587_improve_export_laporanpemeriksaanpasienhiv_v
 */
class m230831_152442_migrate_RPP_587_improve_export_laporanpemeriksaanpasienhiv_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpemeriksaanpasienhiv_v";');
        $this->execute('
			CREATE OR REPLACE VIEW public.laporanpemeriksaanpasienhiv_v
        AS  SELECT pendaftaran_t.tgl_pendaftaran AS "Tanggal Pendaftaran",
    pasien_m.no_rekam_medik AS "No Rekam Medik",
    pasien_m.nama_pasien AS "Nama Pasien",
    jk.lookup_name AS "Jenis Kelamin",
    pasien_m.tanggal_lahir AS "Tanggal lahir",
    daftartindakan_m.daftartindakan_nama AS "Tindakan",
    pasien_m.alamat_pasien AS "Alamat",
    kelurahan_m.kelurahan_nama AS "Kelurahan",
    kecamatan_m.kecamatan_nama AS "Kecamatan",
    kabupaten_m.kabupaten_nama AS "Kabupaten",
    propinsi_m.propinsi_nama AS "Propinsi",
    concat(\'="\',
        CASE
            WHEN pasien_m.jenisidentitas::text = \'94\'::character varying::text THEN pasien_m.no_identitas_pasien
            ELSE \'-\'::character varying
        END, \'"\')::character varying AS "No KTP",
    ruangan_m.ruangan_nama AS "Nama Ruangan"
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
  WHERE daftartindakan_m.daftartindakan_nama::text ~~* \'%HIV%\'::text AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true AND (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2]))
  GROUP BY pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jk.lookup_name, pasien_m.tanggal_lahir, daftartindakan_m.daftartindakan_nama, pasien_m.alamat_pasien, kelurahan_m.kelurahan_nama, kecamatan_m.kecamatan_nama, kabupaten_m.kabupaten_nama, propinsi_m.propinsi_nama, pasien_m.jenisidentitas, pasien_m.no_identitas_pasien, ruangan_m.ruangan_nama
  ORDER BY pendaftaran_t.tgl_pendaftaran;');
  
  
	    $this->execute("
	  	 DELETE FROM konfiglaporan_k where source_view = 'laporanpemeriksaanpasienhiv_v' ;
	  	  ");
  
  $this->execute("
	  INSERT INTO public.konfiglaporan_k ( key_laporan, jenis_laporan, source_view, filter, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer) VALUES 
	  ( 'laporan_kasir', 'Laporan Pemeriksaan HIV', 'laporanpemeriksaanpasienhiv_v', 'Tanggal Pendaftaran', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, NULL);
	  ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230831_152442_migrate_RPP_587_improve_export_laporanpemeriksaanpasienhiv_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230831_152442_migrate_RPP_587_improve_export_laporanpemeriksaanpasienhiv_v cannot be reverted.\n";

        return false;
    }
    */
}
