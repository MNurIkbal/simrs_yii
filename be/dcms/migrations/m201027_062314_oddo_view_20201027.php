<?php

use yii\db\Migration;

/**
 * Class m201027_062314_oddo_view_20201027
 */
class m201027_062314_oddo_view_20201027 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_stokmoveheader_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stokmoveheader_v\" AS  SELECT concat('RSP', penjualanresep_t.penjualanresep_id) AS sync_id_api,
    penjualanresep_t.noresep AS name,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN pasien_m.nama_pasien
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.nama_pegawai
            ELSE NULL::character varying
        END AS partner_id,
    penjualanresep_t.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS aspicking_type,
    penjualanresep_t.tglresep AS date_move,
    NULL::text AS min_date,
    6 AS sync_type
   FROM ((penjualanresep_t
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE (penjualanresep_t.is_deleted = false)
UNION ALL
 SELECT concat('BHP', pendaftaran_t.pendaftaran_id) AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
    pasien_m.nama_pasien AS partner_id,
    obatalkespasien_t.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS aspicking_type,
    pendaftaran_t.tgl_pendaftaran AS date_move,
    NULL::text AS min_date,
    6 AS sync_type
   FROM ((obatalkespasien_t
     JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((obatalkespasien_t.penjualanresep_id IS NULL) AND (obatalkespasien_t.is_deleted = false))
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.nama_pasien, obatalkespasien_t.ruangan_id, 'stockout'::text, 'stockout'::text, pendaftaran_t.tgl_pendaftaran, NULL::text, 6::integer;");

        $this->execute('ALTER TABLE "public"."int_stokmoveheader_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_pembayaran_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_pembayaran_v\" AS  SELECT concat('BYR', pembayaran_r.id) AS sync_id_api,
        CASE
            WHEN ((pembayaran_r.keterangan)::text = ANY ((ARRAY['BILL CANCEL'::character varying, 'DISCOUNT CANCEL'::character varying])::text[])) THEN peg_deleted.nama_pegawai
            ELSE pegawai_m.nama_pegawai
        END AS user_name,
    fgetnamalookup(pembayaran_r.tipe_pembayaran) AS trans_type,
    concat(kasir.ruangan_nama, ' - ', pembayaranpelayanan_t.no_pembayaran) AS facility_name,
    pembayaran_r.tgl_proses AS tglproses,
    pembayaran_r.keterangan AS payment_name,
    pembayaran_r.nama_edc AS edc_machine,
        CASE
            WHEN (pembayaran_r.total_tunai <> (0)::double precision) THEN (pembayaran_r.total_tunai - pembayaran_r.total_kembalian)
            WHEN (pembayaran_r.total_nontunai <> (0)::double precision) THEN pembayaran_r.total_nontunai
            ELSE NULL::double precision
        END AS total_collect,
        CASE
            WHEN (pembayaranpelayanan_t.pendaftaran_id IS NULL) THEN penjualanresep_t.noresep
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
   FROM (((((((((pembayaran_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1
          WHERE (((pendaftaran_r_1.keterangan)::text = 'INSERT'::text) AND (pendaftaran_r_1.is_sent = true))) pendaftaran_r ON ((pembayaran_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN pembayaranpelayanan_t ON ((pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
     JOIN loginpemakai_k ON ((pembayaran_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN loginpemakai_k deleted_by ON ((pembayaran_r.deleted_by = deleted_by.loginpemakai_id)))
     LEFT JOIN pegawai_m peg_deleted ON ((deleted_by.pegawai_id = peg_deleted.pegawai_id)))
     LEFT JOIN ruangan_m kasir ON ((pembayaranpelayanan_t.ruangan_id = kasir.ruangan_id)))
     LEFT JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
UNION ALL
 SELECT concat('UM', bayaruangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
        CASE
            WHEN (bayaruangmuka_r.metode_pembayaran = 27) THEN 'CASH'::text
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
   FROM (((((bayaruangmuka_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1) pendaftaran_r ON ((bayaruangmuka_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN loginpemakai_k ON ((bayaruangmuka_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((bayaruangmuka_r.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pendaftaran_t ON ((bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
  WHERE (bayaruangmuka_r.is_deleted = false)
UNION ALL
 SELECT concat('PUM', pengembalianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
        CASE
            WHEN (tandabuktikeluar_t.is_tunai IS TRUE) THEN 'CASH'::text
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
   FROM (((((pengembalianuangmuka_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.*::pendaftaran_r AS pendaftaran_r_1,
            pendaftaran_r_1.no_pendaftaran,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1
          WHERE ((pendaftaran_r_1.keterangan)::text = 'INSERT'::text)) pendaftaran_r ON ((pengembalianuangmuka_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN loginpemakai_k ON ((pengembalianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     JOIN tandabuktikeluar_t ON ((pengembalianuangmuka_r.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id)))
     JOIN ruangan_m ON ((pengembalianuangmuka_r.ruangan_id = ruangan_m.ruangan_id)))
  WHERE (pengembalianuangmuka_r.is_deleted = false);");

        $this->execute('ALTER TABLE "public"."int_pembayaran_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_obatalkespasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_obatalkespasien_v\" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    351 AS product_uom,
    obatalkespasien_r.qty_oa AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa AS price_unit,
    obatalkespasien_r.hargajual_oa AS price_subtotal,
    total_tagihan.total_tagihan AS price_total,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = 'BILLING'::text) THEN obatalkespasien_r.tarif_dibayarkan
            ELSE (- obatalkespasien_r.tarif_dibayarkan)
        END AS personal_amount,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = 'BILLING'::text) THEN obatalkespasien_r.tarif_dijamin
            ELSE (- obatalkespasien_r.tarif_dijamin)
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id AS order_id,
    jenisobatalkes_m.servicecategory_id AS service_categ_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS primary_doc_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS prescribe_doc_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS perform_doc_id,
    (obatalkespasien_r.ruangan_id)::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
        CASE
            WHEN (pendaftaran_r.instalasi_id = 1) THEN 'OPD'::text
            WHEN (pendaftaran_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (pendaftaran_r.instalasi_id = 3) THEN 'IPD'::text
            WHEN (pendaftaran_r.instalasi_id = 21) THEN 'MCU'::text
            ELSE '-'::text
        END AS patient_group,
    NULL::text AS special_group,
        CASE
            WHEN (pendaftaran_r.pasienadmisi_id IS NULL) THEN 'PHARMACY OUTPATIENT'::text
            ELSE 'PHARMACY INPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, 'GENERAL'::character varying) AS bed_type,
    concat('PEN', obatalkespasien_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    obatalkespasien_r.no_obatalkespasien AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN (obatalkespasien_r.penjualanresep_id IS NULL) THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS account_analytic_id,
        CASE
            WHEN (obatalkespasien_r.penjualanresep_id IS NULL) THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    (pendaftaran_r.tglpasienpulang)::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'bill'::text
        END AS status_bill
   FROM (((((((((((((((obatalkespasien_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasienadmisi_id,
            pendaftaran_r_1.instalasi_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang
           FROM ((((((pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON ((pendaftaran_r_1.id = max.id)))
             JOIN pasien_m ON ((pendaftaran_r_1.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
          WHERE (pendaftaran_r_1.is_sent = true)) pendaftaran_r ON ((obatalkespasien_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     LEFT JOIN servicegroup_m ON ((jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id)))
     JOIN ruangan_m ON ((obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN penjamin_m ON ((obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN ( SELECT obatalkespasien_r_1.pendaftaran_id,
            sum(obatalkespasien_r_1.hargajual_oa) AS total_tagihan
           FROM obatalkespasien_r obatalkespasien_r_1
          WHERE (obatalkespasien_r_1.is_deleted = false)
          GROUP BY obatalkespasien_r_1.pendaftaran_id) total_tagihan ON ((obatalkespasien_r.pendaftaran_id = total_tagihan.pendaftaran_id)))
     LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_r.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON ((obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id)))
     LEFT JOIN kelaspelayanan_m ON ((obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM (pegawai_m
             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai ON ((obatalkespasien_r.pegawai_id = pegawai.pegawai_id)))
     LEFT JOIN ( SELECT penjualanresep_t_1.penjualanresep_id,
            penjualanresep_t_1.pegawai_id
           FROM penjualanresep_t penjualanresep_t_1) penjualanresep_t ON ((obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id)));");

        $this->execute('ALTER TABLE "public"."int_obatalkespasien_v" OWNER TO "postgres";');
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201027_062314_oddo_view_20201027 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201027_062314_oddo_view_20201027 cannot be reverted.\n";

        return false;
    }
    */
}
