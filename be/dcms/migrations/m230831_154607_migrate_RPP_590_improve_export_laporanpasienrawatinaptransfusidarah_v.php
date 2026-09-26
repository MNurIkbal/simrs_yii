<?php

use yii\db\Migration;

/**
 * Class m230831_154607_migrate_RPP_590_improve_export_laporanpasienrawatinaptransfusidarah_v
 */
class m230831_154607_migrate_RPP_590_improve_export_laporanpasienrawatinaptransfusidarah_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpasienrawatinaptransfusidarah_v";');
        $this->execute('
			CREATE OR REPLACE VIEW public.laporanpasienrawatinaptransfusidarah_v
        AS  SELECT pasien_m.no_rekam_medik AS "No Rekam Medik",
    pendaftaran_t.no_pendaftaran AS "No Pendaftaran",
    pasienadmisi_t.tgl_admisi AS "Tanggal Pendaftaran",
    pasien_m.nama_pasien AS "Nama",
    pasien_m.tanggal_lahir AS "Tanggal Lahir",
    ruangan_m.ruangan_nama AS "Ruangan",
    propinsi_m.propinsi_nama AS "Provinsi",
    kabupaten_m.kabupaten_nama AS "Kabupaten",
    kecamatan_m.kecamatan_nama AS "Kecamatan",
    kelurahan_m.kelurahan_nama AS "Kelurahan",
    jk.lookup_name AS "Jenis Kelamin",
    pasien_m.alamat_pasien AS "Alamat",
    pasien_m.no_telepon_pasien AS "No Telpone",
    ji.lookup_name AS "Jenis Identitas",
    concat(\'=\',
        CASE
            WHEN pasien_m.jenisidentitas::text = \'94\'::character varying::text THEN pasien_m.no_identitas_pasien
            ELSE \'-\'::character varying
        END, \'"\')::character varying AS "No Identitas",
    penanggungjawab_m.penanggungjawab_nama AS "Nama Penanggung Jawab"
   FROM pasienadmisi_t
     JOIN tindakanpelayanan_t ON tindakanpelayanan_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     LEFT JOIN propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     LEFT JOIN lookup_m ji ON pasien_m.jenisidentitas::integer = ji.lookup_id
  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true AND pasienadmisi_t.status_ranap <> 453 AND tindakanpelayanan_t.daftartindakan_id = 3478
  GROUP BY pasien_m.no_rekam_medik, pendaftaran_t.no_pendaftaran, pasienadmisi_t.tgl_admisi, pasien_m.nama_pasien, pasien_m.tanggal_lahir, ruangan_m.ruangan_nama, propinsi_m.propinsi_nama, kabupaten_m.kabupaten_nama, kecamatan_m.kecamatan_nama, kelurahan_m.kelurahan_nama, jk.lookup_name, pasien_m.alamat_pasien, pasien_m.no_telepon_pasien, ji.lookup_name, pasien_m.jenisidentitas, pasien_m.no_identitas_pasien, penanggungjawab_m.penanggungjawab_nama
  ORDER BY pasienadmisi_t.tgl_admisi ;');
  
  
	    $this->execute("
	  	 DELETE FROM konfiglaporan_k where source_view = 'laporanpasienrawatinaptransfusidarah_v' ;
	  	  ");
  
  $this->execute("
	  INSERT INTO public.konfiglaporan_k ( key_laporan, jenis_laporan, source_view, filter, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer) 
	  VALUES ( 'laporan_kasir', 'Laporan pasien transfusi darah', 'laporanpasienrawatinaptransfusidarah_v', 'Tanggal Pendaftaran', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, NULL);
	  ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230831_154607_migrate_RPP_590_improve_export_laporanpasienrawatinaptransfusidarah_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230831_154607_migrate_RPP_590_improve_export_laporanpasienrawatinaptransfusidarah_v cannot be reverted.\n";

        return false;
    }
    */
}
