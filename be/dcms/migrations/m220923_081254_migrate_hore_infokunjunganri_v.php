<?php

use yii\db\Migration;

/**
 * Class m220923_081254_migrate_hore_infokunjunganri_v
 */
class m220923_081254_migrate_hore_infokunjunganri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infokunjunganri_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganri_v\" AS  SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    look_namadepan.lookup_name AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.tgl_pulang,
    pasienadmisi_t.kunjungan,
    pasienadmisi_t.status_keluar,
    pasienadmisi_t.rawat_gabung,
    kamarruangan_m.kamarruangan_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pegawai_m.kelompokpegawai_id,
    pasien_m.is_deleted,
    look_statusranap.lookup_name AS status_periksa,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    golonganumur_m.golonganumur_nama,
    look_jeniskelamin.lookup_name AS jenis_kelamin,
    pasienadmisi_t.created_by,
    kamartempattidur_m.kamartempattidur_id,
    pasienadmisi_t.bpjs_id,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    bpjs_t.nosep,
    kelaspelayanan_m.urutankelas,
    kelaspelayanan_m.bpjs_kelas,
    bpjs_t.klsrawat,
    pasienadmisi_t.is_aps,
    pasienadmisi_t.is_pasientitipan,
        CASE
            WHEN pindah_kamar.pindahkamar_id IS NULL THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE pindah_kamar.kelas_ditagihkan_id
        END AS kelas_ditagihkan_id,
        CASE
            WHEN pindah_kamar.pindahkamar_id IS NULL THEN kelas_ditagihkan.kelaspelayanan_nama
            ELSE pindah_kamar.kelas_ditagihkan
        END AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan,
    pindah_kamar.pindahkamar_id,
        CASE
            WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS FALSE THEN false
            WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS TRUE THEN true
            WHEN stop_titipan.is_pasientitipan IS FALSE AND stop_titipan.is_stoptitipan IS FALSE THEN false
            WHEN stop_titipan.is_pasientitipan IS TRUE AND stop_titipan.is_stoptitipan IS TRUE THEN true
            ELSE false
        END AS is_stoppasientitipan,
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
    carakeluar_m.carakeluar_nama,
    look_agama.lookup_name AS agama_nama,
    pendaftaran_t.last_modified_date AS tgl_update_terakhir,
    petugas_pemakai.nama_pegawai AS petugas_nama,
    pendaftaran_t.created_date AS tgl_pembuatan,
    petugas_pembuat.nama_pegawai AS pembuat_nama,
    pasien_m.additional_pasien,
    pendaftaran_t.is_stopakomodasi,
    carabayar_m.carabayar_kode_warna,
        CASE
            WHEN antrian_poli.jenisantrian_id = 312 THEN antrian_poli.no_antrian::text
            ELSE '-'::text
        END AS no_antrian_poli,
    pasien_m.catatanpenting_pasien,
    penanggungjawab_m.penanggungjawab_alamat,
    penanggungjawab_m.penanggungjawab_notelp,
    penanggungjawab_m.pj_pekerjaan_id,
    pj_kerja.pekerjaan_nama AS pj_pekerjaan_nama,
    penanggungjawab_m.pj_propinsi_id,
    pj_prop.propinsi_nama AS pj_propinsi_nama,
    penanggungjawab_m.pj_kabupaten_id,
    pj_kab.kabupaten_nama AS pj_kabupaten_nama,
    penanggungjawab_m.pj_kecamatan_id,
    pj_kec.kecamatan_nama AS pj_kecamatan_nama,
    penanggungjawab_m.pj_kelurahan_id,
    pj_kel.kelurahan_nama AS pj_kelurahan_nama,
    pasien_m.bahasa_sehari,
    look_bahasasehari.lookup_name AS bahasa_sehari_nama,
    penanggungjawab_m.pj_namadepan,
    look_pjnamadepan.lookup_name AS pj_namadepan_nama,
    pasienadmisi_t.limit_tagihan,
    perujuk_m.namaperujuk AS rujukan_dari,
    resumemedisri_t.resumemedisri_id,
    bpjs_t.klsrawat AS kelas_hak,
    permintaan_kelas.kelaspelayanan_nama AS kelas_permintaan,
    pendaftaran_t.tgl_stopakomodasi,
        CASE
            WHEN COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_m.bpjs_kelas) = bpjs_t.klsrawat::integer THEN 'Sesuai Kelas'::text
            WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat::integer > COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_m.bpjs_kelas) THEN
            CASE
                WHEN pasienadmisi_t.is_pasientitipan = true THEN 'Titipan / Naik Kelas'::text
                ELSE 'APS / Naik Kelas'::text
            END
            WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_m.bpjs_kelas) IS NULL THEN
            CASE
                WHEN pasienadmisi_t.is_aps = true THEN 'APS / Naik Kelas'::text
                WHEN pasienadmisi_t.is_aps = false AND pasienadmisi_t.is_pasientitipan = true THEN 'Titipan / Naik Kelas'::text
                ELSE 'Sesuai Kelas'::text
            END
            WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat::integer < COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_m.bpjs_kelas) THEN 'APS / Turun Kelas'::text
            WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND bpjs_t.klsrawat::integer > COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_m.bpjs_kelas) THEN 'Titipan / Naik Kelas'::text
            WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_m.bpjs_kelas) IS NULL THEN 'Titipan / Naik Kelas'::text
            WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND bpjs_t.klsrawat::integer < COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_m.bpjs_kelas) THEN 'Titipan / Turun Kelas'::text
            ELSE '-'::text
        END AS status_kelas,
    pendaftaran_t.prev_pendaftaran_id AS prev_no_pendaftaran,
    pendaftaran_t.status_bayar,
    bpjs_t.additional_data,
    bpjs_t.nokartuasuransi,
    carabayar_m.groupcarabayar_id,
    look_groupcarabayar.lookup_name AS groupcarabayar_nama,
        CASE
            WHEN kelahiranbayi_t.is_bayi > 0 THEN true
            ELSE false
        END AS is_bayi
   FROM pendaftaran_t
     JOIN ( SELECT a.pasien_id,
            a.jenisidentitas,
            a.no_identitas_pasien,
            a.namadepan,
            a.nama_pasien,
            a.nama_bin,
            a.jeniskelamin,
            a.tempat_lahir,
            a.tanggal_lahir,
            a.alamat_pasien,
            a.rt,
            a.rw,
            a.agama,
            a.golongandarah,
            a.photopasien,
            a.alamatemail,
            a.statusrekammedis,
            a.statusperkawinan,
            a.no_rekam_medik,
            a.tgl_rekam_medik,
            a.rhesus,
            a.anakke,
            a.jumlah_bersaudara,
            a.no_telepon_pasien,
            a.no_mobile_pasien,
            a.warga_negara,
            a.nama_ibu,
            a.nama_ayah,
            a.is_deleted,
            a.additional_pasien,
            a.catatanpenting_pasien,
            a.bahasa_sehari,
            a.suku_id,
            a.pendidikan_id
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.no_rujukan,
            a.nama_perujuk,
            a.tanggal_rujukan,
            a.kodediagnosa_rujukan,
            a.rujukan_id,
            a.rujukandari_id,
            a.asalrujukan_id
           FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN ( SELECT a.perujuk_id,
            a.namaperujuk
           FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     LEFT JOIN ( SELECT a.asalrujukan_id,
            a.asalrujukan_nama
           FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN ( SELECT a.penanggungjawab_id,
            a.pengantar,
            a.hubungankeluarga,
            a.penanggungjawab_nama,
            a.pj_pekerjaan_id,
            a.pj_propinsi_id,
            a.pj_kabupaten_id,
            a.pj_kecamatan_id,
            a.pj_kelurahan_id,
            a.pj_namadepan,
            a.penanggungjawab_alamat,
            a.penanggungjawab_notelp
           FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ( SELECT a.tgl_admisi,
            a.pasienadmisi_id,
            a.tgl_pulang,
            a.kunjungan,
            a.status_keluar,
            a.rawat_gabung,
            a.pegawai_id,
            a.created_by,
            a.bpjs_id,
            a.status_ranap,
            a.is_aps,
            a.is_pasientitipan,
            a.kelas_ditagihkan_id,
            a.kamar_titipan_id,
            a.ruangan_titipan_id,
            a.is_stoptitipan,
            a.limit_tagihan,
            a.caramasuk_id,
            a.kamarruangan_id,
            a.ruangan_id,
            a.carabayar_id,
            a.penjamin_id,
            a.kelaspelayanan_id,
            a.hakkelas_id,
            a.kelaspermintaan_id,
            a.kamartempattidur_id,
            a.pasienpulang_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.caramasuk_id,
            a.caramasuk_nama
           FROM caramasuk_m a) caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
     JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.carabayar_kode_warna,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama,
            a.urutankelas,
            a.bpjs_kelas
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id
           FROM kelaspelayanan_m a) hak_kelas ON pasienadmisi_t.hakkelas_id = hak_kelas.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) permintaan_kelas ON pasienadmisi_t.kelaspermintaan_id = permintaan_kelas.kelaspelayanan_id
     LEFT JOIN ( SELECT a.antrian_id
           FROM antrian_t a) antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     LEFT JOIN ( SELECT a.jenisantrian_id,
            a.no_antrian,
            a.pendaftaran_id
           FROM antrian_t a) antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id AND antrian_poli.jenisantrian_id = 312
     LEFT JOIN ( SELECT a.gelardepan,
            a.nama_pegawai,
            a.gelarbelakang,
            a.kelompokpegawai_id,
            a.pegawai_id
           FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
     LEFT JOIN ( SELECT a.pekerjaan_id,
            a.pekerjaan_nama
           FROM pekerjaan_m a) pj_kerja ON penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id
     LEFT JOIN ( SELECT a.propinsi_id,
            a.propinsi_nama
           FROM propinsi_m a) pj_prop ON penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id
     LEFT JOIN ( SELECT a.kabupaten_id,
            a.kabupaten_nama
           FROM kabupaten_m a) pj_kab ON penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id
     LEFT JOIN ( SELECT a.kecamatan_id,
            a.kecamatan_nama
           FROM kecamatan_m a) pj_kec ON penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id
     LEFT JOIN ( SELECT a.kelurahan_id,
            a.kelurahan_nama
           FROM kelurahan_m a) pj_kel ON penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id
     LEFT JOIN ( SELECT a.suku_id,
            a.suku_nama
           FROM suku_m a) suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN ( SELECT a.pendidikan_id,
            a.pendidikan_nama
           FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN ( SELECT a.nokartuasuransi,
            a.namapemilikasuransi,
            a.nomorpokokperusahaan,
            a.status_konfirmasi,
            a.tgl_konfirmasi,
            a.nopeserta,
            a.tglcetakkartuasuransi,
            a.kodefeskestk1,
            a.nama_feskestk1,
            a.masaberlakukartu,
            a.nokartukeluarga,
            a.nopassport,
            a.is_active,
            a.asuransipasien_id
           FROM asuransipasien_m a) asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN ( SELECT a.golonganumur_id,
            a.golonganumur_nama
           FROM golonganumur_m a) golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     JOIN ( SELECT a.no_tempattidur,
            a.kamartempattidur_id
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT bpjs_t_1.bpjs_id,
            bpjs_t_1.nosep,
            bpjs_t_1.additional_data,
            bpjs_t_1.nokartuasuransi,
            ((((bpjs_t_1.additional_data::json ->> 'sep'::text)::json) ->> 'klsRawat'::text)::json) ->> 'klsRawatHak'::text AS klsrawat
           FROM bpjs_t bpjs_t_1) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama,
            a.bpjs_kelas
           FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
     LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
            pindahkamar_t.pasienadmisi_id,
            pindahkamar_t.kelas_ditagihkan_id,
            kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
            pindahkamar_t.is_stoptitipan
           FROM pindahkamar_t
             JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                    pk.pasienadmisi_id
                   FROM pindahkamar_t pk
                  GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id
          WHERE pindahkamar_t.is_deleted = false) pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
     LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
            pindahkamar_t.pasienadmisi_id,
            pindahkamar_t.is_pasientitipan,
            pindahkamar_t.is_stoptitipan
           FROM pindahkamar_t
             JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                    pk.pasienadmisi_id
                   FROM pindahkamar_t pk
                  GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
          WHERE pindahkamar_t.is_deleted = false) stop_titipan ON pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.carakeluar_id
           FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
           FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.resumemedisri_id
           FROM resumemedisri_t a) resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_groupcarabayar ON carabayar_m.groupcarabayar_id = look_groupcarabayar.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusranap ON pasienadmisi_t.status_ranap = look_statusranap.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_agama ON pasien_m.agama::integer = look_agama.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_bahasasehari ON pasien_m.bahasa_sehari::integer = look_bahasasehari.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_pjnamadepan ON penanggungjawab_m.pj_namadepan::integer = look_pjnamadepan.lookup_id
     LEFT JOIN ( SELECT count(*) AS is_bayi,
            a.pendaftaranbaru_id
           FROM kelahiranbayi_t a
          GROUP BY a.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220923_081254_migrate_hore_infokunjunganri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220923_081254_migrate_hore_infokunjunganri_v cannot be reverted.\n";

        return false;
    }
    */
}
