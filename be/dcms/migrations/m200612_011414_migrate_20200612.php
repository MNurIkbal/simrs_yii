<?php

use yii\db\Migration;   

/**
 * Class m200612_011414_migrate_20200612
 */
class m200612_011414_migrate_20200612 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$this->execute('ALTER TABLE "public"."pemakaianambulan_t" ADD COLUMN "estimasi_jarak" int4;');

        $this->execute('DROP VIEW if exists "public"."bridging_orderlab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"bridging_orderlab_v\" AS  SELECT pasien_m.no_rekam_medik,
    jeniskelamin.lookup_kode AS gender_id,
    jeniskelamin.lookup_value AS gender_name,
    pasien_m.tanggal_lahir AS dateofbirth,
    pasien_m.nama_pasien AS patient_name,
    pasien_m.alamat_pasien AS patient_address,
    pasien_m.kabupaten_id AS city_id,
    kabupaten_m.kabupaten_nama AS city_name,
    pasien_m.no_telepon_pasien AS phone_number,
    pasien_m.no_mobile_pasien AS mobile_number,
    pasien_m.alamatemail AS email,
    pendaftaran_t.no_pendaftaran AS visit_number,
    pasienmasukpenunjang_t.no_masukpenunjang AS no_order,
    pasienmasukpenunjang_t.tglmasukpenunjang AS order_datetime,
    pasienmasukpenunjang_t.instalasiasal_id AS service_unit_id,
    instalasi_m.instalasi_nama AS service_unit_name,
    pendaftaran_t.penjamin_id AS guarantor_id,
    penjamin_m.penjamin_nama AS guarantor_name,
    pasienmasukpenunjang_t.kelaspelayanan_id AS aggreement_id,
    kelaspelayanan_m.kelaspelayanan_nama AS aggreement_name,
    pasienmasukpenunjang_t.pegawai_id AS doctor_id,
    pegawai_m.nama_pegawai AS doctor_name,
    pendaftaran_t.kelaspelayanan_id AS class_id,
    kelas.kelaspelayanan_nama AS class_name,
    pasienmasukpenunjang_t.ruanganasal_id AS ward_id,
    ruangan_m.ruangan_nama AS ward_name,
    pasienadmisi_t.kamarruangan_id AS room_id,
    kamarruangan_m.kamarruangan_nokamar AS room_name,
    pasienadmisi_t.kamartempattidur_id AS bed_id,
    kamartempattidur_m.no_tempattidur AS bed_name,
    diagnosa_m.diagnosa_kode AS diagnosa_id,
    diagnosa_m.diagnosa_nama AS diagnosa_name,
    pasienmasukpenunjang_t.created_by AS reg_user_id,
    loginpemakai_k.nama_pemakai AS reg_user_name,
    pasienmasukpenunjang_t.created_date,
    NULL::text AS fax_number,
        CASE
            WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
               FROM permintaankepenunjang_t countcyto
              WHERE ((countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id) AND (countcyto.is_cyto IS TRUE))) > 0) THEN true
            ELSE false
        END AS is_cyto,
        CASE
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'lis_reg_no'::text)
            ELSE NULL::text
        END AS lis_reg_no,
        CASE
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'retrieved_dt'::text)
            ELSE NULL::text
        END AS retrieved_dt,
        CASE
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'status'::text)
            ELSE NULL::text
        END AS status,
        CASE
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'retrieved_flag'::text)
            ELSE NULL::text
        END AS retrieved_flag,
        CASE
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang_t_1.no_masukpenunjang AS no_order,
                    permintaankepenunjang_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    permintaankepenunjang_t.permintaankepenunjang_id
                   FROM (((permintaankepenunjang_t
                     JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                     JOIN pasienkirimkeunitlain_t pasienkirimkeunitlain_t_1 ON ((permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t_1.pasienkirimkeunitlain_id)))
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((pasienkirimkeunitlain_t_1.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
                  WHERE (pasienkirimkeunitlain_t_1.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)) d) AS ordered_items,
    pasienmasukpenunjang_t.is_bayar
   FROM (((((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN kelaspelayanan_m ON ((pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN loginpemakai_k ON ((pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN kelaspelayanan_m kelas ON ((pendaftaran_t.kelaspelayanan_id = kelas.kelaspelayanan_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasienmorbiditas_t ON ((pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN diagnosa_m ON ((pasienmorbiditas_t.diagnosa_id = diagnosa_m.diagnosa_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN lookup_m jeniskelamin ON (((pasien_m.jeniskelamin)::integer = jeniskelamin.lookup_id)))
     JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
  WHERE ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NOT NULL) AND (pasienkirimkeunitlain_t.instalasi_id = 4));");

        $this->execute('DROP VIEW if exists "public"."worklistresepdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"worklistresepdetail_v\" AS  SELECT reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket
   FROM ((((((reseptur_t
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
UNION ALL
 SELECT reseptur_t.noresep AS no_reseptur,
    NULL::text AS no_resep,
    racikan_m.racikan_nama AS racikan,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    resepturdetail_t.qty_reseptur AS qty_obat,
    resepturdetail_t.qty_konversi,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    resepturdetail_t.etiket
   FROM (((((reseptur_t
     JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
     JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket
   FROM ((((penjualanresep_t
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
  WHERE ((penjualanresep_t.jenispenjualan)::text <> '344'::text);");

        $this->execute('DROP VIEW if exists "public"."worklistresep_v";');

        $this->execute("
            CREATE VIEW \"public\".\"worklistresep_v\" AS  SELECT penjualanresep_t.penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    array_agg(anamnesa_t.riwayat_alergiobat) AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup((reseptur_t.status_worklist)::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar
   FROM ((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
  GROUP BY reseptur_t.noresep, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pegawai_m.nama_pegawai, reseptur_t.status_worklist, ruangan_asal.instalasi_id, penjualanresep_t.tglpenjualan, penjualanresep_t.status_bayar, penjualanresep_t.penjualanresep_id, reseptur_t.reseptur_id
UNION ALL
 SELECT NULL::integer AS penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    NULL::character varying AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup((reseptur_t.status_worklist)::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    reseptur_t.tglreseptur AS tanggal,
    NULL::smallint AS status_bayar_id,
    NULL::character varying AS status_bayar
   FROM ((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_rm,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.nama_pegawai
            ELSE NULL::character varying
        END AS nama_pasien,
    NULL::date AS tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    penjualanresep_t.status_worklist AS status_worklist_id,
    fgetnamalookup((penjualanresep_t.status_worklist)::integer) AS status_worklist,
    NULL::integer AS instalasi_id,
    penjualanresep_t.jenispenjualan AS jenispenjualan_id,
    fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar
   FROM ((((penjualanresep_t
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE ((penjualanresep_t.jenispenjualan)::text <> '344'::text);");

        $this->execute('CREATE TABLE "public"."hasilpemeriksaanlab_wynacom_t" (
  "hasilpemeriksaanlab_wynacom_id" serial8,
  "his_reg_no" varchar(20) COLLATE "pg_catalog"."default",
  "his_test_id" varchar(20) COLLATE "pg_catalog"."default",
  "lis_reg_no" varchar(20) COLLATE "pg_catalog"."default",
  "lis_test_id" varchar(20) COLLATE "pg_catalog"."default",
  "test_name" varchar(100) COLLATE "pg_catalog"."default",
  "test_method" varchar(50) COLLATE "pg_catalog"."default",
  "result" text COLLATE "pg_catalog"."default",
  "result_comment" text COLLATE "pg_catalog"."default",
  "reference_value" text COLLATE "pg_catalog"."default",
  "reference_note" text COLLATE "pg_catalog"."default",
  "test_flag_sign" varchar(5) COLLATE "pg_catalog"."default",
  "test_units_name" varchar(25) COLLATE "pg_catalog"."default",
  "instrument_name" varchar(50) COLLATE "pg_catalog"."default",
  "authorization_date" timestamp(6),
  "authorization_user" varchar(50) COLLATE "pg_catalog"."default",
  "greaterthan_value" varchar(50) COLLATE "pg_catalog"."default",
  "lessthan_value" varchar(50) COLLATE "pg_catalog"."default",
  "age_year" varchar(5) COLLATE "pg_catalog"."default",
  "age_month" varchar(2) COLLATE "pg_catalog"."default",
  "age_days" varchar(2) COLLATE "pg_catalog"."default",
  "sequence" varchar(20) COLLATE "pg_catalog"."default",
  "transfer_flag" varchar(1) COLLATE "pg_catalog"."default",
  "test_group" varchar(20) COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "pk_hasilpemeriksaanlab_wynacom" PRIMARY KEY ("hasilpemeriksaanlab_wynacom_id")
)
;');

        $this->execute('CREATE TABLE "public"."loketjenisantrian_mp" (
  "loket_id" int4 NOT NULL,
  "jenisantriandetail_id" int4 NOT NULL,
  CONSTRAINT "loketjenisantrian_mp_pkey" PRIMARY KEY ("loket_id", "jenisantriandetail_id")
)
;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanhasillab_v\" AS  SELECT pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
        CASE pasien_m.jeniskelamin
            WHEN '15'::text THEN 'L'::text
            ELSE 'P'::text
        END AS jeniskelamin_id,
        CASE pasien_m.jeniskelamin
            WHEN '15'::text THEN 'Laki-laki'::text
            ELSE 'Perempuan'::text
        END AS jeniskelamin_nama,
    pasien_m.tanggal_lahir AS dateofbirth,
    (hasil_lab.age_year)::character varying(10) AS umur,
    ''::character varying(25) AS status_keluarga,
    NULL::character varying(25) AS kesatuan,
    NULL::character varying(25) AS pangkat,
    ruangan_m.ruangan_id AS poli_id,
    ruangan_m.ruangan_nama AS poli_nama,
    permintaankepenunjang_t.is_cyto,
    pegawai_m.pegawai_id AS dokter_id,
    pegawai_m.nama_pegawai AS dokter_dokter,
    pasienmasukpenunjang_t.no_masukpenunjang,
    permintaankepenunjang_t.tglpermintaankepenunjang AS tglpenunjang,
    ''::character varying(20) AS status_kepegawaian,
    diagnosa_m.diagnosa_id,
    diagnosa_m.diagnosa_nama,
    kelompoktindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    (permintaankepenunjang_t.daftartindakan_id)::character varying(20) AS test_id,
    (((permintaankepenunjang_t.additional_data)::json ->> 'name'::text))::character varying(100) AS test_nama,
    hasil_lab.test_name AS test_nama_lis,
    (hasil_lab.result)::character varying(50) AS hasil_saatini,
    hasil_lab.reference_value AS nilai_rujukan,
    hasil_lab.authorization_date AS tgl_pemeriksaan,
    pasienmasukpenunjang_t.catatan,
    hasil_lab.test_group,
    hasil_lab.authorization_user AS petugas_pemeriksaan,
    hasil_lab.test_method
   FROM ((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id)))
     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
     LEFT JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN hasilpemeriksaanlab_wynacom_t hasil_lab ON ((((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_lab.his_reg_no)::text) AND ((permintaankepenunjang_t.daftartindakan_id)::text = (hasil_lab.his_test_id)::text))))
     LEFT JOIN pasienmorbiditas_t ON ((pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN diagnosa_m ON ((pasienmorbiditas_t.diagnosa_id = diagnosa_m.diagnosa_id)));
     ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200612_011414_migrate_20200612 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200612_011414_migrate_20200612 cannot be reverted.\n";

        return false;
    }
    */
}
