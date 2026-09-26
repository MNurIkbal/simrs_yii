<?php

use yii\db\Migration;

/**
 * Class m230213_093549_migrate_GB768_view_ringkasanmedispeasien_v
 */
class m230213_093549_migrate_GB768_view_ringkasanmedispeasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."ringkasanmedispasien_v";
        ');  

        $this->execute('
            CREATE VIEW "public"."ringkasanmedispasien_v" AS  SELECT \'RJ\'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik, 
    pasien_m.nama_pasien,
    pendaftaran_t.tgl_pendaftaran,
    soaprj_t.tgl_soaprj,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai AS nama_dokter,
    soaprj_t.a_diag_utama,
    soaprj_t.planning AS obat_tindakan,
    carakeluar_m.carakeluar_nama AS tindak_lanjut,
    soaprj_t.subject AS anamnesa,
    soaprj_t.object
   FROM soaprj_t
     JOIN pendaftaran_t ON soaprj_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id AND pegawai_m.kelompokpegawai_id = 1
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
  WHERE soaprj_t.is_deleted IS FALSE
UNION ALL
 SELECT \'IGD\'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.tgl_pendaftaran,
    cppt_t.tgl_cppt AS tgl_soaprj,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai AS nama_dokter,
    cppt_t.a_diag_utama,
    cppt_t.planning AS obat_tindakan,
    carakeluar_m.carakeluar_nama AS tindak_lanjut,
    cppt_t.subject AS anamnesa,
    cppt_t.object
   FROM cppt_t
     JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON cppt_t.pegawai_id = pegawai_m.pegawai_id AND pegawai_m.kelompokpegawai_id = 1
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
  WHERE cppt_t.is_deleted IS FALSE AND cppt_t.pasienadmisi_id IS NULL;
        ');  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230213_093549_migrate_GB768_view_ringkasanmedispeasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230213_093549_migrate_GB768_view_ringkasanmedispeasien_v cannot be reverted.\n";

        return false;
    }
    */
}
