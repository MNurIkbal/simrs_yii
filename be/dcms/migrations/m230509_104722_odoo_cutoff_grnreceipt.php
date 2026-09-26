<?php

use yii\db\Migration;

/**
 * Class m230509_104722_odoo_cutoff_grnreceipt
 */
class m230509_104722_odoo_cutoff_grnreceipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_grnreceipt_v");
        
        $this->execute("
        CREATE OR REPLACE VIEW public.newodoo_grnreceipt_v
        AS
        SELECT 'obat'::text AS jenis,
            concat('POS', penerimaansupp_r.penerimaansupp_id) AS sync_id_api,
            penerimaansupp_r.no_penerimaan AS origin,
            penerimaansupp_r.no_penerimaan AS title,
            penerimaansupp_r.no_penerimaan AS name,
            penerimaansupp_r.no_penerimaan AS rfq,
            penerimaansupp_r.no_penerimaan AS purchase_name,
            concat(supplier_m.supplier_nama, '-', penerimaansupp_r.no_penerimaan) AS vendor_ref,
            13 AS currency_id,
            penerimaansupp_r.tgl_penerimaan AS date_order,
            concat('SUP', penerimaansupp_r.supplier_id) AS partner_id,
            'incoming'::text AS picking_type_id,
            'no'::text AS invoice_status,
            penerimaansupp_detail.amount_untaxed,
            penerimaansupp_detail.amount_tax,
            penerimaansupp_detail.total_discount,
            penerimaansupp_detail.total_tampilan,
            penerimaansupp_detail.amount_total,
            'draft'::text AS state,
            penerimaansupp_r.tgl_verifikasi AS date_planned,
            false AS is_return,
            penerimaansupp_detail.ppn,
            0 AS pph,
            penerimaansupp_r.tgl_penerimaan AS wipro_date,
            true AS no_approval,
                CASE
                    WHEN penerimaansupp_r.is_consigment = true THEN true
                    ELSE false
                END AS is_consignment,
            'POS'::text AS tipe_rekap,
            penerimaansupp_r.id,
            NULL::text AS batch_no,
            NULL::text AS batch_id,
            penerimaansupp_detail.tgl_kadaluarsa AS expiry_date
        FROM penerimaansupp_r
            JOIN supplier_m ON penerimaansupp_r.supplier_id = supplier_m.supplier_id
            JOIN ( SELECT penerimaansupp_t.penerimaansupp_id,
                    penerimaansupp_t.no_penerimaan,
                    penerimaansupp_t.supplier_id,
                    supplier_m_1.supplier_nama,
                    sum(obatalkes_m.harganetto / penerimaansuppdetail_t.qty_besar::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS amount_untaxed,
                    sum((penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision) AS amount_tax,
                    sum(penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision * penerimaansuppdetail_t.qty_besar::double precision) AS total_discount,
                    sum(penerimaansuppdetail_t.harga_netto) AS total_tampilan,
                    sum(penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * penerimaansuppdetail_t.qty_besar::double precision + (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_t.qty_besar::double precision) AS amount_total,
                    pajak_m.pajak_persen AS ppn,
                    penerimaansuppdetail_t.tgl_kadaluarsa
                FROM penerimaansuppdetail_t
                    JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
                    JOIN obatalkes_m ON penerimaansuppdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                    JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
                    LEFT JOIN supplier_m supplier_m_1 ON penerimaansupp_t.supplier_id = supplier_m_1.supplier_id
                GROUP BY penerimaansupp_t.penerimaansupp_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, supplier_m_1.supplier_nama, pajak_m.pajak_persen, penerimaansuppdetail_t.tgl_kadaluarsa) penerimaansupp_detail ON penerimaansupp_detail.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id
        WHERE penerimaansupp_r.is_deleted = false AND penerimaansupp_r.is_verifikasi = true
        UNION ALL
        SELECT 'obat'::text AS jenis,
            concat('POM', penerimaanobat_r.penerimaanobat_id) AS sync_id_api,
            penerimaanobat_r.no_penerimaan AS origin,
            penerimaanobat_r.no_penerimaan AS title,
            penerimaanobat_r.no_penerimaan AS name,
            penerimaanobat_r.no_penerimaan AS rfq,
            penerimaanobat_r.no_penerimaan AS purchase_name,
            concat(supplier_m.supplier_nama, '-', validasipoobat_t.no_poobat) AS vendor_ref,
            13 AS currency_id,
            penerimaanobat_r.tgl_penerimaan AS date_order,
            concat('SUP', penerimaanobat_r.supplier_id) AS partner_id,
            'incoming'::text AS picking_type_id,
            'no'::text AS invoice_status,
            penerimaanobatdetail.amount_untaxed,
            penerimaanobatdetail.amount_tax,
            penerimaanobatdetail.total_discount,
            penerimaanobatdetail.total_tampilan,
            penerimaanobatdetail.amount_total,
            'draft'::text AS state,
            penerimaanobat_r.tgl_penerimaan AS date_planned,
            false AS is_return,
            penerimaanobatdetail.ppn,
            0 AS pph,
            penerimaanobat_r.tgl_penerimaan AS wipro_date,
            true AS no_approval,
            penerimaanobat_r.is_consigment AS is_consignment,
            'POM'::text AS tipe_rekap,
            penerimaanobat_r.id,
            NULL::text AS batch_no,
            NULL::text AS batch_id,
            penerimaanobatdetail.tgl_kadaluarsa AS expiry_date
        FROM penerimaanobat_r
            JOIN ( SELECT penerimaanobat_t.penerimaanobat_id,
                    penerimaanobat_t.no_penerimaan,
                    po.no_poobat,
                    penerimaanobat_t.supplier_id,
                    supplier_m_1.supplier_nama,
                    sum(qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) AS amount_untaxed,
                    sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision) AS amount_tax,
                    sum(qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_discount,
                    sum(qty_konversi.harga_konversi) AS total_tampilan,
                    sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_total,
                    po.pajak_persen AS ppn,
                    penerimaanobatdetail_t.tgl_kadaluarsa
                FROM penerimaanobatdetail_t
                    JOIN penerimaanobat_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
                    JOIN obatalkes_m ON penerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                    LEFT JOIN supplier_m supplier_m_1 ON penerimaanobat_t.supplier_id = supplier_m_1.supplier_id
                    LEFT JOIN ( SELECT validasipoobat_t_1.validasipoobat_id,
                            pajak_m.pajak_persen,
                            validasipoobat_t_1.no_poobat
                        FROM validasipoobat_t validasipoobat_t_1
                            JOIN pajak_m ON validasipoobat_t_1.pajak_id = pajak_m.pajak_id
                        WHERE validasipoobat_t_1.is_deleted = false) po ON penerimaanobat_t.validasipoobat_id = po.validasipoobat_id
                    LEFT JOIN ( SELECT penerimaanobatdetail_t_1.penerimaanobatdetail_id,
                            penerimaanobatdetail_t_1.qty_diterima AS qty_konversi,
                            penerimaanobatdetail_t_1.harga / satuankonversi_m.nilai_konversi AS harga_konversi
                        FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                            JOIN satuankonversi_m ON penerimaanobatdetail_t_1.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                        WHERE satuankonversi_m.is_deleted = false) qty_konversi ON penerimaanobatdetail_t.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id
                GROUP BY penerimaanobat_t.penerimaanobat_id, penerimaanobat_t.no_penerimaan, po.no_poobat, penerimaanobat_t.supplier_id, supplier_m_1.supplier_nama, po.pajak_persen, penerimaanobatdetail_t.tgl_kadaluarsa) penerimaanobatdetail ON penerimaanobatdetail.penerimaanobat_id = penerimaanobat_r.penerimaanobat_id
            JOIN supplier_m ON penerimaanobat_r.supplier_id = supplier_m.supplier_id
            JOIN validasipoobat_t ON penerimaanobat_r.validasipoobat_id = validasipoobat_t.validasipoobat_id
        WHERE penerimaanobat_r.is_deleted = false
        UNION ALL
        SELECT 'barang'::text AS jenis,
            concat('PBM', penerimaanbarang_r.penerimaanbarang_id) AS sync_id_api,
            penerimaanbarang_r.no_penerimaan AS origin,
            penerimaanbarang_r.no_penerimaan AS title,
            penerimaanbarang_r.no_penerimaan AS name,
            penerimaanbarang_r.no_penerimaan AS rfq,
            penerimaanbarang_r.no_penerimaan AS purchase_name,
            concat(supplier_m.supplier_nama, '-', validasipobarang_t.no_pobarang) AS vendor_ref,
            13 AS currency_id,
            penerimaanbarang_r.tgl_penerimaan AS date_order,
            concat('SUP', penerimaanbarang_r.supplier_id) AS partner_id,
            'incoming'::text AS picking_type_id,
            'no'::text AS invoice_status,
            penerimaanbarangdetail.amount_untaxed,
            penerimaanbarangdetail.amount_tax,
            penerimaanbarangdetail.total_discount,
            penerimaanbarangdetail.total_tampilan,
            penerimaanbarangdetail.amount_total,
            'draft'::text AS state,
            penerimaanbarang_r.tgl_penerimaan AS date_planned,
            false AS is_return,
            penerimaanbarangdetail.ppn,
            0 AS pph,
            penerimaanbarang_r.tgl_penerimaan AS wipro_date,
            true AS no_approval,
            false AS is_consignment,
            'PBM'::text AS tipe_rekap,
            penerimaanbarang_r.id,
            NULL::text AS batch_no,
            NULL::text AS batch_id,
            penerimaanbarangdetail.tgl_kadaluarsa AS expiry_date
        FROM penerimaanbarang_r
            JOIN ( SELECT penerimaanbarang_t.penerimaanbarang_id,
                    penerimaanbarang_t.no_penerimaan,
                    po.no_pobarang,
                    penerimaanbarang_t.supplier_id,
                    supplier_m_1.supplier_nama,
                    sum(qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) AS amount_untaxed,
                    sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision) AS amount_tax,
                    sum(qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_discount,
                    sum(qty_konversi.harga_konversi) AS total_tampilan,
                    sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_total,
                    po.pajak_persen AS ppn,
                    penerimaanbarangdetail_t.tgl_kadaluarsa
                FROM penerimaanbarangdetail_t
                    JOIN penerimaanbarang_t ON penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
                    JOIN barang_m ON penerimaanbarangdetail_t.barang_id = barang_m.barang_id
                    LEFT JOIN supplier_m supplier_m_1 ON penerimaanbarang_t.supplier_id = supplier_m_1.supplier_id
                    LEFT JOIN ( SELECT validasipobarang_t_1.validasipobarang_id,
                            pajak_m.pajak_persen,
                            validasipobarang_t_1.no_pobarang
                        FROM validasipobarang_t validasipobarang_t_1
                            JOIN pajak_m ON validasipobarang_t_1.pajak_id = pajak_m.pajak_id
                        WHERE validasipobarang_t_1.is_deleted = false) po ON penerimaanbarang_t.validasipobarang_id = po.validasipobarang_id
                    LEFT JOIN ( SELECT penerimaanbarangdetail_t_1.penerimaanbarangdetail_id,
                            penerimaanbarangdetail_t_1.qty_diterima AS qty_konversi,
                            penerimaanbarangdetail_t_1.harga / satuankonversibrg_m.nilai_konversi AS harga_konversi
                        FROM penerimaanbarangdetail_t penerimaanbarangdetail_t_1
                            JOIN satuankonversibrg_m ON penerimaanbarangdetail_t_1.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                        WHERE satuankonversibrg_m.is_deleted = false) qty_konversi ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = qty_konversi.penerimaanbarangdetail_id
                GROUP BY penerimaanbarang_t.penerimaanbarang_id, penerimaanbarang_t.no_penerimaan, po.no_pobarang, penerimaanbarang_t.supplier_id, supplier_m_1.supplier_nama, po.pajak_persen, penerimaanbarangdetail_t.tgl_kadaluarsa) penerimaanbarangdetail ON penerimaanbarangdetail.penerimaanbarang_id = penerimaanbarang_r.penerimaanbarang_id
            JOIN supplier_m ON penerimaanbarang_r.supplier_id = supplier_m.supplier_id
            JOIN validasipobarang_t ON penerimaanbarang_r.validasipobarang_id = validasipobarang_t.validasipobarang_id
        WHERE penerimaanbarang_r.is_deleted = false
        UNION ALL
        SELECT 'BARANG'::text AS jenis,
            concat('PBS', penerimaansupp_r.penerimaansupp_id) AS sync_id_api,
            penerimaansupp_r.no_penerimaan AS origin,
            penerimaansupp_r.no_penerimaan AS title,
            penerimaansupp_r.no_penerimaan AS name,
            penerimaansupp_r.no_penerimaan AS rfq,
            penerimaansupp_r.no_penerimaan AS purchase_name,
            concat(supplier_m.supplier_nama, '-', penerimaansupp_r.no_penerimaan) AS vendor_ref,
            13 AS currency_id,
            penerimaansupp_r.tgl_penerimaan AS date_order,
            concat('SUP', penerimaansupp_r.supplier_id) AS partner_id,
            'incoming'::text AS picking_type_id,
            'no'::text AS invoice_status,
            penerimaansupp_detail.amount_untaxed,
            penerimaansupp_detail.amount_tax,
            penerimaansupp_detail.total_discount,
            penerimaansupp_detail.total_tampilan,
            penerimaansupp_detail.amount_total,
            'draft'::text AS state,
            penerimaansupp_r.tgl_verifikasi AS date_planned,
            false AS is_return,
            penerimaansupp_detail.ppn,
            0 AS pph,
            penerimaansupp_r.tgl_penerimaan AS wipro_date,
            true AS no_approval,
            false AS is_consignment,
            'PBS'::text AS tipe_rekap,
            penerimaansupp_r.id,
            NULL::text AS batch_no,
            NULL::text AS batch_id,
            penerimaansupp_detail.tgl_kadaluarsa AS expiry_date
        FROM penerimaansupp_r
            JOIN supplier_m ON penerimaansupp_r.supplier_id = supplier_m.supplier_id
            JOIN ( SELECT penerimaansupp_t.penerimaansupp_id,
                    penerimaansupp_t.no_penerimaan,
                    penerimaansupp_t.supplier_id,
                    supplier_m_1.supplier_nama,
                    sum(barang_m.barang_harganetto / penerimaansuppdetail_t.qty_besar::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS amount_untaxed,
                    sum((penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision) AS amount_tax,
                    sum(penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision * penerimaansuppdetail_t.qty_besar::double precision) AS total_discount,
                    sum(penerimaansuppdetail_t.harga_netto) AS total_tampilan,
                    sum(penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * penerimaansuppdetail_t.qty_besar::double precision + (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_t.qty_besar::double precision) AS amount_total,
                    pajak_m.pajak_persen AS ppn,
                    penerimaansuppdetail_t.tgl_kadaluarsa
                FROM penerimaansuppdetail_t
                    JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
                    JOIN barang_m ON penerimaansuppdetail_t.barang_id = barang_m.barang_id
                    JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
                    LEFT JOIN supplier_m supplier_m_1 ON penerimaansupp_t.supplier_id = supplier_m_1.supplier_id
                GROUP BY penerimaansupp_t.penerimaansupp_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, supplier_m_1.supplier_nama, pajak_m.pajak_persen, penerimaansuppdetail_t.tgl_kadaluarsa) penerimaansupp_detail ON penerimaansupp_detail.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id
        WHERE penerimaansupp_r.is_deleted = false AND penerimaansupp_r.is_verifikasi = true;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_104722_odoo_cutoff_grnreceipt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_104722_odoo_cutoff_grnreceipt cannot be reverted.\n";

        return false;
    }
    */
}
