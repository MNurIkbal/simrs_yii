<?php

use yii\db\Migration;

/**
 * Class m230718_093558_migrate_RPP304_laporankunjunganri_v
 */
class m230718_093558_migrate_RPP304_laporankunjunganri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporankunjunganri_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporankunjunganri_v
        AS SELECT pasien_m.pasien_id,
            pasien_m.no_identitas_pasien,
            pasien_m.nama_pasien,
            pasien_m.nama_panggilan AS nama_bin,
            pasien_m.tempat_lahir,
            pasien_m.tanggal_lahir,
            pasien_m.alamat_pasien,
            pasien_m.rt,
            pasien_m.rw,
            pasien_m.photopasien,
            pasien_m.alamatemail,
            pasien_m.statusrekammedis,
            pasien_m.no_rekam_medik,
            pasien_m.tgl_rekam_medik,
            pasien_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pasien_m.kabupaten_id,
            fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
            pendaftaran_t.alih_status,
            pendaftaran_t.by_phone,
            pendaftaran_t.kunjungan_rumah,
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
            pasienadmisi_t.status_keluar,
            pasienadmisi_t.rawat_gabung,
            kamarruangan_m.kamarruangan_id,
            pegawai_m.nama_pegawai,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pasienadmisi_t.pegawai_id,
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
            fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            pasienpulang_t.pasienpulang_id,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_namalain,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            fgetnamalookup(pasien_m.agama::integer) AS agama,
            fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
            fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
            fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
            fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
            fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
            fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
            fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
            gelarbelakang.gelarbelakang_nama,
            fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
            fgetnamalookup(pendaftaran_t.status_masuk::integer) AS status_masuk,
            pasien_m.jeniskelamin,
            pasienbatalperiksa_t.alasan_batal,
            pasienadmisi_t.status_ranap,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_ranap_nama,
            golonganumur_m.golonganumur_nama,
            carakeluar_m.carakeluar_nama,
            pasienadmisi_t.kamartempattidur_id,
            bpjs_t.nosep,
            bpjs_t.bpjs_id,
            pasienadmisi_t.is_pasientitipan,
            pasienadmisi_t.is_stoptitipan,
            pindah_kamar.pindahkamar_id,
                CASE
                    WHEN pindah_kamar.pindahkamar_id IS NULL THEN pasienadmisi_t.kelas_ditagihkan_id
                    ELSE pindah_kamar.kelas_ditagihkan_id
                END AS kelas_ditagihkan_id,
                CASE
                    WHEN pindah_kamar.pindahkamar_id IS NULL THEN kelas_ditagihkan.kelaspelayanan_nama
                    ELSE pindah_kamar.kelas_ditagihkan
                END AS kelas_ditagihkan_nama,
                CASE
                    WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS FALSE THEN false
                    WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS TRUE THEN true
                    WHEN stop_titipan.is_pasientitipan IS FALSE AND stop_titipan.is_stoptitipan IS FALSE THEN true
                    WHEN stop_titipan.is_pasientitipan IS TRUE AND stop_titipan.is_stoptitipan IS TRUE THEN true
                    ELSE false
                END AS is_stoppasientitipan,
            stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
            resume.tgl_keluar,
            resume.diagnosa
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pasienadmisi_t ON pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
             LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
             LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
             LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
             LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
             LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
             LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
             LEFT JOIN pasienbatalperiksa_t ON pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
             LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
             LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
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
                     LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id
                  WHERE pindahkamar_t.is_deleted = false AND pindahkamar_t.is_pasientitipan = true) pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
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
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.tgl_keluar,
                    a.diag_utama ->> 'text'::text AS diagnosa
                   FROM resumemedisri_t a
                     JOIN ( SELECT max(b.resumemedisri_id) AS resumemedisri_id,
                            b.pendaftaran_id
                           FROM resumemedisri_t b
                          GROUP BY b.pendaftaran_id) resume_max ON a.resumemedisri_id = resume_max.resumemedisri_id) resume ON pendaftaran_t.pendaftaran_id = resume.pendaftaran_id
          WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND pendaftaran_t.status_periksa::text <> '402'::text AND pendaftaran_t.status_periksa::text <> '628'::text AND pendaftaran_t.status_periksa::text <> '453'::text;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230718_093558_migrate_RPP304_laporankunjunganri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230718_093558_migrate_RPP304_laporankunjunganri_v cannot be reverted.\n";

        return false;
    }
    */
}
