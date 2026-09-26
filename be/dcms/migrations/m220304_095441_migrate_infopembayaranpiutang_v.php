<?php

use yii\db\Migration;

/**
 * Class m220304_095441_migrate_infopembayaranpiutang_v
 */
class m220304_095441_migrate_infopembayaranpiutang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."infopembayaranpiutang_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infopembayaranpiutang_v\" AS  SELECT pembayaranpiutang_t.pembayaranpiutang_id,
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
    pegawai_m.nama_pegawai
   FROM pembayaranpiutang_t
     JOIN pemberianpiutang_t ON pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
     LEFT JOIN pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN penjualanresep_t ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pegawai_m karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = pembayaranpiutang_t.created_by
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id
  WHERE pembayaranpiutang_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220304_095441_migrate_infopembayaranpiutang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220304_095441_migrate_infopembayaranpiutang_v cannot be reverted.\n";

        return false;
    }
    */
}
