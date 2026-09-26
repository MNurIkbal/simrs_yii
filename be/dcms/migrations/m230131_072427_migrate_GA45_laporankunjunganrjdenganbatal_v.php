<?php

use yii\db\Migration;

/**
 * Class m230131_072427_migrate_GA45_laporankunjunganrjdenganbatal_v
 */
class m230131_072427_migrate_GA45_laporankunjunganrjdenganbatal_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE VIEW public.laporankunjunganrjdenganbatal_v
        AS SELECT pasien_m.pasien_id,
            pasien_m.no_identitas_pasien,
            pasien_m.nama_pasien,
            pasien_m.nama_bin,
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
            ruangan_m.ruangan_singkatan,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.tgl_renkontrol,
            pendaftaran_t.pembayaranpelayanan_id,
            pendaftaran_t.panggil_antrian,
            antrian_t.antrian_id,
            antrian_t.tgl_antrian,
            antrian_t.no_antrian,
            antrian_t.panggil_flag,
            loket_m.loket_id,
            loket_m.loket_nama,
            loket_m.loket_fungsi,
            loket_m.loket_singkatan,
            loket_m.loket_nourut,
            loket_m.loket_formatnomor,
            loket_m.loket_maxantrian,
            asuransipasien_m.nopeserta,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            asuransipasien_m.is_active,
            pendaftaran_t.keterangan_pendaftaran,
            pendaftaran_t.statusdok_rekammedik,
            pegawai_m.kelompokpegawai_id,
            NULL::integer AS konsulpoli_id,
            pasien_m.is_deleted,
            pasienpulang_t.tglpasienpulang,
            look_jeniskelamin.lookup_name AS jenis_kelamin,
            look_statusperiksa.lookup_name AS status_periksa,
            NULL::integer AS ruanganasal_id,
            NULL::character varying AS ruanganasal_nama,
            look_jenisidentitas.lookup_name AS jenisidentitas,
            look_namadepan.lookup_name AS namadepan,
            pasien_m.jeniskelamin,
            look_golongandarah.lookup_name AS golongandarah,
            look_statusperkawinan.lookup_name AS statusperkawinan,
            look_statuspasien.lookup_name AS status_pasien,
            look_kunjungan.lookup_name AS kunjungan,
            look_gelardepan.lookup_name AS gelardepan,
            gelarbelakang.gelarbelakang_nama,
            look_rhesus.lookup_name AS rhesus,
            kondisikeluar_m.kondisikeluar_nama,
            carakeluar_m.carakeluar_nama,
            look_agama.lookup_name AS agama,
            pasienbatalperiksa_t.alasan_batal,
            pendaftaran_t.bpjs_id,
            bpjs_t.nosep,
            pendaftaran_t.status_periksa AS status_periksa_id,
            pendaftaran_t.status_bayar,
            pendaftaran_t.asuransipasien_id,
            pasienpulang_t.carakeluar_id,
            pendaftaran_t.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pendaftaran_t.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            pendaftaran_t.penanggungbiaya_id,
            carabayar_m.carabayar_kode_warna,
                CASE
                    WHEN antrian_poli.jenisantrian_id = 312 THEN antrian_poli.no_antrian::text
                    ELSE '-'::text
                END AS no_antrian_poli,
            pendaftaran_t.limit_tagihan,
            dokter_pengganti.nama_pegawai AS dokter_pengganti,
            bpjs_t.nokartuasuransi,
            carabayar_m.groupcarabayar_id,
            look_groupcarabayar.lookup_name AS groupcarabayar_nama
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_identitas_pasien,
                    a.nama_pasien,
                    a.nama_panggilan AS nama_bin,
                    a.tempat_lahir,
                    a.tanggal_lahir,
                    a.alamat_pasien,
                    a.rt,
                    a.rw,
                    a.photopasien,
                    a.alamatemail,
                    a.statusrekammedis,
                    a.no_rekam_medik,
                    a.tgl_rekam_medik,
                    a.propinsi_id,
                    a.kabupaten_id,
                    a.kecamatan_id,
                    a.kelurahan_id,
                    a.is_deleted,
                    a.jeniskelamin,
                    a.pekerjaan_id,
                    a.jenisidentitas,
                    a.namadepan,
                    a.golongandarah,
                    a.statusperkawinan,
                    a.rhesus,
                    a.agama
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
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
             LEFT JOIN ( SELECT a.no_rujukan,
                    a.nama_perujuk,
                    a.tanggal_rujukan,
                    a.kodediagnosa_rujukan,
                    a.rujukan_id,
                    a.asalrujukan_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.penanggungjawab_id,
                    a.pengantar,
                    a.hubungankeluarga,
                    a.penanggungjawab_nama
                   FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.ruangan_singkatan,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.kelompokpegawai_id,
                    a.gelardepan,
                    a.gelarbelakang
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_pengganti ON pendaftaran_t.dokterpengganti_id = dokter_pengganti.pegawai_id
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
             LEFT JOIN ( SELECT a.antrian_id,
                    a.tgl_antrian,
                    a.no_antrian,
                    a.panggil_flag,
                    a.loket_id
                   FROM antrian_t a) antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
             LEFT JOIN ( SELECT a.jenisantrian_id,
                    a.no_antrian,
                    a.pendaftaran_id
                   FROM antrian_t a) antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id AND antrian_poli.jenisantrian_id = 312
             LEFT JOIN ( SELECT a.loket_id,
                    a.loket_nama,
                    a.loket_fungsi,
                    a.loket_singkatan,
                    a.loket_nourut,
                    a.loket_formatnomor,
                    a.loket_maxantrian
                   FROM loket_m a) loket_m ON antrian_t.loket_id = loket_m.loket_id
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
             LEFT JOIN ( SELECT a.kelompokpegawai_id
                   FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
             LEFT JOIN ( SELECT a.tglpasienpulang,
                    a.carakeluar_id,
                    a.pasienpulang_id,
                    a.kondisikeluar_id
                   FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
             LEFT JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             LEFT JOIN ( SELECT a.pasienbatalperiksa_id,
                    a.alasan_batal
                   FROM pasienbatalperiksa_t a) pasienbatalperiksa_t ON pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
             LEFT JOIN ( SELECT a.nosep,
                    a.nokartuasuransi,
                    a.bpjs_id,
                    a.is_deleted
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted IS FALSE
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_groupcarabayar ON carabayar_m.groupcarabayar_id = look_groupcarabayar.lookup_id
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
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperiksa ON pendaftaran_t.status_periksa::integer = look_statusperiksa.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jenisidentitas ON pasien_m.jenisidentitas::integer = look_jenisidentitas.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_golongandarah ON pasien_m.golongandarah::integer = look_golongandarah.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperkawinan ON pasien_m.statusperkawinan::integer = look_statusperkawinan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statuspasien ON pendaftaran_t.status_pasien::integer = look_statuspasien.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_kunjungan ON pendaftaran_t.kunjungan::integer = look_kunjungan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_gelardepan ON pegawai_m.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_rhesus ON pasien_m.rhesus::integer = look_rhesus.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_agama ON pasien_m.agama::integer = look_agama.lookup_id
          WHERE pendaftaran_t.instalasi_id = 1
        UNION
         SELECT pasien_m.pasien_id,
            pasien_m.no_identitas_pasien,
            pasien_m.nama_pasien,
            pasien_m.nama_bin,
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
            konsulpoli_t.tgl_konsulpoli AS tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
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
            ruangan_m.ruangan_singkatan,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.tgl_renkontrol,
            pendaftaran_t.pembayaranpelayanan_id,
            pendaftaran_t.panggil_antrian,
            antrian_t.antrian_id,
            antrian_t.tgl_antrian,
            antrian_t.no_antrian,
            antrian_t.panggil_flag,
            loket_m.loket_id,
            loket_m.loket_nama,
            loket_m.loket_fungsi,
            loket_m.loket_singkatan,
            loket_m.loket_nourut,
            loket_m.loket_formatnomor,
            loket_m.loket_maxantrian,
            asuransipasien_m.nopeserta,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            asuransipasien_m.is_active,
            pendaftaran_t.keterangan_pendaftaran,
            pendaftaran_t.statusdok_rekammedik,
            pegawai_m.kelompokpegawai_id,
            konsulpoli_t.konsulpoli_id,
            pasien_m.is_deleted,
            pasienpulang_t.tglpasienpulang,
            look_jeniskelamin.lookup_name AS jenis_kelamin,
            look_statusperiksa.lookup_name AS status_periksa,
            konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
            ruanganasal_m.ruangan_nama AS ruanganasal_nama,
            look_jenisidentitas.lookup_name AS jenisidentitas,
            look_namadepan.lookup_name AS namadepan,
            pasien_m.jeniskelamin,
            look_golongandarah.lookup_name AS golongandarah,
            look_statusperkawinan.lookup_name AS statusperkawinan,
            look_statuspasien.lookup_name AS status_pasien,
            look_kunjungan.lookup_name AS kunjungan,
            look_gelardepan.lookup_name AS gelardepan,
            gelarbelakang.gelarbelakang_nama,
            look_rhesus.lookup_name AS rhesus,
            kondisikeluar_m.kondisikeluar_nama,
            carakeluar_m.carakeluar_nama,
            look_agama.lookup_name AS agama,
            pasienbatalperiksa_t.alasan_batal,
            pendaftaran_t.bpjs_id,
            bpjs_t.nosep,
            pendaftaran_t.status_periksa AS status_periksa_id,
            pendaftaran_t.status_bayar,
            pendaftaran_t.asuransipasien_id,
            pasienpulang_t.carakeluar_id,
            pendaftaran_t.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pendaftaran_t.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            pendaftaran_t.penanggungbiaya_id,
            carabayar_m.carabayar_kode_warna,
                CASE
                    WHEN antrian_poli.jenisantrian_id = 312 THEN antrian_poli.no_antrian::text
                    ELSE '-'::text
                END AS no_antrian_poli,
            pendaftaran_t.limit_tagihan,
            dokter_pengganti.nama_pegawai AS dokter_pengganti,
            bpjs_t.nokartuasuransi,
            carabayar_m.groupcarabayar_id,
            look_groupcarabayar.lookup_name AS groupcarabayar_nama
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_identitas_pasien,
                    a.nama_pasien,
                    a.nama_panggilan AS nama_bin,
                    a.tempat_lahir,
                    a.tanggal_lahir,
                    a.alamat_pasien,
                    a.rt,
                    a.rw,
                    a.photopasien,
                    a.alamatemail,
                    a.statusrekammedis,
                    a.no_rekam_medik,
                    a.tgl_rekam_medik,
                    a.propinsi_id,
                    a.kabupaten_id,
                    a.kecamatan_id,
                    a.kelurahan_id,
                    a.is_deleted,
                    a.jeniskelamin,
                    a.pekerjaan_id,
                    a.jenisidentitas,
                    a.namadepan,
                    a.golongandarah,
                    a.statusperkawinan,
                    a.rhesus,
                    a.agama
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.groupcarabayar_id,
                    a.carabayar_kode_warna
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
             LEFT JOIN ( SELECT a.no_rujukan,
                    a.nama_perujuk,
                    a.tanggal_rujukan,
                    a.kodediagnosa_rujukan,
                    a.rujukan_id,
                    a.asalrujukan_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.penanggungjawab_id,
                    a.pengantar,
                    a.hubungankeluarga,
                    a.penanggungjawab_nama
                   FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.tgl_konsulpoli,
                    a.konsulpoli_id,
                    a.asalpoliklinikkonsul_id,
                    a.pegawai_id,
                    a.ruangan_id,
                    a.pendaftaran_id
                   FROM konsulpoli_t a) konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id
             LEFT JOIN ( SELECT a.nama_pegawai,
                    a.kelompokpegawai_id,
                    a.pegawai_id,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.nama_pegawai,
                    a.pegawai_id
                   FROM pegawai_m a) dokter_pengganti ON pendaftaran_t.dokterpengganti_id = dokter_pengganti.pegawai_id
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
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.ruangan_singkatan,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruanganasal_m ON konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.tgl_antrian,
                    a.no_antrian,
                    a.panggil_flag,
                    a.loket_id
                   FROM antrian_t a) antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
             LEFT JOIN ( SELECT a.jenisantrian_id,
                    a.no_antrian,
                    a.pendaftaran_id
                   FROM antrian_t a) antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id AND antrian_poli.jenisantrian_id = 312
             LEFT JOIN ( SELECT a.loket_id,
                    a.loket_nama,
                    a.loket_fungsi,
                    a.loket_singkatan,
                    a.loket_nourut,
                    a.loket_formatnomor,
                    a.loket_maxantrian
                   FROM loket_m a) loket_m ON antrian_t.loket_id = loket_m.loket_id
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
             LEFT JOIN ( SELECT a.kelompokpegawai_id
                   FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
             LEFT JOIN ( SELECT a.tglpasienpulang,
                    a.carakeluar_id,
                    a.kondisikeluar_id,
                    a.pasienpulang_id
                   FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
             LEFT JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             LEFT JOIN ( SELECT a.pasienbatalperiksa_id,
                    a.alasan_batal
                   FROM pasienbatalperiksa_t a) pasienbatalperiksa_t ON pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
             LEFT JOIN ( SELECT a.nosep,
                    a.nokartuasuransi,
                    a.bpjs_id,
                    a.is_deleted
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted IS FALSE
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_groupcarabayar ON carabayar_m.groupcarabayar_id = look_groupcarabayar.lookup_id
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
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperiksa ON pendaftaran_t.status_periksa::integer = look_statusperiksa.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jenisidentitas ON pasien_m.jenisidentitas::integer = look_jenisidentitas.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_golongandarah ON pasien_m.golongandarah::integer = look_golongandarah.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperkawinan ON pasien_m.statusperkawinan::integer = look_statusperkawinan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statuspasien ON pendaftaran_t.status_pasien::integer = look_statuspasien.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_kunjungan ON pendaftaran_t.kunjungan::integer = look_kunjungan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_gelardepan ON pegawai_m.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_rhesus ON pasien_m.rhesus::integer = look_rhesus.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_agama ON pasien_m.agama::integer = look_agama.lookup_id
          WHERE pendaftaran_t.instalasi_id = 1;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230131_072427_migrate_GA45_laporankunjunganrjdenganbatal_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230131_072427_migrate_GA45_laporankunjunganrjdenganbatal_v cannot be reverted.\n";

        return false;
    }
    */
}
