<?php

use yii\db\Migration;

/**
 * Class m210128_105205_migrate_20210128_penyesuaian
 */
class m210128_105205_migrate_20210128_penyesuaian extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_pembayaran_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_pembayaran_v\" AS  SELECT concat('BYR', pembayaran_r.id) AS sync_id_api,
        CASE
            WHEN pembayaran_r.keterangan::text = ANY (ARRAY['REFUND'::character varying::text, 'DISCOUNT CANCEL'::character varying::text]) THEN peg_deleted.nama_pegawai
            ELSE pegawai_m.nama_pegawai
        END AS user_name,
    fgetnamalookup(pembayaran_r.tipe_pembayaran) AS trans_type,
    concat(kasir.ruangan_nama, ' - ', pembayaranpelayanan_t.no_pembayaran) AS facility_name,
    pembayaran_r.tgl_proses AS tglproses,
    pembayaran_r.keterangan AS payment_name,
    pembayaran_r.nama_edc AS edc_machine,
        CASE
            WHEN pembayaran_r.total_tunai <> 0::double precision THEN pembayaran_r.total_tunai - pembayaran_r.total_kembalian
            WHEN pembayaran_r.total_nontunai <> 0::double precision THEN pembayaran_r.total_nontunai
            ELSE NULL::double precision
        END AS total_collect,
        CASE
            WHEN pembayaranpelayanan_t.pendaftaran_id IS NULL THEN penjualanresep_t.noresep
            ELSE pendaftaran_t.no_pendaftaran
        END AS note,
    pembayaran_r.pendaftaran_id AS admission_id,
    pendaftaran_t.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    pembayaran_r.keterangan,
    pembayaran_r.id,
    pembayaran_r.is_sent,
    pembayaran_r.is_sending,
    pembayaran_r.is_update,
    'PEMBAYARAN'::text AS tipe_rekap
   FROM pembayaran_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1
          WHERE pendaftaran_r_1.keterangan::text = 'INSERT'::text AND pendaftaran_r_1.is_sent = true) pendaftaran_r ON pembayaran_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     LEFT JOIN loginpemakai_k ON pembayaran_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN loginpemakai_k deleted_by ON pembayaran_r.deleted_by = deleted_by.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON deleted_by.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ruangan_m kasir ON pembayaranpelayanan_t.ruangan_id = kasir.ruangan_id
     LEFT JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
  WHERE pembayaran_r.total_dijamin <= 0::double precision
UNION ALL
 SELECT concat('UM', bayaruangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
        CASE
            WHEN bayaruangmuka_r.metode_pembayaran = 27 THEN 'CASH'::text
            ELSE 'BankTransfer'::text
        END AS trans_type,
    concat(ruangan_m.ruangan_nama, ' - ', bayaruangmuka_r.no_uangmuka) AS facility_name,
    bayaruangmuka_r.tgl_uangmuka AS tglproses,
    'DEPOSIT'::character varying AS payment_name,
    '-'::text AS edc_machine,
    bayaruangmuka_r.jumlah_uangmuka AS total_collect,
    pendaftaran_t.no_pendaftaran AS note,
    bayaruangmuka_r.pendaftaran_id AS admission_id,
    pendaftaran_t.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    bayaruangmuka_r.keterangan,
    bayaruangmuka_r.id,
    bayaruangmuka_r.is_sent_scr AS is_sent,
    bayaruangmuka_r.is_sending_scr AS is_sending,
    NULL::boolean AS is_update,
    'UANG_MUKA'::text AS tipe_rekap
   FROM bayaruangmuka_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1) pendaftaran_r ON bayaruangmuka_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     LEFT JOIN loginpemakai_k ON bayaruangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON bayaruangmuka_r.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pendaftaran_t ON bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
  WHERE bayaruangmuka_r.is_deleted = false
UNION ALL
 SELECT concat('PUM', pengembalianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
        CASE
            WHEN tandabuktikeluar_t.is_tunai IS TRUE THEN 'CASH'::text
            ELSE 'BankTransfer'::text
        END AS trans_type,
    concat(ruangan_m.ruangan_nama, ' - ', tandabuktikeluar_t.no_buktikeluar) AS facility_name,
    pengembalianuangmuka_r.tgl_pengembalian AS tglproses,
    'REFUND'::character varying AS payment_name,
    '-'::text AS edc_machine,
    pengembalianuangmuka_r.total_pengembalian AS total_collect,
    pendaftaran_r.no_pendaftaran AS note,
    pengembalianuangmuka_r.pendaftaran_id AS admission_id,
    pendaftaran_r.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    pengembalianuangmuka_r.keterangan,
    pengembalianuangmuka_r.id,
    pengembalianuangmuka_r.is_sent_scr AS is_sent,
    pengembalianuangmuka_r.is_sending_scr AS is_sending,
    NULL::boolean AS is_update,
    'PENGEMBALIAN_UANGMUKA'::text AS tipe_rekap
   FROM pengembalianuangmuka_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.*::pendaftaran_r AS pendaftaran_r_1,
            pendaftaran_r_1.no_pendaftaran,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1
          WHERE pendaftaran_r_1.keterangan::text = 'INSERT'::text) pendaftaran_r ON pengembalianuangmuka_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     LEFT JOIN loginpemakai_k ON pengembalianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     JOIN tandabuktikeluar_t ON pengembalianuangmuka_r.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id
     JOIN ruangan_m ON pengembalianuangmuka_r.ruangan_id = ruangan_m.ruangan_id
  WHERE pengembalianuangmuka_r.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_pembayaran_v" OWNER TO "postgres";');

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
    penerimaansupp_r.is_sent
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
    penerimaanobat_r.is_sent
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
    returpenerimaanobat_r.is_sent
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
    returpenerimaanobat_r.is_sent
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
    '6'::text AS sync_type
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
    '6'::text AS sync_type
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
    '6'::text AS sync_type
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
    '6'::text AS sync_type
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

        $this->execute('DROP VIEW if exists "public"."int_saleorderupdate_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_saleorderupdate_v\" AS  SELECT pembayaran_r.id,
    pendaftaran_t.pendaftaran_id AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS confirmation_date,
    pendaftaran_t.pasien_id AS partner_id,
    pendaftaran_t.tgl_pendaftaran AS date_order,
        CASE
            WHEN pendaftaran_t.instalasi_id = 1 THEN 1
            WHEN pendaftaran_t.instalasi_id = 3 THEN 2
            WHEN pendaftaran_t.instalasi_id = 2 THEN 3
            WHEN pendaftaran_t.instalasi_id = 6 THEN 6
            WHEN pendaftaran_t.instalasi_id = 21 THEN 5
            ELSE 4
        END AS patient_type,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(pendaftaran_t.penjamin_id, 0)
            ELSE COALESCE(pasienadmisi_r.penjamin_id, 0)
        END AS payer_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.s_kode, '-'::character varying)
            ELSE COALESCE(p2.s_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
    'done'::text AS state,
    pembayaran.total_tunai + pembayaran.total_nontunai - pembayaran.total_kembalian AS personal_amount,
    pembayaran.total_dijamin AS payer_amount,
    pembayaran.total_tagihan AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     LEFT JOIN pendaftaran_t ON pembayaran_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_r ON pendaftaran_t.pasienadmisi_id = pasienadmisi_r.pasienadmisi_id
     LEFT JOIN penjamin_m p1 ON pendaftaran_t.penjamin_id = p1.penjamin_id
     LEFT JOIN penjamin_m p2 ON pasienadmisi_r.penjamin_id = p2.penjamin_id
     LEFT JOIN carabayar_m cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN carabayar_m cb2 ON p2.carabayar_id = cb2.carabayar_id
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_kembalian, pembayaran_t.total_dijamin) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
  WHERE pembayaran_r.is_update = false;");

       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210128_105205_migrate_20210128_penyesuaian cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210128_105205_migrate_20210128_penyesuaian cannot be reverted.\n";

        return false;
    }
    */
}
