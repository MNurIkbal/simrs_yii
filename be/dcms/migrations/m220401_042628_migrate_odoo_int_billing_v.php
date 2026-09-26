<?php

use yii\db\Migration;

/**
 * Class m220401_042628_migrate_odoo_int_billing_v
 */
class m220401_042628_migrate_odoo_int_billing_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."int_billing_v";');

         $this->execute("
         CREATE VIEW \"public\".\"int_billing_v\" AS  SELECT 6 AS sync_type,
    int_billing_r.id,
    int_billing_r.id AS sync_id_api,
    int_billing_r.pendaftaran_id::text AS admission_id,
    pendaftaran_t.no_pendaftaran AS admission_no,
    pembayaran_t.no_pembayaran AS billno,
    pembayaran_t.tgl_pembayaran AS confirmation_date,
    pembayaran_t.deleted_date AS cancel_date,
    pendaftaran_t.pasien_id::text AS partner_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN concat('PEN', pendaftaran_t.penjamin_id)
            ELSE concat('PEN', pasienadmisi_t.penjamin_id)
        END AS payer_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.penjamin_kode, '-'::character varying)
            ELSE COALESCE(p2.penjamin_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
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
            WHEN pembayaran_t.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = true THEN 'cancel'::text
            WHEN pembayaran_t.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = false THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
        CASE
            WHEN pembayaran_t.is_deleted = true THEN 0::double precision
            ELSE sum(int_billing_r.total_tunai + int_billing_r.total_nontunai - int_billing_r.total_kembalian + int_billing_r.penggunaan_uangmuka)
        END AS personal_amount,
        CASE
            WHEN pembayaran_t.is_deleted = true THEN 0::double precision
            ELSE sum(int_billing_r.total_dijamin)
        END AS payer_amount,
        CASE
            WHEN pembayaran_t.is_deleted = true THEN 0::double precision
            ELSE sum(int_billing_r.total_tagihan + int_billing_r.total_administrasi + int_billing_r.total_pembulatan - int_billing_r.total_discountpembayaran - int_billing_r.total_discount)
        END AS total_amount,
    asuransipasien_m.nama_asuransi,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
        CASE
            WHEN pembayaran_t.penjualanresep_id IS NOT NULL THEN concat('RSPB', pembayaran_t.penjualanresep_id)
            ELSE NULL::text
        END AS penjualanresep,
    int_billing_r.is_sent,
    int_billing_r.is_sending,
        CASE
            WHEN pembayaran_t.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = true AND int_billing_r.is_update = true THEN true
            WHEN pembayaran_t.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = false THEN false
            ELSE false
        END AS is_update,
    int_billing_r.is_update_sent,
    int_billing_r.is_update_sending,
        CASE
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_billing_r.is_sending = false AND int_billing_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_billing_r.is_sending = false AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_billing_r.tgl_proses,
        CASE
            WHEN int_billing_r.is_update = true AND int_billing_r.is_sent = true THEN 'WRITE'::text
            ELSE 'CREATE'::text
        END AS status_create,
    concat('PEG', pendaftaran_t.pegawai_id) AS primary_doc_id,
    concat('REF', rujukan_t.perujuk_id) AS referral_doc_id,
    rujukan_t.no_rujukan AS referral_number
   FROM int_billing_r
     JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
            a.pendaftaran_id
           FROM pendaftaran_r a
          WHERE a.is_sent = true AND a.keterangan::text = 'INSERT'::text) pendaftaran_r ON int_billing_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT a.pembayaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            a.created_date AS tgl_pembayaran,
            a.is_deleted,
            a.deleted_date,
            pembayaranpelayanan_t.penjualanresep_id
           FROM pembayaran_t a
             LEFT JOIN ( SELECT a1.pembayaran_id,
                    a1.penjualanresep_id,
                    a1.no_pembayaran
                   FROM pembayaranpelayanan_t a1
                  GROUP BY a1.pembayaran_id, a1.penjualanresep_id, a1.no_pembayaran) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id) pembayaran_t ON int_billing_r.pembayaran_id = pembayaran_t.pembayaran_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.instalasi_id,
            a.penjamin_id,
            a.asuransipasien_id,
            a.no_pendaftaran,
            a.pasien_id,
            a.is_aps,
            a.pegawai_id,
            a.rujukan_id
           FROM pendaftaran_t a) pendaftaran_t ON int_billing_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.penjamin_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode
           FROM penjamin_m a) p1 ON pendaftaran_t.penjamin_id = p1.penjamin_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode
           FROM penjamin_m a) p2 ON pasienadmisi_t.penjamin_id = p2.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.groupcarabayar_id
           FROM carabayar_m a) cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.groupcarabayar_id
           FROM carabayar_m a) cb2 ON p2.carabayar_id = cb2.carabayar_id
     LEFT JOIN ( SELECT a.asuransipasien_id,
            a.nama_asuransi,
            a.nokartuasuransi
           FROM asuransipasien_m a) asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN ( SELECT a.rujukan_id,
            a.no_rujukan,
            a.rujukandari_id AS perujuk_id
           FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
  GROUP BY int_billing_r.id, int_billing_r.pendaftaran_id, pendaftaran_t.no_pendaftaran, pembayaran_t.no_pembayaran, pembayaran_t.tgl_pembayaran, pembayaran_t.deleted_date, pendaftaran_t.pasien_id, (
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN concat('PEN', pendaftaran_t.penjamin_id)
            ELSE concat('PEN', pasienadmisi_t.penjamin_id)
        END), (
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.penjamin_kode, '-'::character varying)
            ELSE COALESCE(p2.penjamin_kode, '-'::character varying)
        END), (
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END), (
        CASE
            WHEN pendaftaran_t.is_aps = true AND pendaftaran_t.instalasi_id <> 21 THEN '1'::text
            WHEN pendaftaran_t.instalasi_id = 1 THEN '1'::text
            WHEN pendaftaran_t.instalasi_id = 3 THEN '2'::text
            WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NOT NULL THEN '2'::text
            WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NULL THEN '3'::text
            WHEN pendaftaran_t.instalasi_id = 6 THEN '6'::text
            WHEN pendaftaran_t.instalasi_id = 21 THEN '5'::text
            ELSE '4'::text
        END), (
        CASE
            WHEN pembayaran_t.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = true THEN 'cancel'::text
            WHEN pembayaran_t.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = false THEN 'done'::text
            ELSE 'draft'::text
        END), asuransipasien_m.nama_asuransi, asuransipasien_m.nokartuasuransi, (
        CASE
            WHEN pembayaran_t.penjualanresep_id IS NOT NULL THEN concat('RSPB', pembayaran_t.penjualanresep_id)
            ELSE NULL::text
        END), int_billing_r.is_sent, int_billing_r.is_sending, int_billing_r.is_update_sent, int_billing_r.is_update_sending, int_billing_r.is_update, (
        CASE
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_billing_r.is_sending = false AND int_billing_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_billing_r.is_sending = false AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END), (
        CASE
            WHEN pembayaran_t.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = true AND int_billing_r.is_update = true THEN true
            WHEN pembayaran_t.pembayaran_id IS NOT NULL AND pembayaran_t.is_deleted = false THEN false
            ELSE false
        END), int_billing_r.tgl_proses, pembayaran_t.is_deleted, (concat('PEG', pendaftaran_t.pegawai_id)), (concat('REF', rujukan_t.perujuk_id)), rujukan_t.no_rujukan
UNION ALL
 SELECT 6 AS sync_type,
    int_billing_r.id,
    int_billing_r.id AS sync_id_api,
    concat('RSPB', penjualanresep_t.penjualanresep_id) AS admission_id,
    penjualanresep_t.noresep AS admission_no,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS confirmation_date,
    pembayaranpelayanan_t.deleted_date AS cancel_date,
    penjualanresep_t.partner_id,
    concat('PEN', penjualanresep_t.penjamin_id) AS payer_id,
    COALESCE(p1.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying) AS payer_type,
    '1'::text AS patient_type,
        CASE
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = true THEN 'cancel'::text
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = false THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = true THEN 0::double precision
            ELSE sum(int_billing_r.total_tunai + int_billing_r.total_nontunai - int_billing_r.total_kembalian + int_billing_r.penggunaan_uangmuka)
        END AS personal_amount,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = true THEN 0::double precision
            ELSE sum(int_billing_r.total_dijamin)
        END AS payer_amount,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = true THEN 0::double precision
            ELSE sum(int_billing_r.total_tagihan + int_billing_r.total_administrasi + int_billing_r.total_pembulatan - int_billing_r.total_discountpembayaran - int_billing_r.total_discount)
        END AS total_amount,
    NULL::text AS nama_asuransi,
    NULL::text AS no_asuransi,
        CASE
            WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN concat('RSPB', pembayaranpelayanan_t.penjualanresep_id)
            ELSE NULL::text
        END AS penjualanresep,
    int_billing_r.is_sent,
    int_billing_r.is_sending,
        CASE
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = true AND int_billing_r.is_update = true THEN true
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = false THEN false
            ELSE false
        END AS is_update,
    int_billing_r.is_update_sent,
    int_billing_r.is_update_sending,
        CASE
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_billing_r.is_sending = false AND int_billing_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_billing_r.is_sending = false AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_billing_r.tgl_proses,
        CASE
            WHEN int_billing_r.is_update = true THEN 'WRITE'::text
            ELSE 'CREATE'::text
        END AS status_create,
    NULL::text AS primary_doc_id,
    NULL::text AS referral_doc_id,
    NULL::text AS referral_number
   FROM int_billing_r
     JOIN ( SELECT b.pembayaranpelayanan_id,
            b.penjualanresep_id,
            b.pembayaran_id,
            b.no_pembayaran,
            b.tgl_pembayaran,
            b.is_deleted,
            b.deleted_date
           FROM pembayaranpelayanan_t b) pembayaranpelayanan_t ON int_billing_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     JOIN ( SELECT DISTINCT ON (b.penjualanresep_id) b.id,
            b.penjualanresep_id
           FROM penjualanresep_r b
          WHERE b.is_sent = true AND b.keterangan::text = 'INSERT'::text) penjualanresep_r ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_r.penjualanresep_id
     JOIN ( SELECT b.penjualanresep_id,
            b.penjamin_id,
            b.noresep,
                CASE
                    WHEN b.jenispenjualan::text = '343'::text THEN 0::character varying
                    WHEN b.jenispenjualan::text = '345'::text THEN concat('PEG', b.karyawan_id)::character varying
                    ELSE 0::character varying
                END AS partner_id
           FROM penjualanresep_t b) penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT b.penjamin_id,
            b.carabayar_id,
            b.penjamin_kode
           FROM penjamin_m b) p1 ON penjualanresep_t.penjamin_id = p1.penjamin_id
     LEFT JOIN ( SELECT b.carabayar_id,
            b.groupcarabayar_id
           FROM carabayar_m b) cb1 ON p1.carabayar_id = cb1.carabayar_id
  GROUP BY 6::integer, int_billing_r.id, (concat('RSPB', penjualanresep_t.penjualanresep_id)), penjualanresep_t.noresep, pembayaranpelayanan_t.no_pembayaran, pembayaranpelayanan_t.tgl_pembayaran, pembayaranpelayanan_t.deleted_date, penjualanresep_t.partner_id, (concat('PEN', penjualanresep_t.penjamin_id)), (COALESCE(p1.penjamin_kode, '-'::character varying)), (COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)), '1'::text, (
        CASE
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = true THEN 'cancel'::text
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = false THEN 'done'::text
            ELSE 'draft'::text
        END), NULL::text, (
        CASE
            WHEN pembayaranpelayanan_t.penjualanresep_id IS NOT NULL THEN concat('RSPB', pembayaranpelayanan_t.penjualanresep_id)
            ELSE NULL::text
        END), int_billing_r.is_sent, int_billing_r.is_sending, (
        CASE
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = true AND int_billing_r.is_update = true THEN true
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan_t.is_deleted = false THEN false
            ELSE false
        END), int_billing_r.is_update_sent, int_billing_r.is_update_sending, (
        CASE
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_billing_r.is_sending = false AND int_billing_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_billing_r.is_sending = true AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_billing_r.is_sending = false AND int_billing_r.is_sent = false AND int_billing_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END), int_billing_r.tgl_proses, (
        CASE
            WHEN int_billing_r.is_update = true THEN 'WRITE'::text
            ELSE 'CREATE'::text
        END), (pembayaranpelayanan_t.is_deleted = true);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220401_042628_migrate_odoo_int_billing_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220401_042628_migrate_odoo_int_billing_v cannot be reverted.\n";

        return false;
    }
    */
}
