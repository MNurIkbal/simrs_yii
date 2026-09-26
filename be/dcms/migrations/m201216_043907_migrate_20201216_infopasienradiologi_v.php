<?php

use yii\db\Migration;

/**
 * Class m201216_043907_migrate_20201216_infopasienradiologi_v
 */
class m201216_043907_migrate_20201216_infopasienradiologi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopasienradiologi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienradiologi_v\" AS  SELECT header.tipe_pasien,
    header.pendaftaran_id,
    header.pasienmasukpenunjang_id,
    header.pasienkirimkeunitlain_id,
    header.tglmasukpenunjang,
    header.no_pendaftaran,
    header.no_masukpenunjang,
    header.no_rekam_medik,
    header.nama_pasien,
        CASE header.is_mcu
            WHEN true THEN header.pegawai_id
            ELSE detail.dokter_id_detail
        END AS pegawai_id,
        CASE header.is_mcu
            WHEN true THEN header.dokter_penunjang
            ELSE detail.dokter_nama_detail
        END AS dokter_penunjang,
    header.no_rujukan,
    header.asalrujukan_id,
    header.asalrujukan_nama,
    header.ruanganasal_id,
    header.ruangan_nama,
    header.status_periksa,
    header.no_antrian,
    header.carabayar_id,
    header.carabayar_nama,
    header.penjamin_id,
    header.penjamin_nama,
    header.kelaspelayanan_id,
    header.kelaspelayanan_nama,
    header.umur,
    header.jeniskelamin,
    header.j_kelamin,
    header.tanggal_lahir,
    header.kuning,
    header.merah,
    header.ungu,
    header.coklat,
    header.tgl_rujukan,
    header.pasien_id,
    header.pasienadmisi_id,
    header.ruangan_id,
    header.is_bayar,
    header.status_penunjang,
    header.catatan_dokterpengirim,
    header.no_telepon_pasien,
    header.dokter_perujuk_id,
    header.dokter_perujuk_nama,
        CASE header.is_mcu
            WHEN true THEN header.nama_dokter_penunjang::character varying
            ELSE detail.dokter_nama_detail
        END AS nama_dokter_penunjang,
    header.unit_asal,
    header.nama_diagnosa,
    header.is_mcu,
    detail.jenispemeriksaanrad_nama,
    detail.tipepaket_nama,
    detail.detail_2,
    detail.daftartindakan_id,
    detail.daftartindakan_nama,
        CASE
            WHEN hasil.is_hasil >= 1 THEN true
            ELSE false
        END AS is_hasil,
    detail.tindakanpelayanan_id,
    hasil.tgl_verifikasi,
    header.created_by,
    header.penjamin_kode,
    detail.cyto_tindakan,
    detail.qty_tindakan,
    header.no_identitas_pasien
   FROM ( SELECT 'ORDER'::text AS tipe_pasien,
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
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
            pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            pasienadmisi_t.pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasien_m.no_telepon_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
            concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            diagnosa.diagnosa_utama AS nama_diagnosa,
            false AS is_mcu,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien
           FROM pasienmasukpenunjang_t
             JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pegawai_m dokter_perujuk ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
             LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
                  WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
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
                  WHERE cppt_t.a_diag_utama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                     JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
                  WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5
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
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
            pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            pasienadmisi_t.pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasien_m.no_telepon_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
            concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            diagnosa.diagnosa_utama AS nama_diagnosa,
            false AS is_mcu,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien
           FROM pasienmasukpenunjang_t
             JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
                  WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
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
                  WHERE cppt_t.a_diag_utama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                     JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
                  WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
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
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
            pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            NULL::integer AS pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasien_m.no_telepon_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
            concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            diagnosa.diagnosa_utama AS nama_diagnosa,
            false AS is_mcu,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien
           FROM pasienmasukpenunjang_t
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
                  WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
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
                  WHERE cppt_t.a_diag_utama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                     JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
                  WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
          WHERE pendaftaran_t.instalasi_id = 5
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
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            NULL::character varying AS no_rujukan,
            pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
            'APS'::character varying AS asalrujukan_nama,
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
            pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
            pasienmasukpenunjang_t.pasien_id,
            NULL::integer AS pasienadmisi_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.is_bayar,
            pasienkirimkeunitlain_t.status_penunjang,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            pasien_m.no_telepon_pasien,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
            concat(COALESCE(fgetnamalookup(dokter_perujuk.gelardepan::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
            concat(COALESCE(fgetnamalookup(pegawai_m.gelardepan::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN 'Pendaftaran'::text
                    ELSE 'Unit'::text
                END AS unit_asal,
            diagnosa.diagnosa_utama AS nama_diagnosa,
                CASE ruangan_pendaftaran.instalasi_id
                    WHEN 21 THEN true
                    ELSE false
                END AS is_mcu,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien
           FROM pasienmasukpenunjang_t
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
             LEFT JOIN pegawai_m dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN gelarbelakang_m gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                        CASE
                            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                            ELSE NULL::json
                        END AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
                  WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    cppt_t.a_diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
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
                  WHERE cppt_t.a_diag_utama IS NOT NULL
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    resumemedisri_t.diag_utama AS diagnosa_utama
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN pasien_m pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
                     JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                     JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
                  WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
             LEFT JOIN ruangan_m ruangan_pendaftaran ON pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id
          WHERE ruang_penunjang.instalasi_id = 5 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.is_bayar = true) header
     LEFT JOIN ( SELECT 'NON_PAKET'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
            tindakanpelayanan_t.tipepaket_id,
            ''::character varying AS tipepaket_nama,
            NULL::text AS detail_2,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS dokter_id_detail,
            pegawai_m.nama_pegawai AS dokter_nama_detail,
            pasienmasukpenunjang_t.created_by
           FROM pasienmasukpenunjang_t
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
             LEFT JOIN pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
             LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             LEFT JOIN pegawai_m ON COALESCE(permintaankepenunjang_t.dokter_id::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id
          WHERE daftartindakan_m.kelompoktindakan_id = 10 AND tindakanpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT 'PAKET'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            NULL::text AS detail_2,
            paketpelayanan_mp.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS dokter_id_detail,
            pegawai_m.nama_pegawai AS dokter_nama_detail,
            pasienmasukpenunjang_t.created_by
           FROM pasienmasukpenunjang_t
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
             LEFT JOIN pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
             LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             LEFT JOIN pegawai_m ON COALESCE(permintaankepenunjang_t.dokter_id::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id
          WHERE daftartindakan_m.kelompoktindakan_id = 10 AND tindakanpelayanan_t.is_deleted IS FALSE
        UNION ALL
         SELECT 'PAKET_MCU'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            detail_1.j_rad AS jenispemeriksaanrad_nama,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            detail_1.detail_2,
            detail_1.detail_3id AS daftartindakan_id,
            detail_1.detail_3 AS daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            COALESCE(pasienmasukpenunjang_t.status_periksa, '477'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS dokter_id_detail,
            pegawai_m.nama_pegawai AS dokter_nama_detail,
            pasienmasukpenunjang_t.created_by
           FROM pasienmasukpenunjang_t
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    paketpelayanan_mp.paketdetail_id AS detail_2id,
                    paket_detail.tipepaket_nama AS detail_2,
                    paket_detail.daftartindakan_id AS detail_3id,
                    paket_detail.daftartindakan_nama AS detail_3,
                    paket_detail.p_rad,
                    paket_detail.j_rad
                   FROM tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            daftartindakan_m.daftartindakan_id,
                            daftartindakan_m.daftartindakan_nama,
                            pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                           FROM tipepaket_m a
                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id
                             JOIN ruangan_m ruangan_m_1 ON paketpelayanan_mp_1.ruangan_id = ruangan_m_1.ruangan_id AND ruangan_m_1.instalasi_id = 5
                             JOIN daftartindakan_m ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
                             LEFT JOIN pemeriksaanrad_m ON daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                             LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id) paket_detail ON paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id
                  WHERE tipepaket_m_1.is_deleted = false
                UNION ALL
                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    NULL::integer AS detail_2id,
                    NULL::character varying AS detail_2,
                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
                    tindakan_detail.daftartindakan_nama AS detail_3,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                   FROM tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN ruangan_m ruangan_m_1 ON paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id AND ruangan_m_1.instalasi_id = 5
                     JOIN daftartindakan_m tindakan_detail ON paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id
                     LEFT JOIN pemeriksaanrad_m ON tindakan_detail.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                  WHERE tipepaket_m_1.is_deleted = false) detail_1 ON tindakanpelayanan_t.tipepaket_id = detail_1.detail_1id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id) detail ON header.pasienmasukpenunjang_id = detail.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT count(*) AS is_hasil,
            hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
            hasilpemeriksaanrad_t.tindakanpelayanan_id,
            pemeriksaanrad_m.daftartindakan_id,
            hasilpemeriksaanrad_t.tgl_verifikasi
           FROM hasilpemeriksaanrad_t
             JOIN pemeriksaanrad_m ON pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id
          WHERE hasilpemeriksaanrad_t.is_deleted = false
          GROUP BY hasilpemeriksaanrad_t.pasienmasukpenunjang_id, hasilpemeriksaanrad_t.tindakanpelayanan_id, pemeriksaanrad_m.daftartindakan_id, hasilpemeriksaanrad_t.tgl_verifikasi) hasil ON header.pasienmasukpenunjang_id = hasil.pasienmasukpenunjang_id AND detail.tindakanpelayanan_id = hasil.tindakanpelayanan_id AND detail.daftartindakan_id = hasil.daftartindakan_id;");

        $this->execute('ALTER TABLE "public"."infopasienradiologi_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201216_043907_migrate_20201216_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201216_043907_migrate_20201216_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }
    */
}
