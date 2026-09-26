<?php

use yii\db\Migration;

/**
 * Class m220310_035950_migrate_oddo_int_pembayaran_v
 */
class m220310_035950_migrate_oddo_int_pembayaran_v extends Migration
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
        CASE
            WHEN pembayaranpiutang_t.pembayaranpiutang_id IS NULL THEN concat(kasir.ruangan_nama, ' - ', pembayaran.no_pembayaran)
            ELSE concat(kasir.ruangan_nama, ' - ', pembayaranpiutang_t.no_pembayaranpiutang)
        END AS facility_name,
    pembayaran_r.tgl_proses AS tglproses,
    pembayaran_r.keterangan AS payment_name,
    pembayaran_r.nama_edc AS edc_machine,
        CASE
            WHEN pembayaran_r.total_tunai <> 0::double precision THEN pembayaran_r.total_tunai - pembayaran_r.total_kembalian
            WHEN pembayaran_r.total_nontunai <> 0::double precision THEN pembayaran_r.total_nontunai
            ELSE NULL::double precision
        END AS total_collect,
    pendaftaran_r.no_pendaftaran AS note,
    pembayaran_r.pendaftaran_id::character varying AS admission_id,
    pendaftaran_r.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    pembayaran_r.keterangan,
    pembayaran_r.id,
    pembayaran_r.is_sent,
    pembayaran_r.is_sending,
    pembayaran_r.is_update,
    'PEMBAYARAN'::text AS tipe_rekap,
        CASE
            WHEN pembayaran_r.is_sending = true AND pembayaran_r.is_sent = false AND pembayaran_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pembayaran_r.is_sending = true AND pembayaran_r.is_sent = true THEN 'SUKSES'::text
            WHEN pembayaran_r.is_sending = false AND pembayaran_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pembayaran_r.is_sending = true AND pembayaran_r.is_sent = false AND pembayaran_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pembayaran_r.is_sending = false AND pembayaran_r.is_sent = false AND pembayaran_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM pembayaran_r
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran
           FROM pendaftaran_r a
          WHERE a.keterangan::text = 'INSERT'::text AND a.is_sent = true) pendaftaran_r ON pembayaran_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT a.pembayaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.ruangan_id
           FROM pembayaran_t a
             JOIN ( SELECT a1.pembayaran_id,
                    a1.ruangan_id,
                    a1.no_pembayaran
                   FROM pembayaranpelayanan_t a1
                  GROUP BY a1.pembayaran_id, a1.ruangan_id, a1.no_pembayaran) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON pembayaran_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) deleted_by ON pembayaran_r.deleted_by = deleted_by.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_deleted ON deleted_by.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) kasir ON pembayaran.ruangan_id = kasir.ruangan_id
     LEFT JOIN ( SELECT a.id,
            a.pembayaran_id,
            a.is_sent
           FROM int_billing_r a) int_billing_r ON pembayaran_r.pembayaran_id = int_billing_r.pembayaran_id
     LEFT JOIN pemberianpiutang_t ON pembayaran_r.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
     LEFT JOIN pembayaranpiutang_t ON pemberianpiutang_t.pemberianpiutang_id = pembayaranpiutang_t.pemberianpiutang_id
  WHERE (pembayaran_r.total_tunai <> 0::double precision OR pembayaran_r.total_nontunai <> 0::double precision) AND pembayaran_r.is_update = true
UNION ALL
 SELECT concat('BYR', pembayaran_r.id) AS sync_id_api,
        CASE
            WHEN pembayaran_r.keterangan::text = ANY (ARRAY['REFUND'::character varying::text, 'DISCOUNT CANCEL'::character varying::text]) THEN peg_deleted.nama_pegawai
            ELSE pegawai_m.nama_pegawai
        END AS user_name,
    fgetnamalookup(pembayaran_r.tipe_pembayaran) AS trans_type,
    concat(kasir.ruangan_nama, ' - ', pembayaran.no_pembayaran) AS facility_name,
    pembayaran_r.tgl_proses AS tglproses,
    pembayaran_r.keterangan AS payment_name,
    pembayaran_r.nama_edc AS edc_machine,
        CASE
            WHEN pembayaran_r.total_tunai <> 0::double precision THEN pembayaran_r.total_tunai - pembayaran_r.total_kembalian
            WHEN pembayaran_r.total_nontunai <> 0::double precision THEN pembayaran_r.total_nontunai
            ELSE NULL::double precision
        END AS total_collect,
    penjualanresep_r.noresep AS note,
    concat('RSPB', penjualanresep_r.penjualanresep_id) AS admission_id,
    penjualanresep_r.noresep AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    pembayaran_r.keterangan,
    pembayaran_r.id,
    pembayaran_r.is_sent,
    pembayaran_r.is_sending,
    pembayaran_r.is_update,
    'PEMBAYARAN'::text AS tipe_rekap,
        CASE
            WHEN pembayaran_r.is_sending = true AND pembayaran_r.is_sent = false AND pembayaran_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pembayaran_r.is_sending = true AND pembayaran_r.is_sent = true THEN 'SUKSES'::text
            WHEN pembayaran_r.is_sending = false AND pembayaran_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pembayaran_r.is_sending = true AND pembayaran_r.is_sent = false AND pembayaran_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pembayaran_r.is_sending = false AND pembayaran_r.is_sent = false AND pembayaran_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM pembayaran_r
     JOIN ( SELECT b.pembayaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaranpelayanan_t.ruangan_id
           FROM pembayaran_t b
             JOIN ( SELECT b1.pembayaran_id,
                    b1.penjualanresep_id,
                    b1.ruangan_id,
                    b1.no_pembayaran
                   FROM pembayaranpelayanan_t b1
                  GROUP BY b1.pembayaran_id, b1.penjualanresep_id, b1.ruangan_id, b1.no_pembayaran) pembayaranpelayanan_t ON b.pembayaran_id = pembayaranpelayanan_t.pembayaran_id) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
     JOIN ( SELECT b.penjualanresep_id,
            b.noresep
           FROM penjualanresep_r b
          WHERE (b.jenispenjualan::text = ANY (ARRAY['343'::text, '345'::text])) AND b.keterangan::text = 'INSERT'::text AND b.is_sent = true) penjualanresep_r ON pembayaran.penjualanresep_id = penjualanresep_r.penjualanresep_id
     LEFT JOIN ( SELECT b.loginpemakai_id,
            b.pegawai_id
           FROM loginpemakai_k b) loginpemakai_k ON pembayaran_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT b.loginpemakai_id,
            b.pegawai_id
           FROM loginpemakai_k b) deleted_by ON pembayaran_r.deleted_by = deleted_by.loginpemakai_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) peg_deleted ON deleted_by.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama
           FROM ruangan_m b) kasir ON pembayaran.ruangan_id = kasir.ruangan_id
     LEFT JOIN ( SELECT b.id,
            b.pembayaran_id,
            b.is_sent
           FROM int_billing_r b) int_billing_r ON pembayaran_r.pembayaran_id = int_billing_r.pembayaran_id
  WHERE (pembayaran_r.total_tunai <> 0::double precision OR pembayaran_r.total_nontunai <> 0::double precision) AND pembayaran_r.is_update = true
UNION ALL
 SELECT concat('UM', bayaruangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
        CASE
            WHEN bayaruangmuka_r.metode_pembayaran = 27 THEN 'Cash'::text
            ELSE 'DebitCard'::text
        END AS trans_type,
    concat(ruangan_m.ruangan_nama, ' - ', bayaruangmuka_r.no_uangmuka) AS facility_name,
    bayaruangmuka_r.tgl_uangmuka AS tglproses,
    'DEPOSIT'::character varying AS payment_name,
    concat(jenisnontunai_m.nama, ' - ', tandabuktibayar_t.no_rek) AS edc_machine,
    bayaruangmuka_r.jumlah_uangmuka AS total_collect,
    pendaftaran_t.no_pendaftaran AS note,
    bayaruangmuka_r.pendaftaran_id::character varying AS admission_id,
    pendaftaran_t.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    bayaruangmuka_r.keterangan,
    bayaruangmuka_r.id,
    bayaruangmuka_r.is_sent_scr AS is_sent,
    bayaruangmuka_r.is_sending_scr AS is_sending,
    NULL::boolean AS is_update,
    'UANG_MUKA'::text AS tipe_rekap,
        CASE
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = true THEN 'SUKSES'::text
            WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    NULL::text AS billing_id,
    NULL::boolean AS is_sent_billing
   FROM bayaruangmuka_r
     LEFT JOIN ( SELECT c.loginpemakai_id,
            c.pegawai_id
           FROM loginpemakai_k c) loginpemakai_k ON bayaruangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama
           FROM ruangan_m c) ruangan_m ON bayaruangmuka_r.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT c.pendaftaran_id,
            c.no_pendaftaran
           FROM pendaftaran_t c) pendaftaran_t ON bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT c.bayaruangmuka_id,
            c.no_rek
           FROM tandabuktibayar_t c) tandabuktibayar_t ON bayaruangmuka_r.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
     LEFT JOIN ( SELECT c.jenisnontunai_id,
            c.nama
           FROM jenisnontunai_m c) jenisnontunai_m ON bayaruangmuka_r.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
  WHERE bayaruangmuka_r.is_deleted = false
UNION ALL
 SELECT concat('PUM', pengembalianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
        CASE
            WHEN tandabuktikeluar_t.is_tunai IS TRUE THEN 'Cash'::text
            ELSE 'DebitCard'::text
        END AS trans_type,
    concat(ruangan_m.ruangan_nama, ' - ', tandabuktikeluar_t.no_buktikeluar) AS facility_name,
    pengembalianuangmuka_r.tgl_pengembalian AS tglproses,
    'REFUND'::character varying AS payment_name,
    '-'::text AS edc_machine,
    pengembalianuangmuka_r.total_pengembalian AS total_collect,
    pendaftaran_r.no_pendaftaran AS note,
    pengembalianuangmuka_r.pendaftaran_id::character varying AS admission_id,
    pendaftaran_r.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    pengembalianuangmuka_r.keterangan,
    pengembalianuangmuka_r.id,
    pengembalianuangmuka_r.is_sent_scr AS is_sent,
    pengembalianuangmuka_r.is_sending_scr AS is_sending,
    NULL::boolean AS is_update,
    'PENGEMBALIAN_UANGMUKA'::text AS tipe_rekap,
        CASE
            WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = true THEN 'SUKSES'::text
            WHEN pengembalianuangmuka_r.is_sending = false AND pengembalianuangmuka_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pengembalianuangmuka_r.is_sending = true AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pengembalianuangmuka_r.is_sending = false AND pengembalianuangmuka_r.is_sent = false AND pengembalianuangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    NULL::text AS billing_id,
    NULL::boolean AS is_sent_billing
   FROM pengembalianuangmuka_r
     LEFT JOIN ( SELECT d.pendaftaran_id,
            d.no_pendaftaran,
            d.pegawai_id
           FROM pendaftaran_r d
          WHERE d.keterangan::text = 'INSERT'::text) pendaftaran_r ON pengembalianuangmuka_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     LEFT JOIN ( SELECT d.loginpemakai_id,
            d.pegawai_id
           FROM loginpemakai_k d) loginpemakai_k ON pengembalianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT d.pegawai_id,
            d.nama_pegawai
           FROM pegawai_m d) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT d.pengembalianuangmuka_id,
            d.is_tunai,
            d.no_buktikeluar
           FROM tandabuktikeluar_t d) tandabuktikeluar_t ON pengembalianuangmuka_r.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id
     JOIN ( SELECT d.ruangan_id,
            d.ruangan_nama
           FROM ruangan_m d) ruangan_m ON pengembalianuangmuka_r.ruangan_id = ruangan_m.ruangan_id
  WHERE pengembalianuangmuka_r.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220310_035950_migrate_oddo_int_pembayaran_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220310_035950_migrate_oddo_int_pembayaran_v cannot be reverted.\n";

        return false;
    }
    */
}
