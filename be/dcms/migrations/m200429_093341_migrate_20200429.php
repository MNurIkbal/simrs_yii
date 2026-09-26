<?php

use yii\db\Migration;

/**
 * Class m200429_093341_migrate_20200429
 */
class m200429_093341_migrate_20200429 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."inpostoperasi_t" ADD COLUMN "posisioperasi_id" int4;');
        $this->execute('ALTER TABLE "public"."inpostoperasi_t" ADD COLUMN "pegawai_id" int4;');

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
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien_nama
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
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien_nama
   FROM ((((((((((((((((((((((((pendaftaran_t
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
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
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
        
        $this->execute('ALTER TABLE "public"."infokunjunganrs_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infotarifakomodasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotarifakomodasi_v\" AS  SELECT kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kamarruangan_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    tariftindakan_m.kamarruangan_id,
    tariftindakan_m.penjamin_id,
    kamarruangan_m.kamarruangan_jenis AS kamarruangan_jenis_id,
    fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS kamarruangan_jenis,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    kamartempattidur_m.kettempattidur_id,
    kamartempattidur_m.status_isi,
    kettempattidur_m.kode_warna,
    daftartindakan_m.is_akomodasi,
    tariftindakan_m.harga_tariftindakan,
    ( SELECT pasien_m.jeniskelamin
           FROM ((pendaftaran_t
             JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
          WHERE ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id) AND (pasienadmisi_t.pasienpulang_id IS NULL) AND (pasienadmisi_t.status_ranap = ANY (ARRAY[440, 441])))
         LIMIT 1) AS isi_jk
   FROM (((((((tariftindakan_m
     JOIN kamarruangan_m ON ((tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN ruangan_m ON ((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id)))
     JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
     JOIN kettempattidur_m ON ((kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id)))
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
  WHERE (tariftindakan_m.komponentarif_id = 6);");
        
        $this->execute('ALTER TABLE "public"."infotarifakomodasi_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infoobatalkesexpired_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoobatalkesexpired_v\" AS  SELECT hit.obatalkes_id,
    sum((hit.qtystok_in - hit.qtystok_out)) AS stok,
    hit.obatalkes_nama,
    hit.satuankecil_id,
    hit.s_kecil AS satuan_kecil,
    hit.tglkadaluarsa,
    hit.harganetto,
    (hit.harganetto * sum((hit.qtystok_in - hit.qtystok_out))) AS jumlah_harganetto,
    hit.instalasi_nama,
    hit.ruangan_nama,
    hit.periodestokobat_id,
    hit.tglperiodestok_awal AS tglperiodeposting_awal,
    hit.tglperiodestok_akhir AS tglperiodeposting_akhir,
    hit.ruangan_id,
    hit.instalasi_id,
    hit.id_stok,
    hit.nobatch,
    hit.margin,
    hit.ppn,
    hit.disc,
    hit.hn_last,
    hit.a1 AS hn_last_margin,
    hit.a2 AS hn_last_diskon,
    hit.a3 AS hn_last_margin_diskon,
    hit.a4 AS hn_last_ppn,
    hit.a5 AS hargajual_last,
    hit.hn_min,
    hit.b1 AS hn_min_margin,
    hit.b2 AS hn_min_diskon,
    hit.b3 AS hn_min_margin_diskon,
    hit.b4 AS hn_min_ppn,
    hit.b5 AS hargajual_min,
    hit.hn_max,
    hit.c1 AS hn_max_margin,
    hit.c2 AS hn_max_diskon,
    hit.c3 AS hn_max_margin_diskon,
    hit.c4 AS hn_max_ppn,
    hit.c5 AS hargajual_max,
    hit.hn_avg,
    hit.d1 AS hn_avg_margin,
    hit.d2 AS hn_avg_diskon,
    hit.d3 AS hn_avg_margin_diskon,
    hit.d4 AS hn_avg_ppn,
    hit.d5 AS hargajual_avg,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c5
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b5
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d5
            ELSE hit.a5
        END AS hargaygdipakai,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.hn_max
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.hn_min
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.hn_avg
            ELSE hit.hn_last
        END AS harganetto_ygdipakai,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c1
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b1
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d1
            ELSE hit.a1
        END AS hn_margin,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c2
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b2
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d2
            ELSE hit.a2
        END AS hn_diskon,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c4
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b4
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d4
            ELSE hit.a4
        END AS hn_ppn
   FROM ( SELECT
                CASE
                    WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.tglkadaluarsa,
            obatalkes_m.obatalkes_nama,
            stokobatalkes_t.satuankecil_id,
            satuan_kecil.satuanunit_nama AS s_kecil,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokobatalkes_r.periodestokobat_id,
            periodestokobat_m.tglperiodestok_awal,
            periodestokobat_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            stokobatalkes_t.nobatch,
            obatalkes_m.harganetto,
            obatalkes_m.hargaterakhir AS hn_last,
            obatalkes_m.hargaminimum AS hn_min,
            obatalkes_m.hargamaksimum AS hn_max,
            obatalkes_m.hargaratarata AS hn_avg,
            konfigfarmasi_k.persenppn AS ppn,
            konfigfarmasi_k.persenmargin AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            konfigfarmasi_k.hargaygdigunakan,
            (obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS a1,
            (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS a2,
            ((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS a3,
            ((((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS a4,
            (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS a5,
            (obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS b1,
            (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS b2,
            ((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS b3,
            ((((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS b4,
            (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS b5,
            (obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS c1,
            (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS c2,
            ((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS c3,
            ((((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS c4,
            (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS c5,
            (obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS d1,
            (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS d2,
            ((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS d3,
            ((((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS d4,
            (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS d5
           FROM ((((((((stokobatalkes_t
             JOIN obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN ruangan_m ON ((stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN formstokopname_t ON (((stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id) AND (stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id))))
             JOIN stokobatalkes_r ON (((stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id) AND (stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id))))
             LEFT JOIN periodestokobat_m ON ((stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((stokobatalkes_t.satuankecil_id = satuan_kecil.satuanunit_id)))
             JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
          WHERE (stokobatalkes_r.is_periode = true)) hit
  GROUP BY hit.obatalkes_id, hit.obatalkes_nama, hit.tglkadaluarsa, hit.instalasi_nama, hit.ruangan_nama, hit.harganetto, hit.periodestokobat_id, hit.tglperiodestok_awal, hit.tglperiodestok_akhir, hit.ruangan_id, hit.instalasi_id, hit.id_stok, hit.s_kecil, hit.nobatch, hit.satuankecil_id, hit.margin, hit.ppn, hit.disc, hit.hn_last, hit.a1, hit.a2, hit.a3, hit.a4, hit.a5, hit.hn_min, hit.b1, hit.b2, hit.b3, hit.b4, hit.b5, hit.hn_max, hit.c1, hit.c2, hit.c3, hit.c4, hit.c5, hit.hn_avg, hit.d1, hit.d2, hit.d3, hit.d4, hit.d5,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c5
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b5
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d5
            ELSE hit.a5
        END,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.hn_max
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.hn_min
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.hn_avg
            ELSE hit.hn_last
        END,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c1
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b1
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d1
            ELSE hit.a1
        END,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c2
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b2
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d2
            ELSE hit.a2
        END,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c4
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b4
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d4
            ELSE hit.a4
        END;");
        
        $this->execute('ALTER TABLE "public"."infoobatalkesexpired_v" OWNER TO "postgres";');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200429_093341_migrate_20200429 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200429_093341_migrate_20200429 cannot be reverted.\n";

        return false;
    }
    */
}
