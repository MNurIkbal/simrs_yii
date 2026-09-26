<?php

use yii\db\Migration;

/**
 * Class m230710_150506_migrate_RPP_366_imptovr_infopembayarantransaksi_v
 */
class m230710_150506_migrate_RPP_366_imptovr_infopembayarantransaksi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopembayarantransaksi_v");

        $this->execute("CREATE OR REPLACE VIEW public.infopembayarantransaksi_v
            AS  SELECT pembayarantransaksi_t.pembayarantransaksi_id,
    pembayarantransaksi_t.jenis_transaksi,
    fgetnamalookup(pembayarantransaksi_t.jenis_transaksi::integer) AS jenis,
    pembayarantransaksi_t.tgl_transaksi,
    pembayarantransaksi_t.no_transaksi,
    pembayarantransaksi_t.tipe_transaksi,
    fgetnamalookup(pembayarantransaksi_t.tipe_transaksi::integer) AS tipe,
    pembayarantransaksi_t.supplier_id,
    supplier_m.supplier_nama,
    pembayarantransaksi_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pembayarantransaksi_t.pasien_id,
    pasien_m.nama_pasien,
    pembayarantransaksi_t.metode_pembayaran,
    fgetnamalookup(pembayarantransaksi_t.metode_pembayaran::integer) AS metode_pembayaran_nama,
    pembayarantransaksi_t.jumlah,
    pembayarantransaksi_t.kategoritransaksi_id,
    kategoritransaksi_m.kategoritransaksi_nama,
    pembayarantransaksi_t.deskripsi,
    pembayarantransaksi_t.referensi,
        CASE
            WHEN pembayarantransaksi_t.tipe_transaksi = 700 THEN supplier_m.supplier_nama
            WHEN pembayarantransaksi_t.tipe_transaksi = 701 THEN pegawai_m.nama_pegawai
            WHEN pembayarantransaksi_t.tipe_transaksi = 702 THEN pasien_m.nama_pasien
            ELSE NULL::character varying
        END AS dari_kepada,
    pembayarantransaksi_t.created_by,
    pembuat.nama_pegawai AS created_by_nama
   FROM pembayarantransaksi_t
     LEFT JOIN pegawai_m ON pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pasien_m ON pembayarantransaksi_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN supplier_m ON pembayarantransaksi_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN kategoritransaksi_m ON pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id
     LEFT JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = pembayarantransaksi_t.created_by
     LEFT JOIN pegawai_m pembuat ON pembuat.pegawai_id = loginpemakai_k.pegawai_id
  WHERE pembayarantransaksi_t.is_deleted = false  ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230710_150506_migrate_RPP_366_imptovr_infopembayarantransaksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230710_150506_migrate_RPP_366_imptovr_infopembayarantransaksi_v cannot be reverted.\n";

        return false;
    }
    */
}
