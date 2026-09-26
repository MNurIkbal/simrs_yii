<?php

use yii\db\Migration;

/**
 * Class m210219_041850_migrate_20210219_infopenerimaanbarang_v
 */
class m210219_041850_migrate_20210219_infopenerimaanbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."infopenerimaanbarang_v";');

    $this->execute("
        CREATE VIEW \"public\".\"infopenerimaanbarang_v\" AS  SELECT penerimaanbarang_t.penerimaanbarang_id,
    penerimaanbarang_t.no_penerimaan,
    penerimaanbarang_t.tgl_penerimaan,
    penerimaanbarang_t.supplier_id,
    supplier_m.supplier_nama,
    validasipobarang_t.no_pobarang,
    penerimaanbarang_t.no_suratjalan,
    penerimaanbarang_t.tgl_suratjalan,
    penerimaanbarang_t.no_faktur,
    penerimaanbarang_t.catatan,
    penerimaanbarang_t.peg_mengetahui,
    peg_mengetahui.nama_pegawai AS mengetahui,
    penerimaanbarang_t.peg_menyetujui,
    peg_menyetujui.nama_pegawai AS menyetujui,
    penerimaanbarang_t.diterima_oleh,
    peg_menerima.nama_pegawai AS menerima,
    'Gudang Umum'::text AS nama_ruangan,
    validasipobarang_t.no_pobarang AS no_transaksi,
    penerimaanbarang_t.is_verifikasi,
        CASE
            WHEN penerimaanbarang_t.is_verifikasi = 0 THEN 'belum verifikasi'::text
            WHEN penerimaanbarang_t.is_verifikasi = 1 THEN 'sudah verifikasi'::text
            WHEN penerimaanbarang_t.is_verifikasi = 2 THEN 'batal'::text
            ELSE NULL::text
        END AS verifikasi_nama,
    validasipobarang_t.pajak_id,
    pajak_m.pajak_persen,
    validasipobarang_t.sub_total,
    validasipobarang_t.total_discount,
    validasipobarang_t.ppn_persen,
    validasipobarang_t.ppn_nilai,
    validasipobarang_t.total,
    supplier_m.no_tlp,
    supplier_m.no_fax,
    supplier_m.supplier_alamat
   FROM penerimaanbarang_t
     JOIN validasipobarang_t ON penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id
     JOIN supplier_m ON supplier_m.supplier_id = penerimaanbarang_t.supplier_id
     LEFT JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaanbarang_t.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaanbarang_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_menerima ON penerimaanbarang_t.diterima_oleh = peg_menerima.pegawai_id;");
    
    $this->execute('ALTER TABLE "public"."infopenerimaanbarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210219_041850_migrate_20210219_infopenerimaanbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210219_041850_migrate_20210219_infopenerimaanbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
