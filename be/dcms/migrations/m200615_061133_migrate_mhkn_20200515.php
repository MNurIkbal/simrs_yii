<?php

use yii\db\Migration;

/**
 * Class m200615_061133_migrate_mhkn_20200515
 */
class m200615_061133_migrate_mhkn_20200515 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasienmasukpenunjang_t" ADD COLUMN "is_hasil" bool NOT NULL DEFAULT false;');
        
        // $this->execute('ALTER TABLE "public"."hasilpemeriksaanlab_wynacom_t" ALTER COLUMN "hasilpemeriksaanlab_wynacom_id" SET DEFAULT nextval(\'hasilpemeriksaanlab_wynacom_t_hasilpemeriksaanlab_wynacom_id_se\'::regclass);');
       
        $this->execute('CREATE INDEX "hasilpemeriksaanlab_wynacom_his_reg_no_idx" ON "public"."hasilpemeriksaanlab_wynacom_t" USING btree (
  "his_reg_no" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX "hasilpemeriksaanlab_wynacom_his_test_id_idx" ON "public"."hasilpemeriksaanlab_wynacom_t" USING btree (
  "his_test_id" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);');

        $this->execute('DROP VIEW if exists "public"."laporanhasillab_v";');
        $this->execute("
            CREATE VIEW \"public\".\"laporanhasillab_v\" AS  SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
    pasien_m.tanggal_lahir AS dateofbirth,
    (hasil_lab.age_year)::character varying(10) AS umur,
    pegawai_m.pegawai_id AS dokter_id, 
    pegawai_m.nama_pegawai AS dokter_nama,
    ruangan_m.ruangan_id AS poli_id,
    ruangan_m.ruangan_nama AS poli_nama,
    pasienmasukpenunjang_t.no_masukpenunjang,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tglpermintaankepenunjang AS tglpenunjang,
    permintaankepenunjang_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    hasil_lab.test_name AS test_nama_lis,
    hasil_lab.result AS hasil,
    hasil_lab.reference_value AS nilai_rujukan,
    hasil_lab.test_units_name AS satuan,
    hasil_lab.authorization_date AS tgl_pemeriksaan,
    pasienmasukpenunjang_t.catatan,
    hasil_lab.test_group,
    hasil_lab.authorization_user AS petugas_pemeriksaan,
    hasil_lab.test_method,
    pasienmasukpenunjang_t.is_hasil,
    pendaftaran_t.pendaftaran_id
   FROM ((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id)))
     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
     JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN hasilpemeriksaanlab_wynacom_t hasil_lab ON ((((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_lab.his_reg_no)::text) AND ((daftartindakan_m.daftartindakan_kode)::text = (hasil_lab.lis_test_id)::text))))
  ORDER BY hasil_lab.hasilpemeriksaanlab_wynacom_id;
");
        $this->execute('ALTER TABLE "public"."laporanhasillab_v" OWNER TO "postgres";');

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
    diagnosa.diagnosa_utama AS diagnosa_id,
    diagnosa.diagnosa_utama AS diagnosa_name,
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
   FROM ((((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN kelaspelayanan_m ON ((pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN loginpemakai_k ON ((pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN kelaspelayanan_m kelas ON ((pendaftaran_t.kelaspelayanan_id = kelas.kelaspelayanan_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN lookup_m jeniskelamin ON (((pasien_m.jeniskelamin)::integer = jeniskelamin.lookup_id)))
     JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM ((pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
             JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
          WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM ((pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM (cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE (cppt_last.is_deleted = false)
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
          WHERE (cppt_t.a_diag_utama IS NOT NULL)
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM (((pendaftaran_t pendaftaran_t_1
             JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
             JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
             JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
          WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
  WHERE ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NOT NULL) AND (pasienkirimkeunitlain_t.instalasi_id = 4));
");

        $this->execute('ALTER TABLE "public"."bridging_orderlab_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."pasienrsambulan_v";');
        $this->execute("
            CREATE VIEW \"public\".\"pasienrsambulan_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN r_pendaftaran.ruangan_id
            ELSE r_admisi.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN r_pendaftaran.ruangan_nama
            ELSE r_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN i_pendaftaran.instalasi_id
            ELSE i_admisi.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN i_pendaftaran.instalasi_nama
            ELSE i_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN (pendaftaran_t.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
            WHEN (pendaftaran_t.instalasi_id = 2) THEN (cppt_t.a_diag_utama ->> 'text'::text)
            ELSE
            CASE COALESCE(pasienadmisi_t.pasienpulang_id, 0)
                WHEN 0 THEN (cppt_t.a_diag_utama ->> 'text'::text)
                ELSE (resumemedisri_t.diag_utama ->> 'text'::text)
            END
        END AS diagnosa,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN cb_pendaftaran.carabayar_id
            ELSE cb_admisi.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN cb_pendaftaran.carabayar_nama
            ELSE cb_admisi.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pj_pendaftaran.penjamin_nama
            ELSE pj_admisi.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kls_pendaftaran.kelaspelayanan_nama
            ELSE kls_pendaftaran.kelaspelayanan_nama
        END AS kelaspelayanan_nama
   FROM ((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN instalasi_m i_pendaftaran ON ((r_pendaftaran.instalasi_id = i_pendaftaran.instalasi_id)))
     LEFT JOIN instalasi_m i_admisi ON ((r_admisi.instalasi_id = i_admisi.instalasi_id)))
     LEFT JOIN pasienmorbiditas_t ON (((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false) AND (pasienmorbiditas_t.kelompokdiagnosa_id = 2))))
     LEFT JOIN cppt_t ON ((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id)))
     LEFT JOIN resumemedisri_t ON (((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
     LEFT JOIN ( SELECT pendaftaran_t_1.pasien_id,
            max(pendaftaran_t_1.tgl_pendaftaran) AS tgl_pendaftaran
           FROM pendaftaran_t pendaftaran_t_1
          GROUP BY pendaftaran_t_1.pasien_id) pendaftaran_t1 ON ((pasien_m.pasien_id = pendaftaran_t1.pasien_id)))
     LEFT JOIN carabayar_m cb_pendaftaran ON ((pendaftaran_t.carabayar_id = cb_pendaftaran.carabayar_id)))
     LEFT JOIN carabayar_m cb_admisi ON ((pasienadmisi_t.carabayar_id = cb_admisi.carabayar_id)))
     LEFT JOIN penjamin_m pj_pendaftaran ON ((pendaftaran_t.penjamin_id = pj_pendaftaran.penjamin_id)))
     LEFT JOIN penjamin_m pj_admisi ON ((pasienadmisi_t.penjamin_id = pj_admisi.penjamin_id)))
     LEFT JOIN kelaspelayanan_m kls_pendaftaran ON ((pendaftaran_t.kelaspelayanan_id = kls_pendaftaran.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kls_admisi ON ((pasienadmisi_t.kelaspelayanan_id = kls_admisi.kelaspelayanan_id)));");
        
        $this->execute('ALTER TABLE "public"."bridging_orderlab_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienlab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienlab_v\" AS  SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
        CASE
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
        END AS received_flag,
    pasienmasukpenunjang_t.is_hasil
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienmasukpenunjang_t.no_antrian IS NOT NULL))
UNION ALL
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
        CASE
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
        END AS received_flag,
    pasienmasukpenunjang_t.is_hasil
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienmasukpenunjang_t.no_antrian IS NOT NULL) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL))
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    rujukan_t.no_rujukan,
    rujukan_t.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    rujukan_t.rujukandari_id AS ruanganasal_id,
    perujuk_m.namaperujuk AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    NULL::text AS received_flag,
    pasienmasukpenunjang_t.is_hasil
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
  WHERE ((pendaftaran_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienmasukpenunjang_t.no_antrian IS NOT NULL))
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    'APS'::character varying AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienmasukpenunjang_t.tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
        CASE
            WHEN (pasienkirimkeunitlain_t.additional_data IS NOT NULL) THEN (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            ELSE NULL::text
        END AS received_flag,
    pasienmasukpenunjang_t.is_hasil
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruang_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id)))
  WHERE ((ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienmasukpenunjang_t.no_antrian IS NOT NULL));");
       


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200615_061133_migrate_mhkn_20200515 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200615_061133_migrate_mhkn_20200515 cannot be reverted.\n";

        return false;
    }
    */
}
