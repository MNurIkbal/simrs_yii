<?php

use yii\db\Migration;

/**
 * Class m200722_091233_migrate_mhkn_20200722
 */
class m200722_091233_migrate_mhkn_20200722 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."obatalkes_m" 
                        DROP CONSTRAINT "obatalkes_kode",
                        ADD CONSTRAINT "obatalkes_kode" UNIQUE ("obatalkes_kode", "is_deleted");
                        ');

         $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN "kelas_ditagihkan_id" int4;');
         $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN "kamar_titipan_id" int4;');
         $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN "tempattidur_titipan_id" int4;');
         $this->execute('ALTER TABLE "public"."pasienadmisi_t" ADD COLUMN "ruangan_titipan_id" int4;');

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
    pendaftaran_t.pegawai_id AS doctor_id,
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
        CASE (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'retrieved_flag'::text)
            WHEN '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            WHEN '1'::text THEN true
            ELSE false
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
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar
   FROM ((((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN kelaspelayanan_m ON ((pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN loginpemakai_k ON ((pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN kelaspelayanan_m kelas ON ((pendaftaran_t.kelaspelayanan_id = kelas.kelaspelayanan_id)))
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN lookup_m jeniskelamin ON (((pasien_m.jeniskelamin)::integer = jeniskelamin.lookup_id)))
     LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
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
  WHERE ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NOT NULL) AND (pasienkirimkeunitlain_t.instalasi_id = 4))
UNION ALL
 SELECT pasien_m.no_rekam_medik,
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
    pendaftaran_t.pegawai_id AS doctor_id,
    pegawai_m.nama_pegawai AS doctor_name,
    pendaftaran_t.kelaspelayanan_id AS class_id,
    kelaspelayanan_m.kelaspelayanan_nama AS class_name,
    pasienmasukpenunjang_t.ruanganasal_id AS ward_id,
    ruangan_m.ruangan_nama AS ward_name,
    ruangan_m.ruangan_id AS room_id,
    ruangan_m.ruangan_nama AS room_name,
    NULL::integer AS bed_id,
    NULL::character varying AS bed_name,
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
        CASE (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'retrieved_flag'::text)
            WHEN '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            WHEN '1'::text THEN true
            ELSE false
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang.no_masukpenunjang AS no_order,
                    tindakanpelayanan_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    pasienmasukpenunjang.pasienmasukpenunjang_id
                   FROM ((pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                  WHERE (pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id)) d) AS ordered_items,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar
   FROM ((((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN lookup_m jeniskelamin ON (((pasien_m.jeniskelamin)::integer = jeniskelamin.lookup_id)))
     LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN loginpemakai_k ON ((pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id)))
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
  WHERE ((pendaftaran_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
UNION ALL
 SELECT pasien_m.no_rekam_medik,
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
    pendaftaran_t.pegawai_id AS doctor_id,
    pegawai_m.nama_pegawai AS doctor_name,
    pendaftaran_t.kelaspelayanan_id AS class_id,
    kelaspelayanan_m.kelaspelayanan_nama AS class_name,
    pasienmasukpenunjang_t.ruanganasal_id AS ward_id,
    ruangan_m.ruangan_nama AS ward_name,
    ruangan_m.ruangan_id AS room_id,
    ruangan_m.ruangan_nama AS room_name,
    NULL::integer AS bed_id,
    NULL::character varying AS bed_name,
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
        CASE (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'retrieved_flag'::text)
            WHEN '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE (((pasienkirimkeunitlain_t.additional_data)::json -> 'lisattr'::text) ->> 'received_flag'::text)
            WHEN '1'::text THEN true
            ELSE false
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang.no_masukpenunjang AS no_order,
                    tindakanpelayanan_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    pasienmasukpenunjang.pasienmasukpenunjang_id
                   FROM ((pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                  WHERE (pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id)) d) AS ordered_items,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar
   FROM ((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruang_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id)))
     LEFT JOIN lookup_m jeniskelamin ON (((pasien_m.jeniskelamin)::integer = jeniskelamin.lookup_id)))
     LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
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
     JOIN loginpemakai_k ON ((pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id)))
  WHERE ((ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL));");

         $this->execute('DROP VIEW if exists "public"."infopasienri_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infopasienri_v\" AS  SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.jeniskelamin AS jeniskelamin_id,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
    kamarruangan_m.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE ((x.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (x.is_instruksi_pulang = true))) > 0) THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenmedis_t.r_peskk,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    COALESCE(tagihan.sub_total, (0)::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, (0)::double precision) AS tarif_inacbg,
    carabayar_m.groupcarabayar_id AS group_carabayar,
        CASE
            WHEN (monitorsetdiagnosa.diag_utama_id IS NULL) THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor,
    bpjs_t.nosep,
    pasienadmisi_t.is_aps,
    pasienadmisi_t.is_pasientitipan,
    kelaspelayanan_m.urutankelas,
    kelaspelayanan_m.bpjs_kelas,
    pendaftaran_t.keterangan_pendaftaran,
    pasienadmisi_t.asuransipasien_id,
    pendaftaran_t.is_stopakomodasi,
    pendaftaran_t.tgl_stopakomodasi,
        CASE
            WHEN (implementasi.sisa = 0) THEN true
            WHEN (implementasi.sisa <> 0) THEN false
            ELSE false
        END AS status_implementasi,
    pasienadmisi_t.kelas_ditagihkan_id
   FROM (((((((((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN pegawai_m dokter_pendaftaran ON ((pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id)))
     JOIN pegawai_m dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN bpjs_t ON ((pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (asesmenmedis_t.is_deleted = false))))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN asesmenawal_t ON ((pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id)))
     LEFT JOIN rencanapulang_t ON (((pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id) AND (rencanapulang_t.is_deleted = false))))
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM (pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON (((pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM (pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON (((pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON (((pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id))))
     LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
            monitorsetdiagnosa_t.pendaftaran_id,
            monitorsetdiagnosa_t.pasienadmisi_id,
            monitorsetdiagnosa_t.diag_utama_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            monitorsetdiagnosa_t.diag_penyerta,
            monitorsetdiagnosa_t.diag_tindakan,
            monitorsetdiagnosa_t.total,
            monitorsetdiagnosa_t.is_dokter
           FROM (monitorsetdiagnosa_t
             JOIN diagnosa_m ON ((monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id)))
          WHERE (monitorsetdiagnosa_t.is_deleted = false)) monitorsetdiagnosa ON ((pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id)))
     LEFT JOIN ( SELECT cppt_t.pendaftaran_id,
            (count(instruksitindakan_t.status_implementasi) + count(instruksitindakanbmhp_t.status_implementasi)) AS sisa
           FROM (((cppt_t
             LEFT JOIN instruksi_t ON (((cppt_t.cppt_id = instruksi_t.cppt_id) AND (instruksi_t.is_deleted = false))))
             LEFT JOIN instruksitindakan_t ON (((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id) AND (instruksitindakan_t.is_deleted = false) AND ((instruksitindakan_t.status_implementasi)::text <> '455'::text))))
             LEFT JOIN instruksitindakanbmhp_t ON (((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id) AND (instruksitindakanbmhp_t.is_deleted = false) AND ((instruksitindakanbmhp_t.status_implementasi)::text <> '455'::text))))
          WHERE (cppt_t.is_deleted = false)
          GROUP BY cppt_t.pendaftaran_id) implementasi ON ((pendaftaran_t.pendaftaran_id = implementasi.pendaftaran_id)))
  WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false));");
         
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200722_091233_migrate_mhkn_20200722 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200722_091233_migrate_mhkn_20200722 cannot be reverted.\n";

        return false;
    }
    */
}
