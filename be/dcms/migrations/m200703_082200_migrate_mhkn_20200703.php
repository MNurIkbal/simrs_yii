<?php

use yii\db\Migration;

/**
 * Class m200703_082200_migrate_mhkn_20200703
 */
class m200703_082200_migrate_mhkn_20200703 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
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
    pasienmasukpenunjang_t.pegawai_id AS doctor_id,
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
   FROM ((((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN lookup_m jeniskelamin ON (((pasien_m.jeniskelamin)::integer = jeniskelamin.lookup_id)))
     LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
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
    pasienmasukpenunjang_t.pegawai_id AS doctor_id,
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
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
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

        $this->execute('DROP VIEW if exists "public"."infopasienpulangrjrd_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienpulangrjrd_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.tgl_pendaftaran,
    pasienpulang_t.tglpasienpulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    carakeluar_m.carakeluar_nama,
    pendaftaran_t.instalasi_id,
    pasienpulang_t.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.pasienpulang_id,
    pendaftaran_t.umur,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama
   FROM (((((((((pendaftaran_t
     JOIN pasienpulang_t ON (((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id) AND (pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id))))
     JOIN pasien_m ON ((pasienpulang_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
  WHERE (pasienpulang_t.pasienbatalpulang_id IS NULL);");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200703_082200_migrate_mhkn_20200703 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200703_082200_migrate_mhkn_20200703 cannot be reverted.\n";

        return false;
    }
    */
}
