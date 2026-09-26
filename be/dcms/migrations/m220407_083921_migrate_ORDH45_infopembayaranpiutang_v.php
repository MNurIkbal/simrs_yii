<?php

use yii\db\Migration;

/**
 * Class m220407_083921_migrate_ORDH45_infopembayaranpiutang_v
 */
class m220407_083921_migrate_ORDH45_infopembayaranpiutang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopembayaranpiutang_v";');
        $this->execute("CREATE VIEW \"public\".\"infopembayaranpiutang_v\" AS  SELECT pembayaranpiutang_t.pembayaranpiutang_id,
        pembayaranpiutang_t.no_pembayaranpiutang,
        pembayaranpiutang_t.tgl_pembayaranpiutang,
        COALESCE(pemberianpiutang_t.pendaftaran_id, pemberianpiutang_t.penjualanresep_id) AS pendaftaran_id,
        COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
        pasien_m.pasien_id,
        penjualanresep_t.pegawai_id AS karyawan_id,
        penjualanresep_t.pasien_id AS pasienresep_id,
        pasien_m.no_rekam_medik,
        COALESCE(penjualanresep_t.nama_pembeli, karyawan.nama_pegawai, pasien_m.nama_pasien) AS nama_pasien,
        pembayaranpiutang_t.total_bayarpiutang,
        pembayaranpiutang_t.catatan,
        pembayaranpiutang_t.created_date,
        pegawai_m.nama_pegawai,
        pembayaranpiutang_t.metode_pembayaran,
        metode_bayar.lookup_value AS metode_pembayaran_nama,
        pembayaranpiutang_t.jenisnontunai_id,
        jenis_nontunai.nama AS jenisnontunai_nama,
        pemberianpiutang_t.total_sisapiutang
       FROM pembayaranpiutang_t
         JOIN ( SELECT a.pemberianpiutang_id,
                a.pendaftaran_id,
                a.penjualanresep_id,
                a.total_sisapiutang
               FROM pemberianpiutang_t a) pemberianpiutang_t ON pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
         LEFT JOIN ( SELECT a.pendaftaran_id,
                a.no_pendaftaran,
                a.pasien_id
               FROM pendaftaran_t a) pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         LEFT JOIN ( SELECT a.pasien_id,
                a.no_rekam_medik,
                a.nama_pasien
               FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         LEFT JOIN ( SELECT a.penjualanresep_id,
                a.pegawai_id,
                a.pasien_id,
                a.noresep,
                a.karyawan_id,
                a.nama_pembeli
               FROM penjualanresep_t a) penjualanresep_t ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
         LEFT JOIN ( SELECT a.loginpemakai_id,
                a.pegawai_id
               FROM loginpemakai_k a) loginpemakai_k ON loginpemakai_k.loginpemakai_id = pembayaranpiutang_t.created_by
         LEFT JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) pegawai_m ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id
         LEFT JOIN ( SELECT a.lookup_id,
                a.lookup_type,
                a.lookup_value
               FROM lookup_m a) metode_bayar ON metode_bayar.lookup_type::text = 'metode_bayar'::text AND pembayaranpiutang_t.metode_pembayaran = metode_bayar.lookup_id
         LEFT JOIN ( SELECT a.jenisnontunai_id,
                a.nama
               FROM jenisnontunai_m a) jenis_nontunai ON pembayaranpiutang_t.jenisnontunai_id = jenis_nontunai.jenisnontunai_id
      WHERE pembayaranpiutang_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220407_083921_migrate_ORDH45_infopembayaranpiutang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_083921_migrate_ORDH45_infopembayaranpiutang_v cannot be reverted.\n";

        return false;
    }
    */
}
