<?php

use yii\db\Migration;

/**
 * Class m210211_091049_oddo_20210211_penyesuaianview
 */
class m210211_091049_oddo_20210211_penyesuaianview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_uangmuka_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_uangmuka_v\" AS  SELECT concat('UM', bayaruangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
    bayaruangmuka_r.pendaftaran_id AS admission_id,
    bayaruangmuka_r.no_uangmuka AS trans_no,
    bayaruangmuka_r.tgl_uangmuka AS trans_date,
    'Deposit Collect'::text AS trans_type,
    bayaruangmuka_r.no_uangmuka AS reference_no,
    pendaftaran_t.no_pendaftaran AS admission_no,
    pasien_m.nama_pasien AS patient_name,
        CASE
            WHEN bayaruangmuka_r.metode_pembayaran = 27 THEN 'Cash'::text
            WHEN bayaruangmuka_r.metode_pembayaran = 28 THEN 'DebitCard'::text
            ELSE '-'::text
        END AS payment_name,
    bayaruangmuka_r.tgl_uangmuka AS tglproses,
    tandabuktibayar_t.no_rek AS edc_machine,
    bayaruangmuka_r.jumlah_uangmuka AS amount,
    bayaruangmuka_r.keterangan_uangmuka AS note,
    'draft'::text AS state,
    6 AS sync_type,
    bayaruangmuka_r.id,
    bayaruangmuka_r.is_sent,
    bayaruangmuka_r.is_sending,
    'UANG_MUKA'::text AS tipe_rekap,
        CASE
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = true THEN 'SUKSES'::text
            WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses
   FROM bayaruangmuka_r
     JOIN loginpemakai_k ON bayaruangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN tandabuktibayar_t ON bayaruangmuka_r.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
     JOIN pendaftaran_t ON bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE bayaruangmuka_r.is_deleted = false
UNION ALL
 SELECT concat('PUM', pengembalianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
    pengembalianuangmuka_r.pendaftaran_id AS admission_id,
    tandabuktikeluar_t.no_buktikeluar AS trans_no,
    pengembalianuangmuka_r.tgl_pengembalian AS trans_date,
    pengembalianuangmuka_r.keterangan AS trans_type,
    tandabuktikeluar_t.no_buktikeluar AS reference_no,
    pendaftaran_t.no_pendaftaran AS admission_no,
    pasien_m.nama_pasien AS patient_name,
        CASE
            WHEN tandabuktikeluar_t.is_tunai IS TRUE THEN 'Cash'::text
            WHEN tandabuktikeluar_t.is_tunai IS FALSE THEN 'DebitCard'::text
            ELSE '-'::text
        END AS payment_name,
    pengembalianuangmuka_r.tgl_pengembalian AS tglproses,
    tandabuktikeluar_t.no_rek AS edc_machine,
    pengembalianuangmuka_r.total_pengembalian AS amount,
    '-'::text AS note,
    'draft'::text AS state,
    6 AS sync_type,
    pengembalianuangmuka_r.id,
    pengembalianuangmuka_r.is_sent,
    pengembalianuangmuka_r.is_sending,
    'PENGEMBALIAN_UANGMUKA'::text AS tipe_rekap,
        CASE
            WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = true THEN 'SUKSES'::text
            WHEN pengembalianuangmuka_r.is_sending = false AND pengembalianuangmuka_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pengembalianuangmuka_r.is_sending = false AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM pengembalianuangmuka_r
     JOIN loginpemakai_k ON pengembalianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN tandabuktikeluar_t ON pengembalianuangmuka_r.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id
     JOIN pendaftaran_t ON pengembalianuangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
UNION ALL
 SELECT concat('PKUM', pemakaianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
    pemakaianuangmuka_r.pendaftaran_id AS admission_id,
    pembayaranpelayanan_t.no_pembayaran AS trans_no,
    pemakaianuangmuka_r.tgl_pemakaian AS trans_date,
    pemakaianuangmuka_r.keterangan AS trans_type,
    pembayaranpelayanan_t.no_pembayaran AS reference_no,
    pendaftaran_t.no_pendaftaran AS admission_no,
    pasien_m.nama_pasien AS patient_name,
    'Cash'::text AS payment_name,
    pemakaianuangmuka_r.tgl_proses AS tglproses,
    '-'::character varying AS edc_machine,
    pemakaianuangmuka_r.pemakaian_uangmuka AS amount,
    '-'::text AS note,
    'draft'::text AS state,
    6 AS sync_type,
    pemakaianuangmuka_r.id,
    pemakaianuangmuka_r.is_sent,
    pemakaianuangmuka_r.is_sending,
    'PEMAKAIAN_UANGMUKA'::text AS tipe_rekap,
        CASE
            WHEN pemakaianuangmuka_r.is_sending = true AND pemakaianuangmuka_r.is_sent = false AND pemakaianuangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemakaianuangmuka_r.is_sending = true AND pemakaianuangmuka_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemakaianuangmuka_r.is_sending = false AND pemakaianuangmuka_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemakaianuangmuka_r.is_sending = true AND pemakaianuangmuka_r.is_sent = false AND pemakaianuangmuka_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pemakaianuangmuka_r.is_sending = false AND pemakaianuangmuka_r.is_sent = false AND pemakaianuangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM pemakaianuangmuka_r
     JOIN loginpemakai_k ON pemakaianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     JOIN pembayaranpelayanan_t ON pemakaianuangmuka_r.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN pendaftaran_t ON pemakaianuangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id;");

        $this->execute('ALTER TABLE "public"."int_uangmuka_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_purchase_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_purchase_v\" AS  SELECT concat('POS', penerimaansupp_r.penerimaansupp_id) AS sync_id_api,
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
 SELECT concat('POM', penerimaanobat_r.penerimaanobat_id) AS sync_id_api,
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
  WHERE penerimaanobat_r.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_purchase_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_purchasedetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_purchasedetail_v\" AS  SELECT concat('POS', penerimaansuppdetail_r.id) AS sync_id_api,
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
 SELECT concat('POM', penerimaanobatdetail_r.id) AS sync_id_api,
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
          WHERE satuankonversi_m_1.is_deleted = false) qty_konversi ON penerimaanobatdetail_r.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id;");

        $this->execute('ALTER TABLE "public"."int_purchasedetail_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_purchasegrn_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_purchasegrn_v\" AS  SELECT concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS sync_id_api,
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
 SELECT concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS sync_id_api,
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
          GROUP BY returpenerimaanobatdetail_t.returpenerimaanobat_id, penerimaanobat_t.no_penerimaan, po.no_poobat, penerimaanobat_t.supplier_id, supplier_m.supplier_nama, po.pajak_persen) retur_detail ON returpenerimaanobat_r.returpenerimaanobat_id = retur_detail.returpenerimaanobat_id;");

        $this->execute('ALTER TABLE "public"."int_purchasegrn_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_purchasegrndetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_purchasegrndetail_v\" AS  SELECT concat('RPOS', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
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
 SELECT concat('RPOM', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
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
          WHERE satuankonversi_m.is_deleted = false) satuan ON penerimaanobatdetail.s_konversiobt_id = satuan.satuankonversi_id;");

        $this->execute('ALTER TABLE "public"."int_purchasegrndetail_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_stockoutdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockoutdetail_v\" AS  SELECT concat('PRSP', int_obatalkespasien_r.obatalkespasien_id) AS sync_id_api,
    concat('PRSP', int_obatalkespasien_r.penjualanresep_id) AS picking_id,
    concat('OBT', int_obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE
            WHEN int_obatalkespasien_r.det_konversi = 0::double precision THEN int_obatalkespasien_r.det_konversi
            WHEN int_obatalkespasien_r.det_konversi IS NULL THEN int_obatalkespasien_r.qty_konversi
            ELSE int_obatalkespasien_r.det_konversi
        END AS product_uom_qty,
    int_obatalkespasien_r.satuankecil_id AS product_uom_id,
    int_obatalkespasien_r.satuankecil_id AS product_uom,
    stokobat.nobatch AS lot_id,
    6 AS sync_type,
    int_obatalkespasien_r.id,
    int_obatalkespasien_r.is_sent,
    int_obatalkespasien_r.is_sending,
    int_obatalkespasien_r.sync_respon,
        CASE
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM int_obatalkespasien_r
     JOIN int_penjualanresep_r ON int_obatalkespasien_r.penjualanresep_id = int_penjualanresep_r.penjualanresep_id AND int_penjualanresep_r.is_sent = true
     JOIN obatalkes_m ON int_obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m ON int_obatalkespasien_r.satuankecil_id = satuanunit_m.satuanunit_id
     JOIN ( SELECT stokobatalkes_t.obatalkespasien_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.obatalkespasien_id, stokobatalkes_t.nobatch) stokobat ON int_obatalkespasien_r.obatalkespasien_id = stokobat.obatalkespasien_id
UNION ALL
 SELECT concat('PBHP', int_obatalkespasien_r.obatalkespasien_id) AS sync_id_api,
    concat('PBHP', int_obatalkespasien_r.pendaftaran_id) AS picking_id,
    concat('OBT', int_obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
    int_obatalkespasien_r.qty_oa AS product_uom_qty,
    int_obatalkespasien_r.satuankecil_id AS product_uom_id,
    int_obatalkespasien_r.satuankecil_id AS product_uom,
    stokobat.nobatch AS lot_id,
    6 AS sync_type,
    int_obatalkespasien_r.id,
    int_obatalkespasien_r.is_sent,
    int_obatalkespasien_r.is_sending,
    int_obatalkespasien_r.sync_respon,
        CASE
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM int_obatalkespasien_r
     JOIN int_pendaftaranbmhp_r ON int_obatalkespasien_r.pendaftaran_id = int_pendaftaranbmhp_r.pendaftaran_id AND int_pendaftaranbmhp_r.is_sent = true
     JOIN obatalkes_m ON int_obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m ON int_obatalkespasien_r.satuankecil_id = satuanunit_m.satuanunit_id
     JOIN ( SELECT stokobatalkes_t.obatalkespasien_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.obatalkespasien_id, stokobatalkes_t.nobatch) stokobat ON int_obatalkespasien_r.obatalkespasien_id = stokobat.obatalkespasien_id
UNION ALL
 SELECT concat('PRSP', int_obatalkespasien_r.obatalkespasien_id) AS sync_id_api,
    concat('PRSP', int_obatalkespasien_r.penjualanresep_id) AS picking_id,
    concat('OBT', int_obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE
            WHEN int_obatalkespasien_r.det_konversi = 0::double precision THEN int_obatalkespasien_r.det_konversi
            WHEN int_obatalkespasien_r.det_konversi IS NULL THEN int_obatalkespasien_r.qty_konversi
            ELSE int_obatalkespasien_r.det_konversi
        END AS product_uom_qty,
    int_obatalkespasien_r.satuankecil_id AS product_uom_id,
    int_obatalkespasien_r.satuankecil_id AS product_uom,
    stokobatalkes_t.nobatch AS lot_id,
    6 AS sync_type,
    int_obatalkespasien_r.id,
    int_obatalkespasien_r.is_sent,
    int_obatalkespasien_r.is_sending,
    int_obatalkespasien_r.sync_respon,
        CASE
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM int_obatalkespasien_r
     JOIN int_penjualanresep_r ON int_obatalkespasien_r.penjualanresep_id = int_penjualanresep_r.penjualanresep_id AND int_penjualanresep_r.is_sent = true
     JOIN penjualanresep_t ipr ON ipr.penjualanresep_id = int_obatalkespasien_r.penjualanresep_id
     JOIN stokobatalkes_t ON stokobatalkes_t.obatalkespasien_id = int_obatalkespasien_r.obatalkespasien_id
     JOIN obatalkes_m ON int_obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
  WHERE ipr.status_reseptur = 432 AND ipr.pembatalanresep_id IS NOT NULL AND int_obatalkespasien_r.racikan_id = 1;");

        $this->execute('ALTER TABLE "public"."int_stockoutdetail_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_stockreturn_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockreturn_v\" AS  SELECT concat('RTR', returresep_r.returresep_id) AS sync_id_api,
    concat(penjualanresep_t.noresep, '-', returresep_r.no_returresep) AS name,
    penjualanresep_t.pasien_id::character varying AS partner_id,
    'return'::text AS location_id,
    returresep_r.ruangan_id AS dest_location_id,
    'return'::text AS picking_type_id,
    returresep_r.tgl_retur AS date_move,
    NULL::text AS min_date,
    6 AS sync_type,
    returresep_r.is_sent,
    returresep_r.is_sending,
    returresep_r.sync_respon,
    returresep_r.id,
    'RETUR_RESEP'::text AS tipe_rekap,
        CASE
            WHEN returresep_r.is_sending = true AND returresep_r.is_sent = false AND returresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returresep_r.is_sending = true AND returresep_r.is_sent = true THEN 'SUKSES'::text
            WHEN returresep_r.is_sending = false AND returresep_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returresep_r.is_sending = true AND returresep_r.is_sent = false AND returresep_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN returresep_r.is_sending = false AND returresep_r.is_sent = false AND returresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM returresep_r
     JOIN penjualanresep_t ON returresep_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
UNION ALL
 SELECT concat('BTL', pembatalanresep_r.pembatalanresep_id) AS sync_id_api,
    concat(penjualanresep_t.noresep, '-', pembatalanresep_r.no_pembatalan) AS name,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN penjualanresep_t.pasien_id::character varying
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat('PEG', penjualanresep_t.karyawan_id)::character varying
            ELSE penjualanresep_t.nama_pembeli
        END AS partner_id,
    'return'::text AS location_id,
    penjualanresep_t.ruangan_id AS dest_location_id,
    'return'::text AS picking_type_id,
    pembatalanresep_r.tgl_pembatalan AS date_move,
    NULL::text AS min_date,
    6 AS sync_type,
    pembatalanresep_r.is_sent,
    pembatalanresep_r.is_sending,
    pembatalanresep_r.sync_respon,
    pembatalanresep_r.id,
    'BATAL_RESEP'::text AS tipe_rekap,
        CASE
            WHEN pembatalanresep_r.is_sending = true AND pembatalanresep_r.is_sent = false AND pembatalanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pembatalanresep_r.is_sending = true AND pembatalanresep_r.is_sent = true THEN 'SUKSES'::text
            WHEN pembatalanresep_r.is_sending = false AND pembatalanresep_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pembatalanresep_r.is_sending = true AND pembatalanresep_r.is_sent = false AND pembatalanresep_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pembatalanresep_r.is_sending = false AND pembatalanresep_r.is_sent = false AND pembatalanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM pembatalanresep_r
     JOIN penjualanresep_t ON pembatalanresep_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
  WHERE penjualanresep_t.status_reseptur = 660;");

        $this->execute('ALTER TABLE "public"."int_stockreturn_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_stockreturndetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockreturndetail_v\" AS  SELECT concat('RTR', returresepdetail_r.returresepdetail_id) AS sync_id_api,
    concat('RTR', returresepdetail_r.returresep_id) AS picking_id,
    concat('OBT', obatalkespasien_t.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE COALESCE(obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text, ''::text)
            WHEN ''::text THEN returresepdetail_r.qty_retur
            ELSE ((obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text)::double precision) * returresepdetail_r.qty_retur
        END AS product_uom_qty,
    obatalkespasien_t.satuankecil_id AS product_uom_id,
    obatalkespasien_t.satuankecil_id AS product_uom,
    NULL::text AS lot_id,
    6 AS sync_type,
    returresepdetail_r.id,
    returresepdetail_r.is_sent,
    returresepdetail_r.is_sending,
    returresepdetail_r.sync_respon,
    'RETUR_RESEP'::text AS tipe_rekap,
        CASE
            WHEN returresepdetail_r.is_sending = true AND returresepdetail_r.is_sent = false AND returresepdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returresepdetail_r.is_sending = true AND returresepdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN returresepdetail_r.is_sending = false AND returresepdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returresepdetail_r.is_sending = true AND returresepdetail_r.is_sent = false AND returresepdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses
   FROM returresepdetail_r
     JOIN returresep_r ON returresepdetail_r.returresep_id = returresep_r.returresep_id AND returresep_r.is_sent = true
     JOIN obatalkespasien_t ON returresepdetail_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
UNION ALL
 SELECT concat('BTL', pembatalanresepdetail_r.obatalkespasien_id) AS sync_id_api,
    concat('BTL', pembatalanresep_r.pembatalanresep_id) AS picking_id,
    concat('OBT', pembatalanresepdetail_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE
            WHEN pembatalanresepdetail_r.det_konversi = 0::double precision THEN pembatalanresepdetail_r.det_konversi
            WHEN pembatalanresepdetail_r.det_konversi IS NULL THEN pembatalanresepdetail_r.qty_konversi
            ELSE pembatalanresepdetail_r.det_konversi
        END AS product_uom_qty,
    pembatalanresepdetail_r.satuankecil_id AS product_uom_id,
    pembatalanresepdetail_r.satuankecil_id AS product_uom,
    NULL::text AS lot_id,
    6 AS sync_type,
    pembatalanresepdetail_r.id,
    pembatalanresepdetail_r.is_sent,
    pembatalanresepdetail_r.is_sending,
    pembatalanresepdetail_r.sync_respon,
    'BATAL_RESEP'::text AS tipe_rekap,
        CASE
            WHEN pembatalanresepdetail_r.is_sending = true AND pembatalanresepdetail_r.is_sent = false AND pembatalanresepdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pembatalanresepdetail_r.is_sending = true AND pembatalanresepdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pembatalanresepdetail_r.is_sending = false AND pembatalanresepdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pembatalanresepdetail_r.is_sending = true AND pembatalanresepdetail_r.is_sent = false AND pembatalanresepdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pembatalanresepdetail_r.is_sending = false AND pembatalanresepdetail_r.is_sent = false AND pembatalanresepdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM pembatalanresepdetail_r
     JOIN penjualanresep_t ON pembatalanresepdetail_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN pembatalanresep_r ON penjualanresep_t.penjualanresep_id = pembatalanresep_r.penjualanresep_id AND pembatalanresep_r.is_sent = true
     JOIN obatalkes_m ON pembatalanresepdetail_r.obatalkes_id = obatalkes_m.obatalkes_id;");

        $this->execute('DROP VIEW if exists "public"."int_stockscrap_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockscrap_v\" AS  SELECT 'adj_keluar'::text AS tipe_rekap,
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
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
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
        END AS status_proses
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
 SELECT 'adj_masuk'::text AS tipe_rekap,
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
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
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
        END AS status_proses
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
 SELECT 'pemusnahan_obat'::text AS tipe_rekap,
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
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
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
        END AS status_proses
   FROM pemusnahanobatdetail_r
     JOIN pemusnahanobat_t ON pemusnahanobatdetail_r.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id
     JOIN obatalkes_m ON pemusnahanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.pemusnahanobatdetail_id
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.pemusnahanobatdetail_id) stok ON pemusnahanobatdetail_r.pemusnahanobatdetail_id = stok.pemusnahanobatdetail_id
UNION ALL
 SELECT 'pemakaian_obat'::text AS tipe_rekap,
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
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
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
        END AS status_proses
   FROM pemakaianobatdetail_r
     JOIN pemakaianobat_t ON pemakaianobatdetail_r.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
     JOIN obatalkes_m ON pemakaianobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.pemakaianobatdetail_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.pemakaianobatdetail_id, stokobatalkes_t.nobatch) stok ON pemakaianobatdetail_r.pemakaianobatdetail_id = stok.pemakaianobatdetail_id;");

        $this->execute('ALTER TABLE "public"."int_stockscrap_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."saleorder_v";');

        $this->execute("
            CREATE VIEW \"public\".\"saleorder_v\" AS  SELECT pendaftaran_r.id,
    pendaftaran_r.pendaftaran_id::character varying AS sync_id_api,
    pendaftaran_r.no_pendaftaran AS name,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, '-'::character varying) AS billno,
        CASE
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NULL THEN pendaftaran_r.tgl_pendaftaran
            ELSE pembayaranpelayanan_t.tgl_pembayaran
        END AS confirmation_date,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.tgl_pendaftaran AS date_order,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 1
            WHEN pendaftaran_r.instalasi_id = 1 THEN 1
            WHEN pendaftaran_r.instalasi_id = 3 THEN 2
            WHEN pendaftaran_r.instalasi_id = 2 THEN 3
            WHEN pendaftaran_r.instalasi_id = 6 THEN 6
            WHEN pendaftaran_r.instalasi_id = 21 THEN 5
            ELSE 4
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
            WHEN pendaftaran_r.keterangan::text = 'UPDATE'::text THEN 'done'::text
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
        END AS status_proses
   FROM pendaftaran_r
     LEFT JOIN pasienadmisi_r ON pendaftaran_r.pasienadmisi_id = pasienadmisi_r.pasienadmisi_id
     LEFT JOIN penjamin_m p1 ON pendaftaran_r.penjamin_id = p1.penjamin_id
     LEFT JOIN penjamin_m p2 ON pasienadmisi_r.penjamin_id = p2.penjamin_id
     LEFT JOIN carabayar_m cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN carabayar_m cb2 ON p2.carabayar_id = cb2.carabayar_id
     LEFT JOIN asuransipasien_m ON pendaftaran_r.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_r.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
UNION ALL
 SELECT penjualanresep_r.id,
    concat('RSPB', penjualanresep_r.penjualanresep_id) AS sync_id_api,
    penjualanresep_r.noresep AS name,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, '-'::character varying) AS billno,
        CASE
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NULL THEN penjualanresep_r.tglresep
            ELSE pembayaranpelayanan_t.tgl_pembayaran
        END AS confirmation_date,
    0 AS partner_id,
    penjualanresep_r.tglresep AS date_order,
    1 AS patient_type,
    concat('PEN', penjualanresep_r.penjamin_id) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), '-'::character varying) AS payer_type,
    6 AS sync_type,
        CASE
            WHEN penjualanresep_r.keterangan::text = 'UPDATE'::text THEN 'done'::text
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
        END AS status_proses
   FROM penjualanresep_r
     LEFT JOIN penjamin_m ON penjualanresep_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaranpelayanan_t ON penjualanresep_r.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id AND pembayaranpelayanan_t.is_deleted = false
  WHERE penjualanresep_r.jenispenjualan::text = '343'::text;");

        $this->execute('ALTER TABLE "public"."saleorder_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_obat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_obat_v\" AS  SELECT concat('OBT', obatalkes_r.obatalkes_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    true AS purchase_ok,
    obatalkes_r.obatalkes_nama AS name,
    concat('OBT', obatalkes_r.jenisobatalkes_id) AS categ_id,
    COALESCE(obatalkes_r.satuanbesar_id, obatalkes_r.satuankecil_id) AS uom_po_id,
    COALESCE(obatalkes_r.satuanbesar_id, obatalkes_r.satuankecil_id) AS uom2_id,
    obatalkes_r.satuankecil_id AS uom_id,
    obatalkes_r.obatalkes_kode AS default_code,
    'product'::text AS type,
        CASE
            WHEN obatalkes_r.is_active IS TRUE AND obatalkes_r.is_deleted IS TRUE THEN true
            WHEN obatalkes_r.is_active IS TRUE AND obatalkes_r.is_deleted IS FALSE THEN false
            WHEN obatalkes_r.is_active IS FALSE AND obatalkes_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    obatalkes_r.strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
        CASE
            WHEN obatalkes_r.satuanbesar_id IS NULL THEN 1
            ELSE obatalkes_r.kemasan_besar
        END AS conversion_rate,
    6 AS sync_type,
    obatalkes_r.keterangan_rekap,
    obatalkes_r.id,
    obatalkes_r.is_sent,
    obatalkes_r.is_sending,
        CASE
            WHEN obatalkes_r.is_sending = true AND obatalkes_r.is_sent = false AND obatalkes_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN obatalkes_r.is_sending = true AND obatalkes_r.is_sent = true THEN 'SUKSES'::text
            WHEN obatalkes_r.is_sending = false AND obatalkes_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN obatalkes_r.is_sending = true AND obatalkes_r.is_sent = false AND obatalkes_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN obatalkes_r.is_sending = false AND obatalkes_r.is_sent = false AND obatalkes_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM obatalkes_r;");

        $this->execute('ALTER TABLE "public"."int_obat_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_pasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_pasien_v\" AS  SELECT pasien_r.id,
    pasien_r.pasien_id AS sync_id_api,
    pasien_r.no_rekam_medik AS registration_code,
    pasien_r.nama_pasien AS name,
    pasien_r.nama_pasien AS display_name,
    lower(fgetnamalookup(pasien_r.namadepan::integer)::text) AS title_name,
    COALESCE(pasien_r.tanggal_lahir, '1000-01-01'::date) AS date_of_birth,
    lower(fgetvaluelookup(pasien_r.jeniskelamin::integer)::text) AS gender,
    COALESCE(pasien_r.no_telepon_pasien, '-'::character varying) AS phone,
    COALESCE(pasien_r.no_mobile_pasien, '-'::character varying) AS mobile,
    '-'::text AS fax,
    COALESCE(pasien_r.alamatemail, '-'::character varying) AS email,
    COALESCE(pasien_r.alamat_sekarang, '-'::text) AS street,
    '-'::text AS street2,
    '-'::text AS street3,
    COALESCE(pasien_r.alamat_pasien, '-'::text) AS city,
    '-'::text AS zip,
        CASE
            WHEN pasien_r.jenisidentitas::text = '99'::text THEN pasien_r.no_identitas_pasien
            ELSE '-'::character varying
        END AS passport,
        CASE
            WHEN pasien_r.jenisidentitas::text = '94'::text THEN pasien_r.no_identitas_pasien
            ELSE '-'::character varying
        END AS ktp,
        CASE
            WHEN pasien_r.is_active IS TRUE AND pasien_r.is_deleted IS TRUE THEN true
            WHEN pasien_r.is_active IS TRUE AND pasien_r.is_deleted IS FALSE THEN false
            WHEN pasien_r.is_active IS FALSE AND pasien_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    true AS patient,
    true AS active,
    6 AS sync_type,
    pasien_r.keterangan,
    pasien_r.tgl_proses,
    pasien_r.is_sent,
    pasien_r.is_sending,
    pasien_r.pasien_id,
    pasien_r.additional_data,
    COALESCE(pasien_m.additional_pasien, '-'::character varying::text) AS no_identitas,
        CASE
            WHEN pasien_r.is_sending = true AND pasien_r.is_sent = false AND pasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pasien_r.is_sending = true AND pasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN pasien_r.is_sending = false AND pasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pasien_r.is_sending = true AND pasien_r.is_sent = false AND pasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pasien_r.is_sending = false AND pasien_r.is_sent = false AND pasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM pasien_r
     JOIN pasien_m ON pasien_r.pasien_id = pasien_m.pasien_id;");

        $this->execute('ALTER TABLE "public"."int_pasien_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_satuanunit_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_satuanunit_v\" AS  SELECT satuanunit_r.satuanunit_id AS sync_id_api,
    satuanunit_r.satuanunit_nama AS name,
    satuanunit_r.satuanunit_namalain AS code,
    true AS active,
    1 AS factor,
    'reference'::text AS uom_type,
    1 AS category_id,
    6 AS sync_type,
    satuanunit_r.keterangan_rekap,
    satuanunit_r.id,
    satuanunit_r.is_sent,
    satuanunit_r.is_sending,
        CASE
            WHEN satuanunit_r.is_active IS TRUE AND satuanunit_r.is_deleted IS TRUE THEN true
            WHEN satuanunit_r.is_active IS TRUE AND satuanunit_r.is_deleted IS FALSE THEN false
            WHEN satuanunit_r.is_active IS FALSE AND satuanunit_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
        CASE
            WHEN satuanunit_r.is_sending = true AND satuanunit_r.is_sent = false AND satuanunit_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN satuanunit_r.is_sending = true AND satuanunit_r.is_sent = true THEN 'SUKSES'::text
            WHEN satuanunit_r.is_sending = false AND satuanunit_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN satuanunit_r.is_sending = true AND satuanunit_r.is_sent = false AND satuanunit_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN satuanunit_r.is_sending = false AND satuanunit_r.is_sent = false AND satuanunit_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM satuanunit_r;");

        $this->execute('ALTER TABLE "public"."int_satuanunit_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_supplier_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_supplier_v\" AS  SELECT concat('SUP', supplier_r.supplier_id) AS sync_id_api,
    supplier_r.supplier_kode AS vendor_code,
    supplier_r.supplier_nama AS name,
    supplier_r.supplier_nama AS display_name,
    'CORPORATE'::text AS customer_type_api,
    supplier_r.no_tlp AS contact_person,
    supplier_r.no_tlp AS phone,
    supplier_r.no_tlp AS mobile,
    supplier_r.no_fax AS fax,
    supplier_r.email,
    supplier_r.website,
    supplier_r.supplier_alamat AS street,
    supplier_r.supplier_alamat AS street2,
    supplier_r.supplier_alamat AS street3,
    propinsi_m.propinsi_nama AS city,
    NULL::text AS zip,
    supplier_r.credit_limit AS credit_days,
    NULL::text AS opdiscountid,
    NULL::text AS ipdiscountid,
    NULL::text AS astaxid,
        CASE
            WHEN supplier_r.is_active IS TRUE AND supplier_r.is_deleted IS TRUE THEN true
            WHEN supplier_r.is_active IS TRUE AND supplier_r.is_deleted IS FALSE THEN false
            WHEN supplier_r.is_active IS FALSE AND supplier_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    true AS insruance,
    true AS customer,
    true AS active,
    6 AS sync_type,
    supplier_r.keterangan_rekap,
    supplier_r.id,
    supplier_r.is_sent,
    supplier_r.is_sending,
        CASE
            WHEN supplier_r.is_sending = true AND supplier_r.is_sent = false AND supplier_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN supplier_r.is_sending = true AND supplier_r.is_sent = true THEN 'SUKSES'::text
            WHEN supplier_r.is_sending = false AND supplier_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN supplier_r.is_sending = true AND supplier_r.is_sent = false AND supplier_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN supplier_r.is_sending = false AND supplier_r.is_sent = false AND supplier_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
   FROM supplier_r
     LEFT JOIN propinsi_m ON supplier_r.propinsi_id = propinsi_m.propinsi_id;");

        $this->execute('ALTER TABLE "public"."int_supplier_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_stockout_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockout_v\" AS  SELECT concat('PBHP', int_pendaftaranbmhp_r.pendaftaran_id) AS sync_id_api,
    int_pendaftaranbmhp_r.no_pendaftaran AS name,
    int_pendaftaranbmhp_r.pasien_id::character varying AS partner_id,
    obatalkespasien_t.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS picking_type_id,
    obatalkespasien_t.tglpelayanan AS date_move,
    obatalkespasien_t.tglpelayanan AS min_date,
    6 AS sync_type,
    int_pendaftaranbmhp_r.is_sent,
    int_pendaftaranbmhp_r.is_sending,
    int_pendaftaranbmhp_r.sync_respon,
    int_pendaftaranbmhp_r.id,
    'BMHP'::text AS tipe_rekap,
        CASE
            WHEN int_pendaftaranbmhp_r.is_sending = true AND int_pendaftaranbmhp_r.is_sent = false AND int_pendaftaranbmhp_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_pendaftaranbmhp_r.is_sending = true AND int_pendaftaranbmhp_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_pendaftaranbmhp_r.is_sending = false AND int_pendaftaranbmhp_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_pendaftaranbmhp_r.is_sending = true AND int_pendaftaranbmhp_r.is_sent = false AND int_pendaftaranbmhp_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_pendaftaranbmhp_r.is_sending = false AND int_pendaftaranbmhp_r.is_sent = false AND int_pendaftaranbmhp_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    obatalkespasien_t.tglpelayanan AS tanggal_transaksi,
    ruangan_m.ruangan_nama AS location_name,
    pasien_m.nama_pasien AS partner_name
   FROM int_pendaftaranbmhp_r
     LEFT JOIN pasien_m ON pasien_m.pasien_id = int_pendaftaranbmhp_r.pasien_id
     JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
            obatalkespasien_t_1.ruangan_id,
            obatalkespasien_t_1.tglpelayanan
           FROM obatalkespasien_t obatalkespasien_t_1
          WHERE obatalkespasien_t_1.is_deleted = false AND obatalkespasien_t_1.status_bmhp = 680
          GROUP BY obatalkespasien_t_1.pendaftaran_id, obatalkespasien_t_1.ruangan_id, obatalkespasien_t_1.tglpelayanan) obatalkespasien_t ON int_pendaftaranbmhp_r.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = obatalkespasien_t.ruangan_id
UNION ALL
 SELECT concat('PRSP', int_penjualanresep_r.penjualanresep_id) AS sync_id_api,
        CASE
            WHEN int_penjualanresep_r.nama_pembeli IS NOT NULL THEN concat(int_penjualanresep_r.noresep, '-', int_penjualanresep_r.nama_pembeli)::character varying
            ELSE int_penjualanresep_r.noresep
        END AS name,
        CASE
            WHEN int_penjualanresep_r.pasien_id IS NOT NULL THEN int_penjualanresep_r.pasien_id::character varying
            WHEN int_penjualanresep_r.karyawan_id IS NOT NULL THEN concat('PEG', int_penjualanresep_r.karyawan_id)::character varying
            ELSE NULL::character varying
        END AS partner_id,
    int_penjualanresep_r.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS picking_type_id,
    int_penjualanresep_r.tglresep AS date_move,
    int_penjualanresep_r.tglresep AS min_date,
    6 AS sync_type,
    int_penjualanresep_r.is_sent,
    int_penjualanresep_r.is_sending,
    int_penjualanresep_r.sync_respon,
    int_penjualanresep_r.id,
    'RESEP'::text AS tipe_rekap,
        CASE
            WHEN int_penjualanresep_r.is_sending = true AND int_penjualanresep_r.is_sent = false AND int_penjualanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_penjualanresep_r.is_sending = true AND int_penjualanresep_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_penjualanresep_r.is_sending = false AND int_penjualanresep_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_penjualanresep_r.is_sending = true AND int_penjualanresep_r.is_sent = false AND int_penjualanresep_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_penjualanresep_r.is_sending = false AND int_penjualanresep_r.is_sent = false AND int_penjualanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_penjualanresep_r.tglresep AS tanggal_transaksi,
    ruangan_m.ruangan_nama AS location_name,
        CASE
            WHEN int_penjualanresep_r.pasien_id IS NOT NULL THEN pasien_m.nama_pasien
            WHEN int_penjualanresep_r.karyawan_id IS NOT NULL THEN pegawai_m.nama_pegawai
            ELSE NULL::character varying
        END AS partner_name
   FROM int_penjualanresep_r
     LEFT JOIN pasien_m ON pasien_m.pasien_id = int_penjualanresep_r.pasien_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = int_penjualanresep_r.karyawan_id
     LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = int_penjualanresep_r.ruangan_id
     JOIN ( SELECT penjualanresep_t.penjualanresep_id
           FROM penjualanresep_t
          WHERE penjualanresep_t.status_reseptur = 660) penjualan_resep ON int_penjualanresep_r.penjualanresep_id = penjualan_resep.penjualanresep_id
UNION ALL
 SELECT concat('PRSP', int_penjualanresep_r.penjualanresep_id) AS sync_id_api,
        CASE
            WHEN int_penjualanresep_r.nama_pembeli IS NOT NULL THEN concat(int_penjualanresep_r.noresep, '-', int_penjualanresep_r.nama_pembeli)::character varying
            ELSE int_penjualanresep_r.noresep
        END AS name,
        CASE
            WHEN int_penjualanresep_r.pasien_id IS NOT NULL THEN int_penjualanresep_r.pasien_id::character varying
            WHEN int_penjualanresep_r.karyawan_id IS NOT NULL THEN concat('PEG', int_penjualanresep_r.karyawan_id)::character varying
            ELSE NULL::character varying
        END AS partner_id,
    int_penjualanresep_r.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS picking_type_id,
    int_penjualanresep_r.tglresep AS date_move,
    int_penjualanresep_r.tglresep AS min_date,
    6 AS sync_type,
    int_penjualanresep_r.is_sent,
    int_penjualanresep_r.is_sending,
    int_penjualanresep_r.sync_respon,
    int_penjualanresep_r.id,
    'RESEP_RACIKAN'::text AS tipe_rekap,
        CASE
            WHEN int_penjualanresep_r.is_sending = true AND int_penjualanresep_r.is_sent = false AND int_penjualanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_penjualanresep_r.is_sending = true AND int_penjualanresep_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_penjualanresep_r.is_sending = false AND int_penjualanresep_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_penjualanresep_r.is_sending = true AND int_penjualanresep_r.is_sent = false AND int_penjualanresep_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_penjualanresep_r.is_sending = false AND int_penjualanresep_r.is_sent = false AND int_penjualanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_penjualanresep_r.tglresep AS tanggal_transaksi,
    ruangan_m.ruangan_nama AS location_name,
        CASE
            WHEN int_penjualanresep_r.pasien_id IS NOT NULL THEN pasien_m.nama_pasien
            WHEN int_penjualanresep_r.karyawan_id IS NOT NULL THEN pegawai_m.nama_pegawai
            ELSE NULL::character varying
        END AS partner_name
   FROM int_penjualanresep_r
     LEFT JOIN pasien_m ON pasien_m.pasien_id = int_penjualanresep_r.pasien_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = int_penjualanresep_r.karyawan_id
     LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = int_penjualanresep_r.ruangan_id
     JOIN ( SELECT penjualanresep_t.penjualanresep_id
           FROM penjualanresep_t
          WHERE penjualanresep_t.status_reseptur = 432 AND penjualanresep_t.pembatalanresep_id IS NOT NULL) penjualan_resep ON int_penjualanresep_r.penjualanresep_id = penjualan_resep.penjualanresep_id
  WHERE (EXISTS ( SELECT 1
           FROM stokobatalkes_t
             LEFT JOIN int_obatalkespasien_r ON int_obatalkespasien_r.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
          WHERE int_obatalkespasien_r.penjualanresep_id = int_penjualanresep_r.penjualanresep_id));");

        $this->execute('ALTER TABLE "public"."int_stockout_v" OWNER TO "postgres";');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210211_091049_oddo_20210211_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210211_091049_oddo_20210211_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
