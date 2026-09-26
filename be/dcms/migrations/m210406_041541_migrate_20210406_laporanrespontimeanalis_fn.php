<?php

use yii\db\Migration;

/**
 * Class m210406_041541_migrate_20210406_laporanrespontimeanalis_fn
 */
class m210406_041541_migrate_20210406_laporanrespontimeanalis_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE OR REPLACE FUNCTION "public"."laporanrespontimeanalis_fn"("xtipe" varchar, "xstart_date" date, "xend_date" date)
  RETURNS TABLE("tipe" varchar, "item_code" varchar, "item_name" varchar, "manufacturer" varchar, "category" varchar, "pr_no" varchar, "create_date_pr" date, "approval_date_pr" date, "qty_pr" float8, "uom_pr" varchar, "remarks" text, "po_no" varchar, "create_date_po" date, "approval_date_po" date, "qty_po" float8, "uom_po" text, "price" float8, "ded_persen" float8, "add_persen" float8, "gross_amount" float8, "nett_amount" float8, "status_po" varchar, "reject_date" date, "reject_remarks" text, "supplier_code" varchar, "supplier" varchar, "do_no" varchar, "receive_date" date, "receive_qty" float8, "uom_terima" text, "outstanding_qty" float8, "uom_grn" text, "grn_no" varchar, "grn_date" date, "pr_created_to_po_created" float8, "po_created_to_do_received" float8, "pr_created_to_do_received" float8, "pr_approved_to_po_created" float8, "pr_approved_to_po_approved" float8, "po_created_to_po_approved" float8, "po_approved_to_do_received" float8, "pr_approved_to_do_received" float8, "additional_remarks" text, "remarks_date" date) AS $BODY$BEGIN
        
IF (xtipe = \'obat\')
THEN
    RETURN QUERY 
    
SELECT *FROM (          
    SELECT \'obat\'::VARCHAR AS tipe,
    obat.item_code,
    obat.item_name,
    obat.manufacturer,
    obat.category,
    obat.pr_no,
    obat.create_date_pr,
    obat.approval_date_pr,
    obat.qty_pr,
    obat.uom_pr,
    obat.remarks,
    obat.po_no,
    obat.create_date_po,
        obat.approval_date_po,
    obat.qty_po,
    obat.uom_po,
    obat.price,
    obat.ded_persen,
    obat.add_persen,
    obat.gross_amount,
    obat.nett_amount,
    obat.status_po,
    obat.reject_date,
    obat.reject_remarks,
    obat.supplier_code,
    obat.supplier,
    obat.do_no,
    obat.receive_date,
    obat.receive_qty,
    obat.uom_terima,
    obat.outstanding_qty,
    obat.uom_grn,
    obat.grn_no,
    obat.grn_date,
    obat.pr_created_to_po_created,
    obat.po_created_to_do_received,
    obat.pr_created_to_do_received,
    obat.pr_approved_to_po_created,
    obat.pr_approved_to_po_approved,
    obat.po_created_to_po_approved,
    obat.po_approved_to_do_received,
    obat.pr_approved_to_do_received,
    obat.additional_remarks,
    obat.remarks_date
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
    ) AS t_date
    JOIN (
        SELECT \'obat\' AS tipe,
    obatalkes_m.obatalkes_kode AS item_code,
    obatalkes_m.obatalkes_nama AS item_name,
    manufaktur_m.nama AS manufacturer,
    jenisobatalkes_m.jenisobatalkes_nama AS category,
    purchasereq_t.no_pr AS pr_no,
    purchasereq_t.tgl_pr::date AS create_date_pr,
    validasipoobat_t.created_date::date AS approval_date_pr,
    purchasereqdetail_t.qty_input::float AS qty_pr,
    CASE
            WHEN uom_pr.satuankonversi_id IS NULL THEN NULL::VARCHAR
            ELSE concat(\'1 \', uom_pr.uom_besar, \' = \', uom_pr.nilai_konversi, \' \', uom_pr.uom_kecil)::VARCHAR
        END AS uom_pr,
    purchasereqdetail_t.catatan AS remarks,
    validasipoobat_t.no_poobat AS po_no,
    validasipoobat_t.created_date::date AS create_date_po,
        validasipoobat_t.tgl_validasi::date AS approval_date_po,
    validasipoobatdetail_t.qty_input::float8 AS qty_po,
    CASE
            WHEN uom_po.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat(\'1 \', uom_po.uom_besar, \' = \', uom_po.nilai_konversi, \' \', uom_po.uom_kecil)
        END AS uom_po,
    validasipoobatdetail_t.harga::float8 AS price,
    validasipoobatdetail_t.discount::float8 AS ded_persen,
    pajak_m.pajak_persen::float8 AS add_persen,
    validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp::float8 AS gross_amount,
    COALESCE(validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp +
        CASE
            WHEN COALESCE(validasipoobat_t.ppn_persen::integer, 0) = 0 THEN 0::double precision
            ELSE (validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp) / (100 / validasipoobat_t.ppn_persen)::double precision
        END, 0::double precision) AS nett_amount,
    fgetnamalookup(validasipoobat_t.status_penerimaan)::VARCHAR AS status_po,
    CASE
            WHEN validasipoobat_t.status_penerimaan = 575 THEN validasipoobat_t.last_modified_date ::date 
            ELSE NULL::date
        END AS reject_date,
    validasipoobat_t.catatan AS reject_remarks,
    supplier_m.supplier_kode AS supplier_code,
    supplier_m.supplier_nama AS supplier,
    penerimaan.no_suratjalan AS do_no,
    penerimaan.tgl::date AS receive_date,
    validasipoobatdetail_t.qty_penerimaan::float8 AS receive_qty,
    CASE
            WHEN uom_terima.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat(\'1 \', uom_terima.uom_besar, \' = \', uom_terima.nilai_konversi, \' \', uom_terima.uom_kecil)
        END AS uom_terima,
    validasipoobatdetail_t.qty_sisa::float8 AS outstanding_qty,
     CASE
            WHEN uom_terima.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat(\'1 \', uom_terima.uom_besar, \' = \', uom_terima.nilai_konversi, \' \', uom_terima.uom_kecil)
        END AS uom_grn,
    penerimaan.no_penerimaan::VARCHAR AS grn_no,
    penerimaan.tgl::date AS grn_date,
        date_part(\'day\'::text, validasipoobat_t.created_date::date) - date_part(\'day\'::text, purchasereq_t.tgl_pr) AS pr_created_to_po_created,
        date_part(\'day\'::text, penerimaan.tgl_do::date) - date_part(\'day\'::text, purchasereq_t.tgl_pr) AS pr_created_to_do_received,
        date_part(\'day\'::text, validasipoobat_t.created_date::date) - date_part(\'day\'::text, purchasereq_t.tgl_pr) AS pr_approved_to_po_created,
        date_part(\'day\'::text, validasipoobat_t.tgl_validasi::date) - date_part(\'day\'::text, purchasereq_t.tgl_pr) AS pr_approved_to_po_approved,
        date_part(\'day\'::text, penerimaan.tgl::date) - date_part(\'day\'::text, validasipoobat_t.created_date) AS po_created_to_do_received,
        date_part(\'day\'::text, validasipoobat_t.tgl_validasi::date) - date_part(\'day\'::text, validasipoobat_t.created_date) AS po_created_to_po_approved,
        date_part(\'day\'::text, penerimaan.tgl::date) - date_part(\'day\'::text, validasipoobat_t.tgl_validasi)  AS po_approved_to_do_received,
        date_part(\'day\'::text, penerimaan.tgl::date) - date_part(\'day\'::text, purchasereq_t.tgl_pr) AS pr_approved_to_do_received,
    validasipoobat_t.catatan AS additional_remarks,
    validasipoobat_t.tgl_rencanaterima::date AS remarks_date
FROM validasipoobat_t
    JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
    JOIN purchasereqdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
    JOIN purchasereq_t ON purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id
    LEFT JOIN 
        (SELECT 
            satuankonversi_m_1.satuankonversi_id,
            satuankonversi_m_1.obatalkes_id,
            satuankonversi_m_1.satuankecil_id,
            satuankonversi_m_1.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m_1.nilai_konversi
        FROM satuankonversi_m satuankonversi_m_1
            LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m_1.satuanbesar_id = uom_besar.satuanunit_id
            LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m_1.satuankecil_id = uom_kecil.satuanunit_id
        WHERE satuankonversi_m_1.is_deleted = false AND satuankonversi_m_1.is_active = true
        GROUP BY satuankonversi_m_1.satuankonversi_id, satuankonversi_m_1.obatalkes_id, satuankonversi_m_1.satuankecil_id, satuankonversi_m_1.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m_1.nilai_konversi) uom_pr ON purchasereqdetail_t.obatalkes_id = uom_pr.obatalkes_id AND purchasereqdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom_pr.satuankecil_id
    LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
    JOIN obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
    JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
    LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
    LEFT JOIN satuanunit_m kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
    LEFT JOIN satuanunit_m besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
    LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
    LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
    LEFT JOIN 
        (SELECT 
            satuankonversi_m_1.satuankonversi_id,
            satuankonversi_m_1.obatalkes_id,
            satuankonversi_m_1.satuankecil_id,
            satuankonversi_m_1.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m_1.nilai_konversi
        FROM satuankonversi_m satuankonversi_m_1
            LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m_1.satuanbesar_id = uom_besar.satuanunit_id
            LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m_1.satuankecil_id = uom_kecil.satuanunit_id
            WHERE satuankonversi_m_1.is_deleted = false AND satuankonversi_m_1.is_active = true) uom_po ON validasipoobatdetail_t.obatalkes_id = uom_po.obatalkes_id AND validasipoobatdetail_t.s_konversiobt_id = uom_po.satuankonversi_id
    LEFT JOIN 
        (SELECT 
            max_penerimaan.validasipoobat_id,
            penerimaanobatdetail_t.validasipoobatdetail_id,
            penerimaanobat_t.no_penerimaan,
            penerimaanobat_t.no_faktur,
            penerimaanobat_t.no_suratjalan,
            penerimaanobat_t.tgl_suratjalan as tgl_do,
            penerimaanobat_t.tgl_penerimaan AS tgl,
            penerimaanobatdetail_t.obatalkes_id,
            penerimaanobatdetail_t.s_konversiobt_id
        FROM penerimaanobat_t
            JOIN 
                (SELECT 
                    max(penerimaanobat_max.penerimaanobat_id) AS max_id,
                    penerimaanobat_max.validasipoobat_id
                FROM penerimaanobat_t penerimaanobat_max
                GROUP BY penerimaanobat_max.validasipoobat_id) max_penerimaan ON penerimaanobat_t.penerimaanobat_id = max_penerimaan.max_id 
    JOIN penerimaanobatdetail_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id) 
                 penerimaan ON validasipoobat_t.validasipoobat_id = penerimaan.validasipoobat_id
    LEFT JOIN 
        ( SELECT 
            satuankonversi_m_1.satuankonversi_id,
            satuankonversi_m_1.obatalkes_id,
            satuankonversi_m_1.satuankecil_id,
            satuankonversi_m_1.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m_1.nilai_konversi
        FROM satuankonversi_m satuankonversi_m_1
            LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m_1.satuanbesar_id = uom_besar.satuanunit_id
            LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m_1.satuankecil_id = uom_kecil.satuanunit_id
        WHERE satuankonversi_m_1.is_deleted = false AND satuankonversi_m_1.is_active = true) uom_terima ON penerimaan.obatalkes_id = uom_terima.obatalkes_id AND penerimaan.s_konversiobt_id = uom_terima.satuankonversi_id
WHERE validasipoobatdetail_t.is_deleted = false
        ) obat ON t_date.tgl_generate = obat.create_date_po

        
            ) x
        ORDER BY x.create_date_po;
        END IF;
        
-----------------------------------------------------------------------------------------------------------------------     
IF (xtipe = \'barang\')
THEN
    RETURN QUERY 
    
SELECT *FROM (
        SELECT \'barang\'::VARCHAR AS tipe,
    barang.item_code,
    barang.item_name,
    barang.manufacturer,
    barang.category,
    barang.pr_no,
    barang.create_date_pr,
    barang.approval_date_pr,
    barang.qty_pr,
    barang.uom_pr,
    barang.remarks,
    barang.po_no,
    barang.create_date_po,
        barang.approval_date_po,
    barang.qty_po,
    barang.uom_po,
    barang.price,
    barang.ded_persen,
    barang.add_persen,
    barang.gross_amount,
    barang.nett_amount,
    barang.status_po,
    barang.reject_date,
    barang.reject_remarks,
    barang.supplier_code,
    barang.supplier,
    barang.do_no,
    barang.receive_date,
    barang.receive_qty,
    barang.uom_terima,
    barang.outstanding_qty,
    barang.uom_grn,
    barang.grn_no,
    barang.grn_date,
    barang.pr_created_to_po_created,
    barang.po_created_to_do_received,
    barang.pr_created_to_do_received,
    barang.pr_approved_to_po_created,
    barang.pr_approved_to_po_approved,
    barang.po_created_to_po_approved,
    barang.po_approved_to_do_received,
    barang.pr_approved_to_do_received,
    barang.additional_remarks,
    barang.remarks_date
    FROM (
        SELECT CURRENT_DATE + i AS tgl_generate
        FROM generate_series(date (xstart_date) - CURRENT_DATE, 
             date (xend_date)  - CURRENT_DATE ) i
        ) AS t_date
     JOIN (SELECT \'barang\'::VARCHAR AS tipe,
    barang_m.barang_kode AS item_code,
    barang_m.barang_nama AS item_name,
    NULL::VARCHAR AS manufacturer,
    kelompokbarang_m.kelompokbarang_nama AS category,
    purchasereqbrg_t.no_pr AS pr_no,
    purchasereqbrg_t.tgl_pr::date AS create_date_pr,
    validasipobarang_t.created_date::date AS approval_date_pr,
    purchasereqbrgdetail_t.qty_input::float8 AS qty_pr,
    CASE
            WHEN uom_pr.satuankonversibrg_id IS NULL THEN NULL::VARCHAR
            ELSE concat(\'1 \', uom_pr.uom_besar, \' = \', uom_pr.nilai_konversi, \' \', uom_pr.uom_kecil)::VARCHAR
        END AS uom_pr,
    purchasereqbrgdetail_t.catatan AS remarks,
    validasipobarang_t.no_pobarang AS po_no,
    validasipobarang_t.created_date::date AS create_date_po,
        validasipobarang_t.tgl_validasi::date AS approval_date_po,
    validasipobarangdetail_t.qty_input::float8 AS qty_po,
    CASE
            WHEN uom_po.satuankonversibrg_id IS NULL THEN NULL::text
            ELSE concat(\'1 \', uom_po.uom_besar, \' = \', uom_po.nilai_konversi, \' \', uom_po.uom_kecil)
        END AS uom_po,
    validasipobarangdetail_t.harga::float8 AS price,
    validasipobarangdetail_t.discount::float8 AS ded_persen,
    pajak_m.pajak_persen::float8 AS add_persen,
    validasipobarangdetail_t.jumlah - validasipobarangdetail_t.discount_rp::float8 AS gross_amount,
    COALESCE(validasipobarangdetail_t.jumlah - validasipobarangdetail_t.discount_rp +
        CASE
            WHEN COALESCE(validasipobarang_t.ppn_persen::integer, 0) = 0 THEN 0::double precision
            ELSE (validasipobarangdetail_t.jumlah - validasipobarangdetail_t.discount_rp) / (100 / validasipobarang_t.ppn_persen)::double precision
        END, 0::double precision) AS nett_amount,
    fgetnamalookup(validasipobarang_t.status_penerimaan)::VARCHAR AS status_po,
    CASE
            WHEN validasipobarang_t.status_penerimaan = 575 THEN validasipobarang_t.last_modified_date ::date 
            ELSE NULL::date
        END AS reject_date,
    validasipobarang_t.catatan1 AS reject_remarks,
    supplier_m.supplier_kode AS supplier_code,
    supplier_m.supplier_nama AS supplier,
    penerimaan.no_suratjalan AS do_no,
    penerimaan.tgl::date AS receive_date,
    validasipobarangdetail_t.qty_penerimaan::float8 AS receive_qty,
    CASE
            WHEN uom_terima.satuankonversibrg_id IS NULL THEN NULL::text
            ELSE concat(\'1 \', uom_terima.uom_besar, \' = \', uom_terima.nilai_konversi, \' \', uom_terima.uom_kecil)
        END AS uom_terima,
    validasipobarangdetail_t.qty_sisa::float8 AS outstanding_qty,
     CASE
            WHEN uom_terima.satuankonversibrg_id IS NULL THEN NULL::text
            ELSE concat(\'1 \', uom_terima.uom_besar, \' = \', uom_terima.nilai_konversi, \' \', uom_terima.uom_kecil)
        END AS uom_grn,
    penerimaan.no_penerimaan::VARCHAR AS grn_no,
    penerimaan.tgl::date AS grn_date,
    date_part(\'day\'::text, validasipobarang_t.created_date::date) - date_part(\'day\'::text, purchasereqbrg_t.tgl_pr) AS pr_created_to_po_created,
    date_part(\'day\'::text, penerimaan.tgl_do::date) - date_part(\'day\'::text, purchasereqbrg_t.tgl_pr) AS pr_created_to_do_received,
    date_part(\'day\'::text, validasipobarang_t.created_date::date) - date_part(\'day\'::text, purchasereqbrg_t.tgl_pr) AS pr_approved_to_po_created,
    date_part(\'day\'::text, validasipobarang_t.tgl_validasi::date) - date_part(\'day\'::text, purchasereqbrg_t.tgl_pr) AS pr_approved_to_po_approved,
    date_part(\'day\'::text, penerimaan.tgl::date) - date_part(\'day\'::text, validasipobarang_t.created_date) AS po_created_to_do_received,
    date_part(\'day\'::text, validasipobarang_t.tgl_validasi::date) - date_part(\'day\'::text, validasipobarang_t.created_date) AS po_created_to_po_approved,
    date_part(\'day\'::text, penerimaan.tgl::date) - date_part(\'day\'::text, validasipobarang_t.tgl_validasi)  AS po_approved_to_do_received,
    date_part(\'day\'::text, penerimaan.tgl::date) - date_part(\'day\'::text, purchasereqbrg_t.tgl_pr) AS pr_approved_to_do_received,
    validasipobarang_t.catatan2 AS additional_remarks,
    validasipobarang_t.tgl_rencanaterima::date AS remarks_date
FROM validasipobarang_t
     JOIN validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     JOIN purchasereqbrgdetail_t ON validasipobarangdetail_t.purchasereqbrgdetail_id = purchasereqbrgdetail_t.purchasereqbrgdetail_id
     JOIN purchasereqbrg_t ON purchasereqbrgdetail_t.purchasereqbrg_id = purchasereqbrg_t.purchasereqbrg_id
     LEFT JOIN ( SELECT satuankonversibrg_m.satuankonversibrg_id,
            satuankonversibrg_m.barang_id,
            satuankonversibrg_m.satuankecil_id,
            satuankonversibrg_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversibrg_m.nilai_konversi
           FROM satuankonversibrg_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversibrg_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversibrg_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversibrg_m.is_deleted = false AND satuankonversibrg_m.is_active = true
          GROUP BY satuankonversibrg_m.satuankonversibrg_id, satuankonversibrg_m.barang_id, satuankonversibrg_m.satuankecil_id, satuankonversibrg_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversibrg_m.nilai_konversi) uom_pr ON purchasereqbrgdetail_t.barang_id = uom_pr.barang_id AND purchasereqbrgdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqbrgdetail_t.satuankonversi_id = uom_pr.satuankecil_id
     LEFT JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     JOIN barang_m ON validasipobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     LEFT JOIN satuankonversi_m ON validasipobarangdetail_t.s_konversibrg_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN satuanunit_m besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT satuankonversi_po.satuankonversibrg_id,
            satuankonversi_po.barang_id,
            satuankonversi_po.satuankecil_id,
            satuankonversi_po.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_po.nilai_konversi
           FROM satuankonversibrg_m satuankonversi_po
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_po.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_po.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_po.is_deleted = false AND satuankonversi_po.is_active = true) uom_po ON validasipobarangdetail_t.barang_id = uom_po.barang_id AND validasipobarangdetail_t.s_konversibrg_id = uom_po.satuankonversibrg_id
     LEFT JOIN ( SELECT max_penerimaan.validasipobarang_id,
            penerimaanbarangdetail_t.validasipobarangdetail_id,
            penerimaanbarang_t.no_penerimaan,
            penerimaanbarang_t.no_faktur,
            penerimaanbarang_t.tgl_penerimaan AS tgl,
            penerimaanbarangdetail_t.barang_id,
            penerimaanbarangdetail_t.s_konversibrg_id,
                        penerimaanbarang_t.no_suratjalan,
                        penerimaanbarang_t.tgl_suratjalan as tgl_do
           FROM penerimaanbarang_t
             JOIN ( SELECT max(penerimaanbarang_t_1.penerimaanbarang_id) AS max_id,
                    penerimaanbarang_t_1.validasipobarang_id
                   FROM penerimaanbarang_t penerimaanbarang_t_1
                  GROUP BY penerimaanbarang_t_1.validasipobarang_id) max_penerimaan ON penerimaanbarang_t.penerimaanbarang_id = max_penerimaan.max_id AND penerimaanbarang_t.validasipobarang_id = max_penerimaan.validasipobarang_id
             JOIN penerimaanbarangdetail_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id) penerimaan ON validasipobarang_t.validasipobarang_id = penerimaan.validasipobarang_id
     LEFT JOIN ( SELECT sk_terima.satuankonversibrg_id,
            sk_terima.barang_id,
            sk_terima.satuankecil_id,
            sk_terima.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            sk_terima.nilai_konversi
           FROM satuankonversibrg_m sk_terima
             LEFT JOIN satuanunit_m uom_besar ON sk_terima.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON sk_terima.satuankecil_id = uom_kecil.satuanunit_id
          WHERE sk_terima.is_deleted = false AND sk_terima.is_active = true) uom_terima ON penerimaan.barang_id = uom_terima.barang_id AND penerimaan.s_konversibrg_id = uom_terima.satuankonversibrg_id
  WHERE validasipobarangdetail_t.is_deleted = false)    barang ON t_date.tgl_generate = barang.create_date_po

        
            ) x
        ORDER BY x.create_date_po;
        END IF;
        
END$BODY$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000;');

        $this->execute('ALTER FUNCTION "public"."laporanrespontimeanalis_fn"("xtipe" varchar, "xstart_date" date, "xend_date" date) OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210406_041541_migrate_20210406_laporanrespontimeanalis_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210406_041541_migrate_20210406_laporanrespontimeanalis_fn cannot be reverted.\n";

        return false;
    }
    */
}
