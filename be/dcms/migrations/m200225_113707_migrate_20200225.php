<?php

use yii\db\Migration;

/**
 * Class m200225_113707_migrate_20200225
 */
class m200225_113707_migrate_20200225 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pembayaran_t" 
                      ADD COLUMN "total_tunai" float8 DEFAULT 0,
                      ADD COLUMN "total_nontunai" float8 DEFAULT 0;');

        $this->execute('ALTER TABLE "public"."pembayaran_t" 
                    ADD COLUMN "total_discount" float8 DEFAULT 0;');

        $this->execute('ALTER TABLE "public"."tindakankomponen_t" ADD COLUMN "discount_komponen" float8 DEFAULT 0;');

    	$this->execute('DROP VIEW if exists public.laporankunjunganrj_v;');

    	$this->execute("
    		CREATE OR REPLACE VIEW public.laporankunjunganrj_v AS 
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
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    NULL::integer AS ruanganasal_id,
    NULL::character varying AS ruanganasal_nama,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
    kondisikeluar_m.kondisikeluar_nama,
    carakeluar_m.carakeluar_nama,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    pasienbatalperiksa_t.alasan_batal,
    pendaftaran_t.bpjs_id,
    bpjs_t.nosep,
    pendaftaran_t.status_periksa AS status_periksa_id,
    pendaftaran_t.status_bayar AS status_bayar_id,
    pendaftaran_t.asuransipasien_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     LEFT JOIN loket_m ON antrian_t.loket_id = loket_m.loket_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN pasienbatalperiksa_t ON pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted IS FALSE
  WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.status_periksa::text <> '402'::text
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
    fgetnamalookup(pasien_m.agama::integer) AS jenis_kelamin,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
    ruanganasal_m.ruangan_nama AS ruanganasal_nama,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
    kondisikeluar_m.kondisikeluar_nama,
    carakeluar_m.carakeluar_nama,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    pasienbatalperiksa_t.alasan_batal,
    pendaftaran_t.bpjs_id,
    bpjs_t.nosep,
    pendaftaran_t.status_periksa AS status_periksa_id,
    pendaftaran_t.status_bayar AS status_bayar_id,
    pendaftaran_t.asuransipasien_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id
     LEFT JOIN pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ruangan_m ruanganasal_m ON konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     LEFT JOIN loket_m ON antrian_t.loket_id = loket_m.loket_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN pasienbatalperiksa_t ON pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted IS FALSE
  WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.status_periksa::text <> '402'::text;");

    	$this->execute('ALTER TABLE public.laporankunjunganrj_v
  OWNER TO postgres;');

    	$this->execute('DROP VIEW if exists public.rincianpasien_v;');

    	$this->execute("
    		CREATE OR REPLACE VIEW public.rincianpasien_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    carabayar_admisi.carabayar_nama AS carabayar_admisi,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_admisi.penjamin_nama AS penjamin_admisi,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelas_admisi.kelaspelayanan_nama AS kelas_admisi,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status,
    pendaftaran_t.is_stopakomodasi,
        CASE
            WHEN pendaftaran_t.is_stopakomodasi <> true THEN false
            WHEN pendaftaran_t.is_stopakomodasi = true THEN true
            ELSE NULL::boolean
        END AS is_pulang
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN carabayar_m carabayar_admisi ON pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN penjamin_m penjamin_admisi ON pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kelas_admisi ON pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasienadmisi_t.tgl_admisi, pendaftaran_t.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, pendaftaran_t.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, pendaftaran_t.ruangan_id, r_pendaftaran.ruangan_nama, pasienadmisi_t.ruangan_id, r_admisi.ruangan_nama, pasienadmisi_t.kamarruangan_id, kamarruangan_m.kamarruangan_nokamar, pasienadmisi_t.kamartempattidur_id, kamartempattidur_m.no_tempattidur, pendaftaran_t.status_bayar, (fgetnamalookup(pendaftaran_t.status_bayar)), penjamin_admisi.penjamin_nama, carabayar_admisi.carabayar_nama, kelas_admisi.kelaspelayanan_nama;
");

    	$this->execute('ALTER TABLE public.rincianpasien_v
  OWNER TO postgres;');

    	$this->execute('DROP VIEW if exists public.rincianpasiendetail2_v;');

    	$this->execute("
    		CREATE OR REPLACE VIEW public.rincianpasiendetail2_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasien_m.no_rekam_medik,
    COALESCE(sum(tagihan.tagihan), 0::double precision) AS total_tagihan,
        CASE
            WHEN pendaftaran_t.is_stopakomodasi <> true THEN COALESCE(sum(pendaftaranpenjamin_t.nominal_dijamin), 0::double precision)
            WHEN pendaftaran_t.is_stopakomodasi = true THEN COALESCE(sum(pembayaranpenjamin_t.total_dijamin), 0::double precision)
            ELSE 0::double precision
        END AS subsidi_asuransi,
    COALESCE(sum(pembayaran_t.total_tunai) - sum(pembayaran_t.total_nontunai), 0::double precision) AS uang_masuk,
    COALESCE(sum(tagihan.tagihan), 0::double precision) - (sum(pembayaran_t.total_tunai) - sum(pembayaran_t.total_nontunai) + COALESCE(sum(pembayaranpenjamin_t.total_dijamin), 0::double precision)) AS total_sisa_tagihan,
    COALESCE(sum(pembayaranpelayanan_t.total_terbayar), 0::double precision) AS total_sdh_bayar,
    COALESCE(sum(pembayaranpelayanan_t.total_subsidiasuransi), 0::double precision) AS total_asuransi,
    COALESCE(sum(bayaruangmuka_t.jumlah_uangmuka), 0::double precision) AS total_uang_muka,
    COALESCE(sum(pembayaran_t.total_administrasi), 0::double precision) AS total_administrasi,
    0 AS total_pembulatan,
    0 AS total_pembayaran_pasien
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id AND pembayaran_t.is_deleted = false
     LEFT JOIN ( SELECT 'tindakan'::text AS jenis,
            tindakanpelayanan_t.pendaftaran_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pendaftaran_id
        UNION ALL
         SELECT 'obat'::text AS jenis,
            obatalkespasien_t.pendaftaran_id,
            sum(obatalkespasien_t.hargajual_oa) AS tagihan
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false
          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id
     LEFT JOIN pendaftaranpenjamin_t ON pendaftaran_t.pendaftaran_id = pendaftaranpenjamin_t.pendaftaran_id AND pendaftaranpenjamin_t.is_deleted = false
     LEFT JOIN pembayaranpenjamin_t ON pembayaran_t.pembayaran_id = pembayaranpenjamin_t.pembayaran_id AND pembayaranpenjamin_t.is_deleted = false
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id AND bayaruangmuka_t.is_deleted = false
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE pendaftaran_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pasien_m.no_rekam_medik;");

    	$this->execute('ALTER TABLE public.rincianpasiendetail2_v
  OWNER TO postgres;');

    	$this->execute('DROP VIEW if exists public.closing_kasir_view;');

    	$this->execute("
    		CREATE OR REPLACE VIEW public.closing_kasir_view AS 
 SELECT agr_bukti_bayar.tandabuktibayar_id,
    agr_bukti_bayar.ruangan_id,
    agr_bukti_bayar.bayaruangmuka_id,
    agr_bukti_bayar.closingkasir_id,
    agr_bukti_bayar.pembayaranpelayanan_id,
    agr_bukti_bayar.shift_id,
    agr_bukti_bayar.nourutkasir,
    agr_bukti_bayar.nobuktibayar,
    agr_bukti_bayar.tglbuktibayar,
    agr_bukti_bayar.uangditerima,
    agr_bukti_bayar.pendaftaran_id,
        CASE
            WHEN pendaftaran_t.no_pendaftaran IS NULL THEN penjualanresep_t.noresep
            ELSE pendaftaran_t.no_pendaftaran
        END AS no_pendaftaran,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    agr_bukti_bayar.pegawai1_id,
    agr_bukti_bayar.no_pembayaran,
    pasien_m.no_rekam_medik,
    agr_bukti_bayar.carabayar_id,
    agr_bukti_bayar.carabayar_nama,
    agr_bukti_bayar.penjamin_id,
    agr_bukti_bayar.penjamin_nama,
        CASE
            WHEN agr_bukti_bayar.total_tagihan IS NULL THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tagihan
        END AS jmlpembayaran,
        CASE
            WHEN agr_bukti_bayar.total_tunai IS NULL THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tunai
        END AS pembayaran_tunai,
    COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) AS pembayaran_nontunai,
    COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) AS pembayaran_penjamin,
    agr_bukti_bayar.pembayaran_id
   FROM ( SELECT tandabuktibayar_t.tandabuktibayar_id,
            tandabuktibayar_t.ruangan_id,
            tandabuktibayar_t.bayaruangmuka_id,
            tandabuktibayar_t.closingkasir_id,
            tandabuktibayar_t.pembayaranpelayanan_id,
            tandabuktibayar_t.shift_id,
            tandabuktibayar_t.nourutkasir,
            tandabuktibayar_t.nobuktibayar,
            tandabuktibayar_t.tglbuktibayar,
            tandabuktibayar_t.uangditerima,
            tandabuktibayar_t.pegawai1_id,
            pembayaranpelayanan_t.no_pembayaran,
                CASE
                    WHEN tandabuktibayar_t.bayaruangmuka_id IS NOT NULL THEN bayaruangmuka_t.pendaftaran_id
                    WHEN tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL THEN pembayaranpelayanan_t.pendaftaran_id
                    ELSE NULL::integer
                END AS pendaftaran_id,
            pembayaranpelayanan_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pembayaranpelayanan_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            tandabuktibayar_t.jmlpembayaran,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaran_penjamin.total_penjamin,
            pembayaran_penjamin.total_nontunai,
                CASE
                    WHEN pembayaran_penjamin.total_tunai < 0::double precision THEN 0::double precision
                    ELSE pembayaran_penjamin.total_tunai
                END AS total_tunai,
            pembayaran_penjamin.total_tagihan,
            pembayaran_penjamin.pembayaran_id
           FROM tandabuktibayar_t
             LEFT JOIN bayaruangmuka_t ON bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
             LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN ( SELECT COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    pembayaran_t.pembayaran_id,
                    pembayaran_t.total_nontunai,
                    COALESCE(pembayaran_t.total_tunai, 0::double precision) AS total_tunai,
                    pembayaran_t.total_tagihan + pembayaran_t.total_administrasi AS total_tagihan
                   FROM pembayaran_t
                     LEFT JOIN pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                     LEFT JOIN ( SELECT sum(pembayaranmetode_t.total_dibayar) AS total_nontunai,
                            pembayaranmetode_t.pembayaran_id
                           FROM pembayaranmetode_t
                          WHERE pembayaranmetode_t.is_deleted = false
                          GROUP BY pembayaranmetode_t.pembayaran_id) total_pembayaran ON total_pembayaran.pembayaran_id = pembayaran_t.pembayaran_id
                  WHERE pembayaran_t.is_deleted = false) pembayaran_penjamin ON pembayaran_penjamin.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
             LEFT JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
          WHERE tandabuktibayar_t.is_deleted = false) agr_bukti_bayar
     LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id;");

    	$this->execute('ALTER TABLE public.closing_kasir_view
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.inforiwayatresep_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.inforiwayatresep_v AS 
 SELECT obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    obatalkespasien_t.tglpelayanan::date AS tgl_transaksi,
    penjualanresep_t.noresep AS no_resep,
    signaobat_m.signa_id,
    signaobat_m.signa_nama,
    obatalkespasien_t.qty_oa AS qty,
    satuan_kecil.satuanunit_id,
    satuan_kecil.satuanunit_nama,
    instalasi_m.instalasi_nama,
    ruangan_tujuan.ruangan_nama
   FROM obatalkespasien_t
     JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.reseptur_id IS NULL
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ruangan_m ruangan_tujuan ON penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_tujuan.instalasi_id = instalasi_m.instalasi_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true AND obatalkespasien_t.obatsudahbayar_id IS NOT NULL;
");

        $this->execute('ALTER TABLE public.inforiwayatresep_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200225_113707_migrate_20200225 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200225_113707_migrate_20200225 cannot be reverted.\n";

        return false;
    }
    */
}
