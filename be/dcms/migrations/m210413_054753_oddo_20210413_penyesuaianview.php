<?php

use yii\db\Migration;

/**
 * Class m210413_054753_oddo_20210413_penyesuaianview
 */
class m210413_054753_oddo_20210413_penyesuaianview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."kelompokbarang_m" ADD COLUMN IF NOT exists "servicecategory_id" int4;');
    $this->execute('ALTER TABLE "public"."kelompokbarang_m" ADD COLUMN IF NOT exists "servicegroup_id" int4;');
    $this->execute('COMMENT ON COLUMN "public"."kelompokbarang_m"."servicecategory_id" IS \'kebutuhan ODDO\';');
    $this->execute('COMMENT ON COLUMN "public"."kelompokbarang_m"."servicegroup_id" IS \'kebutuhan ODDO\';');
     
    $this->execute('DROP VIEW if exists "public"."int_purchase_v";');

    $this->execute("
        CREATE VIEW \"public\".\"int_purchase_v\" AS  SELECT 'obat'::text AS jenis,
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
    false AS is_consignment,
    '6'::text AS sync_type,
    'POS'::text AS tipe_rekap,
    penerimaansupp_r.id,
    penerimaansupp_r.is_sending,
    penerimaansupp_r.is_sent,
        CASE
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = false AND penerimaansupp_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaansupp_r.is_sending = false AND penerimaansupp_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = false AND penerimaansupp_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN penerimaansupp_r.is_sending = false AND penerimaansupp_r.is_sent = false AND penerimaansupp_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
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
            pajak_m.pajak_persen AS ppn
           FROM penerimaansuppdetail_t
             JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
             JOIN obatalkes_m ON penerimaansuppdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN supplier_m supplier_m_1 ON penerimaansupp_t.supplier_id = supplier_m_1.supplier_id
          GROUP BY penerimaansupp_t.penerimaansupp_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, supplier_m_1.supplier_nama, pajak_m.pajak_persen) penerimaansupp_detail ON penerimaansupp_detail.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id
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
    false AS is_consignment,
    '6'::text AS sync_type,
    'POM'::text AS tipe_rekap,
    penerimaanobat_r.id,
    penerimaanobat_r.is_sending,
    penerimaanobat_r.is_sent,
        CASE
            WHEN penerimaanobat_r.is_sending = true AND penerimaanobat_r.is_sent = false AND penerimaanobat_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaanobat_r.is_sending = true AND penerimaanobat_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaanobat_r.is_sending = false AND penerimaanobat_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaanobat_r.is_sending = true AND penerimaanobat_r.is_sent = false AND penerimaanobat_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN penerimaanobat_r.is_sending = false AND penerimaanobat_r.is_sent = false AND penerimaanobat_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
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
            po.pajak_persen AS ppn
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
          GROUP BY penerimaanobat_t.penerimaanobat_id, penerimaanobat_t.no_penerimaan, po.no_poobat, penerimaanobat_t.supplier_id, supplier_m_1.supplier_nama, po.pajak_persen) penerimaanobatdetail ON penerimaanobatdetail.penerimaanobat_id = penerimaanobat_r.penerimaanobat_id
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
    '6'::text AS sync_type,
    'PBM'::text AS tipe_rekap,
    penerimaanbarang_r.id,
    penerimaanbarang_r.is_sending,
    penerimaanbarang_r.is_sent,
        CASE
            WHEN penerimaanbarang_r.is_sending = true AND penerimaanbarang_r.is_sent = false AND penerimaanbarang_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaanbarang_r.is_sending = true AND penerimaanbarang_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaanbarang_r.is_sending = false AND penerimaanbarang_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaanbarang_r.is_sending = true AND penerimaanbarang_r.is_sent = false AND penerimaanbarang_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN penerimaanbarang_r.is_sending = false AND penerimaanbarang_r.is_sent = false AND penerimaanbarang_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
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
            po.pajak_persen AS ppn
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
          GROUP BY penerimaanbarang_t.penerimaanbarang_id, penerimaanbarang_t.no_penerimaan, po.no_pobarang, penerimaanbarang_t.supplier_id, supplier_m_1.supplier_nama, po.pajak_persen) penerimaanbarangdetail ON penerimaanbarangdetail.penerimaanbarang_id = penerimaanbarang_r.penerimaanbarang_id
     JOIN supplier_m ON penerimaanbarang_r.supplier_id = supplier_m.supplier_id
     JOIN validasipobarang_t ON penerimaanbarang_r.validasipobarang_id = validasipobarang_t.validasipobarang_id
  WHERE penerimaanbarang_r.is_deleted = false;");

    $this->execute('ALTER TABLE "public"."int_purchase_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."int_purchasegrndetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"int_purchasegrndetail_v\" AS  SELECT 'obat'::text AS jenis,
    concat('RPOS', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
    concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS picking_id,
    concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaansupp_t.supplier_id) AS partner_id,
    concat('OBT', returpenerimaanobatdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    obatalkes_m.obatalkes_nama AS name,
    penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision AS normal_price,
    penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision AS price_unit,
    penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision AS discount_value,
    penerimaansuppdetail_t.diskon AS discount_persen,
    penerimaansuppdetail_t.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN \"left\"(obatalkes_m.obatalkes_kode::text, 3) = 'CGN'::text THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    returpenerimaanobatdetail_r.qty_retur AS product_qty,
    returpenerimaanobatdetail_r.qty_retur AS qty_received,
        CASE COALESCE(pajak_m.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    (penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_tax,
    (penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS has_tax,
    penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * returpenerimaanobatdetail_r.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision + (penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_total,
    penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * returpenerimaanobatdetail_r.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision + (penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_r.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_subtotal,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    returpenerimaanobatdetail_r.id,
    returpenerimaanobatdetail_r.is_sent,
    returpenerimaanobatdetail_r.is_sending,
    'RPOS'::text AS tipe_rekap,
    '6'::text AS sync_type,
        CASE
            WHEN returpenerimaanobatdetail_r.is_sending = true AND returpenerimaanobatdetail_r.is_sent = false AND returpenerimaanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returpenerimaanobatdetail_r.is_sending = true AND returpenerimaanobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN returpenerimaanobatdetail_r.is_sending = false AND returpenerimaanobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returpenerimaanobatdetail_r.is_sending = true AND returpenerimaanobatdetail_r.is_sent = false AND returpenerimaanobatdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN returpenerimaanobatdetail_r.is_sending = false AND returpenerimaanobatdetail_r.is_sent = false AND returpenerimaanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM returpenerimaanobatdetail_r
     JOIN returpenerimaanobat_r ON returpenerimaanobatdetail_r.returpenerimaanobat_id = returpenerimaanobat_r.returpenerimaanobat_id AND returpenerimaanobat_r.is_sent = true
     JOIN penerimaansupp_t ON returpenerimaanobat_r.panerimaanobatsupp_id = penerimaansupp_t.penerimaansupp_id
     JOIN penerimaansuppdetail_t ON returpenerimaanobatdetail_r.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
     JOIN obatalkes_m ON returpenerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    concat('RPOM', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
    concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS picking_id,
    concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaanobat_t.supplier_id) AS partner_id,
    concat('OBT', returpenerimaanobatdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    obatalkes_m.obatalkes_nama AS name,
    penerimaanobatdetail.harga_konversi AS normal_price,
    penerimaanobatdetail.harga_konversi AS price_unit,
    penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision AS discount_value,
    penerimaanobatdetail.discount AS discount_persen,
    satuan.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN \"left\"(obatalkes_m.obatalkes_kode::text, 3) = 'CGN'::text THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    returpenerimaanobatdetail_r.qty_retur AS product_qty,
    returpenerimaanobatdetail_r.qty_retur AS qty_received,
        CASE COALESCE(po.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    (penerimaanobatdetail.harga_konversi - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_tax,
    (penerimaanobatdetail.harga_konversi - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS has_tax,
    penerimaanobatdetail.harga_konversi * returpenerimaanobatdetail_r.qty_retur::double precision - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision + (penerimaanobatdetail.harga_konversi - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_total,
    penerimaanobatdetail.harga_konversi * returpenerimaanobatdetail_r.qty_retur::double precision - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision + (penerimaanobatdetail.harga_konversi - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_subtotal,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    returpenerimaanobatdetail_r.id,
    returpenerimaanobatdetail_r.is_sent,
    returpenerimaanobatdetail_r.is_sending,
    'RPOM'::text AS tipe_rekap,
    '6'::text AS sync_type,
        CASE
            WHEN returpenerimaanobatdetail_r.is_sending = true AND returpenerimaanobatdetail_r.is_sent = false AND returpenerimaanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returpenerimaanobatdetail_r.is_sending = true AND returpenerimaanobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN returpenerimaanobatdetail_r.is_sending = false AND returpenerimaanobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returpenerimaanobatdetail_r.is_sending = true AND returpenerimaanobatdetail_r.is_sent = false AND returpenerimaanobatdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN returpenerimaanobatdetail_r.is_sending = false AND returpenerimaanobatdetail_r.is_sent = false AND returpenerimaanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM returpenerimaanobatdetail_r
     JOIN returpenerimaanobat_r ON returpenerimaanobatdetail_r.returpenerimaanobat_id = returpenerimaanobat_r.returpenerimaanobat_id AND returpenerimaanobat_r.is_sent = true
     LEFT JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
            penerimaanobatdetail_t.qty_diterima::double precision * satuankonversi_m.nilai_konversi AS qty_konversi,
            penerimaanobatdetail_t.harga / satuankonversi_m.nilai_konversi AS harga_konversi,
            penerimaanobatdetail_t.s_konversiobt_id,
            penerimaanobatdetail_t.no_batch,
            penerimaanobatdetail_t.discount
           FROM penerimaanobatdetail_t
             JOIN satuankonversi_m ON penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
          WHERE satuankonversi_m.is_deleted = false) penerimaanobatdetail ON returpenerimaanobatdetail_r.penerimaanobatdetail_id = penerimaanobatdetail.penerimaanobatdetail_id
     JOIN penerimaanobat_t ON returpenerimaanobatdetail_r.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
     JOIN obatalkes_m ON returpenerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
            validasipoobat_t.pajak_id,
            pajak_m.pajak_persen
           FROM validasipoobat_t
             JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
          WHERE validasipoobat_t.is_deleted = false) po ON penerimaanobat_t.validasipoobat_id = po.validasipoobat_id
     LEFT JOIN ( SELECT satuankonversi_m.satuankonversi_id,
            satuankonversi_m.satuankecil_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM satuankonversi_m
             JOIN satuanunit_m satuan_kecil ON satuankonversi_m.satuankecil_id = satuan_kecil.satuanunit_id
          WHERE satuankonversi_m.is_deleted = false) satuan ON penerimaanobatdetail.s_konversiobt_id = satuan.satuankonversi_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    concat('RPBM', returpenerimaanbarangdetail_r.returpenerimaanbarangdetail_id) AS sync_id_api,
    concat('RPBM', returpenerimaanbarang_r.returpenerimaanbarang_id) AS picking_id,
    concat('RPBM', returpenerimaanbarang_r.returpenerimaanbarang_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaanbarang_t.supplier_id) AS partner_id,
    concat('BRG', returpenerimaanbarangdetail_r.barang_id) AS product_id,
    concat('BRG', barang_m.kelompokbarang_id) AS product_categ_id,
    barang_m.barang_nama AS name,
    penerimaanbarangdetail.harga_konversi AS normal_price,
    penerimaanbarangdetail.harga_konversi AS price_unit,
    penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision AS discount_value,
    penerimaanbarangdetail.discount AS discount_persen,
    satuan.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN \"left\"(barang_m.barang_kode::text, 3) = 'CGN'::text THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    returpenerimaanbarangdetail_r.qty_retur AS product_qty,
    returpenerimaanbarangdetail_r.qty_retur AS qty_received,
        CASE COALESCE(po.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    (penerimaanbarangdetail.harga_konversi - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision AS price_tax,
    (penerimaanbarangdetail.harga_konversi - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision AS has_tax,
    penerimaanbarangdetail.harga_konversi * returpenerimaanbarangdetail_r.qty_retur::double precision - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision + (penerimaanbarangdetail.harga_konversi - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision AS price_total,
    penerimaanbarangdetail.harga_konversi * returpenerimaanbarangdetail_r.qty_retur::double precision - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision + (penerimaanbarangdetail.harga_konversi - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision AS price_subtotal,
    returpenerimaanbarang_r.tgl_retur AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    returpenerimaanbarangdetail_r.id,
    returpenerimaanbarangdetail_r.is_sent,
    returpenerimaanbarangdetail_r.is_sending,
    'RPBM'::text AS tipe_rekap,
    '6'::text AS sync_type,
        CASE
            WHEN returpenerimaanbarangdetail_r.is_sending = true AND returpenerimaanbarangdetail_r.is_sent = false AND returpenerimaanbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returpenerimaanbarangdetail_r.is_sending = true AND returpenerimaanbarangdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN returpenerimaanbarangdetail_r.is_sending = false AND returpenerimaanbarangdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returpenerimaanbarangdetail_r.is_sending = true AND returpenerimaanbarangdetail_r.is_sent = false AND returpenerimaanbarangdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN returpenerimaanbarangdetail_r.is_sending = false AND returpenerimaanbarangdetail_r.is_sent = false AND returpenerimaanbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM returpenerimaanbarangdetail_r
     JOIN returpenerimaanbarang_r ON returpenerimaanbarangdetail_r.returpenerimaanbarang_id = returpenerimaanbarang_r.returpenerimaanbarang_id AND returpenerimaanbarang_r.is_sent = true
     LEFT JOIN ( SELECT penerimaanbarangdetail_t.penerimaanbarangdetail_id,
            penerimaanbarangdetail_t.qty_diterima::double precision * satuankonversibrg_m.nilai_konversi AS qty_konversi,
            penerimaanbarangdetail_t.harga / satuankonversibrg_m.nilai_konversi AS harga_konversi,
            penerimaanbarangdetail_t.s_konversibrg_id,
            penerimaanbarangdetail_t.no_batch,
            penerimaanbarangdetail_t.discount
           FROM penerimaanbarangdetail_t
             JOIN satuankonversibrg_m ON penerimaanbarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
          WHERE satuankonversibrg_m.is_deleted = false) penerimaanbarangdetail ON returpenerimaanbarangdetail_r.penerimaanbarangdetail_id = penerimaanbarangdetail.penerimaanbarangdetail_id
     JOIN penerimaanbarang_t ON returpenerimaanbarangdetail_r.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
     JOIN barang_m ON returpenerimaanbarangdetail_r.barang_id = barang_m.barang_id
     LEFT JOIN ( SELECT validasipobarang_t.validasipobarang_id,
            validasipobarang_t.pajak_id,
            pajak_m.pajak_persen
           FROM validasipobarang_t
             JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
          WHERE validasipobarang_t.is_deleted = false) po ON penerimaanbarang_t.validasipobarang_id = po.validasipobarang_id
     LEFT JOIN ( SELECT satuankonversibrg_m.satuankonversibrg_id,
            satuankonversibrg_m.satuankecil_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM satuankonversibrg_m
             JOIN satuanunit_m satuan_kecil ON satuankonversibrg_m.satuankecil_id = satuan_kecil.satuanunit_id
          WHERE satuankonversibrg_m.is_deleted = false) satuan ON penerimaanbarangdetail.s_konversibrg_id = satuan.satuankonversibrg_id;");

    $this->execute('ALTER TABLE "public"."int_purchasegrndetail_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."int_purchasegrn_v";');

    $this->execute("
        CREATE VIEW \"public\".\"int_purchasegrn_v\" AS  SELECT 'obat'::text AS jenis,
    concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanobat_r.no_returpenerimaanobat) AS origin,
    returpenerimaanobat_r.no_returpenerimaanobat AS title,
    returpenerimaanobat_r.no_returpenerimaanobat AS name,
    returpenerimaanobat_r.no_returpenerimaanobat AS rfq,
    returpenerimaanobat_r.no_returpenerimaanobat AS purchase_name,
    concat(retur_detail.supplier_nama, '-', retur_detail.no_penerimaan) AS vendor_ref,
    13 AS currency_id,
    returpenerimaanobat_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    'draft'::text AS state,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanobat_r.tgl_retur AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    '6'::text AS sync_type,
    'RPOS'::text AS tipe_rekap,
    returpenerimaanobat_r.id,
    returpenerimaanobat_r.is_sending,
    returpenerimaanobat_r.is_sent,
        CASE
            WHEN returpenerimaanobat_r.is_sending = true AND returpenerimaanobat_r.is_sent = false AND returpenerimaanobat_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returpenerimaanobat_r.is_sending = true AND returpenerimaanobat_r.is_sent = true THEN 'SUKSES'::text
            WHEN returpenerimaanobat_r.is_sending = false AND returpenerimaanobat_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returpenerimaanobat_r.is_sending = true AND returpenerimaanobat_r.is_sent = false AND returpenerimaanobat_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN returpenerimaanobat_r.is_sending = false AND returpenerimaanobat_r.is_sent = false AND returpenerimaanobat_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM returpenerimaanobat_r
     JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobat_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.supplier_id,
            supplier_m.supplier_nama,
            sum(obatalkes_m.harganetto / returpenerimaanobatdetail_t.qty_retur::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS amount_untaxed,
            sum((penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision * returpenerimaanobatdetail_t.qty_retur::double precision) AS total_discount,
            sum(penerimaansuppdetail_t.harga_netto) AS total_tampilan,
            sum(penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * returpenerimaanobatdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * returpenerimaanobatdetail_t.qty_retur::double precision + (penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_t.qty_retur::double precision) AS amount_total,
            pajak_m.pajak_persen AS ppn
           FROM returpenerimaanobatdetail_t
             JOIN penerimaansuppdetail_t ON returpenerimaanobatdetail_t.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
             JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
             JOIN obatalkes_m ON returpenerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
          GROUP BY returpenerimaanobatdetail_t.returpenerimaanobat_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, supplier_m.supplier_nama, pajak_m.pajak_persen) retur_detail ON returpenerimaanobat_r.returpenerimaanobat_id = retur_detail.returpenerimaanobat_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanobat_r.no_returpenerimaanobat) AS origin,
    returpenerimaanobat_r.no_returpenerimaanobat AS title,
    returpenerimaanobat_r.no_returpenerimaanobat AS name,
    returpenerimaanobat_r.no_returpenerimaanobat AS rfq,
    returpenerimaanobat_r.no_returpenerimaanobat AS purchase_name,
    concat(retur_detail.supplier_nama, '-', retur_detail.no_poobat) AS vendor_ref,
    13 AS currency_id,
    returpenerimaanobat_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    'draft'::text AS state,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanobat_r.tgl_retur AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    '6'::text AS sync_type,
    'RPOM'::text AS tipe_rekap,
    returpenerimaanobat_r.id,
    returpenerimaanobat_r.is_sending,
    returpenerimaanobat_r.is_sent,
        CASE
            WHEN returpenerimaanobat_r.is_sending = true AND returpenerimaanobat_r.is_sent = false AND returpenerimaanobat_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returpenerimaanobat_r.is_sending = true AND returpenerimaanobat_r.is_sent = true THEN 'SUKSES'::text
            WHEN returpenerimaanobat_r.is_sending = false AND returpenerimaanobat_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returpenerimaanobat_r.is_sending = true AND returpenerimaanobat_r.is_sent = false AND returpenerimaanobat_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN returpenerimaanobat_r.is_sending = false AND returpenerimaanobat_r.is_sent = false AND returpenerimaanobat_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM returpenerimaanobat_r
     JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobat_id,
            penerimaanobat_t.no_penerimaan,
            po.no_poobat,
            penerimaanobat_t.supplier_id,
            supplier_m.supplier_nama,
            sum(qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) AS amount_untaxed,
            sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_discount,
            sum(qty_konversi.harga_konversi) AS total_tampilan,
            sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_total,
            po.pajak_persen AS ppn
           FROM returpenerimaanobatdetail_t
             JOIN penerimaanobatdetail_t ON returpenerimaanobatdetail_t.penerimaanobatdetail_id = penerimaanobatdetail_t.penerimaanobatdetail_id
             JOIN penerimaanobat_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
             JOIN obatalkes_m ON returpenerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN supplier_m ON penerimaanobat_t.supplier_id = supplier_m.supplier_id
             LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
                    pajak_m.pajak_persen,
                    validasipoobat_t.no_poobat
                   FROM validasipoobat_t
                     JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
                  WHERE validasipoobat_t.is_deleted = false) po ON penerimaanobat_t.validasipoobat_id = po.validasipoobat_id
             LEFT JOIN ( SELECT penerimaanobatdetail_t_1.penerimaanobatdetail_id,
                    returpenerimaanobatdetail_t_1.qty_retur AS qty_konversi,
                    penerimaanobatdetail_t_1.harga / satuankonversi_m.nilai_konversi AS harga_konversi
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                     JOIN returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1 ON penerimaanobatdetail_t_1.penerimaanobatdetail_id = returpenerimaanobatdetail_t_1.penerimaanobatdetail_id
                     JOIN satuankonversi_m ON penerimaanobatdetail_t_1.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                  WHERE satuankonversi_m.is_deleted = false) qty_konversi ON penerimaanobatdetail_t.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id
          GROUP BY returpenerimaanobatdetail_t.returpenerimaanobat_id, penerimaanobat_t.no_penerimaan, po.no_poobat, penerimaanobat_t.supplier_id, supplier_m.supplier_nama, po.pajak_persen) retur_detail ON returpenerimaanobat_r.returpenerimaanobat_id = retur_detail.returpenerimaanobat_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    concat('RPBM', returpenerimaanbarang_r.returpenerimaanbarang_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanbarang_r.no_returpenerimaanbarang) AS origin,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS title,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS name,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS rfq,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS purchase_name,
    concat(retur_detail.supplier_nama, '-', retur_detail.no_pobarang) AS vendor_ref,
    13 AS currency_id,
    returpenerimaanbarang_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    'draft'::text AS state,
    returpenerimaanbarang_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanbarang_r.tgl_retur AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    '6'::text AS sync_type,
    'RPBM'::text AS tipe_rekap,
    returpenerimaanbarang_r.id,
    returpenerimaanbarang_r.is_sending,
    returpenerimaanbarang_r.is_sent,
        CASE
            WHEN returpenerimaanbarang_r.is_sending = true AND returpenerimaanbarang_r.is_sent = false AND returpenerimaanbarang_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returpenerimaanbarang_r.is_sending = true AND returpenerimaanbarang_r.is_sent = true THEN 'SUKSES'::text
            WHEN returpenerimaanbarang_r.is_sending = false AND returpenerimaanbarang_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returpenerimaanbarang_r.is_sending = true AND returpenerimaanbarang_r.is_sent = false AND returpenerimaanbarang_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN returpenerimaanbarang_r.is_sending = false AND returpenerimaanbarang_r.is_sent = false AND returpenerimaanbarang_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM returpenerimaanbarang_r
     JOIN ( SELECT returpenerimaanbarangdetail_t.returpenerimaanbarang_id,
            penerimaanbarang_t.no_penerimaan,
            po.no_pobarang,
            penerimaanbarang_t.supplier_id,
            supplier_m.supplier_nama,
            sum(qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) AS amount_untaxed,
            sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_discount,
            sum(qty_konversi.harga_konversi) AS total_tampilan,
            sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_total,
            po.pajak_persen AS ppn
           FROM returpenerimaanbarangdetail_t
             JOIN penerimaanbarangdetail_t ON returpenerimaanbarangdetail_t.penerimaanbarangdetail_id = penerimaanbarangdetail_t.penerimaanbarangdetail_id
             JOIN penerimaanbarang_t ON penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
             JOIN barang_m ON returpenerimaanbarangdetail_t.barang_id = barang_m.barang_id
             LEFT JOIN supplier_m ON penerimaanbarang_t.supplier_id = supplier_m.supplier_id
             LEFT JOIN ( SELECT validasipobarang_t.validasipobarang_id,
                    pajak_m.pajak_persen,
                    validasipobarang_t.no_pobarang
                   FROM validasipobarang_t
                     JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
                  WHERE validasipobarang_t.is_deleted = false) po ON penerimaanbarang_t.validasipobarang_id = po.validasipobarang_id
             LEFT JOIN ( SELECT penerimaanbarangdetail_t_1.penerimaanbarangdetail_id,
                    returpenerimaanbarangdetail_t_1.qty_retur AS qty_konversi,
                    penerimaanbarangdetail_t_1.harga / satuankonversibrg_m.nilai_konversi AS harga_konversi
                   FROM penerimaanbarangdetail_t penerimaanbarangdetail_t_1
                     JOIN returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t_1 ON penerimaanbarangdetail_t_1.penerimaanbarangdetail_id = returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id
                     JOIN satuankonversibrg_m ON penerimaanbarangdetail_t_1.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                  WHERE satuankonversibrg_m.is_deleted = false) qty_konversi ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = qty_konversi.penerimaanbarangdetail_id
          GROUP BY returpenerimaanbarangdetail_t.returpenerimaanbarang_id, penerimaanbarang_t.no_penerimaan, po.no_pobarang, penerimaanbarang_t.supplier_id, supplier_m.supplier_nama, po.pajak_persen) retur_detail ON returpenerimaanbarang_r.returpenerimaanbarang_id = retur_detail.returpenerimaanbarang_id;");

    $this->execute('ALTER TABLE "public"."int_purchasegrn_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."int_purchasedetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"int_purchasedetail_v\" AS  SELECT 'obat'::text AS jenis,
    concat('POS', penerimaansuppdetail_r.id) AS sync_id_api,
    concat('POS', penerimaansupp_r.penerimaansupp_id) AS picking_id,
    concat('POS', penerimaansupp_r.penerimaansupp_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaansupp_r.supplier_id) AS partner_id,
    concat('OBT', penerimaansuppdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    obatalkes_m.obatalkes_nama AS name,
    penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS normal_price,
    penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS price_unit,
    penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * (penerimaansuppdetail_r.diskon / 100::numeric)::double precision AS discount_value,
    penerimaansuppdetail_r.diskon AS discount_persen,
    penerimaansuppdetail_r.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN \"left\"(obatalkes_m.obatalkes_kode::text, 3) = 'CGN'::text THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    penerimaansuppdetail_r.qty_kecil AS product_qty,
    penerimaansuppdetail_r.qty_kecil AS qty_received,
        CASE COALESCE(pajak_m.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS price_tax,
    (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS has_tax,
    penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS price_total,
    penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision + (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS price_subtotal,
    penerimaansupp_r.tgl_penerimaan AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    penerimaansuppdetail_r.id,
    penerimaansuppdetail_r.is_sent,
    penerimaansuppdetail_r.is_sending,
    'POS'::text AS tipe_rekap,
    '6'::text AS sync_type,
        CASE
            WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = false AND penerimaansuppdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaansuppdetail_r.is_sending = false AND penerimaansuppdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = false AND penerimaansuppdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN penerimaansuppdetail_r.is_sending = false AND penerimaansuppdetail_r.is_sent = false AND penerimaansuppdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM penerimaansuppdetail_r
     JOIN penerimaansupp_r ON penerimaansuppdetail_r.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id AND penerimaansupp_r.is_sent = true
     JOIN obatalkes_m ON penerimaansuppdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m satuan_kecil ON penerimaansuppdetail_r.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN pajak_m ON penerimaansupp_r.pajak_id = pajak_m.pajak_id
     JOIN supplier_m ON penerimaansupp_r.supplier_id = supplier_m.supplier_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    concat('POM', penerimaanobatdetail_r.id) AS sync_id_api,
    concat('POM', penerimaanobat_r.penerimaanobat_id) AS picking_id,
    concat('POM', penerimaanobat_r.penerimaanobat_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaanobat_r.supplier_id) AS partner_id,
    concat('OBT', penerimaanobatdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    obatalkes_m.obatalkes_nama AS name,
    qty_konversi.harga_konversi AS normal_price,
    qty_konversi.harga_konversi AS price_unit,
    qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision AS discount_value,
    penerimaanobatdetail_r.discount AS discount_persen,
    satuan.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN \"left\"(obatalkes_m.obatalkes_kode::text, 3) = 'CGN'::text THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    qty_konversi.qty_konversi AS product_qty,
    qty_konversi.qty_konversi AS qty_received,
        CASE COALESCE(po.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS price_tax,
    (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS has_tax,
    qty_konversi.qty_konversi * qty_konversi.harga_konversi AS price_total,
    qty_konversi.harga_konversi * qty_konversi.qty_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision * qty_konversi.qty_konversi + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS price_subtotal,
    penerimaanobat_r.tgl_penerimaan AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    penerimaanobatdetail_r.id,
    penerimaanobatdetail_r.is_sent,
    penerimaanobatdetail_r.is_sending,
    'POM'::text AS tipe_rekap,
    '6'::text AS sync_type,
        CASE
            WHEN penerimaanobatdetail_r.is_sending = true AND penerimaanobatdetail_r.is_sent = false AND penerimaanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaanobatdetail_r.is_sending = true AND penerimaanobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaanobatdetail_r.is_sending = false AND penerimaanobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaanobatdetail_r.is_sending = true AND penerimaanobatdetail_r.is_sent = false AND penerimaanobatdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN penerimaanobatdetail_r.is_sending = false AND penerimaanobatdetail_r.is_sent = false AND penerimaanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM penerimaanobatdetail_r
     JOIN penerimaanobat_r ON penerimaanobatdetail_r.penerimaanobat_id = penerimaanobat_r.penerimaanobat_id AND penerimaanobat_r.is_sent = true
     JOIN obatalkes_m ON penerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuankonversi_m ON penerimaanobatdetail_r.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m satuan_kecil ON satuankonversi_m.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN supplier_m ON penerimaanobat_r.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
            pajak_m.pajak_persen
           FROM validasipoobat_t
             JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
          WHERE validasipoobat_t.is_deleted = false) po ON penerimaanobat_r.validasipoobat_id = po.validasipoobat_id
     LEFT JOIN ( SELECT satuankonversi_m_1.satuankonversi_id,
            satuankonversi_m_1.satuankecil_id,
            satuan_kecil_1.satuanunit_nama AS satuan_kecil
           FROM satuankonversi_m satuankonversi_m_1
             JOIN satuanunit_m satuan_kecil_1 ON satuankonversi_m_1.satuankecil_id = satuan_kecil_1.satuanunit_id
          WHERE satuankonversi_m_1.is_deleted = false) satuan ON penerimaanobatdetail_r.s_konversiobt_id = satuan.satuankonversi_id
     LEFT JOIN ( SELECT penerimaanobatdetail_r_1.penerimaanobatdetail_id,
            penerimaanobatdetail_r_1.qty_diterima::double precision * satuankonversi_m_1.nilai_konversi AS qty_konversi,
            penerimaanobatdetail_r_1.harga / satuankonversi_m_1.nilai_konversi AS harga_konversi
           FROM penerimaanobatdetail_r penerimaanobatdetail_r_1
             JOIN satuankonversi_m satuankonversi_m_1 ON penerimaanobatdetail_r_1.s_konversiobt_id = satuankonversi_m_1.satuankonversi_id
          WHERE satuankonversi_m_1.is_deleted = false) qty_konversi ON penerimaanobatdetail_r.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    concat('PBM', penerimaanbarangdetail_r.id) AS sync_id_api,
    concat('PBM', penerimaanbarang_r.penerimaanbarang_id) AS picking_id,
    concat('PBM', penerimaanbarang_r.penerimaanbarang_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaanbarang_r.supplier_id) AS partner_id,
    concat('BRG', penerimaanbarangdetail_r.barang_id) AS product_id,
    concat('BRG', barang_m.kelompokbarang_id) AS product_categ_id,
    barang_m.barang_nama AS name,
    qty_konversi.harga_konversi AS normal_price,
    qty_konversi.harga_konversi AS price_unit,
    qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision AS discount_value,
    penerimaanbarangdetail_r.discount AS discount_persen,
    satuan.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN \"left\"(barang_m.barang_kode::text, 3) = 'CGN'::text THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    qty_konversi.qty_konversi AS product_qty,
    qty_konversi.qty_konversi AS qty_received,
        CASE COALESCE(po.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS price_tax,
    (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS has_tax,
    qty_konversi.qty_konversi * qty_konversi.harga_konversi AS price_total,
    qty_konversi.harga_konversi * qty_konversi.qty_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision * qty_konversi.qty_konversi + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS price_subtotal,
    penerimaanbarang_r.tgl_penerimaan AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    penerimaanbarangdetail_r.id,
    penerimaanbarangdetail_r.is_sent,
    penerimaanbarangdetail_r.is_sending,
    'PBM'::text AS tipe_rekap,
    '6'::text AS sync_type,
        CASE
            WHEN penerimaanbarangdetail_r.is_sending = true AND penerimaanbarangdetail_r.is_sent = false AND penerimaanbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaanbarangdetail_r.is_sending = true AND penerimaanbarangdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaanbarangdetail_r.is_sending = false AND penerimaanbarangdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaanbarangdetail_r.is_sending = true AND penerimaanbarangdetail_r.is_sent = false AND penerimaanbarangdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN penerimaanbarangdetail_r.is_sending = false AND penerimaanbarangdetail_r.is_sent = false AND penerimaanbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM penerimaanbarangdetail_r
     JOIN penerimaanbarang_r ON penerimaanbarangdetail_r.penerimaanbarang_id = penerimaanbarang_r.penerimaanbarang_id AND penerimaanbarang_r.is_sent = true
     JOIN barang_m ON penerimaanbarangdetail_r.barang_id = barang_m.barang_id
     LEFT JOIN satuankonversibrg_m ON penerimaanbarangdetail_r.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     LEFT JOIN satuanunit_m satuan_kecil ON satuankonversibrg_m.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN supplier_m ON penerimaanbarang_r.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT validasipobarang_t.validasipobarang_id,
            pajak_m.pajak_persen
           FROM validasipobarang_t
             JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
          WHERE validasipobarang_t.is_deleted = false) po ON penerimaanbarang_r.validasipobarang_id = po.validasipobarang_id
     LEFT JOIN ( SELECT satuankonversibrg_m_1.satuankonversibrg_id,
            satuankonversibrg_m_1.satuankecil_id,
            satuan_kecil_1.satuanunit_nama AS satuan_kecil
           FROM satuankonversibrg_m satuankonversibrg_m_1
             JOIN satuanunit_m satuan_kecil_1 ON satuankonversibrg_m_1.satuankecil_id = satuan_kecil_1.satuanunit_id
          WHERE satuankonversibrg_m_1.is_deleted = false) satuan ON penerimaanbarangdetail_r.s_konversibrg_id = satuan.satuankonversibrg_id
     LEFT JOIN ( SELECT penerimaanbarangdetail_r_1.penerimaanbarangdetail_id,
            penerimaanbarangdetail_r_1.qty_diterima::double precision * satuankonversibrg_m_1.nilai_konversi AS qty_konversi,
            penerimaanbarangdetail_r_1.harga / satuankonversibrg_m_1.nilai_konversi AS harga_konversi
           FROM penerimaanbarangdetail_r penerimaanbarangdetail_r_1
             JOIN satuankonversibrg_m satuankonversibrg_m_1 ON penerimaanbarangdetail_r_1.s_konversibrg_id = satuankonversibrg_m_1.satuankonversibrg_id
          WHERE satuankonversibrg_m_1.is_deleted = false) qty_konversi ON penerimaanbarangdetail_r.penerimaanbarangdetail_id = qty_konversi.penerimaanbarangdetail_id;");

    $this->execute('ALTER TABLE "public"."int_purchasedetail_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."saleorder_v";');

    $this->execute("
        CREATE VIEW \"public\".\"saleorder_v\" AS  SELECT pendaftaran_r.id,
    pendaftaran_r.pendaftaran_id::character varying AS sync_id_api,
    pendaftaran_r.no_pendaftaran AS name,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN '-'::character varying
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN NULL::timestamp without time zone
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.tgl_pendaftaran AS date_order,
        CASE
            WHEN pendaftaran_r.is_aps = true AND pendaftaran_r.instalasi_id <> 21 THEN '1'::text
            WHEN pendaftaran_r.instalasi_id = 1 THEN '1'::text
            WHEN pendaftaran_r.instalasi_id = 3 THEN '2'::text
            WHEN pendaftaran_r.instalasi_id = 2 AND pendaftaran_r.pasienadmisi_id IS NOT NULL THEN '2'::text
            WHEN pendaftaran_r.instalasi_id = 2 AND pendaftaran_r.pasienadmisi_id IS NULL THEN '3'::text
            WHEN pendaftaran_r.instalasi_id = 6 THEN '6'::text
            WHEN pendaftaran_r.instalasi_id = 21 THEN '5'::text
            ELSE '4'::text
        END AS patient_type,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN concat('PEN', pendaftaran_r.penjamin_id)
            ELSE concat('PEN', pasienadmisi_r.penjamin_id)
        END AS payer_id,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN COALESCE(p1.penjamin_kode, '-'::character varying)
            ELSE COALESCE(p2.penjamin_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 'draft'::text
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
    pendaftaran_r.keterangan,
    pendaftaran_r.is_sending,
    pendaftaran_r.is_sent,
        CASE
            WHEN pendaftaran_r.asuransipasien_id IS NULL THEN COALESCE(asuransipasien_m.namapemilikasuransi, '-'::character varying)
            ELSE COALESCE(asuransipasien_m.namapemilikasuransi, '-'::character varying)
        END AS nama_asuransi,
        CASE
            WHEN pendaftaran_r.asuransipasien_id IS NULL THEN COALESCE(asuransipasien_m.nokartuasuransi, '-'::character varying)
            ELSE COALESCE(asuransipasien_m.nokartuasuransi, '-'::character varying)
        END AS no_asuransi,
        CASE
            WHEN pendaftaran_r.is_sending = true AND pendaftaran_r.is_sent = false AND pendaftaran_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pendaftaran_r.is_sending = true AND pendaftaran_r.is_sent = true THEN 'SUKSES'::text
            WHEN pendaftaran_r.is_sending = false AND pendaftaran_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pendaftaran_r.is_sending = true AND pendaftaran_r.is_sent = false AND pendaftaran_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pendaftaran_r.is_sending = false AND pendaftaran_r.is_sent = false AND pendaftaran_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN COALESCE(pembayaranpelayanan.personal_amount, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN COALESCE(pembayaranpelayanan.total_dijamin, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN COALESCE(pembayaranpelayanan.total_tagihan, 0::double precision)
            ELSE 0::double precision
        END AS total_amount,
    pendaftaran_r.sync_response,
    pendaftaran_r.sync_payload
   FROM pendaftaran_r
     LEFT JOIN pasienadmisi_r ON pendaftaran_r.pasienadmisi_id = pasienadmisi_r.pasienadmisi_id
     LEFT JOIN penjamin_m p1 ON pendaftaran_r.penjamin_id = p1.penjamin_id
     LEFT JOIN penjamin_m p2 ON pasienadmisi_r.penjamin_id = p2.penjamin_id
     LEFT JOIN carabayar_m cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN carabayar_m cb2 ON p2.carabayar_id = cb2.carabayar_id
     LEFT JOIN asuransipasien_m ON pendaftaran_r.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.pendaftaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.is_deleted,
            pembayaran_t.total_tunai + pembayaran_t.total_nontunai - pembayaran_t.total_kembalian AS personal_amount,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan
           FROM pembayaranpelayanan_t
             JOIN ( SELECT max(pembayaranpelayanan_t_1.pembayaranpelayanan_id) AS pembayaranpelayanan_id,
                    pembayaranpelayanan_t_1.pendaftaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) max_pembayaran ON pembayaranpelayanan_t.pendaftaran_id = max_pembayaran.pendaftaran_id AND pembayaranpelayanan_t.pembayaranpelayanan_id = max_pembayaran.pembayaranpelayanan_id
             JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) pembayaranpelayanan ON pendaftaran_r.pendaftaran_id = pembayaranpelayanan.pendaftaran_id
UNION ALL
 SELECT penjualanresep_r.id,
    concat('RSPB', penjualanresep_r.penjualanresep_id) AS sync_id_api,
    penjualanresep_r.noresep AS name,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN '-'::character varying
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN NULL::timestamp without time zone
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    0 AS partner_id,
    penjualanresep_r.tglresep AS date_order,
    '1'::text AS patient_type,
    concat('PEN', penjualanresep_r.penjamin_id) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), '-'::character varying) AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 'draft'::text
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
    penjualanresep_r.keterangan,
    penjualanresep_r.is_sending,
    penjualanresep_r.is_sent,
    '-'::character varying AS nama_asuransi,
    '-'::character varying AS no_asuransi,
        CASE
            WHEN penjualanresep_r.is_sending = true AND penjualanresep_r.is_sent = false AND penjualanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN penjualanresep_r.is_sending = true AND penjualanresep_r.is_sent = true THEN 'SUKSES'::text
            WHEN penjualanresep_r.is_sending = false AND penjualanresep_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penjualanresep_r.is_sending = true AND penjualanresep_r.is_sent = false AND penjualanresep_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN penjualanresep_r.is_sending = false AND penjualanresep_r.is_sent = false AND penjualanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN COALESCE(pembayaranpelayanan.personal_amount, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN COALESCE(pembayaranpelayanan.total_dijamin, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN COALESCE(pembayaranpelayanan.total_tagihan, 0::double precision)
            ELSE 0::double precision
        END AS total_amount,
    penjualanresep_r.sync_response,
    penjualanresep_r.sync_payload
   FROM penjualanresep_r
     LEFT JOIN penjamin_m ON penjualanresep_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.is_deleted,
            pembayaran_t.total_tunai + pembayaran_t.total_nontunai - pembayaran_t.total_kembalian AS personal_amount,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan
           FROM pembayaranpelayanan_t
             JOIN ( SELECT max(pembayaranpelayanan_t_1.pembayaranpelayanan_id) AS pembayaranpelayanan_id,
                    pembayaranpelayanan_t_1.penjualanresep_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  GROUP BY pembayaranpelayanan_t_1.penjualanresep_id) max_pembayaran ON pembayaranpelayanan_t.penjualanresep_id = max_pembayaran.penjualanresep_id AND pembayaranpelayanan_t.pembayaranpelayanan_id = max_pembayaran.pembayaranpelayanan_id
             JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) pembayaranpelayanan ON penjualanresep_r.penjualanresep_id = pembayaranpelayanan.penjualanresep_id
  WHERE penjualanresep_r.jenispenjualan::text = '343'::text;");

    $this->execute('ALTER TABLE "public"."saleorder_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."saleorder_line_v";');

    $this->execute("
        CREATE VIEW \"public\".\"saleorder_line_v\" AS  SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('TND', tindakanpelayanan_r.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
    tindakanpelayanan_r.qty_tindakan AS product_uom_qty,
    tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision) AS price_unit,
    tindakanpelayanan_r.tarif_tindakan AS price_subtotal,
    tindakanpelayanan_r.tarif_tindakan AS price_total,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin = 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.tarif_dibayarkan <> 0::double precision AND tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dijamin, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', daftartindakan_m.servicecategory_id) AS service_categ_id,
    concat('PEG', pendaftaran_r.pegawai_id) AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE concat('PEG', pendaftaran_r.pegawai_id)
        END AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, no_pembayaran.no_pembayaran) AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 'LOS'::text
            WHEN kelompoktindakan_m.kelompoktindakan_namalainnya::text = 'LOS'::text THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
            WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
            WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN ruangan_m.instalasi_id = 1 THEN 'OUTPATIENT'::text
            WHEN ruangan_m.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN ruangan_m.instalasi_id = 3 THEN 'INPATIENT'::text
            WHEN ruangan_m.instalasi_id = 21 THEN 'MCU'::text
            WHEN ruangan_m.instalasi_id = 4 THEN 'LABORATORY'::text
            WHEN ruangan_m.instalasi_id = 5 THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tindakanpelayanan_r.no_tindakanpelayanan
        END AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'TINDAKAN'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    NULL::json AS additional_paket,
    pendaftaran_r.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN 'SUKSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.tgl_pendaftaran AS admit_date
   FROM tindakanpelayanan_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            pendaftaran_r_1.is_aps,
            pendaftaran_t.tgl_pendaftaran
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pendaftaran_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ruangan_m ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN pasienpulang_t ON pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pendaftaran_r_1.is_sent = true) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pembayaranpelayanan_t ON tindakanpelayanan_r.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) no_pembayaran ON tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_diskon
           FROM tindakanpelayanan_t) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('PKT', tindakanpelayanan_r.tipepaket_id) AS product_id,
    tipepaket_m.tipepaket_nama AS name,
    351 AS product_uom,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'ACCRUAL REVERSAL'::text THEN - tindakanpelayanan_r.qty_tindakan
            ELSE tindakanpelayanan_r.qty_tindakan
        END AS product_uom_qty,
    0 AS price_unit,
    0 AS price_subtotal,
    0 AS price_total,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin = 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.tarif_dibayarkan <> 0::double precision AND tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dijamin, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', 10) AS service_categ_id,
    concat('PEG', pendaftaran_r.pegawai_id) AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE concat('PEG', pendaftaran_r.pegawai_id)
        END AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, no_pembayaran.no_pembayaran) AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    '-'::character varying AS item_specialisation,
        CASE
            WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
            WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
            WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN ruangan_m.instalasi_id = 1 THEN 'OUTPATIENT'::text
            WHEN ruangan_m.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN ruangan_m.instalasi_id = 3 THEN 'INPATIENT'::text
            WHEN ruangan_m.instalasi_id = 21 THEN 'MCU'::text
            WHEN ruangan_m.instalasi_id = 4 THEN 'LABORATORY'::text
            WHEN ruangan_m.instalasi_id = 5 THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    'others'::text AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tindakanpelayanan_r.no_tindakanpelayanan
        END AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    true AS is_package,
    tipepaket_m.tipepaket_nama AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'PAKET'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    NULL::json AS additional_paket,
    pendaftaran_r.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN 'SUKSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.tgl_pendaftaran AS admit_date
   FROM tindakanpelayanan_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            pendaftaran_r_1.is_aps,
            pendaftaran_t.tgl_pendaftaran
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pendaftaran_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ruangan_m ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN pasienpulang_t ON pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pendaftaran_r_1.is_sent = true) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN tipepaket_m ON tindakanpelayanan_r.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pembayaranpelayanan_t ON tindakanpelayanan_r.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) no_pembayaran ON tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_diskon
           FROM tindakanpelayanan_t) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('TND', tindakanpelayanan_r.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
    tindakanpelayanan_r.qty_tindakan AS product_uom_qty,
    tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision) AS price_unit,
    tindakanpelayanan_r.tarif_tindakan::integer AS price_subtotal,
    tindakanpelayanan_r.tarif_tindakan::integer AS price_total,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin = 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.tarif_dibayarkan <> 0::double precision AND tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dijamin, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    concat('RSPB', penjualanresep.penjualanresep_id) AS order_id,
    concat('CATEG', daftartindakan_m.servicecategory_id) AS service_categ_id,
    concat('PEG', penjualanresep.pegawairesep_id) AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE concat('PEG', penjualanresep.pegawairesep_id)
        END AS prescribe_doc_id,
    concat('PEG', penjualanresep.pegawairesep_id) AS perform_doc_id,
    penjualanresep.ruangan_id::text AS location_id,
    penjualanresep.ruangan_nama AS department_id,
    penjualanresep.no_pembayaran AS billno,
    penjualanresep.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
    'OPD'::text AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN tindakanpelayanan_r.instalasi_id = 1 THEN 'OUTPATIENT'::text
            WHEN tindakanpelayanan_r.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN tindakanpelayanan_r.instalasi_id = 3 THEN 'INPATIENT'::text
            WHEN tindakanpelayanan_r.instalasi_id = 21 THEN 'MCU'::text
            WHEN tindakanpelayanan_r.instalasi_id = 4 THEN 'LABORATORY'::text
            WHEN tindakanpelayanan_r.instalasi_id = 5 THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    NULL::character varying AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjualanresep.penjamin_kode AS payer_code,
    penjualanresep.carabayar_nama AS payer_type,
    penjualanresep.penjamin_nama AS payer_name,
    penjualanresep.noresep AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
    concat('PEG', penjualanresep.pegawairesep_id) AS account_analytic_id,
    concat('PEG', penjualanresep.pegawairesep_id) AS backup_analytic_id,
    NULL::character varying AS kota,
    NULL::character varying AS kecamatan,
    NULL::character varying AS kelurahan,
    0 AS partner_id,
    penjualanresep.no_rekam_medik AS registration_code,
    penjualanresep.noresep AS number_admission,
    '-'::text AS manufacture,
    penjualanresep.tglresep::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'TINDAKAN'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    NULL::json AS additional_paket,
    penjualanresep.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN 'SUKSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    penjualanresep.tglresep AS admit_date
   FROM tindakanpelayanan_r
     JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
            penjualanresep_r.pegawairesep_id,
            penjualanresep_r.noresep,
            pasien_m.no_rekam_medik,
            penjualanresep_r.tglresep,
            pasien_m.nama_pasien,
            penjamin_m.penjamin_nama,
            penjamin_m.penjamin_kode,
            carabayar_m.carabayar_nama,
            pembayaranpelayanan_t.no_pembayaran,
            penjualanresep_r.penjualanresep_id,
            penjualanresep_r.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaranpelayanan_t.tgl_pembayaran
           FROM penjualanresep_r
             JOIN pasien_m ON 0 = pasien_m.pasien_id
             JOIN ruangan_m ON penjualanresep_r.ruangan_id = ruangan_m.ruangan_id
             JOIN pembayaranpelayanan_t ON penjualanresep_r.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
             JOIN penjamin_m ON penjualanresep_r.penjamin_id = penjamin_m.penjamin_id
             JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
          WHERE penjualanresep_r.jenispenjualan::text = '343'::text AND penjualanresep_r.keterangan::text = 'ACCRUAL'::text AND penjualanresep_r.is_sent = true) penjualanresep ON tindakanpelayanan_r.pembayaran_id = penjualanresep.pembayaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id) pembayaran ON penjualanresep.pembayaran_id = pembayaran.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON penjualanresep.pegawairesep_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_diskon
           FROM tindakanpelayanan_t) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id;");

    $this->execute('ALTER TABLE "public"."saleorder_line_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."int_obatalkespasien_v";');

    $this->execute("
        CREATE VIEW \"public\".\"int_obatalkespasien_v\" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
        CASE
            WHEN obatalkespasien_r.det = 0::double precision OR obatalkespasien_r.det IS NULL THEN obatalkespasien_r.qty_oa
            ELSE obatalkespasien_r.det
        END AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa AS price_unit,
    obatalkespasien_r.hargajual_oa AS price_subtotal,
    obatalkespasien_r.hargajual_oa AS price_total,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin = 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            WHEN obatalkespasien_r.tarif_dibayarkan <> 0::double precision AND obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dijamin, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('PEG', pendaftaran_r.pegawai_id) AS primary_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS prescribe_doc_id,
    NULL::text AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
            WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
            WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
    NULL::text AS special_group,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN 'PHARMACY OUTPATIENT'::text
            ELSE 'PHARMACY INPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, 'GENERAL'::character varying) AS bed_type,
    concat('PEN', obatalkespasien_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
        CASE
            WHEN obatalkespasien_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE COALESCE(penjualanresep_t.no_resep, obatalkespasien_r.no_obatalkespasien)
        END AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS account_analytic_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
        CASE
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.nama_pasien,
    pendaftaran_r.tgl_pendaftaran AS admit_date
   FROM obatalkespasien_r
     JOIN ( SELECT obatalkespasien_t_1.obatalkespasien_id,
            obatalkespasien_t_1.tarif_dibayarkan,
            obatalkespasien_t_1.tarif_dijamin,
            obatalkespasien_t_1.tarif_diskon
           FROM obatalkespasien_t obatalkespasien_t_1) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasienadmisi_id,
            pendaftaran_r_1.instalasi_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_r_1.tgl_pendaftaran
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN pasienpulang_t ON pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pendaftaran_r_1.is_sent = true) pendaftaran_r ON obatalkespasien_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
     JOIN ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pasienmasukpenunjang_t ON obatalkespasien_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT obatalkespasien_r_1.pendaftaran_id,
            sum(obatalkespasien_r_1.hargajual_oa) AS total_tagihan
           FROM obatalkespasien_r obatalkespasien_r_1
          WHERE obatalkespasien_r_1.is_deleted = false
          GROUP BY obatalkespasien_r_1.pendaftaran_id) total_tagihan ON obatalkespasien_r.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN obatsudahbayar_t ON obatalkespasien_r.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT penjualanresep_t_1.penjualanresep_id,
            penjualanresep_t_1.pegawai_id,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM penjualanresep_t penjualanresep_t_1
             LEFT JOIN reseptur_t ON penjualanresep_t_1.penjualanresep_id = reseptur_t.penjualanresep_id
          GROUP BY penjualanresep_t_1.penjualanresep_id, penjualanresep_t_1.pegawai_id, (
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
UNION ALL
 SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
        CASE
            WHEN obatalkespasien_r.det = 0::double precision OR obatalkespasien_r.det IS NULL THEN obatalkespasien_r.qty_oa
            ELSE obatalkespasien_r.det
        END AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa::integer AS price_unit,
    obatalkespasien_r.hargajual_oa::integer AS price_subtotal,
    obatalkespasien_r.hargajual_oa::integer AS price_total,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin = 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            WHEN obatalkespasien_r.tarif_dibayarkan <> 0::double precision AND obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dijamin, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    concat('RSPB', obatalkespasien_r.penjualanresep_id) AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('PEG', penjualanresep_t.pegawai_id) AS primary_doc_id,
    concat('PEG', penjualanresep_t.pegawai_id) AS prescribe_doc_id,
    NULL::text AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
    'OPD'::text AS patient_group,
    NULL::text AS special_group,
    'PHARMACY OUTPATIENT'::text AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, 'GENERAL'::character varying) AS bed_type,
    concat('PEN', obatalkespasien_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    COALESCE(penjualanresep_t.no_resep) AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
    concat('PEG', penjualanresep_t.pegawai_id) AS account_analytic_id,
    concat('PEG', penjualanresep_t.pegawai_id) AS backup_analytic_id,
    penjualanresep_r.kota,
    penjualanresep_r.kecamatan,
    penjualanresep_r.kelurahan,
    penjualanresep_r.pasien_id AS partner_id,
    penjualanresep_r.no_rekam_medik AS registration_code,
    penjualanresep_r.noresep AS number_admission,
    '-'::text AS manufacture,
    penjualanresep_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
        CASE
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    penjualanresep_r.nama_pasien,
    penjualanresep_r.tglresep AS admit_date
   FROM obatalkespasien_r
     JOIN ( SELECT obatalkespasien_t_1.obatalkespasien_id,
            obatalkespasien_t_1.tarif_dibayarkan,
            obatalkespasien_t_1.tarif_dijamin,
            obatalkespasien_t_1.tarif_diskon
           FROM obatalkespasien_t obatalkespasien_t_1) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN ( SELECT penjualanresep_r_1.id,
            penjualanresep_r_1.penjualanresep_id,
            penjualanresep_r_1.pegawai_id,
            penjualanresep_r_1.pasienadmisi_id,
            0 AS pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            penjualanresep_r_1.noresep,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            penjualanresep_r_1.tglresep AS tglpasienpulang,
            penjualanresep_r_1.tglresep
           FROM penjualanresep_r penjualanresep_r_1
             JOIN ( SELECT max(resep_max.id) AS id,
                    resep_max.penjualanresep_id
                   FROM penjualanresep_r resep_max
                  WHERE resep_max.keterangan::text = 'ACCRUAL'::text
                  GROUP BY resep_max.penjualanresep_id) max ON penjualanresep_r_1.id = max.id
             JOIN pasien_m ON 0 = pasien_m.pasien_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
          WHERE penjualanresep_r_1.is_sent = true) penjualanresep_r ON obatalkespasien_r.penjualanresep_id = penjualanresep_r.penjualanresep_id
     JOIN obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
     JOIN ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT obatalkespasien_r_1.pendaftaran_id,
            sum(obatalkespasien_r_1.hargajual_oa) AS total_tagihan
           FROM obatalkespasien_r obatalkespasien_r_1
          WHERE obatalkespasien_r_1.is_deleted = false
          GROUP BY obatalkespasien_r_1.pendaftaran_id) total_tagihan ON obatalkespasien_r.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN obatsudahbayar_t ON obatalkespasien_r.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT penjualanresep_t_1.penjualanresep_id,
            penjualanresep_t_1.pegawai_id,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM penjualanresep_t penjualanresep_t_1
             LEFT JOIN reseptur_t ON penjualanresep_t_1.penjualanresep_id = reseptur_t.penjualanresep_id
          GROUP BY penjualanresep_t_1.penjualanresep_id, penjualanresep_t_1.pegawai_id, (
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id;");

    $this->execute('ALTER TABLE "public"."int_obatalkespasien_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."int_stockscrap_v";');

    $this->execute("
        CREATE VIEW \"public\".\"int_stockscrap_v\" AS  SELECT 'obat'::text AS jenis,
    'adj_keluar'::text AS tipe_rekap,
    concat('AJK', adjusmenobatkeluar_r.adjusmenobatkeluar_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenobat_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenobat_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AI'::text AS trans_type,
    adjusmenobat_t.ruangan_adjusmen_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', adjusmenobatkeluar_r.obatalkes_id) AS product_id,
    concat(adjusmenobat_t.no_adjusmen, '-', obatalkes_m.obatalkes_nama) AS name,
    adjusmenobatkeluar_r.satuankecil_id AS product_uom_id,
    adjusmenobatkeluar_r.qty_konversi AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    adjusmenobatkeluar_r.qty_konversi::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    adjusmenobatkeluar_r.id,
    adjusmenobatkeluar_r.is_sending,
    adjusmenobatkeluar_r.is_sent,
    adjusmenobatkeluar_r.sync_respon,
        CASE
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenobatkeluar_r.is_sending = false AND adjusmenobatkeluar_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN adjusmenobatkeluar_r.is_sending = false AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenobatkeluar_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenobatkeluar_r
     JOIN adjusmenobat_t ON adjusmenobatkeluar_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
     JOIN obatalkes_m ON adjusmenobatkeluar_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.adjusmenobatkeluar_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.adjusmenobatkeluar_id, stokobatalkes_t.nobatch) stok ON adjusmenobatkeluar_r.adjusmenobatkeluar_id = stok.adjusmenobatkeluar_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    'adj_masuk'::text AS tipe_rekap,
    concat('AJM', adjusmenobatmasuk_r.adjusmenobatmasuk_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenobat_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenobat_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AR'::text AS trans_type,
    'scrap'::character varying AS location_id,
    adjusmenobat_t.ruangan_adjusmen_id::character varying AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', adjusmenobatmasuk_r.obatalkes_id) AS product_id,
    concat(adjusmenobat_t.no_adjusmen, '-', obatalkes_m.obatalkes_nama) AS name,
    adjusmenobatmasuk_r.satuankecil_id AS product_uom_id,
    adjusmenobatmasuk_r.qty_konversi AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    adjusmenobatmasuk_r.qty_konversi::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    adjusmenobatmasuk_r.id,
    adjusmenobatmasuk_r.is_sending,
    adjusmenobatmasuk_r.is_sent,
    adjusmenobatmasuk_r.sync_respon,
        CASE
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenobatmasuk_r.is_sending = false AND adjusmenobatmasuk_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN adjusmenobatmasuk_r.is_sending = false AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenobatmasuk_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenobatmasuk_r
     JOIN adjusmenobat_t ON adjusmenobatmasuk_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
     JOIN obatalkes_m ON adjusmenobatmasuk_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.adjusmenobatmasuk_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.adjusmenobatmasuk_id, stokobatalkes_t.nobatch) stok ON adjusmenobatmasuk_r.adjusmenobatmasuk_id = stok.adjusmenobatmasuk_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    'pemusnahan_obat'::text AS tipe_rekap,
    concat('PMO', pemusnahanobatdetail_r.pemusnahanobatdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemusnahanobat_t.nopemusnahan AS origin,
    NULL::text AS admission_id,
    pemusnahanobat_t.tglpemusnahan AS transaction_datetime,
    to_char(pemusnahanobat_t.tglpemusnahan, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'BR'::text AS trans_type,
    pemusnahanobat_t.ruangan_id::character varying AS location_id,
    'scrap'::character varying AS scrap_location_id,
    pemusnahanobatdetail_r.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', pemusnahanobatdetail_r.obatalkes_id) AS product_id,
    concat(pemusnahanobat_t.nopemusnahan, '-', obatalkes_m.obatalkes_nama) AS name,
    pemusnahanobatdetail_r.satuan_id AS product_uom_id,
    pemusnahanobatdetail_r.jumlah AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    pemusnahanobatdetail_r.jumlah * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    pemusnahanobatdetail_r.id,
    pemusnahanobatdetail_r.is_sending,
    pemusnahanobatdetail_r.is_sent,
    pemusnahanobatdetail_r.sync_respon,
        CASE
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemusnahanobatdetail_r.is_sending = false AND pemusnahanobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pemusnahanobatdetail_r.is_sending = false AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pemusnahanobatdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemusnahanobatdetail_r
     JOIN pemusnahanobat_t ON pemusnahanobatdetail_r.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id
     JOIN obatalkes_m ON pemusnahanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.pemusnahanobatdetail_id
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.pemusnahanobatdetail_id) stok ON pemusnahanobatdetail_r.pemusnahanobatdetail_id = stok.pemusnahanobatdetail_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    'pemakaian_obat'::text AS tipe_rekap,
    concat('PKO', pemakaianobatdetail_r.pemakaianobatdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemakaianobat_t.nopemakaian_obat AS origin,
    NULL::text AS admission_id,
    pemakaianobat_t.tglpemakaianobat AS transaction_datetime,
    to_char(pemakaianobat_t.tglpemakaianobat::timestamp with time zone, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'SC'::text AS trans_type,
    pemakaianobat_t.ruangan_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', pemakaianobatdetail_r.obatalkes_id) AS product_id,
    concat(pemakaianobat_t.nopemakaian_obat, '-', obatalkes_m.obatalkes_nama) AS name,
    pemakaianobatdetail_r.satuankecil_id AS product_uom_id,
    pemakaianobatdetail_r.qty_satuanpakai AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    pemakaianobatdetail_r.qty_satuanpakai::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    pemakaianobatdetail_r.id,
    pemakaianobatdetail_r.is_sending,
    pemakaianobatdetail_r.is_sent,
    pemakaianobatdetail_r.sync_respon,
        CASE
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemakaianobatdetail_r.is_sending = false AND pemakaianobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pemakaianobatdetail_r.is_sending = false AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pemakaianobatdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemakaianobatdetail_r
     JOIN pemakaianobat_t ON pemakaianobatdetail_r.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
     JOIN obatalkes_m ON pemakaianobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.pemakaianobatdetail_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.pemakaianobatdetail_id, stokobatalkes_t.nobatch) stok ON pemakaianobatdetail_r.pemakaianobatdetail_id = stok.pemakaianobatdetail_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'adj_keluar'::text AS tipe_rekap,
    concat('AJK', adjusmenbarangkeluar_r.adjusmenbarangkeluar_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenbarang_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenbarang_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenbarang_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AI'::text AS trans_type,
    adjusmenbarang_t.ruangan_adjusmen_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('BRG', adjusmenbarangkeluar_r.barang_id) AS product_id,
    concat(adjusmenbarang_t.no_adjusmen, '-', barang_m.barang_nama) AS name,
    adjusmenbarangkeluar_r.satuankecil_id AS product_uom_id,
    adjusmenbarangkeluar_r.qty_konversi AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    adjusmenbarangkeluar_r.qty_konversi::double precision * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    adjusmenbarangkeluar_r.id,
    adjusmenbarangkeluar_r.is_sending,
    adjusmenbarangkeluar_r.is_sent,
    adjusmenbarangkeluar_r.sync_respon,
        CASE
            WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = false AND adjusmenbarangkeluar_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenbarangkeluar_r.is_sending = false AND adjusmenbarangkeluar_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = false AND adjusmenbarangkeluar_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN adjusmenbarangkeluar_r.is_sending = false AND adjusmenbarangkeluar_r.is_sent = false AND adjusmenbarangkeluar_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenbarangkeluar_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenbarangkeluar_r
     JOIN adjusmenbarang_t ON adjusmenbarangkeluar_r.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id
     JOIN barang_m ON adjusmenbarangkeluar_r.barang_id = barang_m.barang_id
     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT stokbarang_t.adjusmenbarangkeluar_id,
            stokbarang_t.nobatch
           FROM stokbarang_t
          WHERE stokbarang_t.is_deleted = false
          GROUP BY stokbarang_t.adjusmenbarangkeluar_id, stokbarang_t.nobatch) stok ON adjusmenbarangkeluar_r.adjusmenbarangkeluar_id = stok.adjusmenbarangkeluar_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'adj_masuk'::text AS tipe_rekap,
    concat('AJM', adjusmenbarangmasuk_r.adjusmenbarangmasuk_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenbarang_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenbarang_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenbarang_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AR'::text AS trans_type,
    'scrap'::character varying AS location_id,
    adjusmenbarang_t.ruangan_adjusmen_id::character varying AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('BRG', adjusmenbarangmasuk_r.barang_id) AS product_id,
    concat(adjusmenbarang_t.no_adjusmen, '-', barang_m.barang_nama) AS name,
    adjusmenbarangmasuk_r.satuankecil_id AS product_uom_id,
    adjusmenbarangmasuk_r.qty_konversi AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    adjusmenbarangmasuk_r.qty_konversi::double precision * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    adjusmenbarangmasuk_r.id,
    adjusmenbarangmasuk_r.is_sending,
    adjusmenbarangmasuk_r.is_sent,
    adjusmenbarangmasuk_r.sync_respon,
        CASE
            WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = false AND adjusmenbarangmasuk_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenbarangmasuk_r.is_sending = false AND adjusmenbarangmasuk_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = false AND adjusmenbarangmasuk_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN adjusmenbarangmasuk_r.is_sending = false AND adjusmenbarangmasuk_r.is_sent = false AND adjusmenbarangmasuk_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenbarangmasuk_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenbarangmasuk_r
     JOIN adjusmenbarang_t ON adjusmenbarangmasuk_r.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id
     JOIN barang_m ON adjusmenbarangmasuk_r.barang_id = barang_m.barang_id
     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT stokbarang_t.adjusmenbarangmasuk_id,
            stokbarang_t.nobatch
           FROM stokbarang_t
          WHERE stokbarang_t.is_deleted = false
          GROUP BY stokbarang_t.adjusmenbarangmasuk_id, stokbarang_t.nobatch) stok ON adjusmenbarangmasuk_r.adjusmenbarangmasuk_id = stok.adjusmenbarangmasuk_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'pemusnahan_barang'::text AS tipe_rekap,
    concat('PMO', pemusnahanbarangdetail_r.pemusnahanbarangdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemusnahanbarang_t.nopemusnahan AS origin,
    NULL::text AS admission_id,
    pemusnahanbarang_t.tglpemusnahan AS transaction_datetime,
    to_char(pemusnahanbarang_t.tglpemusnahan, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'BR'::text AS trans_type,
    pemusnahanbarang_t.ruangan_id::character varying AS location_id,
    'scrap'::character varying AS scrap_location_id,
    pemusnahanbarangdetail_r.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('BRG', pemusnahanbarangdetail_r.barang_id) AS product_id,
    concat(pemusnahanbarang_t.nopemusnahan, '-', barang_m.barang_nama) AS name,
    barang_m.satuankecil_id AS product_uom_id,
    pemusnahanbarangdetail_r.jumlah AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    pemusnahanbarangdetail_r.jumlah * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    pemusnahanbarangdetail_r.id,
    pemusnahanbarangdetail_r.is_sending,
    pemusnahanbarangdetail_r.is_sent,
    pemusnahanbarangdetail_r.sync_respon,
        CASE
            WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = false AND pemusnahanbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemusnahanbarangdetail_r.is_sending = false AND pemusnahanbarangdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = false AND pemusnahanbarangdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pemusnahanbarangdetail_r.is_sending = false AND pemusnahanbarangdetail_r.is_sent = false AND pemusnahanbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pemusnahanbarangdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemusnahanbarangdetail_r
     JOIN pemusnahanbarang_t ON pemusnahanbarangdetail_r.pemusnahanbarang_id = pemusnahanbarang_t.pemusnahanbarang_id
     JOIN barang_m ON pemusnahanbarangdetail_r.barang_id = barang_m.barang_id
     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT stokbarang_t.pemusnahanbarangdetail_id
           FROM stokbarang_t
          WHERE stokbarang_t.is_deleted = false
          GROUP BY stokbarang_t.pemusnahanbarangdetail_id) stok ON pemusnahanbarangdetail_r.pemusnahanbarangdetail_id = stok.pemusnahanbarangdetail_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'pemakaian_barang'::text AS tipe_rekap,
    concat('PKO', pemakaianbarangdetail_r.pemakaianbarangdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemakaianbarang_t.no_pemakaianbarang AS origin,
    NULL::text AS admission_id,
    pemakaianbarang_t.tgl_pemakaianbarang AS transaction_datetime,
    to_char(pemakaianbarang_t.tgl_pemakaianbarang::timestamp with time zone, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'SC'::text AS trans_type,
    pemakaianbarang_t.ruangan_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('BRG', pemakaianbarangdetail_r.barang_id) AS product_id,
    concat(pemakaianbarang_t.no_pemakaianbarang, '-', barang_m.barang_nama) AS name,
    pemakaianbarangdetail_r.satuankecil_id AS product_uom_id,
    pemakaianbarangdetail_r.jumlah_pakai AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    pemakaianbarangdetail_r.jumlah_pakai::double precision * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    pemakaianbarangdetail_r.id,
    pemakaianbarangdetail_r.is_sending,
    pemakaianbarangdetail_r.is_sent,
    pemakaianbarangdetail_r.sync_respon,
        CASE
            WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = false AND pemakaianbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemakaianbarangdetail_r.is_sending = false AND pemakaianbarangdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = false AND pemakaianbarangdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pemakaianbarangdetail_r.is_sending = false AND pemakaianbarangdetail_r.is_sent = false AND pemakaianbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pemakaianbarangdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemakaianbarangdetail_r
     JOIN pemakaianbarang_t ON pemakaianbarangdetail_r.pemakaianbarang_id = pemakaianbarang_t.pemakaianbarang_id
     JOIN barang_m ON pemakaianbarangdetail_r.barang_id = barang_m.barang_id
     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT stokbarang_t.pemakaianbarangdetail_id,
            stokbarang_t.nobatch
           FROM stokbarang_t
          WHERE stokbarang_t.is_deleted = false
          GROUP BY stokbarang_t.pemakaianbarangdetail_id, stokbarang_t.nobatch) stok ON pemakaianbarangdetail_r.pemakaianbarangdetail_id = stok.pemakaianbarangdetail_id;");

    $this->execute('ALTER TABLE "public"."int_stockscrap_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."saleorder_linedetail_v";');

    $this->execute("
        CREATE VIEW \"public\".\"saleorder_linedetail_v\" AS  SELECT 6 AS sync_type,
    tp_paket.tgl_proses::date AS tglproses,
    concat('TND', tp_paket.id, '-', concat('TND', paketpelayanan_mp.daftartindakan_id)) AS sync_id_api,
    concat('TND', paketpelayanan_mp.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
        CASE
            WHEN tp_paket.keterangan::text = 'ACCRUAL REVERSAL'::text THEN - tp_paket.qty_tindakan
            WHEN tp_paket.keterangan::text = 'BILLING CANCEL'::text THEN - tp_paket.qty_tindakan
            ELSE tp_paket.qty_tindakan
        END AS product_uom_qty,
    paket_detail.harga_satuan + paket_detail.harga_cyto + paket_detail.harga_penyulit AS price_unit,
    paket_detail.harga_satuan + paket_detail.harga_cyto + paket_detail.harga_penyulit AS price_subtotal,
    paket_detail.harga_satuan + paket_detail.harga_cyto + paket_detail.harga_penyulit AS price_total,
        CASE
            WHEN tp_paket.keterangan::text = 'BILLING'::text THEN tp_paket.tarif_dibayarkan
            ELSE - tp_paket.tarif_dibayarkan
        END AS personal_amount,
        CASE
            WHEN tp_paket.keterangan::text = 'BILLING'::text THEN tp_paket.tarif_dijamin
            ELSE - tp_paket.tarif_dijamin
        END AS payer_amount,
    tp_paket.pendaftaran_id AS order_id,
    concat('CATEG', daftartindakan_m.servicecategory_id) AS service_categ_id,
    concat('PEG', pendaftaran_r_1.pegawai_id) AS primary_doc_id,
    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS prescribe_doc_id,
    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m_1.ruangan_id::text AS location_id,
    ruangan_m_1.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t_1.no_pembayaran, no_pembayaran_1.no_pembayaran) AS billno,
    pembayaranpelayanan_t_1.tgl_pembayaran AS bill_date,
    tp_paket.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r_1.is_aps = true THEN 'LOS'::text
            WHEN kelompoktindakan_m.kelompoktindakan_namalainnya::text = 'LOS'::text THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r_1.instalasi_id = 1 THEN 'OPD'::text
            WHEN pendaftaran_r_1.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN pendaftaran_r_1.instalasi_id = 3 THEN 'IPD'::text
            WHEN tp_paket.instalasi_id = 21 THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN tp_paket.instalasi_id = 1 THEN 'OUTPATIENT'::text
            WHEN tp_paket.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN tp_paket.instalasi_id = 3 THEN 'INPATIENT'::text
            WHEN tp_paket.instalasi_id = 21 THEN 'MCU'::text
            WHEN tp_paket.instalasi_id = 4 THEN 'LABORATORY'::text
            WHEN tp_paket.instalasi_id = 5 THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    kelaspelayanan_m_1.kelaspelayanan_nama AS bed_type,
    concat('PEN', tp_paket.penjamin_id) AS payer,
    penjamin_m_1.penjamin_kode AS payer_code,
    carabayar_m_1.carabayar_nama AS payer_type,
    penjamin_m_1.penjamin_nama AS payer_name,
        CASE
            WHEN tp_paket.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tp_paket.no_tindakanpelayanan
        END AS order_no,
    tp_paket.tgl_tindakan::date AS order_date,
    true AS is_package,
    tipepaket_m_1.tipepaket_nama AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN tp_paket.dokterpenanggungjawab_id IS NULL THEN concat('PEG', pendaftaran_r_1.pegawai_id)
            ELSE concat('PEG', tp_paket.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tp_paket.dokterpenanggungjawab_id IS NULL THEN concat('PEG', pendaftaran_r_1.pegawai_id)
            ELSE concat('PEG', tp_paket.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r_1.kota,
    pendaftaran_r_1.kecamatan,
    pendaftaran_r_1.kelurahan,
    pendaftaran_r_1.pasien_id AS partner_id,
    pendaftaran_r_1.no_rekam_medik AS registration_code,
    pendaftaran_r_1.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    pendaftaran_r_1.tglpasienpulang::date AS discharge_date,
    pegawai_1.spesialis_nama AS specialization_primary,
    tp_paket.is_sent,
    tp_paket.is_sending,
    tp_paket.id,
    'PAKET_DETAIL'::text AS jenis,
        CASE
            WHEN tp_paket.keterangan::text = ANY (ARRAY['ACCRUAL REVERSAL'::character varying::text, 'ACCRUAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN tp_paket.keterangan::text = ANY (ARRAY['ACCRUAL REVERSAL'::character varying::text, 'ACCRUAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
        CASE
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = true THEN 'SUKSES'::text
            WHEN tp_paket.is_sending = false AND tp_paket.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN tp_paket.is_sending = false AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM tindakanpelayanan_r tp_paket
     JOIN ( SELECT pen_det.id,
            pen_det.pendaftaran_id,
            pen_det.pegawai_id,
            pen_det.pasien_id,
            pasien_m.no_rekam_medik,
            pen_det.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pen_det.pasienadmisi_id IS NULL THEN pen_det.instalasi_id
                    ELSE ruangan_m_2.instalasi_id
                END AS instalasi_id,
            pen_det.is_aps
           FROM pendaftaran_r pen_det
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pen_det.id = max.id
             JOIN pasien_m ON pen_det.pasien_id = pasien_m.pasien_id
             LEFT JOIN pasienadmisi_t ON pen_det.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ruangan_m ruangan_m_2 ON pasienadmisi_t.ruangan_id = ruangan_m_2.ruangan_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN pasienpulang_t ON pen_det.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pen_det.is_sent = true) pendaftaran_r_1 ON tp_paket.pendaftaran_id = pendaftaran_r_1.pendaftaran_id
     JOIN tipepaket_m tipepaket_m_1 ON tp_paket.tipepaket_id = tipepaket_m_1.tipepaket_id
     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN ruangan_m ruangan_m_1 ON tp_paket.ruangan_id = ruangan_m_1.ruangan_id
     JOIN penjamin_m penjamin_m_1 ON tp_paket.penjamin_id = penjamin_m_1.penjamin_id
     JOIN carabayar_m carabayar_m_1 ON penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id
     LEFT JOIN kelaspelayanan_m kelaspelayanan_m_1 ON tp_paket.kelaspelayanan_id = kelaspelayanan_m_1.kelaspelayanan_id
     LEFT JOIN ( SELECT tp_paket_1.pendaftaran_id,
            sum(tp_paket_1.tarif_tindakan) AS total_tagihan
           FROM tindakanpelayanan_r tp_paket_1
          WHERE tp_paket_1.is_deleted = false
          GROUP BY tp_paket_1.pendaftaran_id) total_tagihan_1 ON tp_paket.pendaftaran_id = total_tagihan_1.pendaftaran_id
     LEFT JOIN tindakansudahbayar_t tindakansudahbayar_t_1 ON tp_paket.tindakansudahbayar_id = tindakansudahbayar_t_1.tindakansudahbayar_id
     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON tindakansudahbayar_t_1.pembayaranpelayanan_id = pembayaranpelayanan_t_1.pembayaranpelayanan_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran_1 ON tp_paket.pendaftaran_id = pembayaran_1.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'daftartindakan_id'::text)::integer AS daftartindakan_id,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'harga_satuan'::text)::double precision AS harga_satuan,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'harga_cyto'::text)::double precision AS harga_cyto,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'harga_penyulit'::text)::double precision AS harga_penyulit,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'harga_total'::text)::double precision AS harga_total
           FROM tindakanpelayanan_t) paket_detail ON tp_paket.tindakanpelayanan_id = paket_detail.tindakanpelayanan_id AND daftartindakan_m.daftartindakan_id = paket_detail.daftartindakan_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1_1.pembayaran_id,
            pembayaranpelayanan_t_1_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1_1) no_pembayaran_1 ON tp_paket.pembayaran_id = no_pembayaran_1.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai_1 ON tp_paket.dokterpenanggungjawab_id = pegawai_1.pegawai_id
     LEFT JOIN pasienmasukpenunjang_t ON tp_paket.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id;");

    $this->execute('ALTER TABLE "public"."saleorder_linedetail_v" OWNER TO "postgres";');

    $this->execute('DROP VIEW if exists "public"."int_saleorderupdate_v";');

    $this->execute("
        CREATE VIEW \"public\".\"int_saleorderupdate_v\" AS  SELECT pembayaran_r.id,
    pendaftaran_t.pendaftaran_id::character varying AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = false THEN pembayaranpelayanan_t.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = false THEN pembayaranpelayanan_t.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    pendaftaran_t.pasien_id AS partner_id,
    pendaftaran_t.tgl_pendaftaran AS date_order,
        CASE
            WHEN pendaftaran_t.is_aps = true AND pendaftaran_t.instalasi_id <> 21 THEN '1'::text
            WHEN pendaftaran_t.instalasi_id = 1 THEN '1'::text
            WHEN pendaftaran_t.instalasi_id = 3 THEN '2'::text
            WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NOT NULL THEN '2'::text
            WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NULL THEN '3'::text
            WHEN pendaftaran_t.instalasi_id = 6 THEN '6'::text
            WHEN pendaftaran_t.instalasi_id = 21 THEN '5'::text
            ELSE '4'::text
        END AS patient_type,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN concat('PEN', pendaftaran_t.penjamin_id)
            ELSE concat('PEN', pasienadmisi_t.penjamin_id)
        END AS payer_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.s_kode, '-'::character varying)
            ELSE COALESCE(p2.penjamin_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = true THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    COALESCE(pembayaran.personal_amount, 0::double precision) AS personal_amount,
    COALESCE(pembayaran.payer_amount, 0::double precision) AS payer_amount,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = true THEN 0::double precision
            ELSE COALESCE(pembayaran_amount.total_tagihan, 0::double precision)
        END AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     JOIN pendaftaran_t ON pembayaran_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
          WHERE pendaftaran_r_1.is_sent = true) pendaftaran_r ON pendaftaran_t.pendaftaran_id = pendaftaran_r.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN penjamin_m p1 ON pendaftaran_t.penjamin_id = p1.penjamin_id
     LEFT JOIN penjamin_m p2 ON pasienadmisi_t.penjamin_id = p2.penjamin_id
     LEFT JOIN carabayar_m cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN carabayar_m cb2 ON p2.carabayar_id = cb2.carabayar_id
     LEFT JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount) AS total_tagihan,
            sum(pembayaran_t.total_tunai + pembayaran_t.total_nontunai - pembayaran_t.total_kembalian + pembayaran_t.penggunaan_uangmuka) AS personal_amount,
            sum(pembayaran_t.total_dijamin) AS payer_amount
           FROM pembayaran_t
             JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pembayaran_t.pembayaran_id = pembayaranpelayanan_t_1.pembayaran_id AND pembayaranpelayanan_t_1.is_deleted = false
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON pembayaran_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan
           FROM pembayaran_t) pembayaran_amount ON pembayaran_r.pembayaran_id = pembayaran_amount.pembayaran_id
  WHERE pembayaran_r.is_update = false
UNION ALL
 SELECT pembayaran_r.id,
    concat('RSPB', penjualanresep_t.penjualanresep_id) AS sync_id_api,
    penjualanresep_t.noresep AS name,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN '-'::character varying
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN NULL::timestamp without time zone
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    0 AS partner_id,
    penjualanresep_t.tglresep AS date_order,
    '1'::text AS patient_type,
    concat('PEN', penjualanresep_t.penjamin_id) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), '-'::character varying) AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 'draft'::text
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_tunai + pembayaran.total_nontunai - pembayaran.total_kembalian + pembayaran.penggunaan_uangmuka
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_dijamin
            ELSE 0::double precision
        END AS payer_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_tagihan
            ELSE 0::double precision
        END AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     JOIN ( SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.is_deleted,
            pembayaranpelayanan_t.pembayaran_id
           FROM pembayaranpelayanan_t
             JOIN ( SELECT max(pembayaranpelayanan_t_1.pembayaranpelayanan_id) AS pembayaranpelayanan_id,
                    pembayaranpelayanan_t_1.pendaftaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) max_pembayaran ON pembayaranpelayanan_t.pendaftaran_id = max_pembayaran.pendaftaran_id AND pembayaranpelayanan_t.pembayaranpelayanan_id = max_pembayaran.pembayaranpelayanan_id) pembayaranpelayanan ON pembayaran_r.pembayaran_id = pembayaranpelayanan.pembayaran_id
     JOIN penjualanresep_t ON pembayaranpelayanan.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.jenispenjualan::text = '343'::text
     JOIN ( SELECT penjualanresep_r_1.id,
            penjualanresep_r_1.penjualanresep_id
           FROM penjualanresep_r penjualanresep_r_1
             JOIN ( SELECT max(penjualanresep_r_2.id) AS id,
                    penjualanresep_r_2.penjualanresep_id
                   FROM penjualanresep_r penjualanresep_r_2
                  WHERE penjualanresep_r_2.keterangan::text = 'ACCRUAL'::text
                  GROUP BY penjualanresep_r_2.penjualanresep_id) max ON penjualanresep_r_1.id = max.id
          WHERE penjualanresep_r_1.is_sent = true) penjualanresep_r ON penjualanresep_t.penjualanresep_id = penjualanresep_r.penjualanresep_id
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.total_dijamin,
            pembayaran_t.penggunaan_uangmuka
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_kembalian, pembayaran_t.total_dijamin) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
  WHERE pembayaran_r.is_update = false;");

    $this->execute('ALTER TABLE "public"."int_saleorderupdate_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210413_054753_oddo_20210413_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210413_054753_oddo_20210413_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
