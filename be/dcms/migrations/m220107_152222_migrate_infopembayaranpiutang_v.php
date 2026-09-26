<?php

use yii\db\Migration;

/**
 * Class m220107_152222_migrate_infopembayaranpiutang_v
 */
class m220107_152222_migrate_infopembayaranpiutang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopembayaranpiutang_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infopembayaranpiutang_v\" AS  SELECT pembayaranpiutang_t.pembayaranpiutang_id,
    pembayaranpiutang_t.no_pembayaranpiutang,
    pembayaranpiutang_t.tgl_pembayaranpiutang,
    pembayaranpiutang_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pembayaranpiutang_t.total_bayarpiutang,
    pembayaranpiutang_t.catatan,
    pembayaranpiutang_t.created_date,
    pegawai_m.nama_pegawai
   FROM pembayaranpiutang_t
     JOIN pendaftaran_t ON pembayaranpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = pembayaranpiutang_t.pegawai_id
     JOIN pegawai_m ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id
  WHERE pembayaranpiutang_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220107_152222_migrate_infopembayaranpiutang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220107_152222_migrate_infopembayaranpiutang_v cannot be reverted.\n";

        return false;
    }
    */
}
