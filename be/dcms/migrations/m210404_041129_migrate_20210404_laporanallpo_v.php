<?php

use yii\db\Migration;

/**
 * Class m210404_041129_migrate_20210404_laporanallpo_v
 */
class m210404_041129_migrate_20210404_laporanallpo_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('DROP VIEW if exists "public"."laporanallpo_v";');

       $this->execute("
        CREATE VIEW \"public\".\"laporanallpo_v\" AS  SELECT 'OBAT'::text AS type,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr AS tanggal_pr,
    purchasereq_t.tgl_pr AS tanggal_verifikasi_pr,
    validasipoobat_t.created_date AS tanggal_po,
        CASE validasipoobat_t.is_validasi
            WHEN true THEN validasipoobat_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi_po,
    btrim(validasipoobat_t.no_poobat::text) AS no_po,
    supplier_m.supplier_kode AS supplier_code,
    btrim(supplier_m.supplier_nama::text) AS supplier_name,
    manufaktur_m.nama AS manufacturer,
    obatalkes_m.obatalkes_kode AS item_code,
    obatalkes_m.obatalkes_nama AS item_name,
        CASE
            WHEN validasipoobat_t.is_validasi IS FALSE THEN validasipoobatdetail_t.qty_input::double precision
            WHEN validasipoobat_t.is_validasi IS TRUE THEN validasipoobatdetail_t.qty_po::double precision / COALESCE(satuankonversi_m.nilai_konversi, 1::double precision)
            ELSE validasipoobatdetail_t.qty_po::double precision
        END AS qty_po,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
    COALESCE(validasipoobatdetail_t.qty_po, 0) - COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversi_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
    validasipoobatdetail_t.harga AS price,
    validasipoobatdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS gross_amount,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS nett_amount,
    btrim(fgetnamalookup(validasipoobat_t.status_penerimaan)::text) AS status_po,
    btrim(validasipoobat_t.catatan1) AS catatan_1,
    btrim(validasipoobat_t.catatan2) AS catatan_2,
    purchasereq_t.is_prcyto AS cyto,
        CASE purchasereq_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipoobat_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipoobat_t.catatan IS NOT NULL THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS reject_date,
    btrim(validasipoobat_t.catatan) AS reject_remarks,
    btrim(penerimaanobat_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanobat_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereq_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status_pr
   FROM validasipoobatdetail_t
     JOIN validasipoobat_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN purchasereqdetail_t ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id
     LEFT JOIN purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     JOIN obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT pen_det.validasipoobatdetail_id,
            pen_det.obatalkes_id,
            pen_det.s_konversiobt_id,
            pen_det.qty_diterima,
            pen_det.jumlah,
            pen_det.discount_rp,
            pen_det.po_balance
           FROM penerimaanobatdetail_t pen_det
             JOIN ( SELECT penerimaanobatdetail_t_1.validasipoobatdetail_id,
                    max(penerimaanobatdetail_t_1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                  GROUP BY penerimaanobatdetail_t_1.validasipoobatdetail_id) max_det ON pen_det.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE pen_det.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT penerimaanobat_t_1.penerimaanobat_id,
            penerimaanobat_t_1.validasipoobat_id,
            penerimaanobat_t_1.tgl_penerimaan,
            penerimaanobat_t_1.no_penerimaan
           FROM penerimaanobat_t penerimaanobat_t_1
             JOIN ( SELECT penerimaanobat_t_2.validasipoobat_id,
                    max(penerimaanobat_t_2.penerimaanobat_id) AS penerimaanobat_id
                   FROM penerimaanobat_t penerimaanobat_t_2
                  GROUP BY penerimaanobat_t_2.validasipoobat_id) max_pen ON penerimaanobat_t_1.penerimaanobat_id = max_pen.penerimaanobat_id) penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
  WHERE purchasereq_t.is_deleted = false AND purchasereqdetail_t.is_deleted = false
UNION ALL
 SELECT 'BARANG'::text AS type,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr AS tanggal_pr,
    purchasereq_t.tgl_pr AS tanggal_verifikasi_pr,
    validasipoobat_t.created_date AS tanggal_po,
        CASE validasipoobat_t.is_validasi
            WHEN true THEN validasipoobat_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi_po,
    btrim(validasipoobat_t.no_poobat::text) AS no_po,
    supplier_m.supplier_kode AS supplier_code,
    btrim(supplier_m.supplier_nama::text) AS supplier_name,
    manufaktur_m.nama AS manufacturer,
    obatalkes_m.obatalkes_kode AS item_code,
    obatalkes_m.obatalkes_nama AS item_name,
        CASE
            WHEN validasipoobat_t.is_validasi IS FALSE THEN validasipoobatdetail_t.qty_input::double precision
            WHEN validasipoobat_t.is_validasi IS TRUE THEN validasipoobatdetail_t.qty_po::double precision / COALESCE(satuankonversi_m.nilai_konversi, 1::double precision)
            ELSE validasipoobatdetail_t.qty_po::double precision
        END AS qty_po,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
    COALESCE(validasipoobatdetail_t.qty_po, 0) - COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversi_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
    validasipoobatdetail_t.harga AS price,
    validasipoobatdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS gross_amount,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS nett_amount,
    btrim(fgetnamalookup(validasipoobat_t.status_penerimaan)::text) AS status_po,
    btrim(validasipoobat_t.catatan1) AS catatan_1,
    btrim(validasipoobat_t.catatan2) AS catatan_2,
    purchasereq_t.is_prcyto AS cyto,
        CASE purchasereq_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipoobat_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipoobat_t.catatan IS NOT NULL THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS reject_date,
    btrim(validasipoobat_t.catatan) AS reject_remarks,
    btrim(penerimaanobat_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanobat_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereq_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status_pr
   FROM validasipoobatdetail_t
     JOIN validasipoobat_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN purchasereqdetail_t ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id
     LEFT JOIN purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     JOIN obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT pen_det.validasipoobatdetail_id,
            pen_det.obatalkes_id,
            pen_det.s_konversiobt_id,
            pen_det.qty_diterima,
            pen_det.jumlah,
            pen_det.discount_rp,
            pen_det.po_balance
           FROM penerimaanobatdetail_t pen_det
             JOIN ( SELECT penerimaanobatdetail_t_1.validasipoobatdetail_id,
                    max(penerimaanobatdetail_t_1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                  GROUP BY penerimaanobatdetail_t_1.validasipoobatdetail_id) max_det ON pen_det.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE pen_det.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT penerimaanobat_t_1.penerimaanobat_id,
            penerimaanobat_t_1.validasipoobat_id,
            penerimaanobat_t_1.tgl_penerimaan,
            penerimaanobat_t_1.no_penerimaan
           FROM penerimaanobat_t penerimaanobat_t_1
             JOIN ( SELECT penerimaanobat_t_2.validasipoobat_id,
                    max(penerimaanobat_t_2.penerimaanobat_id) AS penerimaanobat_id
                   FROM penerimaanobat_t penerimaanobat_t_2
                  GROUP BY penerimaanobat_t_2.validasipoobat_id) max_pen ON penerimaanobat_t_1.penerimaanobat_id = max_pen.penerimaanobat_id) penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id;");

       $this->execute('ALTER TABLE "public"."laporanallpo_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210404_041129_migrate_20210404_laporanallpo_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210404_041129_migrate_20210404_laporanallpo_v cannot be reverted.\n";

        return false;
    }
    */
}
