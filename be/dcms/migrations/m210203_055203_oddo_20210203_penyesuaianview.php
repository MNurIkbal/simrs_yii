<?php

use yii\db\Migration;

/**
 * Class m210203_055203_oddo_20210203_penyesuaianview
 */
class m210203_055203_oddo_20210203_penyesuaianview extends Migration
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
    'PEMBAYARAN'::text AS tipe_rekap,
        CASE
            WHEN pembayaran_r.is_sending = true AND pembayaran_r.is_sent = true THEN 'SUKSES'::text
            WHEN pembayaran_r.is_sending = false AND pembayaran_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pembayaran_r.is_sending = true AND pembayaran_r.is_sent = false AND pembayaran_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pembayaran_r.is_sending = false AND pembayaran_r.is_sent = false AND pembayaran_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
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
    'UANG_MUKA'::text AS tipe_rekap,
        CASE
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = true THEN 'SUKSES'::text
            WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses
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
    'PENGEMBALIAN_UANGMUKA'::text AS tipe_rekap,
        CASE
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
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = true THEN 'SUKSES'::text
            WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN bayaruangmuka_r.is_sending = true AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN bayaruangmuka_r.is_sending = false AND bayaruangmuka_r.is_sent = false AND bayaruangmuka_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
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

     $this->execute('DROP VIEW if exists "public"."int_obatalkespasien_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_obatalkespasien_v\" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
    obatalkespasien_r.qty_oa AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa AS price_unit,
    obatalkespasien_r.hargajual_oa AS price_subtotal,
    total_tagihan.total_tagihan AS price_total,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text THEN obatalkespasien_r.tarif_dibayarkan
            ELSE - obatalkespasien_r.tarif_dibayarkan
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text THEN obatalkespasien_r.tarif_dijamin
            ELSE - obatalkespasien_r.tarif_dijamin
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id AS order_id,
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
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id;");

     $this->execute('ALTER TABLE "public"."int_obatalkespasien_v" OWNER TO "postgres";');

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
    'BMHP'::text AS tipe_rekap
   FROM int_pendaftaranbmhp_r
     JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
            obatalkespasien_t_1.ruangan_id,
            obatalkespasien_t_1.tglpelayanan
           FROM obatalkespasien_t obatalkespasien_t_1
          WHERE obatalkespasien_t_1.is_deleted = false AND obatalkespasien_t_1.status_bmhp = 680
          GROUP BY obatalkespasien_t_1.pendaftaran_id, obatalkespasien_t_1.ruangan_id, obatalkespasien_t_1.tglpelayanan) obatalkespasien_t ON int_pendaftaranbmhp_r.pendaftaran_id = obatalkespasien_t.pendaftaran_id
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
    'RESEP'::text AS tipe_rekap
   FROM int_penjualanresep_r
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
    'RESEP_RACIKAN'::text AS tipe_rekap
   FROM int_penjualanresep_r
     JOIN ( SELECT penjualanresep_t.penjualanresep_id
           FROM penjualanresep_t
          WHERE penjualanresep_t.status_reseptur = 432 AND penjualanresep_t.pembatalanresep_id IS NOT NULL) penjualan_resep ON int_penjualanresep_r.penjualanresep_id = penjualan_resep.penjualanresep_id
  WHERE (EXISTS ( SELECT 1
           FROM stokobatalkes_t
             LEFT JOIN int_obatalkespasien_r ON int_obatalkespasien_r.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
          WHERE int_obatalkespasien_r.penjualanresep_id = int_penjualanresep_r.penjualanresep_id));");

     $this->execute('ALTER TABLE "public"."int_stockout_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_tindakan_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_tindakan_v\" AS  SELECT concat('TND', daftartindakan_m.daftartindakan_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    false AS purchase_ok,
    daftartindakan_m.daftartindakan_nama AS name,
    concat('TND', daftartindakan_m.kelompoktindakan_id) AS categ_id,
    351 AS uom_po_id,
    351 AS uom2_id,
    351 AS uom_id,
    daftartindakan_m.daftartindakan_kode AS default_code,
    'service'::text AS type,
        CASE
            WHEN daftartindakan_m.is_active IS TRUE AND daftartindakan_m.is_deleted IS TRUE THEN true
            WHEN daftartindakan_m.is_active IS TRUE AND daftartindakan_m.is_deleted IS FALSE THEN false
            WHEN daftartindakan_m.is_active IS FALSE AND daftartindakan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS strength,
    NULL::text AS catalog_code,
    NULL::text AS brand,
    NULL::text AS manufacturer_code,
    NULL::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    1 AS conversion_rate,
    6 AS sync_type,
    'TINDAKAN'::text AS jenis,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.additional_data
   FROM daftartindakan_m;");

     $this->execute('ALTER TABLE "public"."int_tindakan_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_paket_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_paket_v\" AS  SELECT concat('PKT', tipepaket_m.tipepaket_id) AS sync_id_api,
    tipepaket_m.is_active AS active,
    true AS sale_ok,
    false AS purchase_ok,
    tipepaket_m.tipepaket_nama AS name,
    concat('CATEG', 10) AS categ_id,
    351 AS uom_po_id,
    351 AS uom2_id,
    351 AS uom_id,
    tipepaket_m.tipepaket_kode AS default_code,
    'service'::text AS type,
        CASE
            WHEN tipepaket_m.is_active IS TRUE AND tipepaket_m.is_deleted IS TRUE THEN true
            WHEN tipepaket_m.is_active IS TRUE AND tipepaket_m.is_deleted IS FALSE THEN false
            WHEN tipepaket_m.is_active IS FALSE AND tipepaket_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    NULL::text AS strength,
    NULL::text AS catalog_code,
    NULL::text AS brand,
    NULL::text AS manufacturer_code,
    NULL::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    1 AS conversion_rate,
    6 AS sync_type,
    'PAKET'::text AS jenis,
    tipepaket_m.tipepaket_id,
    tipepaket_m.additional_data
   FROM tipepaket_m;");

     $this->execute('ALTER TABLE "public"."int_paket_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_penjamin_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_penjamin_v\" AS  SELECT concat('PEN', penjamin_m.penjamin_id) AS sync_id_api,
    penjamin_m.penjamin_kode AS vendor_code,
    penjamin_m.penjamin_nama AS name,
    penjamin_m.penjamin_nama AS display_name,
    carabayar_m.carabayar_nama AS customer_type_api,
    '-'::text AS contact_person,
    '-'::text AS phone,
    '-'::text AS mobile,
    '-'::text AS fax,
    '-'::text AS email,
    '-'::text AS website,
    penjamin_m.alamat_penjamin AS street,
    penjamin_m.alamat_penjamin AS street2,
    penjamin_m.alamat_penjamin AS street3,
    '-'::text AS city,
    '-'::text AS zip,
    NULL::text AS credit_days,
    NULL::text AS opdiscountid,
    NULL::text AS ipdiscountid,
    NULL::text AS taxid,
        CASE
            WHEN penjamin_m.is_active IS TRUE AND penjamin_m.is_deleted IS TRUE THEN true
            WHEN penjamin_m.is_active IS TRUE AND penjamin_m.is_deleted IS FALSE THEN false
            WHEN penjamin_m.is_active IS FALSE AND penjamin_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
        CASE
            WHEN carabayar_m.groupcarabayar_id = 417 THEN false
            ELSE true
        END AS insurance,
    true AS customer,
    true AS active,
    6 AS sync_type,
    penjamin_m.penjamin_id,
    penjamin_m.additional_data
   FROM penjamin_m
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id;");

     $this->execute('ALTER TABLE "public"."int_penjamin_v" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."int_pegawai_v";');

     $this->execute("
        CREATE VIEW \"public\".\"int_pegawai_v\" AS  SELECT concat('PEG', pegawai_m.pegawai_id) AS sync_id_api,
    '-'::text AS parent_doctor_id,
    pegawai_m.nomorindukpegawai AS doctor_code,
    pegawai_m.nama_pegawai AS name,
    pegawai_m.nama_pegawai AS display_name,
    spesialis_m.spesialis_nama AS spec,
    '-'::text AS department,
    'internal'::text AS type_employment,
    'internal'::text AS type_doctor,
    '-'::text AS designation,
    kelompokpegawai_m.kelompokpegawai_nama AS hrprofile,
    lower(fgetnamalookup(pegawai_m.jeniskelamin::integer)::text) AS gender,
    pegawai_m.notelp_pegawai AS phone,
    pegawai_m.nomobile_pegawai AS mobile,
    '-'::text AS fax,
    pegawai_m.alamatemail AS email,
    '-'::text AS website,
    pegawai_m.alamat_pegawai AS street,
    pegawai_m.alamat_pegawai AS street2,
    pegawai_m.alamat_pegawai AS street3,
    kabupaten_m.kabupaten_nama AS city,
    '-'::text AS zip,
    spesialis_m.spesialis_nama AS specialise_api_id,
        CASE
            WHEN pegawai_m.is_active IS TRUE AND pegawai_m.is_deleted IS TRUE THEN true
            WHEN pegawai_m.is_active IS TRUE AND pegawai_m.is_deleted IS FALSE THEN false
            WHEN pegawai_m.is_active IS FALSE AND pegawai_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
        CASE
            WHEN pegawai_m.is_active = true THEN false
            ELSE true
        END AS empblocked,
    true AS doctor,
    true AS active,
    6 AS sync_type,
    pegawai_m.pegawai_id,
    pegawai_m.additional_data,
    false AS customer,
    false AS insurance,
    false AS patient,
    false AS supplier
   FROM pegawai_m
     LEFT JOIN pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN kabupaten_m ON pegawai_m.kabupaten_id = kabupaten_m.kabupaten_id
     JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id;");

     $this->execute('ALTER TABLE "public"."int_pegawai_v" OWNER TO "postgres";');

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
    total_tagihan.total_tagihan AS price_total,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text THEN tindakanpelayanan_r.tarif_dibayarkan
            ELSE - tindakanpelayanan_r.tarif_dibayarkan
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text THEN tindakanpelayanan_r.tarif_dijamin
            ELSE - tindakanpelayanan_r.tarif_dijamin
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id AS order_id,
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
     LEFT JOIN ( SELECT tindakanpelayanan_r_1.pendaftaran_id,
            sum(tindakanpelayanan_r_1.tarif_tindakan) AS total_tagihan
           FROM tindakanpelayanan_r tindakanpelayanan_r_1
          WHERE tindakanpelayanan_r_1.is_deleted = false
          GROUP BY tindakanpelayanan_r_1.pendaftaran_id) total_tagihan ON tindakanpelayanan_r.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_r.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
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
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text THEN tindakanpelayanan_r.tarif_dibayarkan
            ELSE - tindakanpelayanan_r.tarif_dibayarkan
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'BILLING'::text THEN tindakanpelayanan_r.tarif_dijamin
            ELSE - tindakanpelayanan_r.tarif_dijamin
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id AS order_id,
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
     LEFT JOIN ( SELECT tindakanpelayanan_r_1.pendaftaran_id,
            sum(tindakanpelayanan_r_1.tarif_tindakan) AS total_tagihan
           FROM tindakanpelayanan_r tindakanpelayanan_r_1
          WHERE tindakanpelayanan_r_1.is_deleted = false
          GROUP BY tindakanpelayanan_r_1.pendaftaran_id) total_tagihan ON tindakanpelayanan_r.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_r.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
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
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id;");

     $this->execute('ALTER TABLE "public"."saleorder_line_v" OWNER TO "postgres";');

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
    paket_detail.harga_total AS price_total,
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
    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS primary_doc_id,
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
    false AS is_package,
    NULL::text AS package_name,
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
        END AS state
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

     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210203_055203_oddo_20210203_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210203_055203_oddo_20210203_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
