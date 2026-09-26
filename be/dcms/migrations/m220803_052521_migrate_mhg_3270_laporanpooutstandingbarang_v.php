<?php

use yii\db\Migration;

/**
 * Class m220803_052521_migrate_mhg_3270_laporanpooutstandingbarang_v
 */
class m220803_052521_migrate_mhg_3270_laporanpooutstandingbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpooutstandingbarang_v ";');

        $this->execute("
           CREATE VIEW \"public\".\"laporanpooutstandingbarang_v \" AS   SELECT pr.no_pr,
    pr.tgl_pr AS tanggal_verifikasi_pr,
    validasipobarang_t.no_pobarang AS no_po,
    validasipobarang_t.created_date AS tanggal_po,
    validasipobarang_t.tgl_validasi AS tanggal_verifikasi_po,
    supplier_m.supplier_kode,
    supplier_m.supplier_nama,
    NULL::text AS manufacturer,
    barang_m.barang_kode AS item_code,
    barang_m.barang_nama AS item_name,
        CASE
            WHEN validasipobarang_t.is_validasi IS FALSE THEN validasipobarangdetail_t.qty_input::double precision
            WHEN validasipobarang_t.is_validasi IS TRUE THEN validasipobarangdetail_t.qty_po::double precision / COALESCE(satuankonversibrg_m.nilai_konversi::double precision, 1::double precision)
            ELSE validasipobarangdetail_t.qty_po::double precision
        END AS qty_po,
    btrim(besar.satuanunit_nama::text) AS uom,
    btrim(besar.satuanunit_nama::text) AS from_uom,
    satuankonversibrg_m.nilai_konversi AS factor,
    btrim(kecil.satuanunit_nama::text) AS to_uom,
    validasipobarangdetail_t.harga AS price,
    validasipobarangdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp AS gross_amount,
    validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp + (validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp) * validasipobarang_t.ppn_persen::double precision / 100::double precision AS nett_amount,
        CASE pr.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipobarang_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipobarang_t.is_cito = true THEN 'Cito'::text
            WHEN validasipobarang_t.is_cito = false THEN 'Reguler'::text
            WHEN validasipobarang_t.is_cito IS NULL THEN 'Reguler'::text
            ELSE NULL::text
        END AS is_cito,
        CASE
            WHEN validasipobarang_t.is_admin = true THEN 'Ya'::text
            WHEN validasipobarang_t.is_admin = false THEN 'Tidak'::text
            WHEN validasipobarang_t.is_admin IS NULL THEN 'Tidak'::text
            ELSE NULL::text
        END AS is_admin,
    pr.tgl_approve
   FROM validasipobarang_t
     JOIN validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     JOIN barang_m ON validasipobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN satuankonversibrg_m ON validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     LEFT JOIN satuanunit_m kecil ON satuankonversibrg_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN satuanunit_m besar ON satuankonversibrg_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN ( SELECT purchasereqbrg_t.no_pr,
            purchasereqbrg_t.tgl_pr,
            validasipobarangdetail_t_1.validasipobarang_id,
            purchasereqbrgdetail_t.qty_input,
            kecil_1.satuanunit_nama AS satuan,
            besar_1.satuanunit_nama AS satuan_konversi,
            purchasereqbrgdetail_t.barang_id,
            purchasereqbrg_t.is_prcyto,
            purchasereqbrg_t.tgl_approve
           FROM purchasereqbrg_t
             JOIN purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
             JOIN validasipobarangdetail_t validasipobarangdetail_t_1 ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t_1.purchasereqbrgdetail_id
             LEFT JOIN satuanunit_m kecil_1 ON purchasereqbrgdetail_t.satuan_id = kecil_1.satuanunit_id
             LEFT JOIN satuanunit_m besar_1 ON purchasereqbrgdetail_t.satuankonversi_id = besar_1.satuanunit_id
          WHERE purchasereqbrg_t.is_deleted = false
          GROUP BY purchasereqbrg_t.no_pr, purchasereqbrg_t.tgl_pr, validasipobarangdetail_t_1.validasipobarang_id, purchasereqbrgdetail_t.qty_input, kecil_1.satuanunit_nama, besar_1.satuanunit_nama, purchasereqbrgdetail_t.barang_id, purchasereqbrg_t.is_prcyto, purchasereqbrg_t.tgl_approve) pr ON validasipobarang_t.validasipobarang_id = pr.validasipobarang_id AND validasipobarangdetail_t.barang_id = pr.barang_id
  WHERE (validasipobarang_t.status_penerimaan <> ALL (ARRAY[574, 580])) AND validasipobarang_t.is_validasi = true; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220803_052521_migrate_mhg_3270_laporanpooutstandingbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220803_052521_migrate_mhg_3270_laporanpooutstandingbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
