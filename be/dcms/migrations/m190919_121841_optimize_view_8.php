<?php

use yii\db\Migration;

/**
 * Class m190919_121841_optimize_view_8
 */
class m190919_121841_optimize_view_8 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    /*pegawai_v*/
        $this->execute('DROP VIEW if exists public.pegawai_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.pegawai_v AS 
 SELECT ruangan_m.ruangan_id,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_nama,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai,
    pegawai_m.jeniskelamin,
    pegawai_m.tempatlahir_pegawai,
    pegawai_m.tgl_lahirpegawai,
    pegawai_m.alamat_pegawai,
    pegawai_m.alamatemail,
    pegawai_m.notelp_pegawai,
    pegawai_m.nomobile_pegawai,
    pegawai_m.photopegawai,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pendidikankualifikasi_m.pendkualifikasi_id,
    pendidikankualifikasi_m.pendkualifikasi_nama,
    pegawai_m.nomorindukpegawai,
    pegawai_m.kelompokpegawai_id,
    pegawai_m.jabatan_id,
    jabatan_m.jabatan_nama,
    ruanganpegawai_mp.is_deleted,
    instalasi_m.instalasi_nama,
    kelompokpegawai_m.kelompokpegawai_nama,
    kelompokpegawai_m.kelompokpegawai_namalainnya,
    kelompokpegawai_m.kelompokpegawai_fungsi,
    concat(fgetnamalookup(pegawai_m.gelardepan::integer), ' ', pegawai_m.nama_pegawai, ' ', gelarbelakang_m.gelarbelakang_nama) AS nama,
    ruanganpegawai_mp.is_active,
    pegawai_m.pangkat_id,
    pangkat_m.pangkat_nama,
    pegawai_m.golonganpegawai_id,
    golonganpegawai_m.golonganpegawai_nama,
    pegawai_m.gelarbelakang,
    gelarbelakang_m.gelarbelakang_nama,
    fgetnamalookup(pegawai_m.status_kawin) AS status_kawin,
    pegawai_m.agama,
    fgetnamalookup(pegawai_m.agama::integer) AS agama_nama,
    pegawai_m.golongan_darah,
    fgetnamalookup(pegawai_m.golongan_darah) AS golongandarah_nama,
    pegawai_m.warganegara_pegawai,
    fgetnamalookup(pegawai_m.warganegara_pegawai::integer) AS warganegara_nama,
    pegawai_m.suku_id,
    suku_m.suku_nama,
    pegawai_m.warna_kulit,
    fgetnamalookup(pegawai_m.warna_kulit::integer) AS warnakulit_nama,
    pegawai_m.propinsi_id,
    fgetnamaarea(pegawai_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pegawai_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pegawai_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pegawai_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pegawai_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pegawai_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pegawai_m.kelurahan_id) AS kelurahan_nama,
    pegawai_m.status_pegawai,
    fgetnamalookup(pegawai_m.status_pegawai::integer) AS statuspegawai_nama,
    pegawai_m.gelardepan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan_nama,
    pegawai_m.is_active AS pegawai_is_active
   FROM ruanganpegawai_mp
     JOIN ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pendidikan_m ON pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN jabatan_m ON pegawai_m.jabatan_id = jabatan_m.jabatan_id
     LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN gelarbelakang_m ON pegawai_m.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
     LEFT JOIN pangkat_m ON pegawai_m.pangkat_id = pangkat_m.pangkat_id
     LEFT JOIN golonganpegawai_m ON pegawai_m.golonganpegawai_id = golonganpegawai_m.golonganpegawai_id
     LEFT JOIN suku_m ON pegawai_m.suku_id = suku_m.suku_id
  WHERE pegawai_m.is_active = true AND pegawai_m.is_deleted = false AND ruanganpegawai_mp.is_deleted = false;");

        $this->execute('ALTER TABLE public.pegawai_v
  OWNER TO postgres;');

    /*pegawai_master_v*/
        $this->execute('DROP VIEW if exists public.pegawai_master_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.pegawai_master_v AS 
 SELECT pegawai_m.pegawai_id,
    pegawai_m.gelardepan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan_nama,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelarbelakang_nama,
    pegawai_m.status_kawin,
    fgetnamalookup(pegawai_m.status_kawin) AS status_kawin_nama,
    pegawai_m.jeniskelamin,
    pegawai_m.tempatlahir_pegawai,
    pegawai_m.tgl_lahirpegawai,
    pegawai_m.alamat_pegawai,
    pegawai_m.agama,
    fgetnamalookup(pegawai_m.agama::integer) AS agama_nama,
    pegawai_m.golongan_darah,
    fgetnamalookup(pegawai_m.golongan_darah) AS golongan_darah_nama,
    pegawai_m.warganegara_pegawai AS warganegara,
    fgetnamalookup(pegawai_m.warganegara_pegawai::integer) AS warganegara_nama,
    pegawai_m.suku_id,
    suku_m.suku_nama,
    pegawai_m.propinsi_id,
    fgetnamaarea(pegawai_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pegawai_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pegawai_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pegawai_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pegawai_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pegawai_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pegawai_m.kelurahan_id) AS kelurahan_nama,
    pegawai_m.alamatemail,
    pegawai_m.notelp_pegawai,
    pegawai_m.nomobile_pegawai,
    pegawai_m.photopegawai,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pendidikankualifikasi_m.pendkualifikasi_id,
    pendidikankualifikasi_m.pendkualifikasi_nama,
    pegawai_m.nomorindukpegawai,
    pegawai_m.pangkat_id,
    pegawai_m.kelompokpegawai_id,
    pegawai_m.jabatan_id,
    jabatan_m.jabatan_nama,
    pangkat_m.pangkat_nama,
    kelompokpegawai_m.kelompokpegawai_nama,
    kelompokpegawai_m.kelompokpegawai_namalainnya,
    kelompokpegawai_m.kelompokpegawai_fungsi,
    pegawai_m.warna_kulit,
    fgetnamalookup(pegawai_m.warna_kulit::integer) AS warna_kulit_nama,
    pegawai_m.status_pegawai,
    fgetnamalookup(pegawai_m.status_pegawai::integer) AS status_pegawai_nama,
    pegawai_m.bank_id,
    bank_m.nama_bank,
    bank_m.no_rekening,
    pegawai_m.created_date,
    pegawai_m.kemampuan_bahasa,
    pegawai_m.tinggibadan,
    pegawai_m.beratbadan,
    pegawai_m.npwp
   FROM pegawai_m
     LEFT JOIN pendidikan_m ON pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN jabatan_m ON pegawai_m.jabatan_id = jabatan_m.jabatan_id
     LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN pangkat_m ON pegawai_m.pangkat_id = pangkat_m.pangkat_id
     LEFT JOIN suku_m ON pegawai_m.suku_id = suku_m.suku_id
     LEFT JOIN bank_m ON pegawai_m.bank_id = bank_m.bank_id
  WHERE pegawai_m.is_active = true AND pegawai_m.is_deleted = false;");

        $this->execute('ALTER TABLE public.pegawai_master_v
  OWNER TO postgres;');

    /*infokunjunganrs_v*/
        $this->execute('DROP VIEW if exists public.infokunjunganrs_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infokunjunganrs_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
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
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
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
    asuransipasien_m.nopeserta,
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
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.created_by,
    antrian_t.no_antrian,
    pendaftaran_t.is_karcis,
    pendaftaran_t.status_periksa::integer AS status_periksa_id,
    pendaftaran_t.pasienpulang_id AS pulang_rj_rd,
    NULL::integer AS pulang_ri,
    NULL::integer AS pasienadmisi_id,
    pendaftaran_t.is_ranap,
    pendaftaran_t.bpjs_id,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan_nama,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelarbelakang_nama,
    pendaftaran_t.pendaftaranibu_id,
    pasienpulang_t.tglpasienpulang,
    carakeluar_m.carakeluar_nama
   FROM pendaftaran_t
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
  WHERE pendaftaran_t.instalasi_id <> 3
UNION ALL
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
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
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
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
    asuransipasien_m.nopeserta,
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
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.created_by,
    antrian_t.no_antrian,
    pendaftaran_t.is_karcis,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    NULL::integer AS pulang_rj_rd,
    pasienadmisi_t.pasienpulang_id AS pulang_ri,
    pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.is_ranap,
    pasienadmisi_t.bpjs_id,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan_nama,
    fgetnamalookup(pegawai_m.gelarbelakang::integer) AS gelarbelakang_nama,
    pendaftaran_t.pendaftaranibu_id,
    pasienpulang_t.tglpasienpulang,
    carakeluar_m.carakeluar_nama
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id;
");

        $this->execute('ALTER TABLE public.infokunjunganrs_v
  OWNER TO postgres;');

    /*infopendaftaranol_v*/
        $this->execute('DROP VIEW if exists public.infopendaftaranol_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopendaftaranol_v AS 
 SELECT pendaftaranol_t.pendaftaranol_id,
    pendaftaranol_t.pendaftaran_id,
    pendaftaranol_t.no_pendaftaranol,
    pendaftaranol_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jk,
    pendaftaranol_t.no_asuransi,
    pendaftaranol_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaranol_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pendaftaranol_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaranol_t.jam_kunjungan,
    pendaftaranol_t.created_date AS tgl_pendaftaran,
    pendaftaranol_t.tgl_pendaftaranol AS tgl_kunjungan,
    pendaftaranol_t.status_daftar_ol,
    fgetnamalookup(pendaftaranol_t.status_daftar_ol) AS status_daftar,
    pendaftaranol_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaranol_t.antrian_id,
    antrian_t.no_antrian,
    pendaftaranol_t.klasifikasipasien_id,
    jenispasien_m.jenispasien_id AS klasifikasipasien_kode,
    jenispasien_m.statuspasien_nama AS klasifikasipasien_nama,
    pendaftaranol_t.jadwaldokter_id,
    pendaftaranol_t.jam_mulai,
    pendaftaranol_t.jam_tutup,
    pendaftaranol_t.created_by,
    pasien_m.no_identitas_pasien,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.no_telepon_pasien,
    pasien_m.agama AS agama_id,
    fgetnamalookup(pasien_m.agama::integer) AS agama_nama,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    pasien_m.statusperkawinan AS statusperkawinan_id,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan_nama,
    pasien_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pasien_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaranol_t.no_rujukan,
    pendaftaranol_t.jenis_reservasi,
    fgetnamalookup(pendaftaranol_t.jenis_reservasi::integer) AS jenis_reservasinama
   FROM pendaftaranol_t
     JOIN pasien_m ON pendaftaranol_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pegawai_m ON pendaftaranol_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaranol_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaranol_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN jenispasien_m ON pendaftaranol_t.klasifikasipasien_id = jenispasien_m.jenispasien_id
     LEFT JOIN antrian_t ON pendaftaranol_t.antrian_id = antrian_t.antrian_id;");

        $this->execute('ALTER TABLE public.infopendaftaranol_v
  OWNER TO postgres;');

    /*laporankunjunganrs_v*/
        $this->execute('DROP VIEW if exists public.laporankunjunganrs_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporankunjunganrs_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pendaftaran_t.pendaftaran_id,
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
    pendaftaran_t.shift_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.rujukan_id,
    pendaftaran_t.pasienpulang_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pasien_m.kecamatan_id,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.is_active,
    pasien_m.is_deleted,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS status_perkawinan,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup(pasien_m.rhesus::integer) AS rhesus
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
  WHERE pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])
UNION ALL
 SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pendaftaran_t.pendaftaran_id,
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
    pendaftaran_t.shift_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.rujukan_id,
    pendaftaran_t.pasienpulang_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pasien_m.kecamatan_id,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.is_active,
    pasien_m.is_deleted,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS status_perkawinan,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup(pasien_m.rhesus::integer) AS rhesus
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id;
");

        $this->execute('ALTER TABLE public.laporankunjunganrs_v
  OWNER TO postgres;');

    /*rl1_1_datars_v*/
        $this->execute('DROP VIEW if exists public.rl1_1_datars_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.rl1_1_datars_v AS 
 SELECT profilrumahsakit_m.nokode_rumahsakit AS \"Nomor Kode RS\",
    profilrumahsakit_m.tglregistrasi AS \"Tanggal Registrasi\",
    profilrumahsakit_m.nama_rumahsakit AS \"Nama Rumah Sakit\",
    fgetnamalookup(profilrumahsakit_m.jenis_rumahsakit) AS \"Jenis Rumah Sakit\",
    fgetnamalookup(profilrumahsakit_m.kelas_rumahsakit::integer) AS \"Kelas Rumah Sakit\",
    pegawai_m.nama_pegawai AS \"Nama Direktur RS\",
    profilrumahsakit_m.nama_penyelenggara AS \"Nama Penyelenggara RS\",
    concat(fgetnamaarea(NULL::integer, profilrumahsakit_m.kabupaten_id, NULL::integer, NULL::integer), '/', fgetnamaarea(profilrumahsakit_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer)) AS \"Kab / Kota\",
    profilrumahsakit_m.kode_pos AS \"Kode Pos\",
    profilrumahsakit_m.no_telp_profilrs AS \"Telepon\",
    profilrumahsakit_m.no_faksimili AS \"Fax\",
    profilrumahsakit_m.email AS \"Email\",
    profilrumahsakit_m.notelphumas AS \"Nomot Telp Umum/Humas RS\",
    profilrumahsakit_m.website AS \"Website\",
    profilrumahsakit_m.luastanah AS \"Tanah\",
    profilrumahsakit_m.luasbangunan AS \"Bangunan\",
    profilrumahsakit_m.nomor_suratizin AS \"Nomor\",
    profilrumahsakit_m.tgl_suratizin AS \"Tanggal\",
    profilrumahsakit_m.oleh_suratizin AS \"Oleh\",
    profilrumahsakit_m.sifat_suratizin AS \"Sifat\",
    profilrumahsakit_m.masaberlaku_dari AS \"Masa Berlaku Dari\",
    profilrumahsakit_m.masaberlaku_sampai AS \"Masa Berlaku Sampai\",
    profilrumahsakit_m.statuskepemilikanrs AS \"Status Penyelenggara Swasta\",
    profilrumahsakit_m.pentahapanakreditasrs AS \"Pentahapan\",
    profilrumahsakit_m.statusakreditasrs AS \"Status\",
    profilrumahsakit_m.tglakreditasi AS \"Tanggal Akreditasi\"
   FROM profilrumahsakit_m
     LEFT JOIN pegawai_m ON profilrumahsakit_m.namadirektur_id = pegawai_m.pegawai_id
  WHERE profilrumahsakit_m.is_active = true AND profilrumahsakit_m.is_deleted = false;
");

        $this->execute('ALTER TABLE public.rl1_1_datars_v
  OWNER TO postgres;');

    /*laporankunjunganri_v*/
        $this->execute('DROP VIEW if exists public.laporankunjunganri_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporankunjunganri_v AS 
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
    pasien_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
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
    pasienadmisi_t.kamartempattidur_id
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
  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.laporankunjunganri_v
  OWNER TO postgres;');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190919_121841_optimize_view_8 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190919_121841_optimize_view_8 cannot be reverted.\n";

        return false;
    }
    */
}
