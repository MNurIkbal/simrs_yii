<?php

use yii\db\Migration;

/**
 * Class m220330_041031_migrate_cdh288_infopenerimaanobatdetail_v
 */
class m220330_041031_migrate_cdh288_infopenerimaanobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           DROP VIEW IF EXISTS "public"."infopenerimaanobatdetail_v";
       ');

        $this->execute("
           CREATE VIEW \"public\".\"infopenerimaanobatdetail_v\" AS   SELECT terima.penerimaanobat_id,
    terima.tgl_penerimaan,
    terima.no_penerimaan,
    terima.nomor_po,
    terima.supplier_id,
    supplier_m.supplier_nama,
    terima.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    terima.qty_po,
    terima.qty_diterima,
    terima.po_balance,
    terima.tgl_kadaluarsa,
    terima.no_batch,
    terima.s_konversiobt_id,
    satuankonversi_m.satuanbesar_id,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuanunit_nama,
    terima.no_suratjalan,
    terima.tgl_suratjalan,
    terima.no_faktur,
    terima.diterima_oleh,
    terima.keterangan,
    terima.upload_berkas,
    terima.catatan_berkas,
    terima.catatan,
    terima.is_verifikasi AS status_invoice,
    besar.satuanunit_nama AS satuan_besar,
    kecil.satuanunit_nama AS satuan_kecil,
    terima.validasipoobatdetail_id,
    terima.harga,
    terima.discount,
    terima.discount_rp,
    terima.jumlah,
    terima.pajak_id,
    terima.penerimaanobatdetail_id,
    satuankonversi_m.nilai_konversi,
    COALESCE(returdetailjumlah.on_retur, 0::bigint) AS on_retur,
    obatalkes_m.obatalkes_kode AS kode_item,
    terima.sub_total,
    terima.total_discount,
    terima.ppn_persen,
    terima.ppn_nilai,
    terima.total,
    terima.harga_total
   FROM ( SELECT penerimaanobat_t.penerimaanobat_id,
            penerimaanobat_t.tgl_penerimaan,
            penerimaanobat_t.no_penerimaan,
            validasipoobat_t.no_poobat AS nomor_po,
            penerimaanobat_t.supplier_id,
            penerimaanobat_t.no_suratjalan,
            penerimaanobat_t.tgl_suratjalan,
            penerimaanobat_t.no_faktur,
            penerimaanobat_t.diterima_oleh,
            penerimaanobat_t.upload_berkas,
            penerimaanobat_t.catatan_berkas,
            penerimaanobat_t.catatan,
            penerimaanobat_t.peg_mengetahui,
            penerimaanobat_t.peg_menyetujui,
            penerimaanobatdetail_t.penerimaanobatdetail_id,
            penerimaanobatdetail_t.obatalkes_id,
            penerimaanobatdetail_t.qty_po,
            penerimaanobatdetail_t.qty_diterima,
            penerimaanobatdetail_t.po_balance,
            penerimaanobatdetail_t.tgl_kadaluarsa,
            penerimaanobatdetail_t.no_batch,
            penerimaanobatdetail_t.s_konversiobt_id,
            penerimaanobatdetail_t.keterangan,
            penerimaanobat_t.is_verifikasi,
            penerimaanobatdetail_t.validasipoobatdetail_id,
            penerimaanobatdetail_t.harga,
            penerimaanobatdetail_t.discount,
            penerimaanobatdetail_t.discount_rp,
            penerimaanobatdetail_t.jumlah,
            validasipoobat_t.pajak_id,
            validasipoobat_t.sub_total,
            validasipoobat_t.total_discount,
            pajak_m.pajak_persen AS ppn_persen,
            validasipoobat_t.ppn_nilai,
            validasipoobat_t.total,
            penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga - penerimaanobatdetail_t.discount / 100::double precision * (penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga) + (penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga - penerimaanobatdetail_t.discount / 100::double precision * (penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga)) * pajak_m.pajak_persen::double precision / 100::double precision AS harga_total
           FROM penerimaanobat_t
             JOIN penerimaanobatdetail_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id
             LEFT JOIN validasipoobat_t ON penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id
             LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
          WHERE penerimaanobatdetail_t.is_deleted = false AND penerimaanobat_t.is_deleted = false) terima
     JOIN supplier_m ON terima.supplier_id = supplier_m.supplier_id
     JOIN obatalkes_m ON terima.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuankonversi_m ON terima.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     JOIN satuanunit_m besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     JOIN satuanunit_m kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN pegawai_m peg_mengetahui ON terima.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON terima.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN ( SELECT returpenerimaanobatdetail_t.penerimaanobatdetail_id,
            sum(returpenerimaanobatdetail_t.qty_retur) AS on_retur
           FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t
          GROUP BY returpenerimaanobatdetail_t.penerimaanobatdetail_id) returdetailjumlah ON terima.penerimaanobatdetail_id = returdetailjumlah.penerimaanobatdetail_id;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220330_041031_migrate_cdh288_infopenerimaanobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220330_041031_migrate_cdh288_infopenerimaanobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
