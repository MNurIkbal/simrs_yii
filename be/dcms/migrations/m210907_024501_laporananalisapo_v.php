<?php

use yii\db\Migration;

/**
 * Class m210907_024501_laporananalisapo_v
 */
class m210907_024501_laporananalisapo_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporananalisapo_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporananalisapo_v\" AS  SELECT validasipoobat_t.validasipoobat_id AS no,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    manufaktur_m.nama AS manufaktur,
    jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr,
    purchasereqdetail_t.qty_input AS qty_pr,
        CASE
            WHEN uom_pr.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat('1 ', uom_pr.uom_besar, ' = ', uom_pr.nilai_konversi, ' ', uom_pr.uom_kecil)
        END AS uom_pr,
    purchasereqdetail_t.catatan,
    validasipoobat_t.no_poobat AS no_po,
    validasipoobat_t.created_date AS tgl_po,
    validasipoobat_t.tgl_validasi AS tgl_po_validasi,
    validasipoobatdetail_t.qty_input AS qty_po,
        CASE
            WHEN uom_po.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat('1 ', uom_po.uom_besar, ' = ', uom_po.nilai_konversi, ' ', uom_po.uom_kecil)
        END AS uom_po,
    validasipoobatdetail_t.harga,
    validasipoobatdetail_t.discount AS disc_persen,
    pajak_m.pajak_persen AS ppn_persen,
    validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp AS sub_total,
    COALESCE(validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp +
        CASE
            WHEN COALESCE(validasipoobat_t.ppn_persen::integer, 0) = 0 THEN 0::double precision
            ELSE (validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp) / (100 / validasipoobat_t.ppn_persen)::double precision
        END, 0::double precision) AS total,
    fgetnamalookup(validasipoobat_t.status_penerimaan) AS status_po,
        CASE
            WHEN validasipoobat_t.status_penerimaan = 575 THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS tgl_po_batal,
    validasipoobat_t.catatan AS catatan_batal,
    supplier_m.supplier_kode AS kode_supplier,
    supplier_m.supplier_nama AS nama_supplier,
    penerimaan.tgl AS tgl_penerimaan,
    validasipoobatdetail_t.qty_penerimaan,
        CASE
            WHEN uom_terima.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat('1 ', uom_terima.uom_besar, ' = ', uom_terima.nilai_konversi, ' ', uom_terima.uom_kecil)
        END AS uom_penerimaan,
    validasipoobatdetail_t.qty_sisa AS sisa_penerimaan,
    validasipoobat_t.is_validasi,
    COALESCE(date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, purchasereq_t.tgl_pr), 0::double precision) AS pr_to_po,
        CASE
            WHEN validasipoobat_t.is_validasi = true THEN COALESCE(date_part('day'::text, validasipoobat_t.tgl_validasi::date) - date_part('day'::text, purchasereq_t.tgl_pr), 0::double precision)
            ELSE COALESCE(date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, purchasereq_t.tgl_pr), 0::double precision)
        END AS pr_to_povalidasi,
        CASE
            WHEN validasipoobat_t.is_validasi = true THEN COALESCE(date_part('day'::text, validasipoobat_t.tgl_validasi::date) - date_part('day'::text, validasipoobat_t.created_date), 0::double precision)
            ELSE COALESCE(date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, validasipoobat_t.created_date), 0::double precision)
        END AS po_to_povalidasi,
    COALESCE(date_part('day'::text, penerimaan.tgl::date) - date_part('day'::text, purchasereq_t.tgl_pr), 0::double precision) AS pr_to_penerimaan,
        CASE
            WHEN validasipoobat_t.is_validasi = true THEN COALESCE(date_part('day'::text, penerimaan.tgl::date) - date_part('day'::text, validasipoobat_t.tgl_validasi), 0::double precision)
            ELSE COALESCE(date_part('day'::text, penerimaan.tgl::date) - date_part('day'::text, validasipoobat_t.created_date), 0::double precision)
        END AS povalidasi_to_penerimaan
   FROM validasipoobat_t
     JOIN ( SELECT a.validasipoobat_id,
            a.purchasereqdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.validasipoobatdetail_id,
            a.qty_input,
            a.harga,
            a.discount,
            a.jumlah,
            a.discount_rp,
            a.qty_penerimaan,
            a.qty_sisa
           FROM validasipoobatdetail_t a) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     JOIN ( SELECT a.purchasereqdetail_id,
            a.purchasereq_id,
            a.obatalkes_id,
            a.satuan_id,
            a.satuankonversi_id,
            a.qty_konversi,
            a.qty_input,
            a.catatan
           FROM purchasereqdetail_t a) purchasereqdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
     JOIN ( SELECT a.purchasereq_id,
            a.no_pr,
            a.tgl_pr
           FROM purchasereq_t a) purchasereq_t ON purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true
          GROUP BY a.satuankonversi_id, a.obatalkes_id, a.satuankecil_id, a.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a.nilai_konversi) uom_pr ON purchasereqdetail_t.obatalkes_id = uom_pr.obatalkes_id AND purchasereqdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom_pr.satuankecil_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.jenisobatalkes_id,
            a.manufaktur_id,
            a.obatalkes_nama
           FROM obatalkes_m a) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuankecil_id,
            a.satuanbesar_id
           FROM satuankonversi_m a) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true) uom_po ON validasipoobatdetail_t.obatalkes_id = uom_po.obatalkes_id AND validasipoobatdetail_t.s_konversiobt_id = uom_po.satuankonversi_id
     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            max(penerimaanobat_t.tgl_penerimaan) AS tgl,
            a.obatalkes_id,
            a.s_konversiobt_id
           FROM penerimaanobatdetail_t a
             JOIN ( SELECT a1.penerimaanobat_id,
                    a1.tgl_penerimaan
                   FROM penerimaanobat_t a1) penerimaanobat_t ON a.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
          GROUP BY a.validasipoobatdetail_id, a.obatalkes_id, a.s_konversiobt_id) penerimaan ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaan.validasipoobatdetail_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true) uom_terima ON penerimaan.obatalkes_id = uom_terima.obatalkes_id AND penerimaan.s_konversiobt_id = uom_terima.satuankonversi_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210907_024501_laporananalisapo_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210907_024501_laporananalisapo_v cannot be reverted.\n";

        return false;
    }
    */
}
