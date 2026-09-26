<?php

use yii\db\Migration;

/**
 * Class m210820_022552_migrate_odoo_resepkaryawan
 */
class m210820_022552_migrate_odoo_resepkaryawan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."saleorder_v";');

        $this->execute("
            CREATE VIEW \"public\".\"saleorder_v\" AS  SELECT 'RUMAH_SAKIT'::text AS jenis,
    pendaftaran_r.id,
    pendaftaran_r.pendaftaran_id::character varying AS sync_id_api,
    pendaftaran_r.no_pendaftaran AS name,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN '-'::character varying
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN pembayaran.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN NULL::timestamp without time zone
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN pembayaran.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    pendaftaran_r.pasien_id::character varying AS partner_id,
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
            ELSE concat('PEN', pasienadmisi_t.penjamin_id)
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
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 'draft'::text
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN 'done'::text
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
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.personal_amount, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.total_dijamin, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.total_tagihan, 0::double precision)
            ELSE 0::double precision
        END AS total_amount,
    pendaftaran_r.sync_response,
    pendaftaran_r.sync_payload
   FROM pendaftaran_r
     LEFT JOIN ( SELECT pasienadmisi.pasienadmisi_id,
            pasienadmisi.penjamin_id
           FROM pasienadmisi_t pasienadmisi) pasienadmisi_t ON pendaftaran_r.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT pen_1.penjamin_id,
            pen_1.carabayar_id,
            pen_1.penjamin_kode
           FROM penjamin_m pen_1) p1 ON pendaftaran_r.penjamin_id = p1.penjamin_id
     LEFT JOIN ( SELECT pen_2.penjamin_id,
            pen_2.carabayar_id,
            pen_2.penjamin_kode
           FROM penjamin_m pen_2) p2 ON pasienadmisi_t.penjamin_id = p2.penjamin_id
     LEFT JOIN ( SELECT carabayar_1.carabayar_id,
            carabayar_1.groupcarabayar_id
           FROM carabayar_m carabayar_1) cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN ( SELECT carabayar_2.carabayar_id,
            carabayar_2.groupcarabayar_id
           FROM carabayar_m carabayar_2) cb2 ON p2.carabayar_id = cb2.carabayar_id
     LEFT JOIN ( SELECT asuransipasien.asuransipasien_id,
            asuransipasien.namapemilikasuransi,
            asuransipasien.nokartuasuransi
           FROM asuransipasien_m asuransipasien) asuransipasien_m ON pendaftaran_r.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN ( SELECT DISTINCT ON (pembayaran_t.pendaftaran_id) pembayaran_t.pembayaran_id,
            pembayaran_t.pendaftaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaran_t.created_date AS tgl_pembayaran,
            pembayaran_t.is_deleted,
            pembayaran_t.total_tunai + pembayaran_t.total_nontunai - pembayaran_t.total_kembalian AS personal_amount,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan
           FROM pembayaran_t
             JOIN pembayaranpelayanan_t ON pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id) pembayaran ON pendaftaran_r.pendaftaran_id = pembayaran.pendaftaran_id
UNION ALL
 SELECT
        CASE
            WHEN penjualanresep_r.jenispenjualan::text = '343'::text THEN 'BEBAS'::text
            ELSE 'KARYAWAN'::text
        END AS jenis,
    penjualanresep_r.id,
    concat('RSPB', penjualanresep_r.penjualanresep_id) AS sync_id_api,
    penjualanresep_r.noresep AS name,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN '-'::character varying
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN pembayaran.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN NULL::timestamp without time zone
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN pembayaran.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
        CASE
            WHEN penjualanresep_r.jenispenjualan::text = '343'::text THEN 0::character varying
            WHEN penjualanresep_r.jenispenjualan::text = '345'::text THEN concat('PEG', penjualanresep_r.karyawan_id)::character varying
            ELSE 0::character varying
        END AS partner_id,
    penjualanresep_r.tglresep AS date_order,
    '1'::text AS patient_type,
    concat('PEN', penjualanresep_r.penjamin_id) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), '-'::character varying) AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 'draft'::text
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN 'done'::text
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
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.personal_amount, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.total_dijamin, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
        CASE
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
            WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.total_tagihan, 0::double precision)
            ELSE 0::double precision
        END AS total_amount,
    penjualanresep_r.sync_response,
    penjualanresep_r.sync_payload
   FROM penjualanresep_r
     LEFT JOIN ( SELECT penjamin.penjamin_id,
            penjamin.carabayar_id,
            penjamin.penjamin_kode
           FROM penjamin_m penjamin) penjamin_m ON penjualanresep_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT carabayar.carabayar_id,
            carabayar.groupcarabayar_id
           FROM carabayar_m carabayar) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT DISTINCT ON (pembayaranpelayanan.pendaftaran_id) pembayaranpelayanan.penjualanresep_id,
            pembayaran_t.pembayaran_id,
            pembayaranpelayanan.no_pembayaran,
            pembayaran_t.created_date AS tgl_pembayaran,
            pembayaran_t.is_deleted,
            pembayaran_t.total_tunai + pembayaran_t.total_nontunai - pembayaran_t.total_kembalian AS personal_amount,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan
           FROM pembayaran_t
             JOIN ( SELECT pembayaranpelayanan_t.pendaftaran_id,
                    pembayaranpelayanan_t.penjualanresep_id,
                    pembayaranpelayanan_t.pembayaran_id,
                    pembayaranpelayanan_t.no_pembayaran
                   FROM pembayaranpelayanan_t) pembayaranpelayanan ON pembayaran_t.pembayaran_id = pembayaranpelayanan.pembayaran_id) pembayaran ON penjualanresep_r.penjualanresep_id = pembayaran.penjualanresep_id
  WHERE penjualanresep_r.jenispenjualan::text = ANY (ARRAY['343'::character varying::text, '345'::character varying::text]);");

        $this->execute('DROP VIEW if exists "public"."int_saleorderupdate_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_saleorderupdate_v\" AS  SELECT pembayaran_r.id,
    pendaftaran_t.pendaftaran_id::character varying AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
        CASE
            WHEN pembayaran.is_deleted = false THEN pembayaran.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaran.is_deleted = false THEN pembayaran.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    pendaftaran_t.pasien_id::character varying AS partner_id,
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
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.penjamin_kode, '-'::character varying)
            ELSE COALESCE(p2.penjamin_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaran.is_deleted = true THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    COALESCE(pembayaran.personal_amount, 0::double precision) AS personal_amount,
    COALESCE(pembayaran.payer_amount, 0::double precision) AS payer_amount,
        CASE
            WHEN pembayaran.is_deleted = true THEN 0::double precision
            ELSE COALESCE(pembayaran.total_tagihan, 0::double precision)
        END AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.penjamin_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran,
            a.pasien_id,
            a.instalasi_id,
            a.is_aps
           FROM pendaftaran_t a) pendaftaran_t ON pembayaran_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
            a.pendaftaran_id
           FROM pendaftaran_r a
          WHERE a.is_sent = true AND a.keterangan::text = 'INSERT'::text) pendaftaran_r ON pendaftaran_t.pendaftaran_id = pendaftaran_r.pendaftaran_id
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
     LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            a.created_date AS tgl_pembayaran,
            a.is_deleted,
            a.pembayaran_id,
            sum(a.total_tagihan + a.total_administrasi + a.total_pembulatan - a.total_discountpembayaran - a.total_discount) AS total_tagihan,
            sum(a.total_tunai + a.total_nontunai - a.total_kembalian + a.penggunaan_uangmuka) AS personal_amount,
            sum(a.total_dijamin) AS payer_amount
           FROM pembayaran_t a
             JOIN pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id, pembayaranpelayanan_t.no_pembayaran, a.created_date, a.is_deleted, a.pembayaran_id) pembayaran ON pembayaran_r.pendaftaran_id = pembayaran.pendaftaran_id
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
    penjualanresep_t.partner_id,
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
                    pembayaranpelayanan_t_1.penjualanresep_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  GROUP BY pembayaranpelayanan_t_1.penjualanresep_id) max_pembayaran ON pembayaranpelayanan_t.penjualanresep_id = max_pembayaran.penjualanresep_id AND pembayaranpelayanan_t.pembayaranpelayanan_id = max_pembayaran.pembayaranpelayanan_id) pembayaranpelayanan ON pembayaran_r.pembayaran_id = pembayaranpelayanan.pembayaran_id
     LEFT JOIN ( SELECT b.penjualanresep_id,
            b.jenispenjualan,
            b.penjamin_id,
            b.noresep,
            b.tglresep,
                CASE
                    WHEN b.jenispenjualan::text = '343'::text THEN 0::character varying
                    ELSE concat('PEG', b.karyawan_id)::character varying
                END AS partner_id
           FROM penjualanresep_t b) penjualanresep_t ON pembayaranpelayanan.penjualanresep_id = penjualanresep_t.penjualanresep_id
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

        $this->execute('DROP VIEW if exists "public"."int_obatalkespasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_obatalkespasien_v\" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS product_uom_qty,
        CASE
            WHEN (obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text) IS NOT NULL AND (obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text) <> ''::text AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END AS price_unit,
        CASE
            WHEN (obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text) IS NOT NULL AND (obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text) <> ''::text AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END *
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS price_subtotal,
        CASE
            WHEN (obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text) IS NOT NULL AND (obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text) <> ''::text AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END *
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS price_total,
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
        CASE
            WHEN obatalkespasien_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', pendaftaran_r.pegadmisi_id)
        END AS primary_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS prescribe_doc_id,
    NULL::text AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN obatalkespasien_r.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
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
    pendaftaran_r.pasien_id::text AS partner_id,
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
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM obatalkespasien_r
     JOIN ( SELECT obatalkespasien_t_1.obatalkespasien_id,
            obatalkespasien_t_1.tarif_dibayarkan,
            obatalkespasien_t_1.tarif_dijamin,
            obatalkespasien_t_1.tarif_diskon
           FROM obatalkespasien_t obatalkespasien_t_1) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
            a.pendaftaran_id,
            a.pegawai_id,
            pendaftaran_t.pasienadmisi_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN a.instalasi_id
                    ELSE 3
                END AS instalasi_id,
            a.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            a.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
            a.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id
           FROM pendaftaran_r a
             JOIN ( SELECT a1.pasien_id,
                    a1.no_rekam_medik,
                    a1.nama_pasien,
                    a1.kabupaten_id,
                    a1.kecamatan_id,
                    a1.kelurahan_id
                   FROM pasien_m a1) pasien_m ON a.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a2.pendaftaran_id,
                    a2.pegawai_id,
                    a2.pasienadmisi_id
                   FROM pendaftaran_t a2) pendaftaran_t ON a.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a3.pasienadmisi_id,
                    a3.pegawai_id
                   FROM pasienadmisi_t a3) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a4.kabupaten_id,
                    a4.kabupaten_nama
                   FROM kabupaten_m a4) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a5.kecamatan_id,
                    a5.kecamatan_nama
                   FROM kecamatan_m a5) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a6.kelurahan_id,
                    a6.kelurahan_nama
                   FROM kelurahan_m a6) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a7.pasienpulang_id,
                    a7.tglpasienpulang
                   FROM pasienpulang_t a7) pasienpulang_t ON a.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE a.is_sent = true AND a.keterangan::text = 'INSERT'::text) pendaftaran_r ON obatalkespasien_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT a.obatalkes_id,
            a.jenisobatalkes_id,
            a.obatalkes_nama,
            a.satuankecil_id
           FROM obatalkes_m a) obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama,
            a.servicegroup_id,
            a.servicecategory_id
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.servicegroup_id,
            a.servicegroup_nama
           FROM servicegroup_m a) servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.no_masukpenunjang
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON obatalkespasien_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            a.created_date AS tgl_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
             JOIN pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
          WHERE a.is_deleted = false) pembayaran ON obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m a
             JOIN ( SELECT a1.spesialis_id,
                    a1.spesialis_nama
                   FROM spesialis_m a1) spesialis_m ON a.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT a.penjualanresep_id,
            a.pegawai_id,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN a.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM penjualanresep_t a
             LEFT JOIN ( SELECT a1.penjualanresep_id,
                    a1.noresep
                   FROM reseptur_t a1) reseptur_t ON a.penjualanresep_id = reseptur_t.penjualanresep_id
          GROUP BY a.penjualanresep_id, a.pegawai_id, (
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN a.noresep
                    ELSE reseptur_t.noresep
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT a.id,
            a.pembayaran_id,
            a.is_sent
           FROM int_billing_r a) int_billing_r ON obatalkespasien_r.pembayaran_id = int_billing_r.pembayaran_id
UNION ALL
 SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS product_uom_qty,
        CASE
            WHEN (obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text) IS NOT NULL AND (obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text) <> ''::text AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END AS price_unit,
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
    concat('RSPB', obatalkespasien_r.penjualanresep_id) AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('PEG', penjualanresep_t.pegawai_id) AS primary_doc_id,
    concat('PEG', penjualanresep_t.pegawai_id) AS prescribe_doc_id,
    NULL::text AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
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
    penjualanresep_r.tglresep AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM obatalkespasien_r
     JOIN ( SELECT b.obatalkespasien_id,
            b.tarif_dibayarkan,
            b.tarif_dijamin,
            b.tarif_diskon
           FROM obatalkespasien_t b) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN ( SELECT DISTINCT ON (b.penjualanresep_id) b.id,
            b.penjualanresep_id,
            b.pegawai_id,
            b.pasienadmisi_id,
                CASE
                    WHEN b.jenispenjualan::text = '345'::text THEN concat('PEG', b.karyawan_id)
                    ELSE 0::text
                END AS pasien_id,
            pasien_m.no_rekam_medik,
                CASE
                    WHEN b.jenispenjualan::text = '345'::text THEN karyawan.nama_pegawai
                    ELSE b.nama_pembeli
                END AS nama_pasien,
            b.noresep,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            b.tglresep AS tglpasienpulang,
            b.tglresep
           FROM penjualanresep_r b
             LEFT JOIN ( SELECT b1.pasien_id,
                    b1.no_rekam_medik,
                    b1.nama_pasien,
                    b1.kabupaten_id,
                    b1.kecamatan_id,
                    b1.kelurahan_id
                   FROM pasien_m b1) pasien_m ON 0 = pasien_m.pasien_id
             LEFT JOIN ( SELECT b2.pegawai_id,
                    b2.nama_pegawai
                   FROM pegawai_m b2) karyawan ON b.karyawan_id = karyawan.pegawai_id
             LEFT JOIN ( SELECT b3.kabupaten_id,
                    b3.kabupaten_nama
                   FROM kabupaten_m b3) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT b4.kecamatan_id,
                    b4.kecamatan_nama
                   FROM kecamatan_m b4) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT b5.kelurahan_id,
                    b5.kelurahan_nama
                   FROM kelurahan_m b5) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
          WHERE b.is_sent = true AND b.keterangan::text = 'ACCRUAL'::text) penjualanresep_r ON obatalkespasien_r.penjualanresep_id = penjualanresep_r.penjualanresep_id
     JOIN ( SELECT b.obatalkes_id,
            b.jenisobatalkes_id,
            b.obatalkes_nama,
            b.satuankecil_id
           FROM obatalkes_m b) obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT b.jenisobatalkes_id,
            b.jenisobatalkes_nama,
            b.servicegroup_id,
            b.servicecategory_id
           FROM jenisobatalkes_m b) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT b.servicegroup_id,
            b.servicegroup_nama
           FROM servicegroup_m b) servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
     JOIN ( SELECT b.ruangan_id,
            b.instalasi_id,
            b.ruangan_nama
           FROM ruangan_m b) ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT b.penjamin_id,
            b.carabayar_id,
            b.penjamin_kode,
            b.penjamin_nama
           FROM penjamin_m b) penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
           FROM carabayar_m b) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT pembayaranpelayanan.penjualanresep_id,
            b.pembayaran_id,
            b.created_date AS tgl_pembayaran,
            pembayaranpelayanan.no_pembayaran
           FROM pembayaran_t b
             JOIN ( SELECT b1.pembayaran_id,
                    b1.penjualanresep_id,
                    b1.no_pembayaran
                   FROM pembayaranpelayanan_t b1) pembayaranpelayanan ON b.pembayaran_id = pembayaranpelayanan.pembayaran_id
          WHERE b.is_deleted = false) pembayaran ON obatalkespasien_r.penjualanresep_id = pembayaran.penjualanresep_id
     LEFT JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
           FROM kelaspelayanan_m b) kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT b.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m b
             JOIN ( SELECT b1.spesialis_id,
                    b1.spesialis_nama
                   FROM spesialis_m b1) spesialis_m ON b.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT b.penjualanresep_id,
            b.pegawai_id,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN b.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM penjualanresep_t b
             LEFT JOIN ( SELECT b1.penjualanresep_id,
                    b1.noresep
                   FROM reseptur_t b1) reseptur_t ON b.penjualanresep_id = reseptur_t.penjualanresep_id
          GROUP BY b.penjualanresep_id, b.pegawai_id, (
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN b.noresep
                    ELSE reseptur_t.noresep
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT b.id,
            b.pembayaran_id,
            b.is_sent
           FROM int_billing_r b) int_billing_r ON obatalkespasien_r.pembayaran_id = int_billing_r.pembayaran_id;");
    
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
        END AS status_create
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
            a.is_aps
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
        END), int_billing_r.tgl_proses, pembayaran_t.is_deleted
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
        END AS status_create
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
          WHERE b.is_sent = true AND b.keterangan::text = 'ACCRUAL'::text) penjualanresep_r ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_r.penjualanresep_id
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

        $this->execute('DROP VIEW if exists "public"."int_pembayaran_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_pembayaran_v\" AS  SELECT concat('BYR', pembayaran_r.id) AS sync_id_api,
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
                  GROUP BY a1.pembayaran_id, a1.ruangan_id, a1.no_pembayaran) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
          WHERE a.is_deleted = false) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
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
  WHERE pembayaran_r.total_dijamin <= 0::double precision AND pembayaran_r.is_update = true
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
          WHERE (b.jenispenjualan::text = ANY (ARRAY['343'::text, '345'::text])) AND b.keterangan::text = 'ACCRUAL'::text AND b.is_sent = true) penjualanresep_r ON pembayaran.penjualanresep_id = penjualanresep_r.penjualanresep_id
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
  WHERE pembayaran_r.total_dijamin <= 0::double precision AND pembayaran_r.is_update = true
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
    concat(jenisnontunai_m.nama, ' - ', tandabuktibayar_t.no_rek) AS edc_machine,
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
        END AS status_proses,
    NULL::text AS billing_id,
    NULL::boolean AS is_sent_billing
   FROM bayaruangmuka_r
     JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON bayaruangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.tandabuktibayar_id,
            a.bayaruangmuka_id,
            a.no_rek
           FROM tandabuktibayar_t a) tandabuktibayar_t ON bayaruangmuka_r.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasien_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.jenisnontunai_id,
            a.nama
           FROM jenisnontunai_m a) jenisnontunai_m ON bayaruangmuka_r.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
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
        END AS status_proses,
    NULL::text AS billing_id,
    NULL::boolean AS is_sent_billing
   FROM pengembalianuangmuka_r
     JOIN ( SELECT b.loginpemakai_id,
            b.pegawai_id
           FROM loginpemakai_k b) loginpemakai_k ON pengembalianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT b.pengembalianuangmuka_id,
            b.no_buktikeluar,
            b.is_tunai,
            b.no_rek
           FROM tandabuktikeluar_t b) tandabuktikeluar_t ON pengembalianuangmuka_r.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id
     JOIN ( SELECT b.pendaftaran_id,
            b.pasien_id,
            b.no_pendaftaran
           FROM pendaftaran_t b) pendaftaran_t ON pengembalianuangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT b.pasien_id,
            b.nama_pasien
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
UNION ALL
 SELECT concat('PKUM', pemakaianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
    pemakaianuangmuka_r.pendaftaran_id AS admission_id,
    pembayaran.no_pembayaran AS trans_no,
    pemakaianuangmuka_r.tgl_pemakaian AS trans_date,
    pemakaianuangmuka_r.keterangan AS trans_type,
    pembayaran.no_pembayaran AS reference_no,
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
        END AS status_proses,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM pemakaianuangmuka_r
     JOIN ( SELECT c.loginpemakai_id,
            c.pegawai_id
           FROM loginpemakai_k c) loginpemakai_k ON pemakaianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id
     JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT c.pendaftaran_id,
            c.pembayaran_id,
            c.no_pembayaran
           FROM pembayaranpelayanan_t c
          WHERE c.is_deleted = false) pembayaran ON pemakaianuangmuka_r.pendaftaran_id = pembayaran.pendaftaran_id
     JOIN ( SELECT c.pendaftaran_id,
            c.pasien_id,
            c.no_pendaftaran
           FROM pendaftaran_t c) pendaftaran_t ON pemakaianuangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT c.pasien_id,
            c.nama_pasien
           FROM pasien_m c) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT c.pembayaran_id,
            c.id,
            c.is_sent
           FROM int_billing_r c) int_billing_r ON pembayaran.pembayaran_id = int_billing_r.pembayaran_id;");

        $this->execute('DROP VIEW if exists "public"."saleorder_line_update_v";');

        $this->execute("
            CREATE VIEW \"public\".\"saleorder_line_update_v\" AS  SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('TND', tindakanpelayanan_r.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
    tindakanpelayanan_r.qty_tindakan AS product_uom_qty,
    tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision) AS price_unit,
    (tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision)) * tindakanpelayanan_r.qty_tindakan::double precision AS price_subtotal,
    (tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision)) * tindakanpelayanan_r.qty_tindakan::double precision AS price_total,
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
        CASE
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE NULL::text
        END AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE NULL::text
        END AS prescribe_doc_id,
    concat('PEG', tindakanpelayananupdate_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 'LOS'::text
            WHEN kelompoktindakan_m.kelompoktindakan_namalainnya::text = 'LOS'::text THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
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
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayananupdate_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayananupdate_r.dokterpenanggungjawab_id)
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
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing,
    tindakanpelayanan_r.tindakanpelayanan_id AS parent_id
   FROM tindakanpelayanan_r
     JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
            a.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            a.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            a.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            a.is_aps,
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id
           FROM pendaftaran_r a
             JOIN ( SELECT a1.pasien_id,
                    a1.no_rekam_medik,
                    a1.nama_pasien,
                    a1.kabupaten_id,
                    a1.kecamatan_id,
                    a1.kelurahan_id
                   FROM pasien_m a1) pasien_m ON a.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a2.pendaftaran_id,
                    a2.pasienadmisi_id,
                    a2.tgl_pendaftaran,
                    a2.instalasi_id,
                    a2.pegawai_id
                   FROM pendaftaran_t a2) pendaftaran_t ON a.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a3.pasienadmisi_id,
                    a3.ruangan_id,
                    a3.pegawai_id
                   FROM pasienadmisi_t a3) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a4.ruangan_id,
                    a4.instalasi_id
                   FROM ruangan_m a4) ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN ( SELECT a5.kabupaten_id,
                    a5.kabupaten_nama
                   FROM kabupaten_m a5) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a6.kecamatan_id,
                    a6.kecamatan_nama
                   FROM kecamatan_m a6) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a7.kelurahan_id,
                    a7.kelurahan_nama
                   FROM kelurahan_m a7) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a8.pasienpulang_id,
                    a8.tglpasienpulang
                   FROM pasienpulang_t a8) pasienpulang_t ON a.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE a.is_sent = true AND a.keterangan::text = 'INSERT'::text) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT a.daftartindakan_id,
            a.servicegroup_id,
            a.servicecategory_id,
            a.kelompoktindakan_id,
            a.kategoritindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.servicegroup_id,
            a.servicegroup_nama
           FROM servicegroup_m a) servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN ( SELECT a.kategoritindakan_id,
            a.kategoritindakan_nama
           FROM kategoritindakan_m a) kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN ( SELECT a.kelompoktindakan_id,
            a.kelompoktindakan_nama,
            a.kelompoktindakan_namalainnya
           FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pembayaran,
            a.created_date AS tgl_pembayaran
           FROM pembayaranpelayanan_t a
          WHERE a.is_deleted = false) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT a.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m a
             JOIN ( SELECT a1.spesialis_id,
                    a1.spesialis_nama
                   FROM spesialis_m a1) spesialis_m ON a.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.no_masukpenunjang
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            a.tarif_dibayarkan,
            a.tarif_dijamin,
            a.tarif_diskon
           FROM tindakanpelayanan_t a) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.id,
            a.is_sent
           FROM int_billing_r a) int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
     JOIN ( SELECT a.tindakanpelayanan_id,
            a.dokterpenanggungjawab_id
           FROM tindakanpelayananupdate_r a) tindakanpelayananupdate_r ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayananupdate_r.tindakanpelayanan_id
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('PKT', tindakanpelayanan_r.tipepaket_id) AS product_id,
    tipepaket_m.tipepaket_nama AS name,
    351 AS product_uom,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'ACCRUAL REVERSAL'::text THEN tindakanpelayanan_r.qty_tindakan
            ELSE tindakanpelayanan_r.qty_tindakan
        END AS product_uom_qty,
    0 AS price_unit,
    0 AS price_subtotal,
    0 AS price_total,
    0 AS personal_amount,
    0 AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', 10) AS service_categ_id,
        CASE
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE NULL::text
        END AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE NULL::text
        END AS prescribe_doc_id,
    concat('PEG', tindakanpelayananupdate_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    '-'::character varying AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
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
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayananupdate_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayananupdate_r.dokterpenanggungjawab_id)
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
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing,
    tindakanpelayanan_r.tindakanpelayanan_id AS parent_id
   FROM tindakanpelayanan_r
     JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.id,
            b.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            b.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            b.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            b.is_aps,
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id
           FROM pendaftaran_r b
             JOIN ( SELECT b1.pasien_id,
                    b1.no_rekam_medik,
                    b1.nama_pasien,
                    b1.kabupaten_id,
                    b1.kecamatan_id,
                    b1.kelurahan_id
                   FROM pasien_m b1) pasien_m ON b.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT b2.pendaftaran_id,
                    b2.pasienadmisi_id,
                    b2.tgl_pendaftaran,
                    b2.instalasi_id,
                    b2.pegawai_id
                   FROM pendaftaran_t b2) pendaftaran_t ON b.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT b3.pasienadmisi_id,
                    b3.ruangan_id,
                    b3.pegawai_id
                   FROM pasienadmisi_t b3) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT b4.ruangan_id,
                    b4.instalasi_id
                   FROM ruangan_m b4) ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN ( SELECT b5.kabupaten_id,
                    b5.kabupaten_nama
                   FROM kabupaten_m b5) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT b6.kecamatan_id,
                    b6.kecamatan_nama
                   FROM kecamatan_m b6) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT b7.kelurahan_id,
                    b7.kelurahan_nama
                   FROM kelurahan_m b7) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT b8.pasienpulang_id,
                    b8.tglpasienpulang
                   FROM pasienpulang_t b8) pasienpulang_t ON b.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE b.is_sent = true) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT b.tipepaket_id,
            b.tipepaket_nama
           FROM tipepaket_m b) tipepaket_m ON tindakanpelayanan_r.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ( SELECT b.ruangan_id,
            b.instalasi_id,
            b.ruangan_nama
           FROM ruangan_m b) ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT b.penjamin_id,
            b.carabayar_id,
            b.penjamin_kode,
            b.penjamin_nama
           FROM penjamin_m b) penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
           FROM carabayar_m b) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
           FROM kelaspelayanan_m b) kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT b.pendaftaran_id,
            b.no_pembayaran,
            b.created_date AS tgl_pembayaran
           FROM pembayaranpelayanan_t b
          WHERE b.is_deleted = false) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT b.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m b
             JOIN ( SELECT b1.spesialis_id,
                    b1.spesialis_nama
                   FROM spesialis_m b1) spesialis_m ON b.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT b.pasienmasukpenunjang_id,
            b.no_masukpenunjang
           FROM pasienmasukpenunjang_t b) pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT b.tindakanpelayanan_id,
            b.tarif_dibayarkan,
            b.tarif_dijamin,
            b.tarif_diskon
           FROM tindakanpelayanan_t b) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN ( SELECT b.pembayaran_id,
            b.id,
            b.is_sent
           FROM int_billing_r b) int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
     JOIN ( SELECT b.tindakanpelayanan_id,
            b.dokterpenanggungjawab_id
           FROM tindakanpelayananupdate_r b) tindakanpelayananupdate_r ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayananupdate_r.tindakanpelayanan_id;");

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
    (tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision)) * tindakanpelayanan_r.qty_tindakan::double precision AS price_subtotal,
    (tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision)) * tindakanpelayanan_r.qty_tindakan::double precision AS price_total,
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
        CASE
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE NULL::text
        END AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE NULL::text
        END AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 'LOS'::text
            WHEN kelompoktindakan_m.kelompoktindakan_namalainnya::text = 'LOS'::text THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
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
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id::character varying AS partner_id,
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
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM tindakanpelayanan_r
     JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
            a.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            a.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            a.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            a.is_aps,
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id
           FROM pendaftaran_r a
             JOIN ( SELECT a1.pasien_id,
                    a1.no_rekam_medik,
                    a1.nama_pasien,
                    a1.kabupaten_id,
                    a1.kecamatan_id,
                    a1.kelurahan_id
                   FROM pasien_m a1) pasien_m ON a.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a2.pendaftaran_id,
                    a2.pasienadmisi_id,
                    a2.tgl_pendaftaran,
                    a2.instalasi_id,
                    a2.pegawai_id
                   FROM pendaftaran_t a2) pendaftaran_t ON a.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a3.pasienadmisi_id,
                    a3.ruangan_id,
                    a3.pegawai_id
                   FROM pasienadmisi_t a3) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a4.ruangan_id,
                    a4.instalasi_id
                   FROM ruangan_m a4) ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN ( SELECT a5.kabupaten_id,
                    a5.kabupaten_nama
                   FROM kabupaten_m a5) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a6.kecamatan_id,
                    a6.kecamatan_nama
                   FROM kecamatan_m a6) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a7.kelurahan_id,
                    a7.kelurahan_nama
                   FROM kelurahan_m a7) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a8.pasienpulang_id,
                    a8.tglpasienpulang
                   FROM pasienpulang_t a8) pasienpulang_t ON a.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE a.is_sent = true AND a.keterangan::text = 'INSERT'::text) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT a.daftartindakan_id,
            a.servicegroup_id,
            a.servicecategory_id,
            a.kelompoktindakan_id,
            a.kategoritindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.servicegroup_id,
            a.servicegroup_nama
           FROM servicegroup_m a) servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN ( SELECT a.kategoritindakan_id,
            a.kategoritindakan_nama
           FROM kategoritindakan_m a) kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN ( SELECT a.kelompoktindakan_id,
            a.kelompoktindakan_nama,
            a.kelompoktindakan_namalainnya
           FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            a.created_date AS tgl_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
             JOIN pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
          WHERE a.is_deleted = false) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT a.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m a
             JOIN ( SELECT a1.spesialis_id,
                    a1.spesialis_nama
                   FROM spesialis_m a1) spesialis_m ON a.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.no_masukpenunjang
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            a.tarif_dibayarkan,
            a.tarif_dijamin,
            a.tarif_diskon
           FROM tindakanpelayanan_t a) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.id,
            a.is_sent
           FROM int_billing_r a) int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('PKT', tindakanpelayanan_r.tipepaket_id) AS product_id,
    tipepaket_m.tipepaket_nama AS name,
    351 AS product_uom,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'ACCRUAL REVERSAL'::text THEN tindakanpelayanan_r.qty_tindakan
            ELSE tindakanpelayanan_r.qty_tindakan
        END AS product_uom_qty,
    0 AS price_unit,
    0 AS price_subtotal,
    0 AS price_total,
    0 AS personal_amount,
    0 AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', 10) AS service_categ_id,
        CASE
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE NULL::text
        END AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE NULL::text
        END AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    '-'::character varying AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
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
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id::character varying AS partner_id,
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
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM tindakanpelayanan_r
     JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.id,
            b.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            b.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            b.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            b.is_aps,
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id
           FROM pendaftaran_r b
             JOIN ( SELECT b1.pasien_id,
                    b1.no_rekam_medik,
                    b1.nama_pasien,
                    b1.kabupaten_id,
                    b1.kecamatan_id,
                    b1.kelurahan_id
                   FROM pasien_m b1) pasien_m ON b.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT b2.pendaftaran_id,
                    b2.pasienadmisi_id,
                    b2.tgl_pendaftaran,
                    b2.instalasi_id,
                    b2.pegawai_id
                   FROM pendaftaran_t b2) pendaftaran_t ON b.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT b3.pasienadmisi_id,
                    b3.ruangan_id,
                    b3.pegawai_id
                   FROM pasienadmisi_t b3) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT b4.ruangan_id,
                    b4.instalasi_id
                   FROM ruangan_m b4) ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN ( SELECT b5.kabupaten_id,
                    b5.kabupaten_nama
                   FROM kabupaten_m b5) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT b6.kecamatan_id,
                    b6.kecamatan_nama
                   FROM kecamatan_m b6) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT b7.kelurahan_id,
                    b7.kelurahan_nama
                   FROM kelurahan_m b7) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT b8.pasienpulang_id,
                    b8.tglpasienpulang
                   FROM pasienpulang_t b8) pasienpulang_t ON b.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE b.is_sent = true) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT b.tipepaket_id,
            b.tipepaket_nama
           FROM tipepaket_m b) tipepaket_m ON tindakanpelayanan_r.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ( SELECT b.ruangan_id,
            b.instalasi_id,
            b.ruangan_nama
           FROM ruangan_m b) ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT b.penjamin_id,
            b.carabayar_id,
            b.penjamin_kode,
            b.penjamin_nama
           FROM penjamin_m b) penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
           FROM carabayar_m b) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
           FROM kelaspelayanan_m b) kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT b.pendaftaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            b.created_date AS tgl_pembayaran
           FROM pembayaran_t b
             JOIN pembayaranpelayanan_t ON b.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
          WHERE b.is_deleted = false) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT b.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m b
             JOIN ( SELECT b1.spesialis_id,
                    b1.spesialis_nama
                   FROM spesialis_m b1) spesialis_m ON b.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT b.tindakanpelayanan_id,
            b.tarif_dibayarkan,
            b.tarif_dijamin,
            b.tarif_diskon
           FROM tindakanpelayanan_t b) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN ( SELECT b.pasienmasukpenunjang_id,
            b.no_masukpenunjang
           FROM pasienmasukpenunjang_t b) pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT b.pembayaran_id,
            b.id,
            b.is_sent
           FROM int_billing_r b) int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
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
            WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(round(tindakanpelayanan_r.tarif_dijamin::integer::numeric, 2), 0::numeric)::double precision + COALESCE(round(tindakanpelayanan_r.tarif_diskon::integer::numeric, 2)::double precision, 0::double precision)
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
    penjualanresep.partner_id,
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
    penjualanresep.tglresep AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM tindakanpelayanan_r
     LEFT JOIN ( SELECT pembayaran.pembayaran_id,
            c.pegawairesep_id,
            c.noresep,
            pasien_m.no_rekam_medik,
            c.tglresep,
                CASE
                    WHEN c.jenispenjualan::text = '345'::text THEN karyawan.nama_pegawai
                    ELSE c.nama_pembeli
                END AS nama_pasien,
            penjamin_m.penjamin_nama,
            penjamin_m.penjamin_kode,
            carabayar_m.carabayar_nama,
            pembayaran.no_pembayaran,
            c.penjualanresep_id,
            c.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran.tgl_pembayaran,
                CASE
                    WHEN c.jenispenjualan::text = '343'::text THEN 0::character varying
                    ELSE concat('PEG', c.karyawan_id)::character varying
                END AS partner_id
           FROM penjualanresep_r c
             JOIN ( SELECT c1.pasien_id,
                    c1.no_rekam_medik,
                    c1.nama_pasien
                   FROM pasien_m c1) pasien_m ON 0 = pasien_m.pasien_id
             JOIN ( SELECT c2.ruangan_id,
                    c2.ruangan_nama
                   FROM ruangan_m c2) ruangan_m ON c.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT pembayaranpelayanan_t.penjualanresep_id,
                    pembayaranpelayanan_t.pembayaran_id,
                    pembayaranpelayanan_t.tgl_pembayaran,
                    pembayaranpelayanan_t.no_pembayaran
                   FROM pembayaranpelayanan_t) pembayaran ON c.penjualanresep_id = pembayaran.penjualanresep_id
             JOIN ( SELECT c4.penjamin_id,
                    c4.carabayar_id,
                    c4.penjamin_nama,
                    c4.penjamin_kode
                   FROM penjamin_m c4) penjamin_m ON c.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT c5.carabayar_id,
                    c5.carabayar_nama
                   FROM carabayar_m c5) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT c6.pegawai_id,
                    c6.nama_pegawai
                   FROM pegawai_m c6) karyawan ON c.karyawan_id = karyawan.pegawai_id
          WHERE (c.jenispenjualan::text = ANY (ARRAY['343'::character varying::text, '345'::character varying::text])) AND c.keterangan::text = 'ACCRUAL'::text AND c.is_sent = true) penjualanresep ON tindakanpelayanan_r.pembayaran_id = penjualanresep.pembayaran_id
     JOIN ( SELECT c.daftartindakan_id,
            c.servicegroup_id,
            c.servicecategory_id,
            c.kelompoktindakan_id,
            c.kategoritindakan_id,
            c.daftartindakan_nama
           FROM daftartindakan_m c) daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT c.servicegroup_id,
            c.servicegroup_nama
           FROM servicegroup_m c) servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN ( SELECT c.kategoritindakan_id,
            c.kategoritindakan_nama
           FROM kategoritindakan_m c) kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN ( SELECT c.kelompoktindakan_id,
            c.kelompoktindakan_nama,
            c.kelompoktindakan_namalainnya
           FROM kelompoktindakan_m c) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ( SELECT c.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m c
             JOIN ( SELECT c1.spesialis_id,
                    c1.spesialis_nama
                   FROM spesialis_m c1) spesialis_m ON c.spesialis_id = spesialis_m.spesialis_id) pegawai ON penjualanresep.pegawairesep_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT c.tindakanpelayanan_id,
            c.tarif_dibayarkan,
            c.tarif_dijamin,
            c.tarif_diskon
           FROM tindakanpelayanan_t c) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN ( SELECT c.pembayaran_id,
            c.id,
            c.is_sent
           FROM int_billing_r c) int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"obatsudahbayar_t_cancel\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
DECLARE
        v_keterangan VARCHAR;
                v_tglbatal TIMESTAMP;
BEGIN
            SELECT
                deleted_date
                INTO 
                v_tglbatal
            FROM pembayaranpelayanan_t
            WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
            
        IF(NEW.is_deleted IS TRUE)
        THEN
            -- INSERT table history obatalkespasien_r menjadi ACCRUAL(+), jika is_deleted=TRUE
            INSERT INTO obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan,
                                no_obatalkespasien,
                                tarif_dijamin,
                                tarif_dibayarkan,
                                tarif_diskon,
                                pembayaran_id
                )
                SELECT
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                NEW.obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                'ACCRUAL',
                                no_obatalkespasien,
                                0,
                                0,
                                0,
                                NULL
                FROM obatalkespasien_t
            WHERE obatalkespasien_id = NEW.obatalkespasien_id;
        
            -- INSERT table history obatalkespasien_r menjadi BILLING CANCEL(-), jika is_deleted=TRUE
                INSERT INTO obatalkespasien_r (     
                    obatalkespasien_id ,
                    sumberdana_id ,
                    racikan_id ,
                    returresepdetail_id ,
                    tipepaket_id ,
                    ruangan_id ,
                    carabayar_id ,
                    pegawai_id ,
                    daftartindakan_id ,
                    tindakanpelayanan_id ,
                    satuankecil_id ,
                    shift_id ,
                    pendaftaran_id ,
                    obatalkes_id ,
                    pasien_id ,
                    penjamin_id ,
                    kelaspelayanan_id ,
                    pasienanastesi_id ,
                    pasienmasukpenunjang_id ,
                    pasienadmisi_id ,
                    obatsudahbayar_id ,
                    penjualanresep_id ,
                    tglpelayanan ,
                    r ,
                    rke ,
                    permintaan_oa ,
                    jmlkemasan_oa ,
                    kekuatan_oa ,
                    satuankekuatan_oa ,
                    qty_oa ,
                    hargasatuan_oa ,
                    signa_oa ,
                    harganetto_oa ,
                    hargajual_oa , 
                    etiket ,
                    jmlexposerad ,
                    kontrasrad ,
                    biayaservice ,
                    biayakonseling ,
                    jasadokterresep ,
                    biayakemasan ,
                    biayaadministrasi ,
                    tarifcyto ,
                    discount , 
                    subsidiasuransi ,
                    subsidipemerintah ,
                    subsidirs ,
                    iurbiaya ,
                    oa ,
                    pembulatan ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    permohonanoadetail_id ,
                    persenppnjual ,
                    resepturdetail_id ,
                    nilaippnjual ,
                    perawat1_id ,
                    perawat2_id ,
                    instruksitindakanbmhp_id ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_jurnal ,
                    konfigmargindetail_id ,
                    qty_konversi ,
                    is_penatajasa ,
                    det ,
                    status_bmhp ,
                    det_konversi ,
                    signa ,
                    additional_data ,
                    created_date ,
                    created_by ,
                    modified_count ,
                    last_modified_date ,
                    last_modified_by ,
                    is_deleted ,
                    is_active ,
                    deleted_date , 
                    deleted_by ,
                    keterangan,
                                        no_obatalkespasien,
                                        tarif_dijamin,
                                        tarif_dibayarkan,
                                        tarif_diskon,
                                        pembayaran_id
                    )
                    SELECT
                    obatalkespasien_id ,
                    sumberdana_id ,
                    racikan_id ,
                    returresepdetail_id ,
                    tipepaket_id ,
                    ruangan_id ,
                                        CASE
                                                WHEN (OLD.additional_data::json->>'carabayar_id')::INTEGER IS NULL THEN carabayar_id
                                                ELSE (OLD.additional_data::json->>'carabayar_id')::INTEGER
                                        END, -- carabayar_id
                    pegawai_id ,
                    daftartindakan_id ,
                    tindakanpelayanan_id ,
                    satuankecil_id ,
                    shift_id ,
                    pendaftaran_id ,
                    obatalkes_id ,
                    pasien_id ,
                                        CASE
                                                WHEN (OLD.additional_data::json->>'penjamin_id')::INTEGER IS NULL THEN penjamin_id
                                                ELSE (OLD.additional_data::json->>'penjamin_id')::INTEGER
                                        END, -- penjamin_id
                    kelaspelayanan_id ,
                    pasienanastesi_id ,
                    pasienmasukpenunjang_id ,
                    pasienadmisi_id ,
                    NEW.obatsudahbayar_id ,
                    penjualanresep_id ,
                    v_tglbatal ,
                    r ,
                    rke ,
                    permintaan_oa ,
                    jmlkemasan_oa ,
                    kekuatan_oa ,
                    satuankekuatan_oa ,
                    -1 * qty_oa ,
                    hargasatuan_oa ,
                    signa_oa ,
                    harganetto_oa ,
                    -1 * hargajual_oa,  
                    etiket ,
                    jmlexposerad ,
                    kontrasrad ,
                    biayaservice ,
                    biayakonseling ,
                    jasadokterresep ,
                    biayakemasan ,
                    biayaadministrasi ,
                    tarifcyto ,
                    discount , 
                    subsidiasuransi ,
                    subsidipemerintah ,
                    subsidirs ,
                    iurbiaya ,
                    oa ,
                    pembulatan ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    permohonanoadetail_id ,
                    persenppnjual ,
                    resepturdetail_id ,
                    nilaippnjual ,
                    perawat1_id ,
                    perawat2_id ,
                    instruksitindakanbmhp_id ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_jurnal ,
                    konfigmargindetail_id ,
                    -1 * qty_konversi ,
                    is_penatajasa ,
                    -1 * det ,
                    status_bmhp ,
                    -1 * det_konversi ,
                    signa ,
                    additional_data ,
                    created_date ,
                    created_by ,
                    modified_count ,
                    last_modified_date ,
                    last_modified_by ,
                    is_deleted ,
                    is_active ,
                    deleted_date , 
                    deleted_by ,
                    'BILLING CANCEL',
                                        no_obatalkespasien,
                                        -1 * ((OLD.additional_data::json->>'data_dijamin')::json->>'dijamin')::float,
                                        -1 * ((OLD.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float, 
                                        -1 * (OLD.additional_data::json->>'tarif_diskon')::float,
                                        OLD.pembayaran_id 
                    FROM obatalkespasien_t
                WHERE obatalkespasien_id = NEW.obatalkespasien_id;
                
    END IF;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_delete\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ DECLARE v_daftartindakan_id int;
v_ruangan_id int;
v_penjamin_id int;
v_instalasi_id int;
v_pegawai_id int;
v_pasienadmisi_id int;
v_kelaspelayanan_id int;
v_total_diskon float;
v_total_discountpembayaran float;
v_totaltunai float;
v_totaldijamin float;
v_adm_dijamin float;
v_adm_dibayar float;
v_total_dibayar float;
v_total_sisatagihan float;
v_total_kembalian float;
v_total_administrasi float; 
v_total_pembulatan float; 
v_total_pembebasan float; 
v_penggunaan_uangmuka float; 
v_pemberianpiutang_id float; 
v_total_ditagihkan float; 
v_total_tunai float; 
v_total_discount float; 
v_total_tagihan float; 
v_catatan text;
v_disc_adm float;
BEGIN 


----------------------------- SETUP META DATA PEMBAYARAN -----------------------------
  SELECT 
    pembayaran_t.total_dijamin,
    pembayaran_t.total_discount,
    pembayaran_t.total_discountpembayaran,
    (
      (
        pembayaran_t.additional_data :: json ->> 'adm_asuransi'
      ):: json ->> 'dijamin'
    ):: float,
    (
      (
        pembayaran_t.additional_data :: json ->> 'adm_asuransi'
      ):: json ->> 'harusbayar'
    ):: float,
    pembayaran_t.pasienadmisi_id,
    pembayaran_t.total_tagihan,
    pembayaran_t.total_dibayar,
    pembayaran_t.total_sisatagihan,
    pembayaran_t.total_kembalian,
    pembayaran_t.total_administrasi, 
    pembayaran_t.total_pembulatan, 
    pembayaran_t.total_pembebasan, 
    pembayaran_t.penggunaan_uangmuka, 
    pembayaran_t.pemberianpiutang_id, 
    pembayaran_t.total_ditagihkan, 
    pembayaran_t.total_tunai, 
    pembayaran_t.total_discount, 
    pembayaran_t.catatan,
         (
      (
        pembayaran_t.additional_data :: json ->> 'adm_asuransi'
      ):: json ->> 'nominal_diskon'
    ):: float
  INTO 
    v_totaldijamin,
    v_total_diskon,
    v_total_discountpembayaran,
    v_adm_dijamin,
    v_adm_dibayar,
    v_pasienadmisi_id,
    v_total_tagihan,
    v_total_dibayar,
    v_total_sisatagihan,
    v_total_kembalian,
    v_total_administrasi, 
    v_total_pembulatan, 
    v_total_pembebasan, 
    v_penggunaan_uangmuka, 
    v_pemberianpiutang_id, 
    v_total_ditagihkan, 
    v_total_tunai, 
    v_total_discount, 
    v_catatan,
        v_disc_adm
  FROM 
    pembayaran_t 
  WHERE 
    pembayaran_t.pembayaran_id = NEW.pembayaran_id;

------------------------- END SETUP ----------------------------------------------------------


---------------------------------- INSERT PEMBAYARAN TUNAI --------------------------------------
IF(
  NEW.is_deleted IS TRUE 
  and v_total_tunai <> 0
) THEN -- INSERT table history pembayaran_r, menjadi Deposit Refund
INSERT INTO pembayaran_r (
  pembayaran_id, 
  pendaftaran_id, 
  pasienadmisi_id, 
  total_tagihan, 
  total_dibayar, 
  total_dijamin, 
  total_sisatagihan, 
  total_kembalian, 
  total_administrasi, 
  total_pembulatan, 
  total_pembebasan, 
  penggunaan_uangmuka, 
  pemberianpiutang_id, 
  total_ditagihkan, 
  total_tunai, 
  total_nontunai, 
  total_discount, 
  total_discountpembayaran, 
  catatan, 
  tipe_pembayaran, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  keterangan
) VALUES  (
  NEW.pembayaran_id,
  NEW.pendaftaran_id,
  v_pasienadmisi_id,
  -1 * v_total_tagihan, 
  -1 * v_total_dibayar, 
  0, 
  -1 * v_total_sisatagihan, 
  -1 * v_total_kembalian, 
  -1 * v_total_administrasi, 
  -1 * v_total_pembulatan, 
  -1 * v_total_pembebasan, 
  v_penggunaan_uangmuka, 
  v_pemberianpiutang_id, 
  -1 * v_total_ditagihkan, 
  -1 * v_total_tunai, 
  0, 
  -1 * v_total_discount, 
  -1 * v_total_discountpembayaran, 
  v_catatan, 
  682, 
  NEW.additional_data, 
  NEW.created_date, 
  NEW.created_by, 
  NEW.modified_count, 
  NEW.last_modified_date, 
  NEW.last_modified_by, 
  NEW.is_deleted, 
  NEW.is_active, 
  NEW.deleted_date, 
  NEW.deleted_by, 
  'REFUND'
);

ELSEIF(
  NEW.is_deleted IS TRUE 
  and v_totaldijamin <> 0
) THEN -- INSERT table history pembayaran_r, menjadi Deposit Refund
INSERT INTO pembayaran_r (
  pembayaran_id, 
  pendaftaran_id, 
  pasienadmisi_id, 
  total_tagihan, 
  total_dibayar, 
  total_dijamin, 
  total_sisatagihan, 
  total_kembalian, 
  total_administrasi, 
  total_pembulatan, 
  total_pembebasan, 
  penggunaan_uangmuka, 
  pemberianpiutang_id, 
  total_ditagihkan, 
  total_tunai, 
  total_nontunai, 
  total_discount, 
  total_discountpembayaran, 
  catatan, 
  tipe_pembayaran, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  keterangan
) VALUES  (
  NEW.pembayaran_id,
  NEW.pendaftaran_id,
  v_pasienadmisi_id,
  -1 * v_total_tagihan, 
  -1 * v_total_dibayar, 
  0, 
  -1 * v_total_sisatagihan, 
  -1 * v_total_kembalian, 
  -1 * v_total_administrasi, 
  -1 * v_total_pembulatan, 
  -1 * v_total_pembebasan, 
  v_penggunaan_uangmuka, 
  v_pemberianpiutang_id, 
  -1 * v_total_ditagihkan, 
  -1 * v_total_tunai, 
  0, 
  -1 * v_total_discount, 
  -1 * v_total_discountpembayaran, 
  v_catatan, 
  682, 
  NEW.additional_data, 
  NEW.created_date, 
  NEW.created_by, 
  NEW.modified_count, 
  NEW.last_modified_date, 
  NEW.last_modified_by, 
  NEW.is_deleted, 
  NEW.is_active, 
  NEW.deleted_date, 
  NEW.deleted_by, 
  'REFUND'
);

END IF;
------------------------- END INSERT -----------------------------------------------------------------------


-------------------------------- SETUP METADATA PENDAFTARAN -----------------------------------------------
IF (NEW.is_deleted IS TRUE AND (
    v_total_pembulatan <> 0 OR 
    (COALESCE(v_total_diskon, 0) + COALESCE(v_total_discountpembayaran, 0) <> 0) OR 
    v_total_administrasi <> 0 )) THEN
  IF (v_pasienadmisi_id IS NOT NULL) THEN
    SELECT 
        ruangan_id, 
        penjamin_id, 
        3 as instalasi_id, 
        pegawai_id, 
        kelaspelayanan_id 
    INTO 
        v_ruangan_id, 
        v_penjamin_id, 
        v_instalasi_id, 
        v_pegawai_id, 
        v_kelaspelayanan_id 
    FROM 
      pasienadmisi_t 
    WHERE 
      pasienadmisi_t.pasienadmisi_id = v_pasienadmisi_id;
  ELSE 
    SELECT 
      pendaftaran_t.ruangan_id, 
      pendaftaran_t.penjamin_id, 
      pendaftaran_t.instalasi_id, 
      pendaftaran_t.pegawai_id,
      kelaspelayanan_id 
    INTO 
      v_ruangan_id, 
      v_penjamin_id, 
      v_instalasi_id, 
      v_pegawai_id,
      v_kelaspelayanan_id 
    FROM 
      pendaftaran_t 
    WHERE 
      pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
  END IF;
END IF;

-------------------------------- END SETUP ----------------------------------------------------------------


----------------------------> insert pembulatan ke tindakanpelayanan_r <---------------------------------   

IF(
  NEW.is_deleted IS TRUE 
  and v_total_pembulatan <> 0
) THEN 


v_daftartindakan_id := 99990;
-- daftartindakan_id untuk pembulatan
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, 
  daftartindakan_id, 
  tarif_satuan, 
  tarif_tindakan, 
  qty_tindakan, 
  keterangan, 
  ruangan_id, 
  instalasi_id, 
  penjamin_id, 
  dokterpenanggungjawab_id, 
  tgl_tindakan, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  tgl_proses, 
  pembayaran_id, 
  tarif_diskon, 
  tarif_dijamin, 
  tarif_dibayarkan,
  kelaspelayanan_id
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    v_total_pembulatan, 
    -1 * v_total_pembulatan, 
    '-1', 
    'BILLING CANCEL', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_pegawai_id, 
    NEW.deleted_date, 
    NEW.additional_data, 
    NEW.created_date, 
    NEW.created_by, 
    NEW.modified_count, 
    NEW.last_modified_date, 
    NEW.last_modified_by, 
    NEW.is_deleted, 
    NEW.is_active, 
    NEW.deleted_date, 
    NEW.deleted_by, 
    NEW.deleted_date, 
    NEW.pembayaran_id, 
    0, 
    0, 
    --tarif_dijamin
    -1 * v_total_pembulatan,
    v_kelaspelayanan_id
    );
END IF;


---------------------------> insert diskon ke tindakanpelayanan_r <-------------------------
IF(
  NEW.is_deleted IS TRUE 
  and (
    COALESCE(v_total_diskon, 0) + COALESCE(v_total_discountpembayaran, 0) <> 0
  )
) THEN 

v_daftartindakan_id := 99991;
-- daftartindakan_id untuk diskon
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, 
  daftartindakan_id, 
  tarif_satuan, 
  tarif_tindakan, 
  qty_tindakan, 
  keterangan, 
  ruangan_id, 
  instalasi_id, 
  penjamin_id, 
  dokterpenanggungjawab_id, 
  tgl_tindakan, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  tgl_proses, 
  pembayaran_id, 
  tarif_diskon, 
  tarif_dijamin, 
  tarif_dibayarkan,
  kelaspelayanan_id
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    COALESCE(-1 * v_total_diskon, 0)+ COALESCE(
      -1 * v_total_discountpembayaran, 0
    ), 
    COALESCE(v_total_diskon, 0)+ COALESCE(v_total_discountpembayaran, 0), 
    '-1', 
    'DISCOUNT CANCEL', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_pegawai_id, 
    NEW.deleted_date, 
    NEW.additional_data, 
    NEW.created_date, 
    NEW.created_by, 
    NEW.modified_count, 
    NEW.last_modified_date, 
    NEW.last_modified_by, 
    NEW.is_deleted, 
    NEW.is_active, 
    NEW.deleted_date, 
    NEW.deleted_by, 
    NEW.deleted_date, 
    NEW.pembayaran_id, 
    0, 
    CASE WHEN v_totaldijamin <> 0 THEN COALESCE(v_total_diskon, 0)+ COALESCE(v_total_discountpembayaran, 0) ELSE 0 END, 
    CASE WHEN v_totaldijamin = 0 THEN COALESCE(v_total_diskon, 0)+ COALESCE(v_total_discountpembayaran, 0) ELSE 0 END,
    v_kelaspelayanan_id
    );
END IF;


-------------------------------------> insert administrasi ke tindakanpelayanan_r <--------------------------------------------
IF(
  NEW.is_deleted IS TRUE 
  and v_total_administrasi <> 0
) THEN 

v_daftartindakan_id := 99992;
-- daftartindakan_id untuk administrasi
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, 
  daftartindakan_id, 
  tarif_satuan, 
  tarif_tindakan, 
  qty_tindakan, 
  keterangan, 
  ruangan_id, 
  instalasi_id, 
  penjamin_id, 
  dokterpenanggungjawab_id, 
  tgl_tindakan, 
  additional_data, 
  created_date, 
  created_by, 
  modified_count, 
  last_modified_date, 
  last_modified_by, 
  is_deleted, 
  is_active, 
  deleted_date, 
  deleted_by, 
  tgl_proses, 
  pembayaran_id, 
  tarif_diskon, 
  tarif_dijamin, 
  tarif_dibayarkan,
  kelaspelayanan_id
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    v_total_administrasi, 
    -1 * v_total_administrasi, 
    '-1', 
    'BILLING CANCEL', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_pegawai_id, 
    NEW.deleted_date, 
    NEW.additional_data, 
    NEW.created_date, 
    NEW.created_by, 
    NEW.modified_count, 
    NEW.last_modified_date, 
    NEW.last_modified_by, 
    NEW.is_deleted, 
    NEW.is_active, 
    NEW.deleted_date, 
    NEW.deleted_by, 
    NEW.deleted_date, 
    NEW.pembayaran_id, 
    -1 * v_disc_adm, -- diskon, 
    -1 * v_adm_dijamin, 
    -1 * v_adm_dibayar,
    v_kelaspelayanan_id
    );
END IF;
RETURN NEW;
END \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembulatan_diskon_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ DECLARE v_daftartindakan_id int;
v_ruangan_id int;
v_penjamin_id int;
v_instalasi_id int;
v_pegawai_id int;
v_kelaspelayanan_id int;
BEGIN

---------------------------------- SETUP META DATA TRANSAKSI --------------------------------------------------------
IF (NEW.total_pembulatan <> 0 OR 
    COALESCE(NEW.total_discount, 0) + COALESCE(NEW.total_discountpembayaran, 0) <> 0 OR
    NEW.total_administrasi <> 0) THEN
    
    IF (NEW.pasienadmisi_id IS NOT NULL) THEN
        SELECT 
            ruangan_id, 
            penjamin_id, 
            3 as instalasi_id, 
            pegawai_id, 
            kelaspelayanan_id 
        INTO 
            v_ruangan_id, 
            v_penjamin_id, 
            v_instalasi_id, 
            v_pegawai_id, 
            v_kelaspelayanan_id 
        FROM 
          pasienadmisi_t 
        WHERE 
          pasienadmisi_t.pasienadmisi_id = NEW.pasienadmisi_id;
    ELSE 
        SELECT 
            ruangan_id, 
            penjamin_id, 
            instalasi_id, 
            pegawai_id, 
           kelaspelayanan_id 
        INTO 
          v_ruangan_id, 
          v_penjamin_id, 
          v_instalasi_id, 
          v_pegawai_id, 
          v_kelaspelayanan_id 
        FROM 
          pendaftaran_t 
        WHERE 
          pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
    END IF;
END IF;

 --------------------------> insert pembulatan ke tindakanpelayanan_r <--------------------------------
IF(NEW.total_pembulatan <> 0) THEN 
v_daftartindakan_id := 99990;
-- daftartindakan_id untuk pembulatan
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, daftartindakan_id, 
  tarif_satuan, tarif_tindakan, qty_tindakan, 
  keterangan, ruangan_id, instalasi_id, 
  penjamin_id, kelaspelayanan_id, 
  dokterpenanggungjawab_id, tgl_tindakan, 
  additional_data, created_date, created_by, 
  modified_count, last_modified_date, 
  last_modified_by, is_deleted, is_active, 
  deleted_date, deleted_by, tgl_proses, 
  pembayaran_id, tarif_diskon, tarif_dijamin, 
  tarif_dibayarkan
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    NEW.total_pembulatan, 
    NEW.total_pembulatan, 
    '1', 
    'BILLING', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_kelaspelayanan_id, 
    v_pegawai_id, 
    new.created_date, 
    new.additional_data, 
    new.created_date, 
    new.created_by, 
    new.modified_count, 
    new.last_modified_date, 
    new.last_modified_by, 
    new.is_deleted, 
    new.is_active, 
    new.deleted_date, 
    new.deleted_by, 
    new.created_date, 
    new.pembayaran_id, 
    0, 
        0, --tarif_dijamin
    NEW.total_pembulatan -- tarif_dibayarkan             
    );
END IF;
-------------------------> insert diskon ke tindakanpelayanan_r <----------------------------------
IF(
  COALESCE(NEW.total_discount, 0) + COALESCE(NEW.total_discountpembayaran, 0) <> 0
) THEN 
v_daftartindakan_id := 99991;
-- daftartindakan_id untuk diskon
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, daftartindakan_id, 
  tarif_satuan, tarif_tindakan, qty_tindakan, 
  keterangan, ruangan_id, instalasi_id, 
  penjamin_id, kelaspelayanan_id, 
  dokterpenanggungjawab_id, tgl_tindakan, 
  additional_data, created_date, created_by, 
  modified_count, last_modified_date, 
  last_modified_by, is_deleted, is_active, 
  deleted_date, deleted_by, tgl_proses, 
  pembayaran_id, tarif_diskon, tarif_dijamin, 
  tarif_dibayarkan
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    COALESCE(-1 * NEW.total_discount, 0) + COALESCE(
      -1 * NEW.total_discountpembayaran, 
      0
    ), 
    COALESCE(-1 * NEW.total_discount, 0) + COALESCE(
      -1 * NEW.total_discountpembayaran, 
      0
    ), 
    '1', 
    'DISCOUNT', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_kelaspelayanan_id, 
    v_pegawai_id, 
    new.created_date, 
    new.additional_data, 
    new.created_date, 
    new.created_by, 
    new.modified_count, 
    new.last_modified_date, 
    new.last_modified_by, 
    new.is_deleted, 
    new.is_active, 
    new.deleted_date, 
    new.deleted_by, 
    new.created_date, 
    new.pembayaran_id, 
    0, 
    CASE WHEN NEW.total_dijamin <> 0 THEN COALESCE(-1 * NEW.total_discount, 0) + COALESCE(
      -1 * NEW.total_discountpembayaran, 
      0
    ) ELSE 0 END, 
    --tarif_dijamin
    CASE WHEN NEW.total_dijamin = 0 THEN COALESCE(-1 * NEW.total_discount, 0) + COALESCE(
      -1 * NEW.total_discountpembayaran, 
      0
    ) ELSE 0 END -- tarif_dibayarkan
    );
END IF;
---------------------------------> insert administrasi ke tindakanpelayanan_r <-------------------------------------
IF(NEW.total_administrasi <> 0) THEN 
v_daftartindakan_id := 99992;
-- daftartindakan_id untuk administrasi
INSERT INTO tindakanpelayanan_r (
  pendaftaran_id, daftartindakan_id, 
  tarif_satuan, tarif_tindakan, qty_tindakan, 
  keterangan, ruangan_id, instalasi_id, 
  penjamin_id, kelaspelayanan_id, 
  dokterpenanggungjawab_id, tgl_tindakan, 
  additional_data, created_date, created_by, 
  modified_count, last_modified_date, 
  last_modified_by, is_deleted, is_active, 
  deleted_date, deleted_by, tgl_proses, 
  pembayaran_id, tarif_diskon, tarif_dijamin, 
  tarif_dibayarkan
) 
VALUES 
  (
    NEW.pendaftaran_id, 
    v_daftartindakan_id, 
    NEW.total_administrasi, 
    NEW.total_administrasi, 
    '1', 
    'BILLING', 
    v_ruangan_id, 
    v_instalasi_id, 
    v_penjamin_id, 
    v_kelaspelayanan_id, 
    v_pegawai_id, 
    new.created_date, 
    new.additional_data, 
    new.created_date, 
    new.created_by, 
    new.modified_count, 
    new.last_modified_date, 
    new.last_modified_by, 
    new.is_deleted, 
    new.is_active, 
    new.deleted_date, 
    new.deleted_by, 
    new.created_date, 
    new.pembayaran_id, 
    (
      (
        NEW.additional_data :: json ->> 'adm_asuransi'
      ):: json ->> 'nominal_diskon'
    ):: float, -- diskon
    (
      (
        NEW.additional_data :: json ->> 'adm_asuransi'
      ):: json ->> 'dijamin'
    ):: float, --tarif_dijamin
    (
      (
        NEW.additional_data :: json ->> 'adm_asuransi'
      ):: json ->> 'harusbayar'
    ):: float --tarif_dibayarkan
    );
END IF;
RETURN NEW;
END \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"tindakansudahbayar_t_cancel\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
DECLARE
        v_keterangan VARCHAR;
                v_tglbatal TIMESTAMP;
                
BEGIN
            SELECT
                deleted_date
                INTO 
                v_tglbatal
            FROM pembayaranpelayanan_t
            WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
            
        IF(NEW.is_deleted IS TRUE)
        THEN
            -- INSERT table history tindakanpelayanan_r menjadi ACCRUAL(+), jika is_deleted=TRUE
            INSERT INTO tindakanpelayanan_r (       
                tindakanpelayanan_id ,
                shift_id ,
                kelaspelayanan_id ,
                kelastanggungan_id ,
                pasien_id ,
                rencanaoperasi_id ,
                instalasi_id ,
                daftartindakan_id ,
                alatmedis_id ,
                tipepaket_id ,
                tindakansudahbayar_id ,
                carabayar_id ,
                pendaftaran_id ,
                hasilpemeriksaanrad_id ,
                jeniskasuspenyakit_id ,
                hasilpemeriksaanrm_id ,
                ruangan_id ,
                konsulpoli_id ,
                pasienmasukpenunjang_id ,
                hasilpemeriksaanlabdetail_id ,
                penjamin_id ,
                pasienadmisi_id ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                instruksitindakan_id ,
                tgl_tindakan ,
                tarif_rsakomodasi ,
                tarif_medis ,
                tarif_paramedis ,
                tarif_bhp ,
                tarif_satuan ,
                tarif_tindakan ,
                tarifcyto_tindakan ,
                satuan_tindakan ,
                qty_tindakan ,
                cyto_tindakan ,
                dokterpenanggungjawab_id ,
                dokterpelaksana_id ,
                dokteranastesi_id ,
                dokterdelegasi_id ,
                bidan1_id ,
                bidan2_id ,
                perawat1_id ,
                perawat2_id ,
                discount_tindakan ,
                pembebasan_tindakan ,
                subsidiasuransi_tindakan ,
                subsidipemerintah_tindakan ,
                subsisidirumahsakit_tindakan ,
                uangditerima_tindakan ,
                keterangantindakan ,
                pembulatan ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_penatajasa ,
                additional_riwayat ,
                is_valid ,
                kamarruangan_id ,
                kamartempattidur_id ,
                penyulit_tindakan ,
                tarifpenyulit_tindakan ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date ,
                deleted_by ,
                keterangan,
                no_tindakanpelayanan,
                                tarif_dijamin,
                                tarif_dibayarkan,
                                tarif_diskon,
                                pembayaran_id
                )
                SELECT
                tindakanpelayanan_id ,
                shift_id ,
                kelaspelayanan_id ,
                kelastanggungan_id ,
                pasien_id ,
                rencanaoperasi_id ,
                instalasi_id ,
                daftartindakan_id ,
                alatmedis_id ,
                tipepaket_id ,
                NULL,
                carabayar_id ,
                pendaftaran_id ,
                hasilpemeriksaanrad_id ,
                jeniskasuspenyakit_id ,
                hasilpemeriksaanrm_id ,
                ruangan_id ,
                konsulpoli_id ,
                pasienmasukpenunjang_id ,
                hasilpemeriksaanlabdetail_id ,
                penjamin_id ,
                pasienadmisi_id ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                instruksitindakan_id ,
                tgl_tindakan ,
                tarif_rsakomodasi ,
                tarif_medis ,
                tarif_paramedis ,
                tarif_bhp ,
                tarif_satuan ,
                tarif_tindakan ,
                tarifcyto_tindakan ,
                satuan_tindakan ,
                qty_tindakan ,
                cyto_tindakan ,
                dokterpenanggungjawab_id ,
                dokterpelaksana_id ,
                dokteranastesi_id ,
                dokterdelegasi_id ,
                bidan1_id ,
                bidan2_id ,
                perawat1_id ,
                perawat2_id ,
                discount_tindakan ,
                pembebasan_tindakan ,
                subsidiasuransi_tindakan ,
                subsidipemerintah_tindakan ,
                subsisidirumahsakit_tindakan ,
                uangditerima_tindakan ,
                keterangantindakan ,
                pembulatan ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_penatajasa ,
                additional_riwayat ,
                is_valid ,
                kamarruangan_id ,
                kamartempattidur_id ,
                penyulit_tindakan ,
                tarifpenyulit_tindakan ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date ,
                deleted_by ,
                'ACCRUAL',
                no_tindakanpelayanan,
                                0,
                                0,
                                0,
                                NULL
                FROM tindakanpelayanan_t
            WHERE tindakanpelayanan_id = NEW.tindakanpelayanan_id;
        
        
            -- INSERT table history tindakanpelayanan_r menjadi BILLING CANCEL(-), jika is_deleted=TRUE
                INSERT INTO tindakanpelayanan_r (       
                    tindakanpelayanan_id ,
                    shift_id ,
                    kelaspelayanan_id ,
                    kelastanggungan_id ,
                    pasien_id ,
                    rencanaoperasi_id ,
                    instalasi_id ,
                    daftartindakan_id ,
                    alatmedis_id ,
                    tipepaket_id ,
                    tindakansudahbayar_id ,
                    carabayar_id ,
                    pendaftaran_id ,
                    hasilpemeriksaanrad_id ,
                    jeniskasuspenyakit_id ,
                    hasilpemeriksaanrm_id ,
                    ruangan_id ,
                    konsulpoli_id ,
                    pasienmasukpenunjang_id ,
                    hasilpemeriksaanlabdetail_id ,
                    penjamin_id ,
                    pasienadmisi_id ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    instruksitindakan_id ,
                    tgl_tindakan ,
                    tarif_rsakomodasi ,
                    tarif_medis ,
                    tarif_paramedis ,
                    tarif_bhp ,
                    tarif_satuan ,
                    tarif_tindakan ,
                    tarifcyto_tindakan ,
                    satuan_tindakan ,
                    qty_tindakan ,
                    cyto_tindakan ,
                    dokterpenanggungjawab_id ,
                    dokterpelaksana_id ,
                    dokteranastesi_id ,
                    dokterdelegasi_id ,
                    bidan1_id ,
                    bidan2_id ,
                    perawat1_id ,
                    perawat2_id ,
                    discount_tindakan ,
                    pembebasan_tindakan ,
                    subsidiasuransi_tindakan ,
                    subsidipemerintah_tindakan ,
                    subsisidirumahsakit_tindakan ,
                    uangditerima_tindakan ,
                    keterangantindakan ,
                    pembulatan ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_penatajasa ,
                    additional_riwayat ,
                    is_valid ,
                    kamarruangan_id ,
                    kamartempattidur_id ,
                    penyulit_tindakan ,
                    tarifpenyulit_tindakan ,
                    additional_data ,
                    created_date ,
                    created_by ,
                    modified_count ,
                    last_modified_date ,
                    last_modified_by ,
                    is_deleted ,
                    is_active ,
                    deleted_date ,
                    deleted_by ,
                    keterangan,
                    no_tindakanpelayanan,
                                        tarif_dijamin,
                                        tarif_dibayarkan,
                                        tarif_diskon,
                                        pembayaran_id
                    )
                    SELECT
                    tindakanpelayanan_id ,
                    shift_id ,
                    kelaspelayanan_id ,
                    kelastanggungan_id ,
                    pasien_id ,
                    rencanaoperasi_id ,
                    instalasi_id ,
                    daftartindakan_id ,
                    alatmedis_id ,
                    tipepaket_id ,
                    NEW.tindakansudahbayar_id ,
                                        CASE
                                                WHEN (OLD.additional_data::json->>'carabayar_id')::INTEGER IS NULL THEN carabayar_id
                                                ELSE (OLD.additional_data::json->>'carabayar_id')::INTEGER
                                        END, -- carabayar_id
                    pendaftaran_id ,
                    hasilpemeriksaanrad_id ,
                    jeniskasuspenyakit_id ,
                    hasilpemeriksaanrm_id ,
                    ruangan_id ,
                    konsulpoli_id ,
                    pasienmasukpenunjang_id ,
                    hasilpemeriksaanlabdetail_id ,
                                        CASE
                                                WHEN (NEW.additional_data::json->>'penjamin_id')::INTEGER IS NULL THEN penjamin_id
                                                ELSE (NEW.additional_data::json->>'penjamin_id')::INTEGER
                                        END, -- penjamin_id
                    pasienadmisi_id ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    instruksitindakan_id ,
                    v_tglbatal ,
                    tarif_rsakomodasi ,
                    tarif_medis ,
                    tarif_paramedis ,
                    tarif_bhp ,
                    tarif_satuan ,
                    -1 * tarif_tindakan ,
                    tarifcyto_tindakan ,
                    satuan_tindakan ,
                    -1 * qty_tindakan ,
                    cyto_tindakan ,
                    dokterpenanggungjawab_id ,
                    dokterpelaksana_id ,
                    dokteranastesi_id ,
                    dokterdelegasi_id ,
                    bidan1_id ,
                    bidan2_id ,
                    perawat1_id ,
                    perawat2_id ,
                    discount_tindakan ,
                    pembebasan_tindakan ,
                    subsidiasuransi_tindakan ,
                    subsidipemerintah_tindakan ,
                    subsisidirumahsakit_tindakan ,
                    uangditerima_tindakan ,
                    keterangantindakan ,
                    pembulatan ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_penatajasa ,
                    additional_riwayat ,
                    is_valid ,
                    kamarruangan_id ,
                    kamartempattidur_id ,
                    penyulit_tindakan ,
                    tarifpenyulit_tindakan ,
                    additional_data ,
                    created_date ,
                    created_by ,
                    modified_count ,
                    last_modified_date ,
                    last_modified_by ,
                    is_deleted ,
                    is_active ,
                    deleted_date ,
                    deleted_by ,
                    'BILLING CANCEL',
                    no_tindakanpelayanan,
                                        -1 * ((OLD.additional_data::json->>'data_dijamin')::json->>'dijamin')::float,
                                        -1 * ((OLD.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float,
                                        -1 * (OLD.additional_data::json->>'tarif_diskon')::float,
                                        OLD.pembayaran_id 
                    FROM tindakanpelayanan_t
                WHERE tindakanpelayanan_id = NEW.tindakanpelayanan_id;
                
    END IF;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210820_022552_migrate_odoo_resepkaryawan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210820_022552_migrate_odoo_resepkaryawan cannot be reverted.\n";

        return false;
    }
    */
}
