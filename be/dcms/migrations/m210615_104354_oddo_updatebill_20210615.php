<?php

use yii\db\Migration;

/**
 * Class m210615_104354_oddo_updatebill_20210615
 */
class m210615_104354_oddo_updatebill_20210615 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT exists "public"."tindakanpelayananupdate_r" (
  "id" serial8,
  "tindakanpelayanan_id" int4,
  "shift_id" int4,
  "kelaspelayanan_id" int4,
  "kelastanggungan_id" int4,
  "pasien_id" int4,
  "rencanaoperasi_id" int4,
  "instalasi_id" int4,
  "daftartindakan_id" int4,
  "alatmedis_id" int4,
  "tipepaket_id" int4,
  "tindakansudahbayar_id" int4,
  "carabayar_id" int4,
  "pendaftaran_id" int4,
  "hasilpemeriksaanrad_id" int4,
  "jeniskasuspenyakit_id" int4,
  "hasilpemeriksaanrm_id" int4,
  "ruangan_id" int4,
  "konsulpoli_id" int4,
  "pasienmasukpenunjang_id" int4,
  "hasilpemeriksaanlabdetail_id" int4,
  "penjamin_id" int4,
  "pasienadmisi_id" int4,
  "verifikasitagihan_id" int4,
  "jurnalrekening_id" int4,
  "instruksitindakan_id" int4,
  "tgl_tindakan" timestamp(6),
  "tarif_rsakomodasi" float8 DEFAULT (0)::double precision,
  "tarif_medis" float8 DEFAULT (0)::double precision,
  "tarif_paramedis" float8 DEFAULT (0)::double precision,
  "tarif_bhp" float8 DEFAULT (0)::double precision,
  "tarif_satuan" float8 DEFAULT (0)::double precision,
  "tarif_tindakan" float8,
  "tarifcyto_tindakan" float8,
  "satuan_tindakan" varchar(10) COLLATE "pg_catalog"."default",
  "qty_tindakan" int4 DEFAULT 1,
  "cyto_tindakan" bool,
  "dokterpenanggungjawab_id" int8,
  "dokterpelaksana_id" int8,
  "dokteranastesi_id" int8,
  "dokterdelegasi_id" int8,
  "bidan1_id" int8,
  "bidan2_id" int8,
  "perawat1_id" int8,
  "perawat2_id" int4,
  "discount_tindakan" float8,
  "pembebasan_tindakan" float8 DEFAULT (0)::double precision,
  "subsidiasuransi_tindakan" float8,
  "subsidipemerintah_tindakan" float8,
  "subsisidirumahsakit_tindakan" float8,
  "uangditerima_tindakan" float8,
  "keterangantindakan" text COLLATE "pg_catalog"."default",
  "pembulatan" float8,
  "implementasi_id" int4,
  "is_dilakukan" bool DEFAULT false,
  "pemakaianambulan_id" int4,
  "is_penatajasa" bool,
  "additional_riwayat" text COLLATE "pg_catalog"."default",
  "is_valid" bool DEFAULT true,
  "kamarruangan_id" int4,
  "kamartempattidur_id" int4,
  "penyulit_tindakan" bool DEFAULT false,
  "tarifpenyulit_tindakan" float8 DEFAULT 0,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "keterangan" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "no_tindakanpelayanan" varchar(255) COLLATE "pg_catalog"."default",
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  "pembayaran_id" int4,
  "tarif_dijamin" float8 DEFAULT 0,
  "tarif_dibayarkan" float8 DEFAULT 0,
  "tarif_diskon" float8 DEFAULT 0,
  CONSTRAINT "tindakanpelayananupdate_r_pkey" PRIMARY KEY ("id")
)
;');

        $this->execute('DROP TRIGGER if exists "tindakanpelayananupdate_r" ON "public"."tindakanpelayanan_t";');


        $this->execute('DROP FUNCTION if exists "public"."tindakanpelayananupdate_r_update"();');



        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"tindakanpelayananupdate_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    v_keterangan VARCHAR;
BEGIN
    IF(NEW.tindakansudahbayar_id IS NOT NULL)
        THEN
            v_keterangan := 'UPDATE_APS';
        --> INSERT table history tindakanpelayanan_r menjadi ACCRUAL REVERSAL(-)
            INSERT INTO tindakanpelayananupdate_r (       
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
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            keterangan,
            no_tindakanpelayanan,
                        tarif_dijamin,
                        tarif_dibayarkan,
                        tarif_diskon,
                        pembayaran_id
        )VALUES(
            OLD.tindakanpelayanan_id ,
            OLD.shift_id ,
            OLD.kelaspelayanan_id ,
            OLD.kelastanggungan_id ,
            OLD.pasien_id ,
            OLD.rencanaoperasi_id ,
            OLD.instalasi_id ,
            OLD.daftartindakan_id ,
            OLD.alatmedis_id ,
            OLD.tipepaket_id ,
            OLD.tindakansudahbayar_id ,
            OLD.carabayar_id ,
            OLD.pendaftaran_id ,
            OLD.hasilpemeriksaanrad_id ,
            OLD.jeniskasuspenyakit_id ,
            OLD.hasilpemeriksaanrm_id ,
            OLD.ruangan_id ,
            OLD.konsulpoli_id ,
            OLD.pasienmasukpenunjang_id ,
            OLD.hasilpemeriksaanlabdetail_id ,
            OLD.penjamin_id ,
            OLD.pasienadmisi_id ,
            OLD.verifikasitagihan_id ,
            OLD.jurnalrekening_id ,
            OLD.instruksitindakan_id ,
            OLD.tgl_tindakan ,
            OLD.tarif_rsakomodasi ,
            OLD.tarif_medis ,
            OLD.tarif_paramedis ,
            OLD.tarif_bhp ,
            OLD.tarif_satuan ,
            -1 * OLD.tarif_tindakan ,
            OLD.tarifcyto_tindakan ,
            OLD.satuan_tindakan ,
            -1 * OLD.qty_tindakan ,
            OLD.cyto_tindakan ,
            NEW.dokterpenanggungjawab_id ,
            NEW.dokterpelaksana_id ,
            NEW.dokteranastesi_id ,
            NEW.dokterdelegasi_id ,
            NEW.bidan1_id ,
            NEW.bidan2_id ,
            NEW.perawat1_id ,
            NEW.perawat2_id ,
            OLD.discount_tindakan ,
            OLD.pembebasan_tindakan ,
            OLD.subsidiasuransi_tindakan ,
            OLD.subsidipemerintah_tindakan ,
            OLD.subsisidirumahsakit_tindakan ,
            OLD.uangditerima_tindakan ,
            OLD.keterangantindakan ,
            OLD.pembulatan ,
            OLD.implementasi_id ,
            OLD.is_dilakukan ,
            OLD.pemakaianambulan_id ,
            OLD.is_penatajasa ,
            OLD.additional_riwayat ,
            OLD.is_valid ,
            OLD.kamarruangan_id ,
            OLD.kamartempattidur_id ,
            OLD.penyulit_tindakan ,
            OLD.tarifpenyulit_tindakan ,
            OLD.additional_data ,
            OLD.created_date ,
            OLD.created_by ,
            OLD.modified_count,
            OLD.last_modified_date,
            OLD.last_modified_by,
            OLD.is_deleted,
            OLD.is_active,
            OLD.deleted_date,
            OLD.deleted_by,
            v_keterangan,
            OLD.no_tindakanpelayanan,
                        0,
                        0,
                        0,
                        NULL
        );
            END IF;
     RETURN NEW;            
          

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "tindakanpelayananupdate_r" AFTER UPDATE OF "dokterpenanggungjawab_id" ON "public"."tindakanpelayanan_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tindakanpelayananupdate_r_update"();');

        $this->execute('DROP VIEW if exists public.saleorder_line_update_v;');

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
    no_pembayaran.no_pembayaran AS billno,
    no_pembayaran.tgl_pembayaran AS bill_date,
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
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_t.pegawai_id,
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
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id
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
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.tgl_pembayaran
           FROM pembayaranpelayanan_t
          WHERE pembayaranpelayanan_t.is_deleted = false) no_pembayaran ON tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id
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
     LEFT JOIN int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
     JOIN tindakanpelayananupdate_r ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayananupdate_r.tindakanpelayanan_id
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
    no_pembayaran.no_pembayaran AS billno,
    no_pembayaran.tgl_pembayaran AS bill_date,
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
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_t.pegawai_id,
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
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id
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
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.tgl_pembayaran
           FROM pembayaranpelayanan_t
          WHERE pembayaranpelayanan_t.is_deleted = false) no_pembayaran ON tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id
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
     LEFT JOIN int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
     JOIN tindakanpelayananupdate_r ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayananupdate_r.tindakanpelayanan_id;");
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210615_104354_oddo_updatebill_20210615 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210615_104354_oddo_updatebill_20210615 cannot be reverted.\n";

        return false;
    }
    */
}
