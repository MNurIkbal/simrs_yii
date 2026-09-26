<?php

use yii\db\Migration;

/**
 * Class m200803_072548_migrate_mhkn_20200803
 */
class m200803_072548_migrate_mhkn_20200803 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien
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
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
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
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien
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
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL))
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
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien
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
  WHERE ((pendaftaran_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
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
    pasienmasukpenunjang_t.is_hasil,
    pasien_m.alamat_pasien
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
  WHERE ((ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienmasukpenunjang_t.is_bayar = true));");

        $this->execute('DROP VIEW if exists "public"."infopasienrad_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienrad_v\" AS  SELECT 'ORDER'::text AS tipe_pasien,
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
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pasien_m.no_telepon_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
    concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa
   FROM ((((((((((((((pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pegawai_m dokter_perujuk ON ((COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
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
  WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
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
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pasien_m.no_telepon_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
    concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa
   FROM ((((((((((((((pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
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
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 5) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL))
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
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pasien_m.no_telepon_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
    concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa
   FROM ((((((((((((((pasienmasukpenunjang_t
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
     LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
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
  WHERE (pendaftaran_t.instalasi_id = 5)
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
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pasien_m.no_telepon_pasien,
    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
    concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), ''::character varying), ' ', dokter_perujuk.nama_pegawai, ' ', COALESCE(gelarbelakang_m.gelarbelakang_nama, ''::character varying)) AS nama_pegawai,
    concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), ''::character varying), ' ', pegawai_m.nama_pegawai, ' ', COALESCE(gelar_penunjang.gelarbelakang_nama, ''::character varying)) AS nama_dokter_penunjang,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN 'Pendaftaran'::text
            ELSE 'Unit'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa
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
     LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
     LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
     LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
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
  WHERE ((ruang_penunjang.instalasi_id = 5) AND (pendaftaran_t.is_aps = true) AND (pasienmasukpenunjang_t.is_bayar = true));");
        
        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
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
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienmasukpenunjang_t.catatan AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    (cppt_t.a_diag_utama ->> 'text'::text) AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM (((((((((((((((((pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
     LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (pasienadmisi_t.pegawai_id = cppt_t.pegawai_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true) AND (cppt_t.is_instruksi_pulang = false))))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 12) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
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
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM ((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pendaftaran_t.instalasi_id = 12) AND (pasienmasukpenunjang_t.is_bayar = true))
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
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
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama
   FROM (((((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ruangan_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (ruangan_penunjang.instalasi_id = 12) AND (pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) AND (pendaftaran_t.instalasi_id <> 12));");
    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200803_072548_migrate_mhkn_20200803 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200803_072548_migrate_mhkn_20200803 cannot be reverted.\n";

        return false;
    }
    */
}
