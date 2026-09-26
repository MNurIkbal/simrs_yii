<?php

use yii\db\Migration;

/**
 * Class m220506_072256_migrate_bridging_orderlab_v
 */
class m220506_072256_migrate_bridging_orderlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."bridging_orderlab_v";');

         $this->execute("
            CREATE VIEW \"public\".\"bridging_orderlab_v\" AS  SELECT 'a'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pasien_m.no_rekam_medik,
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
    pegawai_m.pegawai_id AS doctor_id,
    pegawai_m.nama_pegawai AS doctor_name,
    pendaftaran_t.kelaspelayanan_id AS class_id,
    kelas.kelaspelayanan_nama AS class_name,
    pasienmasukpenunjang_t.ruanganasal_id AS ward_id,
    ruangan_m.ruangan_nama AS ward_name,
    pasienadmisi_t.kamarruangan_id AS room_id,
    kamarruangan_m.kamarruangan_nokamar AS room_name,
    pasienadmisi_t.kamartempattidur_id AS bed_id,
    kamartempattidur_m.no_tempattidur AS bed_name,
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN diagnosa_ri.diagnosa_utama
            WHEN ruangan_m.instalasi_id = 2 THEN diagnosa_rd.diagnosa_utama
            ELSE diagnosa.diagnosa_utama
        END AS diagnosa_id,
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN diagnosa_ri.diagnosa_utama
            WHEN ruangan_m.instalasi_id = 2 THEN diagnosa_rd.diagnosa_utama
            ELSE diagnosa.diagnosa_utama
        END AS diagnosa_name,
    pasienmasukpenunjang_t.created_by AS reg_user_id,
    loginpemakai_k.nama_pemakai AS reg_user_name,
    pasienmasukpenunjang_t.created_date,
    NULL::text AS fax_number,
        CASE
            WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
               FROM permintaankepenunjang_t countcyto
              WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
            ELSE false
        END AS is_cyto,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            ELSE NULL::text
        END AS lis_reg_no,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            ELSE NULL::text
        END AS retrieved_dt,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            ELSE NULL::text
        END AS status,
        CASE
            WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) <> 0 THEN true
            ELSE
            CASE
                WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                ELSE false
            END
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang.no_masukpenunjang AS no_order,
                    tindakanpelayanan_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    pasienmasukpenunjang.pasienmasukpenunjang_id
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN ( SELECT a.daftartindakan_id,
                            a.pasienmasukpenunjang_id
                           FROM tindakanpelayanan_t a
                          WHERE a.is_deleted = false) tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN ( SELECT a.daftartindakan_id,
                            a.daftartindakan_kode,
                            a.daftartindakan_nama
                           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id) d) AS ordered_items,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasien_m.additional_pasien,
    propinsi_m.kode_propinsi AS kode_kemendag_propinsi,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendag_kabupaten,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendag_kecamatan,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kemendag_kelurahan,
    pasien_m.rt,
    pasien_m.rw,
    propinsi_m.propinsi_nama AS propinsi,
    kabupaten_m.kabupaten_nama AS kabupaten,
    kecamatan_m.kecamatan_nama AS kecamatan,
    kelurahan_m.kelurahan_nama AS kelurahan,
    negara_m.nama_negara AS negara,
    negara_m.kode_negara
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.penjamin_id,
            a.kelaspelayanan_id,
            a.pasienadmisi_id,
            a.pegawai_id
           FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.loginpemakai_id,
            a.nama_pemakai
           FROM loginpemakai_k a) loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelas ON pendaftaran_t.kelaspelayanan_id = kelas.kelaspelayanan_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pegawai_id,
            a.kamarruangan_id,
            a.kamartempattidur_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.jeniskelamin,
            a.propinsi_id,
            a.kecamatan_id,
            a.kabupaten_id,
            a.kelurahan_id,
            a.negara_id,
            a.tanggal_lahir,
            a.nama_pasien,
            a.alamat_pasien,
            a.no_telepon_pasien,
            a.no_mobile_pasien,
            a.alamatemail,
            a.additional_pasien,
            a.rt,
            a.rw
           FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.lookup_id,
            a.lookup_kode,
            a.lookup_value
           FROM lookup_m a) jeniskelamin ON pasien_m.jeniskelamin::integer = jeniskelamin.lookup_id
     JOIN ( SELECT a.pasienkirimkeunitlain_id,
            a.additional_data,
            a.instalasi_id
           FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.pendaftaran_id,
                    a.kelompokdiagnosa_id,
                    a.diagnosa_pasien
                   FROM pasienmorbiditas_t a
                  WHERE a.is_deleted = false) pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL) diagnosa_rd ON pendaftaran_t.pendaftaran_id = diagnosa_rd.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.pasienadmisi_id
                   FROM pasienadmisi_t a) pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.diag_utama
                   FROM resumemedisri_t a) resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa_ri ON pendaftaran_t.pendaftaran_id = diagnosa_ri.pendaftaran_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_wynacom_t.his_reg_no
           FROM hasilpemeriksaanlab_wynacom_t
          GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.his_reg_no::text
     LEFT JOIN ( SELECT a.propinsi_id,
            a.kode_propinsi,
            a.propinsi_nama
           FROM propinsi_m a) propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN ( SELECT a.kabupaten_id,
            a.kabupaten_nama,
            a.kode_kabupaten
           FROM kabupaten_m a) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN ( SELECT a.kecamatan_id,
            a.kecamatan_nama,
            a.kode_kecamatan
           FROM kecamatan_m a) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN ( SELECT a.kelurahan_id,
            a.kelurahan_nama,
            a.kode_kelurahan
           FROM kelurahan_m a) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN ( SELECT a.negara_id,
            a.nama_negara,
            a.kode_negara
           FROM negara_m a) negara_m ON pasien_m.negara_id = negara_m.negara_id
  WHERE pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NOT NULL AND pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT 'b'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pasien_m.no_rekam_medik,
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
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN diagnosa_ri.diagnosa_utama
            WHEN ruangan_m.instalasi_id = 2 THEN diagnosa_rd.diagnosa_utama
            ELSE diagnosa.diagnosa_utama
        END AS diagnosa_id,
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN diagnosa_ri.diagnosa_utama
            WHEN ruangan_m.instalasi_id = 2 THEN diagnosa_rd.diagnosa_utama
            ELSE diagnosa.diagnosa_utama
        END AS diagnosa_name,
    pasienmasukpenunjang_t.created_by AS reg_user_id,
    loginpemakai_k.nama_pemakai AS reg_user_name,
    pasienmasukpenunjang_t.created_date,
    NULL::text AS fax_number,
        CASE
            WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
               FROM permintaankepenunjang_t countcyto
              WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
            ELSE false
        END AS is_cyto,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            ELSE NULL::text
        END AS lis_reg_no,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            ELSE NULL::text
        END AS retrieved_dt,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            ELSE NULL::text
        END AS status,
        CASE
            WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) <> 0 THEN true
            ELSE
            CASE
                WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                ELSE false
            END
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang.no_masukpenunjang AS no_order,
                    tindakanpelayanan_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    pasienmasukpenunjang.pasienmasukpenunjang_id
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN ( SELECT b.daftartindakan_id,
                            b.pasienmasukpenunjang_id
                           FROM tindakanpelayanan_t b
                          WHERE b.is_deleted = false) tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN ( SELECT b.daftartindakan_id,
                            b.daftartindakan_kode,
                            b.daftartindakan_nama
                           FROM daftartindakan_m b) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id) d) AS ordered_items,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            WHEN 3 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasien_m.additional_pasien,
    propinsi_m.kode_propinsi AS kode_kemendag_propinsi,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendag_kabupaten,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendag_kecamatan,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kemendag_kelurahan,
    pasien_m.rt,
    pasien_m.rw,
    propinsi_m.propinsi_nama AS propinsi,
    kabupaten_m.kabupaten_nama AS kabupaten,
    kecamatan_m.kecamatan_nama AS kecamatan,
    kelurahan_m.kelurahan_nama AS kelurahan,
    negara_m.nama_negara AS negara,
    negara_m.kode_negara
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT b.pendaftaran_id,
            b.no_pendaftaran,
            b.penjamin_id,
            b.kelaspelayanan_id,
            b.pasienadmisi_id,
            b.pegawai_id,
            b.rujukan_id,
            b.carabayar_id,
            b.instalasi_id
           FROM pendaftaran_t b) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT b.pasien_id,
            b.no_rekam_medik,
            b.jeniskelamin,
            b.propinsi_id,
            b.kecamatan_id,
            b.kabupaten_id,
            b.kelurahan_id,
            b.negara_id,
            b.tanggal_lahir,
            b.nama_pasien,
            b.alamat_pasien,
            b.no_telepon_pasien,
            b.no_mobile_pasien,
            b.alamatemail,
            b.additional_pasien,
            b.rt,
            b.rw
           FROM pasien_m b) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT b.lookup_id,
            b.lookup_kode,
            b.lookup_value
           FROM lookup_m b) jeniskelamin ON pasien_m.jeniskelamin::integer = jeniskelamin.lookup_id
     JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama,
            b.instalasi_id
           FROM ruangan_m b) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN ( SELECT b.instalasi_id,
            b.instalasi_nama
           FROM instalasi_m b) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT b.rujukan_id,
            b.asalrujukan_id,
            b.rujukandari_id
           FROM rujukan_t b) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT b.asalrujukan_id,
            b.asalrujukan_nama
           FROM asalrujukan_m b) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN ( SELECT b.perujuk_id,
            b.namaperujuk
           FROM perujuk_m b) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
           FROM carabayar_m b) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT b.penjamin_id,
            b.penjamin_nama
           FROM penjamin_m b) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
           FROM kelaspelayanan_m b) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT b.pasienkirimkeunitlain_id,
            b.additional_data
           FROM pasienkirimkeunitlain_t b) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT b.loginpemakai_id,
            b.nama_pemakai
           FROM loginpemakai_k b) loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT b.pendaftaran_id,
                    b.kelompokdiagnosa_id,
                    b.diagnosa_pasien
                   FROM pasienmorbiditas_t b
                  WHERE b.is_deleted = false) pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL) diagnosa_rd ON pendaftaran_t.pendaftaran_id = diagnosa_rd.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT b.pasienadmisi_id
                   FROM pasienadmisi_t b) pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN ( SELECT b.pendaftaran_id,
                    b.pasienadmisi_id,
                    b.diag_utama
                   FROM resumemedisri_t b) resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa_ri ON pendaftaran_t.pendaftaran_id = diagnosa_ri.pendaftaran_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_wynacom_t.his_reg_no
           FROM hasilpemeriksaanlab_wynacom_t
          GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.his_reg_no::text
     LEFT JOIN ( SELECT b.propinsi_id,
            b.kode_propinsi,
            b.propinsi_nama
           FROM propinsi_m b) propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN ( SELECT b.kabupaten_id,
            b.kabupaten_nama,
            b.kode_kabupaten
           FROM kabupaten_m b) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN ( SELECT b.kecamatan_id,
            b.kecamatan_nama,
            b.kode_kecamatan
           FROM kecamatan_m b) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN ( SELECT b.kelurahan_id,
            b.kelurahan_nama,
            b.kode_kelurahan
           FROM kelurahan_m b) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN ( SELECT b.negara_id,
            b.nama_negara,
            b.kode_negara
           FROM negara_m b) negara_m ON pasien_m.negara_id = negara_m.negara_id
  WHERE pendaftaran_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
 SELECT 'c'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pasien_m.no_rekam_medik,
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
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN diagnosa_ri.diagnosa_utama
            WHEN ruangan_m.instalasi_id = 2 THEN diagnosa_rd.diagnosa_utama
            ELSE diagnosa.diagnosa_utama
        END AS diagnosa_id,
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN diagnosa_ri.diagnosa_utama
            WHEN ruangan_m.instalasi_id = 2 THEN diagnosa_rd.diagnosa_utama
            ELSE diagnosa.diagnosa_utama
        END AS diagnosa_name,
    pasienmasukpenunjang_t.created_by AS reg_user_id,
    loginpemakai_k.nama_pemakai AS reg_user_name,
    pasienmasukpenunjang_t.created_date,
    NULL::text AS fax_number,
        CASE
            WHEN (( SELECT count(countcyto.permintaankepenunjang_id) AS count
               FROM permintaankepenunjang_t countcyto
              WHERE countcyto.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id AND countcyto.is_cyto IS TRUE)) > 0 THEN true
            ELSE false
        END AS is_cyto,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'lis_reg_no'::text
            ELSE NULL::text
        END AS lis_reg_no,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_dt'::text
            ELSE NULL::text
        END AS retrieved_dt,
        CASE
            WHEN pasienkirimkeunitlain_t.additional_data IS NOT NULL THEN (pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            WHEN pasienmasukpenunjang_t.additional_data IS NOT NULL THEN (pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'status'::text
            ELSE NULL::text
        END AS status,
        CASE
            WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'retrieved_flag'::text) = '1'::text THEN true
            ELSE false
        END AS retrieved_flag,
        CASE
            WHEN COALESCE(hasil.jml_hasil, 0::bigint) <> 0 THEN true
            ELSE
            CASE
                WHEN ((pasienkirimkeunitlain_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                WHEN ((pasienmasukpenunjang_t.additional_data::json -> 'lisattr'::text) ->> 'received_flag'::text) = '1'::text THEN true
                ELSE false
            END
        END AS received_flag,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
           FROM ( SELECT pasienmasukpenunjang.no_masukpenunjang AS no_order,
                    tindakanpelayanan_t.daftartindakan_id AS no_pemeriksaan,
                    daftartindakan_m.daftartindakan_kode AS order_item_id,
                    daftartindakan_m.daftartindakan_nama AS order_item_name,
                    pasienmasukpenunjang.pasienmasukpenunjang_id
                   FROM pasienmasukpenunjang_t pasienmasukpenunjang
                     JOIN ( SELECT c.daftartindakan_id,
                            c.pasienmasukpenunjang_id
                           FROM tindakanpelayanan_t c
                          WHERE c.is_deleted = false) tindakanpelayanan_t ON pasienmasukpenunjang.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                     JOIN ( SELECT c.daftartindakan_id,
                            c.daftartindakan_kode,
                            c.daftartindakan_nama
                           FROM daftartindakan_m c) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienmasukpenunjang.pasienmasukpenunjang_id) d) AS ordered_items,
        CASE instalasi_m.instalasi_id
            WHEN 2 THEN true
            ELSE
            CASE pendaftaran_t.penjamin_id
                WHEN 1 THEN pasienmasukpenunjang_t.is_bayar
                ELSE true
            END
        END AS is_bayar,
    pasien_m.additional_pasien,
    propinsi_m.kode_propinsi AS kode_kemendag_propinsi,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendag_kabupaten,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendag_kecamatan,
    concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kemendag_kelurahan,
    pasien_m.rt,
    pasien_m.rw,
    propinsi_m.propinsi_nama AS propinsi,
    kabupaten_m.kabupaten_nama AS kabupaten,
    kecamatan_m.kecamatan_nama AS kecamatan,
    kelurahan_m.kelurahan_nama AS kelurahan,
    negara_m.nama_negara AS negara,
    negara_m.kode_negara
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT c.pendaftaran_id,
            c.no_pendaftaran,
            c.penjamin_id,
            c.kelaspelayanan_id,
            c.pasienadmisi_id,
            c.pegawai_id,
            c.carabayar_id,
            c.is_aps
           FROM pendaftaran_t c) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT c.pasien_id,
            c.no_rekam_medik,
            c.jeniskelamin,
            c.propinsi_id,
            c.kecamatan_id,
            c.kabupaten_id,
            c.kelurahan_id,
            c.negara_id,
            c.tanggal_lahir,
            c.nama_pasien,
            c.alamat_pasien,
            c.no_telepon_pasien,
            c.no_mobile_pasien,
            c.alamatemail,
            c.additional_pasien,
            c.rt,
            c.rw
           FROM pasien_m c) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT c.carabayar_id,
            c.carabayar_nama
           FROM carabayar_m c) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT c.penjamin_id,
            c.penjamin_nama
           FROM penjamin_m c) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT c.kelaspelayanan_id,
            c.kelaspelayanan_nama
           FROM kelaspelayanan_m c) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT c.pasienkirimkeunitlain_id,
            c.additional_data,
            c.instalasi_id
           FROM pasienkirimkeunitlain_t c) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama,
            c.instalasi_id
           FROM ruangan_m c) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN ( SELECT c.instalasi_id,
            c.instalasi_nama
           FROM instalasi_m c) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama,
            c.instalasi_id
           FROM ruangan_m c) ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     LEFT JOIN ( SELECT c.lookup_id,
            c.lookup_kode,
            c.lookup_value
           FROM lookup_m c) jeniskelamin ON pasien_m.jeniskelamin::integer = jeniskelamin.lookup_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT c.pendaftaran_id,
                    c.kelompokdiagnosa_id,
                    c.diagnosa_pasien
                   FROM pasienmorbiditas_t c
                  WHERE c.is_deleted = false) pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL) diagnosa_rd ON pendaftaran_t.pendaftaran_id = diagnosa_rd.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT c.pasienadmisi_id
                   FROM pasienadmisi_t c) pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN ( SELECT c.pendaftaran_id,
                    c.pasienadmisi_id,
                    c.diag_utama
                   FROM resumemedisri_t c) resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa_ri ON pendaftaran_t.pendaftaran_id = diagnosa_ri.pendaftaran_id
     JOIN ( SELECT c.loginpemakai_id,
            c.nama_pemakai
           FROM loginpemakai_k c) loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT count(*) AS jml_hasil,
            hasilpemeriksaanlab_wynacom_t.his_reg_no
           FROM hasilpemeriksaanlab_wynacom_t
          GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasil ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil.his_reg_no::text
     LEFT JOIN ( SELECT c.propinsi_id,
            c.kode_propinsi,
            c.propinsi_nama
           FROM propinsi_m c) propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN ( SELECT c.kabupaten_id,
            c.kabupaten_nama,
            c.kode_kabupaten
           FROM kabupaten_m c) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN ( SELECT c.kecamatan_id,
            c.kecamatan_nama,
            c.kode_kecamatan
           FROM kecamatan_m c) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN ( SELECT c.kelurahan_id,
            c.kelurahan_nama,
            c.kode_kelurahan
           FROM kelurahan_m c) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN ( SELECT c.negara_id,
            c.nama_negara,
            c.kode_negara
           FROM negara_m c) negara_m ON pasien_m.negara_id = negara_m.negara_id
  WHERE ruang_penunjang.instalasi_id = 4 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220506_072256_migrate_bridging_orderlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220506_072256_migrate_bridging_orderlab_v cannot be reverted.\n";

        return false;
    }
    */
}
