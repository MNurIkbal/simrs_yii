<?php

use yii\db\Migration;

/**
 * Class m210221_080315_migrate_20210221_penyesuaianview
 */
class m210221_080315_migrate_20210221_penyesuaianview extends Migration
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
        END AS status_proses
   FROM pembayaran_r
     JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.no_pendaftaran
           FROM pendaftaran_r pendaftaran_r_1
          WHERE pendaftaran_r_1.keterangan::text = 'INSERT'::text AND pendaftaran_r_1.is_sent = true) pendaftaran_r ON pembayaran_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     LEFT JOIN loginpemakai_k ON pembayaran_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN loginpemakai_k deleted_by ON pembayaran_r.deleted_by = deleted_by.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON deleted_by.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ruangan_m kasir ON pembayaranpelayanan_t.ruangan_id = kasir.ruangan_id
  WHERE pembayaran_r.total_dijamin <= 0::double precision AND pembayaran_r.is_update = true
UNION ALL
 SELECT concat('BYR', pembayaran_r.id) AS sync_id_api,
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
        END AS status_proses
   FROM pembayaran_r
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     JOIN ( SELECT penjualanresep_r_1.penjualanresep_id,
            penjualanresep_r_1.noresep
           FROM penjualanresep_r penjualanresep_r_1
          WHERE penjualanresep_r_1.jenispenjualan::text = '343'::text AND penjualanresep_r_1.keterangan::text = 'ACCRUAL'::text AND penjualanresep_r_1.is_sent = true) penjualanresep_r ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_r.penjualanresep_id
     LEFT JOIN loginpemakai_k ON pembayaran_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN loginpemakai_k deleted_by ON pembayaran_r.deleted_by = deleted_by.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON deleted_by.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ruangan_m kasir ON pembayaranpelayanan_t.ruangan_id = kasir.ruangan_id
  WHERE pembayaran_r.total_dijamin <= 0::double precision AND pembayaran_r.is_update = true
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
        END AS status_proses
   FROM bayaruangmuka_r
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
        END AS status_proses
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

        $this->execute('DROP VIEW if exists "public"."int_saleorderupdate_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_saleorderupdate_v\" AS  SELECT pembayaran_r.id,
    pendaftaran_t.pendaftaran_id::character varying AS sync_id_api,
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
            ELSE COALESCE(pasienadmisi_t.penjamin_id, 0)
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
    'done'::text AS state,
    pembayaran.total_tunai + pembayaran.total_nontunai - pembayaran.total_kembalian AS personal_amount,
    pembayaran.total_dijamin AS payer_amount,
    pembayaran.total_tagihan AS total_amount,
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
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_kembalian, pembayaran_t.total_dijamin) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
  WHERE pembayaran_r.is_update = false
UNION ALL
 SELECT pembayaran_r.id,
    concat('RSPB', penjualanresep_t.penjualanresep_id) AS sync_id_api,
    penjualanresep_t.noresep AS name,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS confirmation_date,
    0 AS partner_id,
    penjualanresep_t.tglresep AS date_order,
    1 AS patient_type,
    COALESCE(penjualanresep_t.penjamin_id, 0) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), '-'::character varying) AS payer_type,
    6 AS sync_type,
    'done'::text AS state,
    pembayaran.total_tunai + pembayaran.total_nontunai - pembayaran.total_kembalian AS personal_amount,
    pembayaran.total_dijamin AS payer_amount,
    pembayaran.total_tagihan AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id AND pembayaranpelayanan_t.is_deleted = false
     JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.jenispenjualan::text = '343'::text
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
            pembayaran_t.total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_kembalian, pembayaran_t.total_dijamin) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
  WHERE pembayaran_r.is_update = false;");

        $this->execute('ALTER TABLE "public"."int_saleorderupdate_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_obatalkespasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_obatalkespasien_v\" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
    obatalkespasien_r.qty_oa AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa::integer AS price_unit,
    obatalkespasien_r.hargajual_oa::integer AS price_subtotal,
    obatalkespasien_r.hargajual_oa::integer AS price_total,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text AND obatalkespasien_t.tarif_dibayarkan <> 0::double precision THEN COALESCE(obatalkespasien_t.tarif_dibayarkan + obatalkespasien_t.tarif_diskon, 0::double precision)
            WHEN obatalkespasien_r.keterangan::text <> 'BILLING'::text AND obatalkespasien_t.tarif_dibayarkan <> 0::double precision THEN COALESCE(obatalkespasien_t.tarif_dibayarkan + obatalkespasien_t.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text AND obatalkespasien_t.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.hargajual_oa::integer::double precision - obatalkespasien_t.tarif_dibayarkan, 0::double precision)
            WHEN obatalkespasien_r.keterangan::text <> 'BILLING'::text AND obatalkespasien_t.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.hargajual_oa::integer::double precision - obatalkespasien_t.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS primary_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS prescribe_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS perform_doc_id,
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
    COALESCE(penjualanresep_t.no_resep, pendaftaran_r.no_pendaftaran) AS order_no,
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
    pendaftaran_r.nama_pasien
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
            pasienpulang_t.tglpasienpulang
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
    obatalkespasien_r.qty_oa AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa::integer AS price_unit,
    obatalkespasien_r.hargajual_oa::integer AS price_subtotal,
    obatalkespasien_r.hargajual_oa::integer AS price_total,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text AND obatalkespasien_t.tarif_dibayarkan <> 0::double precision THEN COALESCE(obatalkespasien_t.tarif_dibayarkan + obatalkespasien_t.tarif_diskon, 0::double precision)
            WHEN obatalkespasien_r.keterangan::text <> 'BILLING'::text AND obatalkespasien_t.tarif_dibayarkan <> 0::double precision THEN COALESCE(obatalkespasien_t.tarif_dibayarkan + obatalkespasien_t.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text AND obatalkespasien_t.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.hargajual_oa::integer::double precision - obatalkespasien_t.tarif_dibayarkan, 0::double precision)
            WHEN obatalkespasien_r.keterangan::text <> 'BILLING'::text AND obatalkespasien_t.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.hargajual_oa::integer::double precision - obatalkespasien_t.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    concat('RSPB', obatalkespasien_r.penjualanresep_id) AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS primary_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS prescribe_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS perform_doc_id,
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
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS account_analytic_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS backup_analytic_id,
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
    penjualanresep_r.nama_pasien
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
            penjualanresep_r_1.tglresep AS tglpasienpulang
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
    tindakanpelayanan_r.tarif_tindakan::integer AS price_subtotal,
    tindakanpelayanan_r.tarif_tindakan::integer AS price_total,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar <> 0::double precision AND pembayaran.total_dijamin = 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text AND tindakanpelayanan.tarif_dibayarkan <> 0::double precision THEN COALESCE(tindakanpelayanan.tarif_dibayarkan + tindakanpelayanan.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.keterangan::text <> 'BILLING'::text AND tindakanpelayanan.tarif_dibayarkan <> 0::double precision THEN COALESCE((- tindakanpelayanan.tarif_dibayarkan) + tindakanpelayanan.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar = 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar <> 0::double precision AND pembayaran.total_dijamin <> 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text AND tindakanpelayanan.tarif_dijamin <> 0::double precision THEN tindakanpelayanan_r.tarif_tindakan::integer::double precision - tindakanpelayanan.tarif_dibayarkan
            ELSE tindakanpelayanan_r.tarif_tindakan::integer::double precision - tindakanpelayanan.tarif_dibayarkan
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', daftartindakan_m.servicecategory_id) AS service_categ_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS primary_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS prescribe_doc_id,
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
            WHEN tindakanpelayanan_r.instalasi_id = 21 THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
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
        END AS status_proses
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
                    WHEN pendaftaran_r_1.pasienadmisi_id IS NULL THEN pendaftaran_r_1.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            pendaftaran_r_1.is_aps
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_r_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
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
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar <> 0::double precision AND pembayaran.total_dijamin = 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text AND tindakanpelayanan.tarif_dibayarkan <> 0::double precision THEN COALESCE(tindakanpelayanan.tarif_dibayarkan + tindakanpelayanan.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.keterangan::text <> 'BILLING'::text AND tindakanpelayanan.tarif_dibayarkan <> 0::double precision THEN COALESCE((- tindakanpelayanan.tarif_dibayarkan) + tindakanpelayanan.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar = 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar <> 0::double precision AND pembayaran.total_dijamin <> 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text AND tindakanpelayanan.tarif_dijamin <> 0::double precision THEN tindakanpelayanan_r.tarif_tindakan::integer::double precision - tindakanpelayanan.tarif_dibayarkan
            ELSE tindakanpelayanan_r.tarif_tindakan::integer::double precision - tindakanpelayanan.tarif_dibayarkan
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', 10) AS service_categ_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS primary_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS prescribe_doc_id,
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
            WHEN tindakanpelayanan_r.instalasi_id = 21 THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
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
    'others'::text AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    tindakanpelayanan_r.no_tindakanpelayanan AS order_no,
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
        END AS status_proses
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
                    WHEN pendaftaran_r_1.pasienadmisi_id IS NULL THEN pendaftaran_r_1.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            pendaftaran_r_1.is_aps
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_r_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
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
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar <> 0::double precision AND pembayaran.total_dijamin = 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text AND tindakanpelayanan.tarif_dibayarkan <> 0::double precision THEN COALESCE(tindakanpelayanan.tarif_dibayarkan + tindakanpelayanan.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.keterangan::text <> 'BILLING'::text AND tindakanpelayanan.tarif_dibayarkan <> 0::double precision THEN COALESCE((- tindakanpelayanan.tarif_dibayarkan) + tindakanpelayanan.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar = 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'DISCOUNT'::text AND pembayaran.total_dibayar <> 0::double precision AND pembayaran.total_dijamin <> 0::double precision THEN tindakanpelayanan_r.tarif_tindakan
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text AND tindakanpelayanan.tarif_dijamin <> 0::double precision THEN tindakanpelayanan_r.tarif_tindakan::integer::double precision - tindakanpelayanan.tarif_dibayarkan
            ELSE tindakanpelayanan_r.tarif_tindakan::integer::double precision - tindakanpelayanan.tarif_dibayarkan
        END AS payer_amount,
    concat('RSPB', penjualanresep.penjualanresep_id) AS order_id,
    concat('CATEG', daftartindakan_m.servicecategory_id) AS service_categ_id,
    concat('PEG', penjualanresep.pegawairesep_id) AS primary_doc_id,
    concat('PEG', penjualanresep.pegawairesep_id) AS prescribe_doc_id,
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
        END AS status_proses
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
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
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

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210221_080315_migrate_20210221_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210221_080315_migrate_20210221_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
