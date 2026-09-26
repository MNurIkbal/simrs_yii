<?php

use yii\db\Migration;

/**
 * Class m220222_070100_migrate_ORDH17_infopenerimaansupp_v
 */
class m220222_070100_migrate_ORDH17_infopenerimaansupp_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopenerimaansupp_v";');
        $this->execute("CREATE VIEW \"public\".\"infopenerimaansupp_v\" AS  SELECT penerimaansupp_t.penerimaansupp_id,
        penerimaansupp_t.no_penerimaan,
        penerimaansupp_t.tgl_penerimaan,
        penerimaansupp_t.no_faktur,
        penerimaansupp_t.supplier_id,
        supplier_m.supplier_nama,
        penerimaansupp_t.peg_menyetujui,
        peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
        penerimaansupp_t.peg_mengetahui,
        peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
        penerimaansupp_t.ruanganpenerima_id,
        ruangan_m.ruangan_nama,
        concat(pajak_m.pajak_name, ' (', pajak_m.pajak_persen, '%)') AS pajak_label,
        payterm_m.payterm_nama,
        penerimaansupp_t.is_verifikasi,
            CASE
                WHEN penerimaansupp_t.is_verifikasi = true THEN 'Sudah Verifikasi'::text
                ELSE 'Belum Verifikasi'::text
            END AS status_verifikasi,
        penerimaansupp_t.tgl_verifikasi,
        supplier_m.supplier_alamat,
        supplier_m.no_tlp,
        supplier_m.no_fax,
        peg_created.nama_pegawai AS peg_created,
        penerimaansupp_t.is_consigment,
        penerimaansupp_t.is_donasi,
        peg_penerima.nama_pegawai AS peg_penerima_nama
       FROM penerimaansupp_t
         JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
         LEFT JOIN pegawai_m peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
         LEFT JOIN pegawai_m peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
         LEFT JOIN loginpemakai_k ON penerimaansupp_t.created_by = loginpemakai_k.loginpemakai_id
         LEFT JOIN pegawai_m peg_created ON loginpemakai_k.pegawai_id = peg_created.pegawai_id
         JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
         LEFT JOIN pajak_m ON pajak_m.pajak_id = penerimaansupp_t.pajak_id
         LEFT JOIN payterm_m ON payterm_m.payterm_id = penerimaansupp_t.payterm_id
         LEFT JOIN pegawai_m peg_penerima ON penerimaansupp_t.peg_penerima_id = peg_penerima.pegawai_id
      WHERE penerimaansupp_t.is_tipe = 0;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220222_070100_migrate_ORDH17_infopenerimaansupp_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220222_070100_migrate_ORDH17_infopenerimaansupp_v cannot be reverted.\n";

        return false;
    }
    */
}
