<?php

use yii\db\Migration;

/**
 * Class m210219_042224_migrate_20210219_infopenerimaanbarangdetail_v
 */
class m210219_042224_migrate_20210219_infopenerimaanbarangdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."infopenerimaanbarangdetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"infopenerimaanbarangdetail_v\" AS  SELECT terima.penerimaanbarang_id,
    terima.tgl_penerimaan,
    terima.no_penerimaan,
    terima.nomor_po,
    terima.supplier_id,
    supplier_m.supplier_nama,
    terima.barang_id,
    barang_m.barang_nama,
    terima.qty_po,
    terima.qty_diterima,
    terima.po_balance,
        CASE
            WHEN barang_m.is_kadaluarsa = true THEN terima.tgl_kadaluarsa
            ELSE NULL::date
        END AS tgl_kadaluarsa,
    terima.no_batch,
    terima.s_konversibrg_id,
    satuankonversibrg_m.satuanbesar_id,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversibrg_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuanunit_nama,
    terima.no_suratjalan,
    terima.tgl_suratjalan,
    terima.no_faktur,
    terima.diterima_oleh,
    terima.keterangan,
    terima.upload_berkas,
    terima.catatan_berkas,
    terima.catatan,
    COALESCE(terima.is_verifikasi::integer, 0) AS status_invoice,
    besar.satuanunit_nama AS satuan_besar,
    kecil.satuanunit_nama AS satuan_kecil,
    barang_m.is_kadaluarsa,
    terima.penerimaanbarangdetail_id,
    terima.harga,
    terima.discount_rp,
    terima.discount,
    terima.jumlah,
    terima.validasipobarangdetail_id,
    terima.pajak_id,
    satuankonversibrg_m.nilai_konversi,
    COALESCE(returdetailjumlah.on_retur, 0::bigint) AS on_retur,
    barang_m.barang_kode AS kode_item
   FROM ( SELECT penerimaanbarang_t.penerimaanbarang_id,
            penerimaanbarang_t.tgl_penerimaan,
            penerimaanbarang_t.no_penerimaan,
            validasipobarang_t.no_pobarang AS nomor_po,
            penerimaanbarang_t.supplier_id,
            penerimaanbarang_t.no_suratjalan,
            penerimaanbarang_t.tgl_suratjalan,
            penerimaanbarang_t.no_faktur,
            penerimaanbarang_t.diterima_oleh,
            penerimaanbarang_t.upload_berkas,
            penerimaanbarang_t.catatan_berkas,
            penerimaanbarang_t.catatan,
            penerimaanbarang_t.peg_mengetahui,
            penerimaanbarang_t.peg_menyetujui,
            penerimaanbarangdetail_t.penerimaanbarangdetail_id,
            penerimaanbarangdetail_t.barang_id,
            penerimaanbarangdetail_t.qty_po,
            penerimaanbarangdetail_t.qty_diterima,
            penerimaanbarangdetail_t.po_balance,
            penerimaanbarangdetail_t.tgl_kadaluarsa,
            penerimaanbarangdetail_t.no_batch,
            penerimaanbarangdetail_t.s_konversibrg_id,
            penerimaanbarangdetail_t.keterangan,
            penerimaanbarangdetail_t.harga,
            penerimaanbarangdetail_t.discount_rp,
            penerimaanbarangdetail_t.discount,
            penerimaanbarangdetail_t.jumlah,
            penerimaanbarangdetail_t.validasipobarangdetail_id,
            penerimaanbarang_t.is_verifikasi,
            validasipobarang_t.pajak_id
           FROM penerimaanbarang_t
             JOIN penerimaanbarangdetail_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id
             JOIN validasipobarang_t ON penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id
          WHERE penerimaanbarangdetail_t.is_deleted = false) terima
     JOIN supplier_m ON terima.supplier_id = supplier_m.supplier_id
     JOIN barang_m ON terima.barang_id = barang_m.barang_id
     JOIN satuankonversibrg_m ON terima.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     JOIN satuanunit_m besar ON satuankonversibrg_m.satuanbesar_id = besar.satuanunit_id
     JOIN satuanunit_m kecil ON satuankonversibrg_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN pegawai_m peg_mengetahui ON terima.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON terima.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN ( SELECT returpenerimaanbarangdetail_t.penerimaanbarangdetail_id,
            sum(returpenerimaanbarangdetail_t.qty_retur) AS on_retur
           FROM returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t
          GROUP BY returpenerimaanbarangdetail_t.penerimaanbarangdetail_id) returdetailjumlah ON terima.penerimaanbarangdetail_id = returdetailjumlah.penerimaanbarangdetail_id;");

    $this->execute('ALTER TABLE "public"."infopenerimaanbarangdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210219_042224_migrate_20210219_infopenerimaanbarangdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210219_042224_migrate_20210219_infopenerimaanbarangdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
