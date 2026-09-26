<?php

use yii\db\Migration;

/**
 * Class m230515_075825_migrate_GLBJ106_infokunjunganrs_tracer_v
 */
class m230515_075825_migrate_GLBJ106_infokunjunganrs_tracer_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infokunjunganrs_tracer_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infokunjunganrs_tracer_v
        AS SELECT pasien_m.pasien_id,
            pasien_m.jenisidentitas,
            pasien_m.no_identitas_pasien,
            look_namadepan.lookup_name AS namadepan,
            pasien_m.nama_pasien,
            pasien_m.nama_panggilan AS nama_bin,
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
            pasien_m.propinsi_id,
            propinsi_m.propinsi_nama,
            pasien_m.kabupaten_id,
            kabupaten_m.kabupaten_nama,
            pasien_m.kecamatan_id,
            kecamatan_m.kecamatan_nama,
            pasien_m.kelurahan_id,
            kelurahan_m.kelurahan_nama,
            pendaftaran_t.pendaftaran_id,
            pekerjaan_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
            pendaftaran_t.status_pasien,
            pendaftaran_t.kunjungan,
            pendaftaran_t.alih_status,
            pendaftaran_t.by_phone,
            pendaftaran_t.kunjungan_rumah,
            pendaftaran_t.status_masuk,
            pendaftaran_t.umur,
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
            golonganumur_m.golonganumur_id,
            golonganumur_m.golonganumur_nama,
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
            pegawai_m.gelardepan,
            pegawai_m.nama_pegawai,
            pegawai_m.gelarbelakang,
            pendaftaran_t.rujukan_id,
            pendaftaran_t.pasienpulang_id,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pembayaranpelayanan_id,
            pasien_m.rhesus,
            pasien_m.anakke,
            pasien_m.jumlah_bersaudara,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.warga_negara,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            suku_m.suku_id,
            suku_m.suku_nama,
            pendidikan_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            carakeluar_m.carakeluar_id,
            carakeluar_m.carakeluar_nama AS carakeluar,
            kondisikeluar_m.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
            asuransipasien_m.nokartuasuransi,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            asuransipasien_m.is_active,
            pendaftaran_t.keterangan_pendaftaran,
            NULL::integer AS konsulpoli_id,
            pasien_m.is_deleted,
            look_statusperiksa.lookup_name AS status_periksa,
            look_jeniskelamin.lookup_name AS jenis_kelamin,
            pendaftaran_t.created_by,
            antrian_t.no_antrian,
            antrian_t.jenisantrian_id,
                CASE
                    WHEN antrian_t.jenisantrian_id = 312 THEN 't'::text
                    ELSE 'f'::text
                END AS is_poliklinik,
            pendaftaran_t.is_karcis,
            pendaftaran_t.status_periksa::integer AS status_periksa_id,
            pendaftaran_t.pasienpulang_id AS pulang_rj_rd,
            NULL::integer AS pulang_ri,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.is_ranap,
            pendaftaran_t.bpjs_id,
            look_gelardepan.lookup_name AS gelardepan_nama,
            look_gelarbelakang.lookup_name AS gelarbelakang_nama,
            pendaftaran_t.pendaftaranibu_id,
            pasienpulang_t.tglpasienpulang,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
            look_statuskonfirmasi.lookup_name AS status_konfirmasirm,
            look_statuspasien.lookup_name AS status_pasien_nama,
            NULL::character varying AS kamar,
            NULL::character varying AS no_tempattidur,
            NULL::boolean AS is_pasientitipan,
            NULL::integer AS kelas_ditagihkan_id,
            NULL::character varying AS kelas_ditagihkan_nama,
            NULL::integer AS kamar_titipan_id,
            NULL::character varying AS kamar_titipan_nama,
            NULL::integer AS ruangan_titipan_id,
            NULL::character varying AS ruangan_titipan_nama,
            false AS is_stoptitipan,
            pasien_m.additional_pasien
           FROM pendaftaran_t
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.jenisantrian_id,
                    a.no_antrian
                   FROM antrian_t a
                  WHERE a.jenisantrian_id = 312) antrian_t ON pendaftaran_t.pendaftaran_id = antrian_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.pekerjaan_id,
                    a.suku_id,
                    a.pendidikan_id,
                    a.propinsi_id,
                    a.kabupaten_id,
                    a.kecamatan_id,
                    a.kelurahan_id,
                    a.jenisidentitas,
                    a.no_identitas_pasien,
                    a.namadepan,
                    a.nama_pasien,
                    a.nama_panggilan,
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
                    a.additional_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
             LEFT JOIN ( SELECT a.golonganumur_id,
                    a.golonganumur_nama
                   FROM golonganumur_m a) golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.no_rujukan,
                    a.nama_perujuk,
                    a.tanggal_rujukan,
                    a.kodediagnosa_rujukan
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.penanggungjawab_id,
                    a.pengantar,
                    a.hubungankeluarga,
                    a.penanggungjawab_nama
                   FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelardepan,
                    a.gelarbelakang
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.carakeluar_id,
                    a.kondisikeluar_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a.asuransipasien_id,
                    a.nokartuasuransi,
                    a.namapemilikasuransi,
                    a.nomorpokokperusahaan,
                    a.status_konfirmasi,
                    a.tgl_konfirmasi,
                    a.tglcetakkartuasuransi,
                    a.kodefeskestk1,
                    a.nama_feskestk1,
                    a.masaberlakukartu,
                    a.nokartukeluarga,
                    a.nopassport,
                    a.is_active
                   FROM asuransipasien_m a) asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             LEFT JOIN ( SELECT a.suku_id,
                    a.suku_nama
                   FROM suku_m a) suku_m ON pasien_m.suku_id = suku_m.suku_id
             LEFT JOIN ( SELECT a.pendidikan_id,
                    a.pendidikan_nama
                   FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT a.propinsi_id,
                    a.propinsi_nama
                   FROM propinsi_m a) propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
             LEFT JOIN ( SELECT a.kabupaten_id,
                    a.kabupaten_nama
                   FROM kabupaten_m a) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a.kecamatan_id,
                    a.kecamatan_nama
                   FROM kecamatan_m a) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a.kelurahan_id,
                    a.kelurahan_nama
                   FROM kelurahan_m a) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperiksa ON pendaftaran_t.status_periksa::integer = look_statusperiksa.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_gelardepan ON pegawai_m.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_gelarbelakang ON pegawai_m.gelarbelakang::integer = look_gelarbelakang.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statuskonfirmasi ON pendaftaran_t.status_konfirmasi::integer = look_statuskonfirmasi.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statuspasien ON pendaftaran_t.status_pasien::integer = look_statuspasien.lookup_id
          WHERE pendaftaran_t.instalasi_id <> 3
        UNION ALL
         SELECT pasien_m.pasien_id,
            pasien_m.jenisidentitas,
            pasien_m.no_identitas_pasien,
            look_namadepan.lookup_name AS namadepan,
            pasien_m.nama_pasien,
            pasien_m.nama_panggilan AS nama_bin,
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
            pasien_m.propinsi_id,
            propinsi_m.propinsi_nama,
            pasien_m.kabupaten_id,
            kabupaten_m.kabupaten_nama,
            pasien_m.kecamatan_id,
            kecamatan_m.kecamatan_nama,
            pasien_m.kelurahan_id,
            kelurahan_m.kelurahan_nama,
            pendaftaran_t.pendaftaran_id,
            pekerjaan_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
            pendaftaran_t.status_pasien,
            pendaftaran_t.kunjungan,
            pendaftaran_t.alih_status,
            pendaftaran_t.by_phone,
            pendaftaran_t.kunjungan_rumah,
            pendaftaran_t.status_masuk,
            pendaftaran_t.umur,
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
            golonganumur_m.golonganumur_id,
            golonganumur_m.golonganumur_nama,
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
            pegawai_m.gelardepan,
            pegawai_m.nama_pegawai,
            pegawai_m.gelarbelakang,
            pendaftaran_t.rujukan_id,
            pasienadmisi_t.pasienpulang_id,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pasienadmisi_t.pegawai_id,
            pendaftaran_t.pembayaranpelayanan_id,
            pasien_m.rhesus,
            pasien_m.anakke,
            pasien_m.jumlah_bersaudara,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.warga_negara,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            suku_m.suku_id,
            suku_m.suku_nama,
            pendidikan_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            carakeluar_m.carakeluar_id,
            carakeluar_m.carakeluar_nama AS carakeluar,
            kondisikeluar_m.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
            asuransipasien_m.nokartuasuransi,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            asuransipasien_m.is_active,
            pendaftaran_t.keterangan_pendaftaran,
            NULL::integer AS konsulpoli_id,
            pasien_m.is_deleted,
            look_statusranap.lookup_name AS status_periksa,
            look_jeniskelamin.lookup_name AS jenis_kelamin,
            pendaftaran_t.created_by,
            antrian_t.no_antrian,
            antrian_t.jenisantrian_id,
                CASE
                    WHEN antrian_t.jenisantrian_id = 312 THEN 't'::text
                    ELSE 'f'::text
                END AS is_poliklinik,
            pendaftaran_t.is_karcis,
            pasienadmisi_t.status_ranap AS status_periksa_id,
            NULL::integer AS pulang_rj_rd,
            pasienadmisi_t.pasienpulang_id AS pulang_ri,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.is_ranap,
            pasienadmisi_t.bpjs_id,
            look_gelardepan.lookup_name AS gelardepan_nama,
            look_gelarbelakang.lookup_name AS gelarbelakang_nama,
            pendaftaran_t.pendaftaranibu_id,
            pasienpulang_t.tglpasienpulang,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
            look_statuskonfirmasi.lookup_name AS status_konfirmasirm,
            look_statuspasien.lookup_name AS status_pasien_nama,
            kamarruangan_m.kamarruangan_nokamar AS kamar,
            kamartempattidur_m.no_tempattidur,
            pasienadmisi_t.is_pasientitipan,
            pasienadmisi_t.kelas_ditagihkan_id,
            kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
            pasienadmisi_t.kamar_titipan_id,
            kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
            pasienadmisi_t.ruangan_titipan_id,
            ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
            NULL::boolean AS is_stoptitipan,
            pasien_m.additional_pasien
           FROM pendaftaran_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.kelaspelayanan_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.ruangan_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.pegawai_id,
                    a.pasienpulang_id,
                    a.kelas_ditagihkan_id,
                    a.kamar_titipan_id,
                    a.ruangan_titipan_id,
                    a.tgl_admisi,
                    a.status_ranap,
                    a.bpjs_id,
                    a.is_pasientitipan
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.jenisantrian_id,
                    a.no_antrian
                   FROM antrian_t a
                  WHERE a.jenisantrian_id = 312) antrian_t ON pendaftaran_t.pendaftaran_id = antrian_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.pekerjaan_id,
                    a.suku_id,
                    a.pendidikan_id,
                    a.propinsi_id,
                    a.kabupaten_id,
                    a.kecamatan_id,
                    a.kelurahan_id,
                    a.jenisidentitas,
                    a.no_identitas_pasien,
                    a.namadepan,
                    a.nama_pasien,
                    a.nama_panggilan,
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
                    a.additional_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
             LEFT JOIN ( SELECT a.golonganumur_id,
                    a.golonganumur_nama
                   FROM golonganumur_m a) golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.no_rujukan,
                    a.nama_perujuk,
                    a.tanggal_rujukan,
                    a.kodediagnosa_rujukan
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.penanggungjawab_id,
                    a.pengantar,
                    a.hubungankeluarga,
                    a.penanggungjawab_nama
                   FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.jeniskasuspenyakit_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.gelardepan,
                    a.gelarbelakang,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.carakeluar_id,
                    a.kondisikeluar_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a.asuransipasien_id,
                    a.nokartuasuransi,
                    a.namapemilikasuransi,
                    a.nomorpokokperusahaan,
                    a.status_konfirmasi,
                    a.tgl_konfirmasi,
                    a.tglcetakkartuasuransi,
                    a.kodefeskestk1,
                    a.nama_feskestk1,
                    a.masaberlakukartu,
                    a.nokartukeluarga,
                    a.nopassport,
                    a.is_active
                   FROM asuransipasien_m a) asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             LEFT JOIN ( SELECT a.suku_id,
                    a.suku_nama
                   FROM suku_m a) suku_m ON pasien_m.suku_id = suku_m.suku_id
             LEFT JOIN ( SELECT a.pendidikan_id,
                    a.pendidikan_nama
                   FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
             LEFT JOIN ( SELECT a.propinsi_id,
                    a.propinsi_nama
                   FROM propinsi_m a) propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
             LEFT JOIN ( SELECT a.kabupaten_id,
                    a.kabupaten_nama
                   FROM kabupaten_m a) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a.kecamatan_id,
                    a.kecamatan_nama
                   FROM kecamatan_m a) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a.kelurahan_id,
                    a.kelurahan_nama
                   FROM kelurahan_m a) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
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
                   FROM lookup_m a) look_gelardepan ON pegawai_m.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_gelarbelakang ON pegawai_m.gelarbelakang::integer = look_gelarbelakang.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statuskonfirmasi ON pendaftaran_t.status_konfirmasi::integer = look_statuskonfirmasi.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statuspasien ON pendaftaran_t.status_pasien::integer = look_statuspasien.lookup_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230515_075825_migrate_GLBJ106_infokunjunganrs_tracer_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230515_075825_migrate_GLBJ106_infokunjunganrs_tracer_v cannot be reverted.\n";

        return false;
    }
    */
}
