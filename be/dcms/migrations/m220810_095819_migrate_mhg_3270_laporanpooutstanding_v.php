<?php

use yii\db\Migration;

/**
 * Class m220810_095819_migrate_mhg_3270_laporanpooutstanding_v
 */
class m220810_095819_migrate_mhg_3270_laporanpooutstanding_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpooutstanding_v";');

        $this->execute("
           CREATE VIEW \"public\".\"laporanpooutstanding_v\" AS  SELECT pr.no_pr,
    validasipoobat_t.no_poobat AS no_po,
    validasipoobat_t.created_date AS tgl_po_dibuat,
    validasipoobat_t.tgl_validasi,
    supplier_m.supplier_kode,
    supplier_m.supplier_nama,
    obatalkes_m.obatalkes_kode AS kode_item,
    obatalkes_m.obatalkes_nama AS nama_item,
    validasipoobatdetail_t.qty_input AS qty_po,
    kecil.satuanunit_nama AS satuan_kecil,
    besar.satuanunit_nama AS satuan_besar,
    validasipoobatdetail_t.qty_po AS qty,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_penerimaan,
    validasipoobatdetail_t.harga,
    validasipoobatdetail_t.discount,
    validasipoobatdetail_t.discount_rp,
    pajak_m.pajak_persen AS ppn_persen,
    validasipoobat_t.ppn_nilai,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS sub_total,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS total,
    validasipoobat_t.catatan1,
    validasipoobat_t.catatan2,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS uom,
    lookup_validasipoobat.lookup_name AS status,
    pr.tgl_pr,
    COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
    jenisobatalkes_m.jenisobatalkes_nama,
    manufaktur_m.nama,
    manufaktur_m.nama AS manufaktur_nama,
        CASE
            WHEN validasipoobat_t.is_consigment = true THEN 'Ya'::text
            WHEN validasipoobat_t.is_consigment = false THEN 'Tidak'::text
            WHEN validasipoobat_t.is_consigment IS NULL THEN 'Tidak'::text
            ELSE NULL::text
        END AS is_consigment,
        CASE
            WHEN validasipoobat_t.is_cito = true THEN 'Cito'::text
            WHEN validasipoobat_t.is_cito = false THEN 'Reguler'::text
            WHEN validasipoobat_t.is_cito IS NULL THEN 'Reguler'::text
            ELSE NULL::text
        END AS is_cito,
        CASE
            WHEN validasipoobat_t.is_admin = true THEN 'Ya'::text
            WHEN validasipoobat_t.is_admin = false THEN 'Tidak'::text
            WHEN validasipoobat_t.is_admin IS NULL THEN 'Tidak'::text
            ELSE NULL::text
        END AS is_admin,
    pr.tgl_approve
   FROM validasipoobat_t
     JOIN ( SELECT a.validasipoobat_id,
            a.validasipoobatdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.qty_input,
            a.qty_po,
            a.qty_penerimaan,
            a.qty_retur,
            a.harga,
            a.discount,
            a.discount_rp,
            a.qty_sisa
           FROM validasipoobatdetail_t a
          WHERE a.is_deleted = false) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            a.penerimaanobatdetail_id,
            a.po_balance
           FROM penerimaanobatdetail_t a
             JOIN ( SELECT a1.validasipoobatdetail_id,
                    max(a1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t a1
                  GROUP BY a1.validasipoobatdetail_id) max_det ON a.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE a.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.jenisobatalkes_id,
            a.manufaktur_id
           FROM obatalkes_m a) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            a.nilai_konversi
           FROM satuankonversi_m a) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN ( SELECT a.no_pr,
            a.tgl_pr,
            validasipoobatdetail_t_1.validasipoobat_id,
            purchasereqdetail_t.qty_input,
            kecil_1.satuanunit_nama AS satuan,
            besar_1.satuanunit_nama AS satuan_konversi,
            purchasereqdetail_t.obatalkes_id,
            a.tgl_approve
           FROM purchasereq_t a
             JOIN ( SELECT a1.purchasereq_id,
                    a1.purchasereqdetail_id,
                    a1.satuan_id,
                    a1.satuankonversi_id,
                    a1.qty_input,
                    a1.obatalkes_id
                   FROM purchasereqdetail_t a1) purchasereqdetail_t ON a.purchasereq_id = purchasereqdetail_t.purchasereq_id
             JOIN ( SELECT a1.purchasereqdetail_id,
                    a1.validasipoobat_id
                   FROM validasipoobatdetail_t a1) validasipoobatdetail_t_1 ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t_1.purchasereqdetail_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) kecil_1 ON purchasereqdetail_t.satuan_id = kecil_1.satuanunit_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) besar_1 ON purchasereqdetail_t.satuankonversi_id = besar_1.satuanunit_id
          WHERE a.is_deleted = false
          GROUP BY a.no_pr, a.tgl_pr, validasipoobatdetail_t_1.validasipoobat_id, purchasereqdetail_t.qty_input, kecil_1.satuanunit_nama, besar_1.satuanunit_nama, purchasereqdetail_t.obatalkes_id, a.tgl_approve) pr ON validasipoobat_t.validasipoobat_id = pr.validasipoobat_id AND validasipoobatdetail_t.obatalkes_id = pr.obatalkes_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lookup_validasipoobat ON validasipoobat_t.status_penerimaan = lookup_validasipoobat.lookup_id
  WHERE (validasipoobat_t.status_penerimaan <> ALL (ARRAY[574, 580])) AND validasipoobat_t.is_validasi = true; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_095819_migrate_mhg_3270_laporanpooutstanding_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_095819_migrate_mhg_3270_laporanpooutstanding_v cannot be reverted.\n";

        return false;
    }
    */
}
