<?php

use yii\db\Migration;

/**
 * Class m200611_000153_migrate_20200611
 */
class m200611_000153_migrate_20200611 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN "det" float8;');

        $this->execute('DROP VIEW if exists "public"."profilrumahsakit_v";');

        $this->execute("
            CREATE VIEW \"public\".\"profilrumahsakit_v\" AS  SELECT profilrumahsakit_m.nokode_rumahsakit,
    profilrumahsakit_m.tglregistrasi,
    profilrumahsakit_m.nama_rumahsakit,
    fgetnamalookup(profilrumahsakit_m.jenis_rumahsakit) AS jenis_rs,
    fgetnamalookup((profilrumahsakit_m.kelas_rumahsakit)::integer) AS kelas_rs,
    profilrumahsakit_m.nama_penyelenggara,
    profilrumahsakit_m.kode_pos,
    profilrumahsakit_m.no_telp_profilrs,
    profilrumahsakit_m.no_faksimili,
    profilrumahsakit_m.email,
    profilrumahsakit_m.notelphumas,
    profilrumahsakit_m.website,
    profilrumahsakit_m.luastanah,
    profilrumahsakit_m.luasbangunan,
    profilrumahsakit_m.nomor_suratizin,
    profilrumahsakit_m.tgl_suratizin,
    profilrumahsakit_m.oleh_suratizin,
    profilrumahsakit_m.sifat_suratizin,
    profilrumahsakit_m.masaberlaku_dari,
    profilrumahsakit_m.masaberlaku_sampai,
    profilrumahsakit_m.statuskepemilikanrs,
    profilrumahsakit_m.pentahapanakreditasrs,
    profilrumahsakit_m.statusakreditasrs,
    profilrumahsakit_m.tglakreditasi,
    profilrumahsakit_m.status_penyelenggara,
    profilrumahsakit_m.profilrs_id,
    profilrumahsakit_m.is_active,
    profilrumahsakit_m.kabupaten_id,
    kabupaten_m.kabupaten_nama AS kota
   FROM (profilrumahsakit_m
     JOIN kabupaten_m ON ((profilrumahsakit_m.kabupaten_id = kabupaten_m.kabupaten_id)))
  WHERE ((profilrumahsakit_m.is_active = true) AND (profilrumahsakit_m.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."infokunjunganrs_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrs_v\" AS  SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
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
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pendaftaran_t.created_by,
    antrian_t.no_antrian,
    pendaftaran_t.is_karcis,
    (pendaftaran_t.status_periksa)::integer AS status_periksa_id,
    pendaftaran_t.pasienpulang_id AS pulang_rj_rd,
    NULL::integer AS pulang_ri,
    NULL::integer AS pasienadmisi_id,
    pendaftaran_t.is_ranap,
    pendaftaran_t.bpjs_id,
    fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan_nama,
    fgetnamalookup((pegawai_m.gelarbelakang)::integer) AS gelarbelakang_nama,
    pendaftaran_t.pendaftaranibu_id,
    pasienpulang_t.tglpasienpulang,
    carakeluar_m.carakeluar_nama,
    bpjs_t.nosep,
    pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
    fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasirm,
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien_nama,
    NULL::character varying AS kamar,
    NULL::character varying AS no_tempattidur
   FROM ((((((((((((((((((((((pendaftaran_t
     LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
  WHERE (pendaftaran_t.instalasi_id <> 3)
UNION ALL
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
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
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pendaftaran_t.created_by,
    antrian_t.no_antrian,
    pendaftaran_t.is_karcis,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    NULL::integer AS pulang_rj_rd,
    pasienadmisi_t.pasienpulang_id AS pulang_ri,
    pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.is_ranap,
    pasienadmisi_t.bpjs_id,
    fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan_nama,
    fgetnamalookup((pegawai_m.gelarbelakang)::integer) AS gelarbelakang_nama,
    pendaftaran_t.pendaftaranibu_id,
    pasienpulang_t.tglpasienpulang,
    carakeluar_m.carakeluar_nama,
    bpjs_t.nosep,
    pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
    fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasirm,
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien_nama,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamartempattidur_m.no_tempattidur
   FROM (((((((((((((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
     LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)));");
       
        $this->execute("
            CREATE VIEW \"public\".\"invoicesudahbayar_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    pasien_m.no_rekam_medik,
        CASE
            WHEN (pasien_m.nama_pasien IS NULL) THEN (tagihan.nama_pembeli)::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    dok_1.nama_pegawai AS dok_pendaftaran,
    dok_2.nama_pegawai AS dok_ranap,
    r_1.ruangan_nama AS r_pendaftaran,
    r_2.ruangan_nama AS r_ranap,
    tagihan.carabayar_nama,
    tagihan.penjamin_nama,
    (tagihan.total_tagihan)::integer AS total_tagihan,
    (tagihan.total_uang_muka)::integer AS total_uang_muka,
    (tagihan.total_sudah_dibayarkan)::integer AS total_sudah_dibayarkan,
    (tagihan.total_sisatagihan)::integer AS total_sisatagihan,
    fgetnamalookup(tagihan.status_bayar) AS status_bayar,
    tagihan.tgl_pendaftaran,
    tagihan.pembayaranpelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.tandabuktibayar_id,
    (tagihan.pembulatan)::integer AS pembulatan,
    tagihan.biaya_administrasi,
    tagihan.tgl_pembayaran,
    tagihan.no_pembayaran,
        CASE
            WHEN (tagihan.pasienadmisi_id IS NULL) THEN i_1.instalasi_id
            WHEN (tagihan.pasienadmisi_id IS NOT NULL) THEN i_2.instalasi_id
            ELSE NULL::integer
        END AS instalasi_id,
    i_1.instalasi_nama AS i_pendaftaran,
    i_2.instalasi_nama AS i_ranap,
    (tagihan.total_subsidiasuransi)::integer AS total_subsidiasuransi,
    tagihan.penjualanresep_id,
    pasien_m.tanggal_lahir,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin
   FROM (((((((((( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN (pembayaranpelayanan_t.total_bayartindakan <= (0)::double precision) THEN (0)::double precision
                    ELSE ((pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi) + pembayaranpelayanan_t.total_terbayar)
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            pendaftaran_t.pasienadmisi_id
           FROM ((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN (pembayaranpelayanan_t.total_bayartindakan <= (0)::double precision) THEN (0)::double precision
                    ELSE ((pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi) + pembayaranpelayanan_t.total_terbayar)
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            pendaftaran_t.pasienadmisi_id
           FROM ((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
             JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            1 AS obatsudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            0 AS total_tagihan,
            bayaruangmuka_t.jumlah_uangmuka AS total_uang_muka,
            0 AS total_sudah_dibayarkan,
            0 AS total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            NULL::integer AS pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            bayaruangmuka_t.tandabuktibayar_id,
            0 AS pembulatan,
            tandabuktibayar_t.biayaadministrasi,
            bayaruangmuka_t.tgl_uangmuka,
            bayaruangmuka_t.no_uangmuka,
            0 AS total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            pendaftaran_t.pasienadmisi_id
           FROM (((((bayaruangmuka_t
             JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
             JOIN tandabuktibayar_t ON ((tandabuktibayar_t.tandabuktibayar_id = bayaruangmuka_t.tandabuktibayar_id)))
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            penjualanresep_t.pasien_id,
            NULL::integer AS jeniskasuspenyakit_id,
            penjualanresep_t.pegawai_id AS dok_pendaftaran_id,
            NULL::integer AS dok_ranap_id,
            penjualanresep_t.ruangan_id AS r_pendaftaran_id,
            NULL::integer AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN (pembayaranpelayanan_t.total_bayartindakan <= (0)::double precision) THEN (0)::double precision
                    ELSE ((pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi) + pembayaranpelayanan_t.total_terbayar)
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            penjualanresep_t.status_bayar,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            NULL::integer AS kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            penjualanresep_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            NULL::integer AS pasienadmisi_id
           FROM (((((penjualanresep_t
             JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
             JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))) tagihan
     LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((tagihan.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_1 ON ((tagihan.dok_pendaftaran_id = dok_1.pegawai_id)))
     LEFT JOIN pegawai_m dok_2 ON ((tagihan.dok_ranap_id = dok_2.pegawai_id)))
     LEFT JOIN ruangan_m r_1 ON ((tagihan.r_pendaftaran_id = r_1.ruangan_id)))
     LEFT JOIN ruangan_m r_2 ON ((tagihan.r_ranap_id = r_2.ruangan_id)))
     LEFT JOIN instalasi_m i_1 ON ((i_1.instalasi_id = r_1.instalasi_id)))
     LEFT JOIN instalasi_m i_2 ON ((i_2.instalasi_id = r_2.instalasi_id)))
     LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
  WHERE (tagihan.tindakansudahbayar_id IS NOT NULL)
  GROUP BY tagihan.pendaftaran_id, tagihan.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, dok_1.nama_pegawai, dok_2.nama_pegawai, r_1.ruangan_nama, r_2.ruangan_nama, tagihan.carabayar_nama, tagihan.penjamin_nama, tagihan.total_tagihan, tagihan.total_uang_muka, tagihan.total_sudah_dibayarkan, tagihan.total_sisatagihan, tagihan.status_bayar, tagihan.tgl_pendaftaran, tagihan.pembayaranpelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tagihan.tandabuktibayar_id, tagihan.pembulatan, tagihan.biaya_administrasi, tagihan.tgl_pembayaran, tagihan.no_pembayaran, i_1.instalasi_nama, i_2.instalasi_nama, tagihan.total_subsidiasuransi, tagihan.penjualanresep_id, tagihan.nama_pembeli, tagihan.pasienadmisi_id, i_1.instalasi_id, i_2.instalasi_id, pasien_m.tanggal_lahir, pasien_m.jeniskelamin;");
        
        $this->execute("
            CREATE VIEW \"public\".\"invoicesudahbayardetail_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.pelayanan_id,
    tagihan.pasien_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN (pasien_m.nama_pasien IS NULL) THEN (tagihan.nama_pembeli)::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    pasien_m.tanggal_lahir,
    tagihan.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    tagihan.tgl_pendaftaran,
    tagihan.no_pendaftaran,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    (tagihan.tarif_satuan)::integer AS tarif_satuan,
    tagihan.qty,
    (tagihan.sub_total)::integer AS sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.tgl_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_tinpelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
    tagihan.penjamin_tinpelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.jeniskasuspenyakit_id,
    tagihan.pembayaranpelayanan_id,
    tagihan.biaya_administrasi,
    tagihan.e_collection,
    tagihan.nama_pemrekening,
    tagihan.no_rekening,
    tagihan.carabayar_pelayanan_id,
    tagihan.carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    tagihan.penjamin_pelayanan,
    (tagihan.tarif_cyto)::integer AS tarif_cyto,
    tagihan.tandabuktibayar_id,
    tagihan.jeniskasuspenyakit_nama,
    tagihan.penjualanresep_id
   FROM ((((((( SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM (((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
             JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
             JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_paket'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM ((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli
           FROM ((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            penjualanresep_t.noresep AS no_pendaftaran,
            NULL::character varying AS umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            NULL::integer AS jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            NULL::character varying AS jeniskasuspenyakit_nama,
            obatalkespasien_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli
           FROM ((((((obatalkespasien_t
             JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))) tagihan
     LEFT JOIN ruangan_m ON ((tagihan.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN carabayar_m ON ((tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)));");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200611_000153_migrate_20200611 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200611_000153_migrate_20200611 cannot be reverted.\n";

        return false;
    }
    */
}
