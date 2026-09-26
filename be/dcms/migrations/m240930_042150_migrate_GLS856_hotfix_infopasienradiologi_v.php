<?php

use yii\db\Migration;

/**
 * Class m240930_042150_migrate_GLS856_hotfix_infopasienradiologi_v
 */
class m240930_042150_migrate_GLS856_hotfix_infopasienradiologi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienradiologi_v;");

        $this->execute('
            CREATE VIEW  "public"."infopasienradiologi_v" AS  SELECT header.tipe_pasien,
    header.pendaftaran_id,
    header.pasienmasukpenunjang_id,
    header.pasienkirimkeunitlain_id,
    header.tglmasukpenunjang,
    header.no_pendaftaran,
    header.no_masukpenunjang,
    header.no_rekam_medik,
    header.nama_pasien, 
    detail.dokter_id_detail AS pegawai_id,
    detail.dokter_nama_detail AS dokter_penunjang,
    header.no_rujukan,
    header.asalrujukan_id,
    header.asalrujukan_nama,
    header.rujukandari_id,
    header.rujukandari_nama,
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
    detail.dokter_nama_detail AS nama_dokter_penunjang,
    header.unit_asal,
        CASE
            WHEN (( SELECT rsmt.diag_utama
               FROM resumemedisri_t rsmt
              WHERE header.pendaftaran_id = rsmt.pendaftaran_id AND rsmt.diag_utama IS NOT NULL)) IS NOT NULL THEN ( SELECT rsmt.diag_utama
               FROM resumemedisri_t rsmt
              WHERE header.pendaftaran_id = rsmt.pendaftaran_id AND rsmt.diag_utama IS NOT NULL)
            ELSE (( SELECT cpt.a_diag_utama AS diagnosa_utama
               FROM cppt_t cpt
              WHERE cpt.pendaftaran_id = header.pendaftaran_id AND cpt.a_diag_utama IS NOT NULL AND cpt.is_deleted IS FALSE AND cpt.additional_data = \'{"via_soap":true}\'::text
              ORDER BY cpt.cppt_id DESC
             LIMIT 1)
            UNION ALL
            ( SELECT pmb.diagnosa_pasien AS diagnosa_utama
               FROM pasienmorbiditas_t pmb
              WHERE pmb.pendaftaran_id = header.pendaftaran_id AND pmb.is_deleted = false AND pmb.kelompokdiagnosa_id = 2 AND pmb.diagnosa_pasien IS NOT NULL
             LIMIT 1))
        END AS nama_diagnosa,
    false AS is_mcu,
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
    header.no_identitas_pasien,
    detail.hasilpemeriksaanrad_id,
    detail.status_bayar,
    detail.status_periksa_penunjang,
    detail.status_batal,
    header.groupcarabayar_id,
    header.sepesial_pemeriksaan,
    header.jenis_kelamin_kode,
        CASE
            WHEN hasil.tgl_verifikasi IS NOT NULL THEN true
            ELSE false
        END AS is_selesai,
    detail.tindakanpelayananasal_id,
    detail.tgl_ambilfoto,
    detail.tgl_hasilrad,
    header.carabayar_kode_warna,
    COALESCE(detail.is_referred, false) AS is_referred,
    detail.nosuratrujukan,
    detail.tgldirujuk,
    detail.rumahsakit_rujukan,
    detail.alamat_rsrujukan,
    detail.telp_fax,
    detail.alasandirujuk,
    detail.pegawai_id_perujuk,
    detail.pegawai_nama_perujuk,
    detail.rujukankeluar_id,
    detail.diagnosa_dirujuk,
    detail.additional_data,
    detail.is_read
   FROM ( SELECT \'ORDER\'::text AS tipe_pasien,
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
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            penjamin_m.carabayar_id,
            carabayar_m.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            look_jeniskelamin.jeniskelamin AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> \'resiko_jatuh\'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> \'alergi\'::text AS merah,
            pendaftaran_t.label_gelang::json ->> \'dnr\'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> \'duplikat\'::text AS coklat,
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
            concat(COALESCE(look_gelardepan.gelardepan), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
            concat(COALESCE(lookpeg_gelardepan.gelardepan), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN \'Pendaftaran\'::text
                    ELSE \'Unit\'::text
                END AS unit_asal,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien,
            carabayar_m.groupcarabayar_id,
            true AS sepesial_pemeriksaan,
            look_jeniskelamin.jeniskelamin_kode AS jenis_kelamin_kode,
            rujukan_t.asalrujukan_id,
            rujukan_t.rujukandari_id,
                CASE
                    WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN \'ORDER\'::text::character varying
                    ELSE asalrujukan_m.asalrujukan_nama
                END AS asalrujukan_nama,
                CASE
                    WHEN perujuk_m.namaperujuk IS NULL THEN ruangan_m.ruangan_nama
                    ELSE perujuk_m.namaperujuk
                END AS rujukandari_nama,
            carabayar_m.carabayar_kode_warna,
            false AS is_mcu
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.pasienadmisi_id,
                    a.no_orderkeunitlain,
                    a.tgl_kirimpasien,
                    a.status_penunjang,
                    a.catatan_dokterpengirim,
                    a.instalasi_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.is_aps,
                    a.pegawai_id,
                    a.rujukan_id,
                    a.no_pendaftaran,
                    a.umur,
                    a.label_gelang
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pendaftaran_t.is_aps = false
             JOIN ( SELECT a.pasienadmisi_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.pegawai_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.no_telepon_pasien,
                    a.no_identitas_pasien,
                    a.additional_pasien
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.instalasi_id
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.penjamin_id,
                    a.carabayar_id,
                    a.penjamin_kode,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) dokter_perujuk ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id AND pendaftaran_t.is_aps = true
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS jeniskelamin,
                    a.lookup_kode AS jeniskelamin_kode
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) look_gelardepan ON dokter_perujuk.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) lookpeg_gelardepan ON dokter_perujuk.gelardepan::integer = lookpeg_gelardepan.lookup_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5
        UNION ALL
         SELECT \'ORDER\'::text AS tipe_pasien,
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
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            look_jeniskelamin.jeniskelamin AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> \'resiko_jatuh\'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> \'alergi\'::text AS merah,
            pendaftaran_t.label_gelang::json ->> \'dnr\'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> \'duplikat\'::text AS coklat,
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
            concat(COALESCE(look_gelardepan.gelardepan), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
            concat(COALESCE(lookpeg_gelardepan.gelardepan), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN \'Pendaftaran\'::text
                    ELSE \'Unit\'::text
                END AS unit_asal,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien,
            carabayar_m.groupcarabayar_id,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 2 THEN true
                    ELSE false
                END AS sepesial_pemeriksaan,
            look_jeniskelamin.jeniskelamin_kode AS jenis_kelamin_kode,
            rujukan_t.asalrujukan_id,
            rujukan_t.rujukandari_id,
                CASE
                    WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN \'ORDER\'::text::character varying
                    ELSE asalrujukan_m.asalrujukan_nama
                END AS asalrujukan_nama,
                CASE
                    WHEN perujuk_m.namaperujuk IS NULL THEN ruangan_m.ruangan_nama
                    ELSE perujuk_m.namaperujuk
                END AS rujukandari_nama,
            carabayar_m.carabayar_kode_warna,
            false AS is_mcu
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.no_orderkeunitlain,
                    a.tgl_kirimpasien,
                    a.status_penunjang,
                    a.catatan_dokterpengirim,
                    a.instalasi_id,
                    a.pasienadmisi_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.pegawai_id,
                    a.rujukan_id,
                    a.no_pendaftaran,
                    a.umur,
                    a.label_gelang,
                    a.instalasi_id,
                    a.is_aps
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.no_telepon_pasien,
                    a.no_identitas_pasien,
                    a.additional_pasien
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.penjamin_kode
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id AND pendaftaran_t.is_aps = true
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS jeniskelamin,
                    a.lookup_kode AS jeniskelamin_kode
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) look_gelardepan ON dokter_perujuk.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) lookpeg_gelardepan ON dokter_perujuk.gelardepan::integer = lookpeg_gelardepan.lookup_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
        UNION ALL
         SELECT \'RUJUKAN RS\'::text AS tipe_pasien,
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
            rujukan_t.rujukandari_id AS ruanganasal_id,
            perujuk_m.namaperujuk AS ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            look_jeniskelamin.jeniskelamin AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> \'resiko_jatuh\'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> \'alergi\'::text AS merah,
            pendaftaran_t.label_gelang::json ->> \'dnr\'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> \'duplikat\'::text AS coklat,
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
            concat(COALESCE(look_gelardepan.gelardepan), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
            concat(COALESCE(lookpeg_gelardepan.gelardepan), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN \'Pendaftaran\'::text
                    ELSE \'Unit\'::text
                END AS unit_asal,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien,
            carabayar_m.groupcarabayar_id,
            false AS sepesial_pemeriksaan,
            look_jeniskelamin.jeniskelamin_kode AS jenis_kelamin_kode,
            rujukan_t.asalrujukan_id,
            rujukan_t.rujukandari_id,
                CASE
                    WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN \'ORDER\'::text::character varying
                    ELSE asalrujukan_m.asalrujukan_nama
                END AS asalrujukan_nama,
                CASE
                    WHEN perujuk_m.namaperujuk IS NULL THEN ruangan_m.ruangan_nama
                    ELSE perujuk_m.namaperujuk
                END AS rujukandari_nama,
            carabayar_m.carabayar_kode_warna,
                CASE pendaftaran_t.instalasi_id
                    WHEN 21 THEN true
                    ELSE false
                END AS is_mcu
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.rujukan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.pegawai_id,
                    a.ruangan_id,
                    a.tgl_pendaftaran,
                    a.no_pendaftaran,
                    a.umur,
                    a.label_gelang,
                    a.instalasi_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.no_telepon_pasien,
                    a.no_identitas_pasien,
                    a.additional_pasien
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id,
                    a.no_rujukan
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.penjamin_kode
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.status_penunjang,
                    a.catatan_dokterpengirim
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS jeniskelamin,
                    a.lookup_kode AS jeniskelamin_kode
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) look_gelardepan ON dokter_perujuk.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) lookpeg_gelardepan ON dokter_perujuk.gelardepan::integer = lookpeg_gelardepan.lookup_id
          WHERE pendaftaran_t.instalasi_id = 5 AND pasienmasukpenunjang_t.ruanganasal_id = 44
        UNION ALL
         SELECT \'APS\'::text AS tipe_pasien,
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
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangan_m.ruangan_nama,
            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.no_antrian,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            look_jeniskelamin.jeniskelamin AS j_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.label_gelang::json ->> \'resiko_jatuh\'::text AS kuning,
            pendaftaran_t.label_gelang::json ->> \'alergi\'::text AS merah,
            pendaftaran_t.label_gelang::json ->> \'dnr\'::text AS ungu,
            pendaftaran_t.label_gelang::json ->> \'duplikat\'::text AS coklat,
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
            concat(COALESCE(look_gelardepan.gelardepan), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
            concat(COALESCE(lookpeg_gelardepan.gelardepan), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
                    WHEN 0 THEN \'Pendaftaran\'::text
                    ELSE \'Unit\'::text
                END AS unit_asal,
            pasienmasukpenunjang_t.created_by,
            penjamin_m.penjamin_kode,
            COALESCE(pasien_m.no_identitas_pasien, pasien_m.additional_pasien::character varying) AS no_identitas_pasien,
            carabayar_m.groupcarabayar_id,
            false AS sepesial_pemeriksaan,
            look_jeniskelamin.jeniskelamin_kode AS jenis_kelamin_kode,
            rujukan_t.asalrujukan_id,
            rujukan_t.rujukandari_id,
                CASE
                    WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN \'APS\'::text::character varying
                    ELSE asalrujukan_m.asalrujukan_nama
                END AS asalrujukan_nama,
                CASE
                    WHEN perujuk_m.namaperujuk IS NULL THEN ruangan_m.ruangan_nama
                    ELSE perujuk_m.namaperujuk
                END AS rujukandari_nama,
            carabayar_m.carabayar_kode_warna,
                CASE pendaftaran_t.instalasi_id
                    WHEN 21 THEN true
                    ELSE false
                END AS is_mcu
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.kelaspelayanan_id,
                    a.pegawai_id,
                    a.rujukan_id,
                    a.ruangan_id,
                    a.tgl_pendaftaran,
                    a.no_pendaftaran,
                    a.umur,
                    a.label_gelang,
                    a.is_aps,
                    a.instalasi_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.jeniskelamin,
                    a.tanggal_lahir,
                    a.no_telepon_pasien,
                    a.no_identitas_pasien,
                    a.additional_pasien
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.penjamin_kode
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.status_penunjang,
                    a.catatan_dokterpengirim
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang_m ON dokter_perujuk.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelar_penunjang ON pegawai_m.gelarbelakang::integer = gelar_penunjang.gelarbelakang_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS jeniskelamin,
                    a.lookup_kode AS jeniskelamin_kode
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) look_gelardepan ON dokter_perujuk.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) lookpeg_gelardepan ON dokter_perujuk.gelardepan::integer = lookpeg_gelardepan.lookup_id
             LEFT JOIN ruangan_m ruangan_pendaftaran ON pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id
          WHERE ruang_penunjang.instalasi_id = 5 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) header
     LEFT JOIN ( SELECT \'NON_PAKET\'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
            tindakanpelayanan_t.tipepaket_id,
            \'\'::character varying AS tipepaket_nama,
            NULL::text AS detail_2,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS dokter_id_detail,
            pegawai_m.nama_pegawai AS dokter_nama_detail,
            pasienmasukpenunjang_t.created_by,
            hasilpemeriksaanrad.hasilpemeriksaanrad_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN false
                    ELSE true
                END AS status_bayar,
                CASE
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NULL THEN \'BELUM PERIKSA\'::text
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL THEN \'SELESAI\'::text
                    WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN \'BATAL\'::text
                    ELSE NULL::text
                END AS status_periksa_penunjang,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN true
                    ELSE false
                END AS status_batal,
            tindakanpelayanan_t.tindakanpelayananasal_id,
            hasilpemeriksaanrad.tgl_ambilfoto,
            hasilpemeriksaanrad.tgl_hasilrad,
            rujukan_t.asalrujukan_id,
            rujukan_t.rujukandari_id,
                CASE
                    WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN \'ORDER\'::text::character varying
                    ELSE asalrujukan_m.asalrujukan_nama
                END AS asalrujukan_nama,
                CASE
                    WHEN perujuk_m.namaperujuk IS NULL THEN ruangan_m.ruangan_nama
                    ELSE perujuk_m.namaperujuk
                END AS rujukandari_nama,
            permintaankepenunjang_t.is_referred,
            pasiendirujukkeluar_t.nosuratrujukan,
            pasiendirujukkeluar_t.tgldirujuk,
            rujukankeluar_m.rumahsakit_rujukan,
            rujukankeluar_m.alamat_rsrujukan,
            rujukankeluar_m.telp_fax,
            pasiendirujukkeluar_t.alasandirujuk,
            pasiendirujukkeluar_t.pegawai_id AS pegawai_id_perujuk,
            pegawai_perujuk.nama_pegawai AS pegawai_nama_perujuk,
            rujukankeluar_m.rujukankeluar_id,
            pasiendirujukkeluar_t.diagnosa AS diagnosa_dirujuk,
            hasilpemeriksaanrad.additional_data,
            hasilpemeriksaanrad.is_read
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pasienmasukpenunjang_id,
                    a.tindakanpelayananasal_id,
                    a.daftartindakan_id,
                    a.dokterpenanggungjawab_id,
                    a.tgl_tindakan,
                    a.tipepaket_id,
                    a.tarif_satuan,
                    a.cyto_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.qty_tindakan,
                    a.tindakansudahbayar_id,
                    a.is_deleted,
                    a.instalasi_id
                   FROM tindakanpelayanan_t a
                  WHERE a.tindakanpelayananasal_id IS NULL) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT a.permintaankepenunjang_id,
                    a.tindakanpelayanan_id,
                    a.pemeriksaanrad_id,
                    a.dokter_id,
                    a.is_referred
                   FROM permintaankepenunjang_t a
                  WHERE a.is_deleted = false) permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
             LEFT JOIN ( SELECT a.pemeriksaanradiologi_id,
                    a.jenispemeriksaanrad_id
                   FROM pemeriksaanrad_m a) pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
             LEFT JOIN ( SELECT a.jenispemeriksaanrad_id,
                    a.jenispemeriksaanrad_nama
                   FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON COALESCE(permintaankepenunjang_t.dokter_id::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.rujukan_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT asalrujukan_m_1.asalrujukan_id,
                    asalrujukan_m_1.asalrujukan_nama
                   FROM asalrujukan_m asalrujukan_m_1) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                    hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
                    hasilpemeriksaanrad_t.tindakanpelayanan_id,
                    hasilpemeriksaanrad_t.tgl_ambilfoto,
                    hasilpemeriksaanrad_t.tgl_hasilrad,
                    hasilpemeriksaanrad_t.additional_data,
                    hasilpemeriksaanrad_t.is_read
                   FROM hasilpemeriksaanrad_t
                  WHERE hasilpemeriksaanrad_t.is_deleted IS FALSE) hasilpemeriksaanrad ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id
             LEFT JOIN ( SELECT a.permintaankepenunjang_id,
                    a.rujukankeluar_id,
                    a.nosuratrujukan,
                    a.tgldirujuk,
                    a.alasandirujuk,
                    a.pegawai_id,
                    a.diagnosa
                   FROM pasiendirujukkeluar_t a) pasiendirujukkeluar_t ON permintaankepenunjang_t.permintaankepenunjang_id = pasiendirujukkeluar_t.permintaankepenunjang_id
             LEFT JOIN ( SELECT a.rujukankeluar_id,
                    a.rumahsakit_rujukan,
                    a.alamat_rsrujukan,
                    a.telp_fax
                   FROM rujukankeluar_m a) rujukankeluar_m ON pasiendirujukkeluar_t.rujukankeluar_id = rujukankeluar_m.rujukankeluar_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_perujuk ON pasiendirujukkeluar_t.pegawai_id = pegawai_perujuk.pegawai_id
          WHERE tindakanpelayanan_t.instalasi_id = 5
        UNION ALL
         SELECT \'PAKET\'::text AS jenis,
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
            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasien_id,
            pegawai_m.pegawai_id AS dokter_id_detail,
            pegawai_m.nama_pegawai AS dokter_nama_detail,
            pasienmasukpenunjang_t.created_by,
            hasilpemeriksaanrad.hasilpemeriksaanrad_id,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN false
                    ELSE true
                END AS status_bayar,
                CASE
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NULL THEN \'BELUM PERIKSA\'::text
                    WHEN hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL THEN \'SELESAI\'::text
                    WHEN tindakanpelayanan_t.is_deleted = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN \'BATAL\'::text
                    ELSE NULL::text
                END AS status_periksa_penunjang,
                CASE
                    WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
                    WHEN tindakanpelayanan_t.is_deleted = true THEN true
                    ELSE false
                END AS status_batal,
            tindakanpelayanan_t.tindakanpelayananasal_id,
            hasilpemeriksaanrad.tgl_ambilfoto,
            hasilpemeriksaanrad.tgl_hasilrad,
            rujukan_t.asalrujukan_id,
            rujukan_t.rujukandari_id,
                CASE
                    WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN \'ORDER\'::text::character varying
                    ELSE asalrujukan_m.asalrujukan_nama
                END AS asalrujukan_nama,
                CASE
                    WHEN perujuk_m.namaperujuk IS NULL THEN ruangan_m.ruangan_nama
                    ELSE perujuk_m.namaperujuk
                END AS rujukandari_nama,
            permintaankepenunjang_t.is_referred,
            pasiendirujukkeluar_t.nosuratrujukan,
            pasiendirujukkeluar_t.tgldirujuk,
            rujukankeluar_m.rumahsakit_rujukan,
            rujukankeluar_m.alamat_rsrujukan,
            rujukankeluar_m.telp_fax,
            pasiendirujukkeluar_t.alasandirujuk,
            pasiendirujukkeluar_t.pegawai_id AS pegawai_id_perujuk,
            pegawai_perujuk.nama_pegawai AS pegawai_nama_perujuk,
            rujukankeluar_m.rujukankeluar_id,
            pasiendirujukkeluar_t.diagnosa AS diagnosa_dirujuk,
            hasilpemeriksaanrad.additional_data,
            hasilpemeriksaanrad.is_read
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pasienmasukpenunjang_id,
                    a.tindakanpelayananasal_id,
                    a.tipepaket_id,
                    a.dokterpenanggungjawab_id,
                    a.tgl_tindakan,
                    a.tarif_satuan,
                    a.cyto_tindakan,
                    a.tarifcyto_tindakan,
                    a.tarif_tindakan,
                    a.qty_tindakan,
                    a.tindakansudahbayar_id,
                    a.is_deleted,
                    a.instalasi_id
                   FROM tindakanpelayanan_t a
                  WHERE a.tindakanpelayananasal_id IS NULL) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama
                   FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN ( SELECT a.tipepaket_id,
                    a.daftartindakan_id
                   FROM paketpelayanan_mp a) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id
                   FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pemeriksaanrad_id,
                    a.dokter_id,
                    a.is_referred,
                    a.permintaankepenunjang_id
                   FROM permintaankepenunjang_t a
                  WHERE a.is_deleted = false) permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
             LEFT JOIN ( SELECT a.pemeriksaanradiologi_id,
                    a.jenispemeriksaanrad_id
                   FROM pemeriksaanrad_m a) pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
             LEFT JOIN ( SELECT a.jenispemeriksaanrad_id,
                    a.jenispemeriksaanrad_nama
                   FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON COALESCE(permintaankepenunjang_t.dokter_id::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.rujukan_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.perujuk_id,
                    a.namaperujuk
                   FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
             LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                    hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
                    hasilpemeriksaanrad_t.tindakanpelayanan_id,
                    hasilpemeriksaanrad_t.tgl_ambilfoto,
                    hasilpemeriksaanrad_t.tgl_hasilrad,
                    hasilpemeriksaanrad_t.additional_data,
                    hasilpemeriksaanrad_t.is_read
                   FROM hasilpemeriksaanrad_t
                  WHERE hasilpemeriksaanrad_t.is_deleted IS FALSE) hasilpemeriksaanrad ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id
             LEFT JOIN ( SELECT a.permintaankepenunjang_id,
                    a.rujukankeluar_id,
                    a.nosuratrujukan,
                    a.tgldirujuk,
                    a.alasandirujuk,
                    a.pegawai_id,
                    a.diagnosa
                   FROM pasiendirujukkeluar_t a) pasiendirujukkeluar_t ON permintaankepenunjang_t.permintaankepenunjang_id = pasiendirujukkeluar_t.permintaankepenunjang_id
             LEFT JOIN ( SELECT a.rujukankeluar_id,
                    a.rumahsakit_rujukan,
                    a.alamat_rsrujukan,
                    a.telp_fax
                   FROM rujukankeluar_m a) rujukankeluar_m ON pasiendirujukkeluar_t.rujukankeluar_id = rujukankeluar_m.rujukankeluar_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_perujuk ON pasiendirujukkeluar_t.pegawai_id = pegawai_perujuk.pegawai_id
          WHERE tindakanpelayanan_t.instalasi_id = 5) detail ON header.pasienmasukpenunjang_id = detail.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT 1 AS is_hasil,
            hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
            hasilpemeriksaanrad_t.tindakanpelayanan_id,
            pemeriksaanrad_m.daftartindakan_id,
            hasilpemeriksaanrad_t.tgl_verifikasi
           FROM hasilpemeriksaanrad_t
             JOIN ( SELECT a.pemeriksaanradiologi_id,
                    a.daftartindakan_id
                   FROM pemeriksaanrad_m a) pemeriksaanrad_m ON pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id
          WHERE hasilpemeriksaanrad_t.is_deleted = false) hasil ON header.pasienmasukpenunjang_id = hasil.pasienmasukpenunjang_id AND detail.tindakanpelayanan_id = hasil.tindakanpelayanan_id AND detail.daftartindakan_id = hasil.daftartindakan_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240930_042150_migrate_GLS856_hotfix_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240930_042150_migrate_GLS856_hotfix_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }
    */
}
