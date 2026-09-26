<?php

use yii\db\Migration;

/**
 * Class m200409_023504_migrate_20200408
 */
class m200409_023504_migrate_20200408 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."antrian_t" ADD COLUMN "is_keteranganpasien" bool DEFAULT true;');

        $this->execute('DROP VIEW if exists "public"."infokunjunganrj_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrj_v\" AS  SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
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
    pendaftaran_t.status_periksa,
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
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
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
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa1,
    NULL::integer AS ruanganasal_id,
    NULL::character varying AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep
   FROM ((((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE (pendaftaran_t.instalasi_id = 1)
UNION
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
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
    konsulpoli_t.tgl_konsulpoli AS tgl_pendaftaran,
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
    konsulpoli_t.status_periksa,
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
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
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
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status_periksa1,
    konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
    ruanganasal_m.ruangan_nama AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS stat_ranap,
    bpjs_t.nosep
   FROM ((((((((((((((((((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN ruangan_m ruanganasal_m ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true));");

        $this->execute('ALTER TABLE "public"."infokunjunganrj_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if  exists "public"."infokartustokobatnew_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokartustokobatnew_v\" AS  SELECT kartu_stok.obatalkes_id,
    kartu_stok.tanggal_transaksi,
    kartu_stok.no_transaksi,
    obatalkes_m.obatalkes_nama,
    kartu_stok.qtystok_in,
    kartu_stok.qtystok_out,
    kartu_stok.stok_tersedia AS stok,
    satuanunit_m.satuanunit_nama,
    kartu_stok.tglkadaluarsa,
    kartu_stok.keterangan,
    kartu_stok.ruangan_asal_id,
    kartu_stok.ruangan_tujuan_id,
    ruangan_asal.ruangan_nama AS ruangan_asal_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan_nama,
        CASE
            WHEN (kartu_stok.keterangan = 'Penerimaan Mutasi'::text) THEN ruangan_asal.ruangan_nama
            WHEN (kartu_stok.keterangan = 'Mutasi Obat'::text) THEN ruangan_tujuan.ruangan_nama
            ELSE kartu_stok.reference
        END AS reference,
    kartu_stok.stok_tersedia
   FROM ((((( SELECT
                CASE
                    WHEN (penjualan_resep.penjualanresep_id IS NULL) THEN 'BMHP'::text
                    ELSE 'Penjualan Resep'::text
                END AS keterangan,
            penjualan_resep.no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            min(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            penjualan_resep.reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT obatalkespasien_t.obatalkespasien_id,
                    obatalkespasien_t.penjualanresep_id,
                        CASE
                            WHEN (penjualanresep_t.noresep IS NOT NULL) THEN penjualanresep_t.noresep
                            ELSE pendaftaran_t.no_pendaftaran
                        END AS no_transaksi,
                        CASE
                            WHEN (obatalkespasien_t.penjualanresep_id IS NULL) THEN pasien_pendaftaran.nama_pasien
                            WHEN (penjualanresep_t.pasien_id IS NOT NULL) THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM ((((obatalkespasien_t
                     LEFT JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
                     LEFT JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                     LEFT JOIN pasien_m pasien_pendaftaran ON ((pendaftaran_t.pasien_id = pasien_pendaftaran.pasien_id)))) penjualan_resep ON ((stokobatalkes_t.obatalkespasien_id = penjualan_resep.obatalkespasien_id)))
          WHERE ((stokobatalkes_t.is_deleted = false) AND (stokobatalkes_t.returresepdetail_id IS NULL) AND (stokobatalkes_t.pembatalanresep_id IS NULL))
          GROUP BY
                CASE
                    WHEN (penjualan_resep.penjualanresep_id IS NULL) THEN 'BMHP'::text
                    ELSE 'Penjualan Resep'::text
                END, penjualan_resep.no_transaksi, stokobatalkes_t.tglstok_out, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.qtystok_in, penjualan_resep.reference, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT 'Pembatalan Resep'::text AS keterangan,
            pembatalan_resep.no_pembatalan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            max(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            pembatalan_resep.reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT pembatalanresep_t.pembatalanresep_id,
                    pembatalanresep_t.tgl_pembatalan,
                    pembatalanresep_t.no_pembatalan,
                        CASE
                            WHEN (penjualanresep_t.pasien_id IS NOT NULL) THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM ((pembatalanresep_t
                     JOIN penjualanresep_t ON ((pembatalanresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))) pembatalan_resep ON ((pembatalan_resep.pembatalanresep_id = stokobatalkes_t.pembatalanresep_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY pembatalan_resep.no_pembatalan, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, pembatalan_resep.reference, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT 'Retur Resep'::text AS keterangan,
            retur_resep.no_returresep AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            retur_resep.reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT returresepdetail_t.returresepdetail_id,
                    returresep_t.no_returresep,
                    returresep_t.tgl_retur,
                        CASE
                            WHEN (penjualanresep_t.pasien_id IS NOT NULL) THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM (((returresepdetail_t
                     JOIN returresep_t ON ((returresepdetail_t.returresep_id = returresep_t.returresep_id)))
                     JOIN penjualanresep_t ON ((returresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))) retur_resep ON ((stokobatalkes_t.returresepdetail_id = retur_resep.returresepdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT 'Adjusmen Masuk'::text AS keterangan,
            adjusmen_masuk.no_adjusmen AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            max(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT adjusmenobatmasuk_t.adjusmenobatmasuk_id,
                    adjusmenobat_t.no_adjusmen,
                    adjusmenobat_t.tgl_adjusmen
                   FROM (adjusmenobatmasuk_t
                     JOIN adjusmenobat_t ON ((adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id)))) adjusmen_masuk ON ((stokobatalkes_t.adjusmenobatmasuk_id = adjusmen_masuk.adjusmenobatmasuk_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY adjusmen_masuk.no_adjusmen, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT 'Adjusmen Keluar'::text AS keterangan,
            adjusmen_keluar.no_adjusmen AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            min(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT adjusmenobatkeluar_t.adjusmenobatkeluar_id,
                    adjusmenobat_t.no_adjusmen,
                    adjusmenobat_t.tgl_adjusmen
                   FROM (adjusmenobatkeluar_t
                     JOIN adjusmenobat_t ON ((adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id)))) adjusmen_keluar ON ((stokobatalkes_t.adjusmenobatkeluar_id = adjusmen_keluar.adjusmenobatkeluar_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY adjusmen_keluar.no_adjusmen, stokobatalkes_t.tglstok_out, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT 'Penerimaan Alternatif'::text AS keterangan,
            penerimaan_alternatif.no_penerimaan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT penerimaansuppdetail_t.penerimaansuppdetail_id,
                    penerimaansupp_t.no_penerimaan,
                    penerimaansupp_t.tgl_penerimaan
                   FROM (penerimaansupp_t
                     JOIN penerimaansuppdetail_t ON ((penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id)))) penerimaan_alternatif ON ((stokobatalkes_t.penerimaansuppdetail_id = penerimaan_alternatif.penerimaansuppdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT 'Pemakaian Ruangan'::text AS keterangan,
            pemakaian_ruangan.nopemakaian_obat AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT pemakaianobatdetail_t.pemakaianobatdetail_id,
                    pemakaianobat_t.nopemakaian_obat,
                    pemakaianobat_t.tglpemakaianobat
                   FROM (pemakaianobat_t
                     JOIN pemakaianobatdetail_t ON ((pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id)))) pemakaian_ruangan ON ((stokobatalkes_t.pemakaianobatdetail_id = pemakaian_ruangan.pemakaianobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT 'Pemusnahan Obat'::text AS keterangan,
            pemusnahan_obat.nopemusnahan AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT pemusnahanobatdetail_t.pemusnahanobatdetail_id,
                    pemusnahanobat_t.nopemusnahan,
                    pemusnahanobat_t.tglpemusnahan
                   FROM (pemusnahanobat_t
                     JOIN pemusnahanobatdetail_t ON ((pemusnahanobat_t.pemusnahanobat_id = pemusnahanobatdetail_t.pemusnahanobat_id)))) pemusnahan_obat ON ((stokobatalkes_t.pemusnahanobatdetail_id = pemusnahan_obat.pemusnahanobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT 'Stok Opname'::text AS keterangan,
            stok_opname.nostokopname AS no_transaksi,
            stok_opname.tglstokopname AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT stokopnamedetail_t.stokopnamedetail_id,
                    stokopname_t.nostokopname,
                    stokopname_t.tglstokopname
                   FROM (stokopname_t
                     JOIN stokopnamedetail_t ON ((stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id)))) stok_opname ON ((stokobatalkes_t.stokopnamedetail_id = stok_opname.stokopnamedetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT 'Penerimaan Supplier'::text AS keterangan,
            penerimaan_supp.no_penerimaan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
                    penerimaanobat_t.no_penerimaan,
                    penerimaanobat_t.tgl_penerimaan
                   FROM (penerimaanobat_t
                     JOIN penerimaanobatdetail_t ON ((penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id)))) penerimaan_supp ON ((stokobatalkes_t.penerimaanobatdetail_id = penerimaan_supp.penerimaanobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT 'Retur Penerimaan Supplier'::text AS keterangan,
            retur_penerimaan.no_returpenerimaanobat AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobatdetail_id,
                    returpenerimaanobat_t.no_returpenerimaanobat,
                    returpenerimaanobat_t.tgl_retur
                   FROM (returpenerimaanobat_t
                     JOIN returpenerimaanobatdetail_t ON ((returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id)))) retur_penerimaan ON ((stokobatalkes_t.returpenerimaanobatdetail_id = retur_penerimaan.returpenerimaanobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT 'Penerimaan Mutasi'::text AS keterangan,
            terima_mutasi.noterimamutasi AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            terima_mutasi.ruanganasal_id AS ruangan_asal_id,
            terima_mutasi.ruanganpenerima_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT terimamutasiobatdetail_t.terimamutasiobatdetail_id,
                    terimamutasiobat_t.noterimamutasi,
                    terimamutasiobat_t.tglterima,
                    terimamutasiobat_t.ruanganpenerima_id,
                    terimamutasiobat_t.ruanganasal_id
                   FROM (terimamutasiobat_t
                     JOIN terimamutasiobatdetail_t ON ((terimamutasiobat_t.terimamutasiobat_id = terimamutasiobatdetail_t.terimamutasiobat_id)))) terima_mutasi ON ((stokobatalkes_t.terimamutasidetail_id = terima_mutasi.terimamutasiobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT 'Mutasi Obat'::text AS keterangan,
            mutasi_obat.nomutasioa AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            mutasi_obat.ruanganasal_id AS ruangan_asal_id,
            mutasi_obat.ruangantujuan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
                    mutasiobatruangan_t.nomutasioa,
                    mutasiobatruangan_t.tglmutasioa,
                    mutasiobatruangan_t.ruanganasal_id,
                    mutasiobatruangan_t.ruangantujuan_id
                   FROM (mutasiobatruangan_t
                     JOIN mutasiobatdetail_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))) mutasi_obat ON ((stokobatalkes_t.mutasiobatdetail_id = mutasi_obat.mutasiobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)) kartu_stok
     JOIN obatalkes_m ON ((kartu_stok.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((kartu_stok.satuanunit_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN ruangan_m ruangan_asal ON ((kartu_stok.ruangan_asal_id = ruangan_asal.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_tujuan ON ((kartu_stok.ruangan_tujuan_id = ruangan_tujuan.ruangan_id)));
");
        $this->execute('ALTER TABLE "public"."infokartustokobatnew_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."cetakkwitansibkm_v";');

        $this->execute("
            CREATE VIEW \"public\".\"cetakkwitansibkm_v\" AS  SELECT 'PEMBAYARAN'::text AS jenis,
    pembayaranpelayanan_t.pembayaranpelayanan_id AS transaksi_id,
    tandabuktibayar_t.tandabuktibayar_id,
    NULL::integer AS tandabuktikeluar_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    NULL::integer AS bayaruangmuka_id,
    NULL::integer AS penjualanresep_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.instalasi_id,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    NULL::text AS tglpulang_pendaftaran,
    NULL::text AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_pendaftaran.nama_pegawai
            ELSE dok_admisi.nama_pegawai
        END AS dokter,
    pegawai_m.nama_pegawai AS kasir,
    instalasi_m.instalasi_nama,
    pembayaran_t.total_dibayar AS total_terbayar,
    ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
    pembayaran_t.total_nontunai,
    pembayaranpelayanan_t.pembayaran_id
   FROM (((((((((tandabuktibayar_t
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
  WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL))
UNION ALL
 SELECT 'UANG_MASUK'::text AS jenis,
    bayaruangmuka_t.bayaruangmuka_id AS transaksi_id,
    tandabuktibayar_t.tandabuktibayar_id,
    NULL::integer AS tandabuktikeluar_id,
    NULL::integer AS pembayaranpelayanan_id,
    bayaruangmuka_t.bayaruangmuka_id,
    NULL::integer AS penjualanresep_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.instalasi_id,
    bayaruangmuka_t.no_uangmuka AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    bayaruangmuka_t.tgl_uangmuka AS tgl_pembayaran,
    NULL::text AS tglpulang_pendaftaran,
    NULL::text AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_pendaftaran.nama_pegawai
            ELSE dok_admisi.nama_pegawai
        END AS dokter,
    pegawai_m.nama_pegawai AS kasir,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    0 AS total_tagihan,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS total_nontunai,
    NULL::integer AS pembayaran_id
   FROM ((((((((tandabuktibayar_t
     JOIN bayaruangmuka_t ON ((tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id)))
     JOIN pendaftaran_t ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
  WHERE ((tandabuktibayar_t.is_deleted = false) AND (tandabuktibayar_t.bayaruangmuka_id IS NOT NULL))
UNION ALL
 SELECT 'RETUR'::text AS jenis,
    tandabuktikeluar_t.returbayarpelayanan_id AS transaksi_id,
    NULL::integer AS tandabuktibayar_id,
    tandabuktikeluar_t.tandabuktikeluar_id,
    NULL::integer AS pembayaranpelayanan_id,
    NULL::integer AS bayaruangmuka_id,
    NULL::integer AS penjualanresep_id,
    NULL::integer AS pendaftaran_id,
    NULL::integer AS instalasi_id,
    returbayarpelayanan_t.no_returbayar AS no_kwitansi,
    tandabuktikeluar_t.no_buktikeluar AS no_bkm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    returbayarpelayanan_t.tgl_returpelayanan AS tgl_pembayaran,
    NULL::text AS tglpulang_pendaftaran,
    NULL::text AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_pendaftaran.nama_pegawai
            ELSE dok_admisi.nama_pegawai
        END AS dokter,
    NULL::character varying AS kasir,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    0 AS total_tagihan,
    (- returbayarpelayanan_t.total_biayaretur) AS total_tunai,
    (- returbayarpelayanan_t.total_nontunai) AS total_nontunai,
    NULL::integer AS pembayaran_id
   FROM ((((((((((returbayarpelayanan_t
     JOIN tandabuktikeluar_t ON ((returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id)))
     JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
  WHERE ((returbayarpelayanan_t.is_deleted = false) AND (tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.is_deleted = false))
UNION ALL
 SELECT 'PENJUALAN_RESEP_BEBAS'::text AS jenis,
    pembayaranpelayanan_t.penjualanresep_id AS transaksi_id,
    tandabuktibayar_t.tandabuktibayar_id,
    NULL::integer AS tandabuktikeluar_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    NULL::integer AS bayaruangmuka_id,
    pembayaranpelayanan_t.penjualanresep_id,
    NULL::integer AS pendaftaran_id,
    NULL::integer AS instalasi_id,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    penjualanresep_t.noresep AS no_pendaftaran,
    penjualanresep_t.tglresep AS tgl_pendaftaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    NULL::text AS tglpulang_pendaftaran,
    NULL::text AS tglpulang_ranap,
    NULL::character varying AS no_rekam_medik,
    penjualanresep_t.nama_pembeli AS nama_pasien,
    NULL::character varying AS dokter,
    pegawai_m.nama_pegawai AS kasir,
    NULL::character varying AS instalasi_nama,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
    pembayaran_t.total_nontunai,
    pembayaranpelayanan_t.pembayaran_id
   FROM (((((pembayaranpelayanan_t
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
     JOIN pegawai_m ON ((tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
  WHERE (penjualanresep_t.pendaftaran_id IS NULL);");

        $this->execute('ALTER TABLE "public"."cetakkwitansibkm_v" OWNER TO "postgres";');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"ins_noantrian_konfig\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE
    v_konfigantrian int;
    v_prefix varchar;
    v_noantrian varchar;
    v_last varchar;
    v_id int;
    v_tgl_antrian DATE;
    v_ruangan int;
    v_jenisantrian int;
    v_jadwaldokter_id int4;
    v_jadwalbukapoli_id int4;
    v_fungsiantrian_id int4;
    v_is_keteranganpasien BOOLEAN;
    
BEGIN
    v_konfigantrian := NEW.konfigantrian_id;
    v_tgl_antrian := NEW.tgl_antrian::DATE;
    v_ruangan := NEW.ruangan_id;
    v_jadwaldokter_id := NEW.jadwaldokter_id;
    v_jadwalbukapoli_id := NEW.jadwalbukapoli_id;
    v_jenisantrian := NEW.jenisantrian_id;
    v_fungsiantrian_id := NEw.fungsiantrian_id;
    
    SELECT is_keteranganpasien INTO v_is_keteranganpasien
    FROM konfigsystem_k 
    WHERE konfigsystem_id = 1;
    
    IF(v_is_keteranganpasien IS FALSE AND v_jenisantrian = 177)
    THEN
        v_prefix := '';
        
        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
        FROM antrian_t 
        WHERE jenisantrian_id = v_jenisantrian
        AND tgl_antrian::DATE = v_tgl_antrian;
    ELSE
            
        IF (COALESCE(v_konfigantrian,0)=0)
        THEN
        -- > jenis_antrian = 'PENUNJANG' <=============================================
            IF (v_jenisantrian = 179) 
            THEN
                SELECT 
                    kode_antrian,
                    konfigantrian_id
                 INTO
                    v_prefix,
                    v_konfigantrian
                FROM konfigantrian_m 
                WHERE jenisantrian_id = 179
                and ruangan_id = v_ruangan 
                limit 1;
                
                NEW.konfigantrian_id = v_konfigantrian;
                
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                WHERE jenisantrian_id = 179
                AND tgl_antrian::DATE = v_tgl_antrian
                AND ruangan_id=v_ruangan;
                        
        -- > jenis_antrian = 'FARMASI' <=============================================
            ELSE
                SELECT 
                    kode_antrian
                 INTO
                    v_prefix
                FROM konfigantrian_m 
                WHERE jenisantrian_id = v_jenisantrian
                and fungsiantrian_id = v_fungsiantrian_id 
                limit 1;
                    
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                WHERE jenisantrian_id = v_jenisantrian
                AND fungsiantrian_id = v_fungsiantrian_id
                AND tgl_antrian::DATE = v_tgl_antrian
                AND ruangan_id=v_ruangan;
            END IF;
            
        ELSE
            SELECT 
                kode_antrian
            INTO
                v_prefix
            FROM konfigantrian_m 
            WHERE konfigantrian_id = v_konfigantrian;


            IF(v_jenisantrian = 312)
            THEN
        -- > jenis_antrian = 'Poliklinik langsung' <=============================================   
                IF(COALESCE(v_jadwaldokter_id,0)=0 AND COALESCE(v_jadwalbukapoli_id,0)=0) 
                THEN
                    SELECT  
                        jadwalbukapoli_m.jadwalbukapoli_id INTO v_jadwalbukapoli_id 
                    FROM jadwalbukapoli_m
                    JOIN 
                    (
                        SELECT 
                            ruangan_id,
                            tgl_antrian,
                            CASE
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'monday' THEN '75' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'tuesday' THEN '76' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'wednesday' THEN '77' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'thursday' THEN '78' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'friday' THEN '79' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'saturday' THEN '80' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'sunday' THEN '81' 
                            END AS hari
                        FROM antrian_t 
                        WHERE konfigantrian_id = v_konfigantrian
                        AND ruangan_id=v_ruangan
                        and jadwalbukapoli_id is null 
                        and jadwaldokter_id is null
                        AND tgl_antrian::DATE = v_tgl_antrian
                    ) antrian_t ON jadwalbukapoli_m.ruangan_id = antrian_t.ruangan_id 
                     and jadwalbukapoli_m.hari::int = antrian_t.hari::int
                     and jadwalbukapoli_m.is_active=TRUE 
                     and jadwalbukapoli_m.is_deleted=false
                     and antrian_t.tgl_antrian::time BETWEEN jadwalbukapoli_m.jam_mulai and jadwalbukapoli_m.jam_tutup;
                     
                        
                    NEW.jadwalbukapoli_id = v_jadwalbukapoli_id;
                        
                    SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                    FROM antrian_t 
                    WHERE konfigantrian_id = v_konfigantrian
                    AND tgl_antrian::DATE = v_tgl_antrian
                    AND jadwalbukapoli_id = v_jadwalbukapoli_id
                    AND ruangan_id=v_ruangan;
                        
        --                  SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
        --                  FROM antrian_t 
        --                  WHERE konfigantrian_id = v_konfigantrian
        --                  AND ruangan_id=v_ruangan
        --                  AND tgl_antrian::DATE = v_tgl_antrian;
                ELSE
        -- > jenis_antrian = 'Poliklinik lewat antrian' <=============================================
                    IF(COALESCE(v_jadwaldokter_id,0)<>0)
                    THEN            
                        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                        FROM antrian_t 
                        WHERE konfigantrian_id = v_konfigantrian
                        AND tgl_antrian::DATE = v_tgl_antrian
                        AND jadwaldokter_id = v_jadwaldokter_id
                        AND ruangan_id=v_ruangan;
                    ELSE
                        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                        FROM antrian_t 
                        WHERE konfigantrian_id = v_konfigantrian
                        AND tgl_antrian::DATE = v_tgl_antrian
                        AND jadwalbukapoli_id = v_jadwalbukapoli_id
                        AND ruangan_id=v_ruangan;
                    END IF;
                END IF;
            
        -- > jenis_antrian = 'xxxxxx' <=============================================    
            ELSE
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                WHERE konfigantrian_id = v_konfigantrian
                AND tgl_antrian::DATE = v_tgl_antrian;
            END IF;
        END IF;
    END IF;
    
    v_noantrian = v_prefix || v_last;

    SELECT MAX(antrian_id)
    INTO v_id
    FROM antrian_t;

--     UPDATE antrian_t
--     SET no_antrian = v_noantrian
--     WHERE antrian_id = v_id;
    NEW.no_antrian = v_noantrian;
    NEW.is_keteranganpasien = v_is_keteranganpasien;
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."ins_noantrian_konfig"() OWNER TO "postgres";');

        $this->execute("
            CREATE VIEW \"public\".\"pendaftarandiagnosa_v\" AS  SELECT koreksidiagnosa_t.koreksidiagnosa_id AS kunjungandetail_id,
    pendaftaran_t.pendaftaran_id AS kunjungan_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rekammedik,
    pasien_m.nama_pasien,
    kelompokdiagnosa_m.kelompokdiagnosa_nama AS kelompok_diagnosa,
    diagnosa_m.diagnosa_kode,
    diagnosa_m.diagnosa_nama,
    koreksidiagnosa_t.additional_data,
    koreksidiagnosa_t.created_date,
    koreksidiagnosa_t.created_by,
    koreksidiagnosa_t.modified_count,
    koreksidiagnosa_t.last_modified_date,
    koreksidiagnosa_t.last_modified_by,
    koreksidiagnosa_t.is_deleted,
    koreksidiagnosa_t.is_active,
    koreksidiagnosa_t.deleted_date,
    koreksidiagnosa_t.deleted_by
   FROM ((((pendaftaran_t
     JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
     JOIN pasien_m ON ((koreksidiagnosa_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelompokdiagnosa_m ON ((koreksidiagnosa_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id)))
     JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)));");

        $this->execute('ALTER TABLE "public"."pendaftarandiagnosa_v" OWNER TO "postgres";');

        $this->execute("
            CREATE VIEW \"public\".\"pendaftaran_v\" AS  SELECT pendaftaran_t.pendaftaran_id AS kunjungan_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rekammedik,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir AS tgl_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.tgl_pendaftaran,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pulang_pedaftaran.tglpasienpulang
            ELSE pulang_admisi.tglpasienpulang
        END AS tgl_pulang,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_pendaftaran.instalasi_id
            ELSE ruangan_pendaftaran.instalasi_id
        END AS instalasi_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_pendaftaran.instalasi_nama
            ELSE instalasi_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_pendaftaran.ruangan_nama
            ELSE ruangan_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_pendaftaran.carabayar_nama
            ELSE carabayar_admisi.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_pendaftaran.penjamin_nama
            ELSE penjamin_admisi.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelas_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_pendaftaran.kelaspelayanan_nama
            ELSE kelas_admisi.kelaspelayanan_nama
        END AS kelas_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS dokter_kode,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_pendaftaran.nama_pegawai
            ELSE dok_admisi.nama_pegawai
        END AS dokter_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS kasus_penyakit,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN bpjs_pendaftaran.nosep
            ELSE bpjs_admisi.nosep
        END AS no_sep,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
            ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
        END AS status_kunjungan,
    pendaftaran_t.additional_data,
    pendaftaran_t.created_date,
    pendaftaran_t.created_by,
    pendaftaran_t.modified_count,
    pendaftaran_t.last_modified_date,
    pendaftaran_t.last_modified_by,
    pendaftaran_t.is_deleted,
    pendaftaran_t.is_active,
    pendaftaran_t.deleted_date,
    pendaftaran_t.deleted_by
   FROM (((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasienpulang_t pulang_pedaftaran ON ((pendaftaran_t.pasienpulang_id = pulang_pedaftaran.pasienpulang_id)))
     LEFT JOIN pasienpulang_t pulang_admisi ON ((pendaftaran_t.pasienpulang_id = pulang_admisi.pasienpulang_id)))
     LEFT JOIN ruangan_m ruangan_pendaftaran ON ((pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_admisi ON ((pasienadmisi_t.ruangan_id = ruangan_admisi.ruangan_id)))
     LEFT JOIN instalasi_m instalasi_pendaftaran ON ((ruangan_pendaftaran.instalasi_id = instalasi_pendaftaran.instalasi_id)))
     LEFT JOIN instalasi_m instalasi_admisi ON ((ruangan_admisi.instalasi_id = instalasi_admisi.instalasi_id)))
     LEFT JOIN carabayar_m carabayar_pendaftaran ON ((pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
     LEFT JOIN penjamin_m penjamin_pendaftaran ON ((pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
     LEFT JOIN kelaspelayanan_m kelas_pendaftaran ON ((pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_admisi ON ((pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN bpjs_t bpjs_pendaftaran ON ((pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id)))
     LEFT JOIN bpjs_t bpjs_admisi ON ((pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id)))
  WHERE ((pendaftaran_t.pasienpulang_id IS NOT NULL) OR (pasienadmisi_t.pasienpulang_id IS NOT NULL));");

        $this->execute('ALTER TABLE "public"."pendaftaran_v" OWNER TO "postgres";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpenerimaankasir_v\" AS  SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien AS info_pasien,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS rupiah,
    'TUNAI'::text AS transaksi,
    'PEMBAYARAN TAGIHAN'::text AS keterangan,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin
   FROM (((((((((pembayaranpelayanan_t
     JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
     JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
  WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) AND (pembayaran_t.total_tunai <> (0)::double precision))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien AS info_pasien,
    pembayaranmetode_t.total_dibayar AS rupiah,
    'NON TUNAI'::text AS transaksi,
    (('PEMBAYARAN TAGIHAN'::text || ' - '::text) || (pembayaranmetode_t.metode_bayar)::text) AS keterangan,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin
   FROM ((((((((((pembayaranpelayanan_t
     JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
     JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pembayaranmetode_t ON ((pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
  WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) AND (pembayaran_t.total_nontunai <> (0)::double precision))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien AS info_pasien,
    (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS rupiah,
    'PENJAMIN'::text AS transaksi,
    'PEMBAYARAN TAGIHAN'::text AS keterangan,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin
   FROM ((((((((((pembayaranpelayanan_t
     JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
     JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
  WHERE ((tandabuktibayar_t.is_deleted = false) AND (pembayaranpelayanan_t.penjualanresep_id IS NULL) AND (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) AND (tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL) AND (pembayaran_t.total_dijamin <> (0)::double precision))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    penjualanresep_t.noresep AS no_registrasi,
    penjualanresep_t.nama_pembeli AS info_pasien,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS rupiah,
    'TUNAI'::text AS transaksi,
    'PEMBAYARAN RESEP BEBAS'::text AS keterangan,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin
   FROM (((((((penjualanresep_t
     JOIN pembayaranpelayanan_t ON ((penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
     JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
  WHERE ((penjualanresep_t.pendaftaran_id IS NULL) AND (pembayaran_t.total_tunai <> (0)::double precision))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    penjualanresep_t.noresep AS no_registrasi,
    penjualanresep_t.nama_pembeli AS info_pasien,
    pembayaranmetode_t.total_dibayar AS rupiah,
    'NON TUNAI'::text AS transaksi,
    (('PEMBAYARAN RESEP BEBAS'::text || ' - '::text) || (pembayaranmetode_t.metode_bayar)::text) AS keterangan,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin
   FROM ((((((((penjualanresep_t
     JOIN pembayaranpelayanan_t ON ((penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
     JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pembayaranmetode_t ON ((pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id)))
  WHERE ((penjualanresep_t.pendaftaran_id IS NULL) AND (pembayaran_t.total_nontunai <> (0)::double precision))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    penjualanresep_t.noresep AS no_registrasi,
    penjualanresep_t.nama_pembeli AS info_pasien,
    (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS rupiah,
    'PENJAMIN'::text AS transaksi,
    'PEMBAYARAN RESEP BEBAS'::text AS keterangan,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin
   FROM ((((((((penjualanresep_t
     JOIN pembayaranpelayanan_t ON ((penjualanresep_t.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN tandabuktibayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id)))
     JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
  WHERE ((penjualanresep_t.pendaftaran_id IS NULL) AND (pembayaran_t.total_dijamin <> (0)::double precision))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    bayaruangmuka_t.no_uangmuka AS no_kwitansi,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien AS info_pasien,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN tandabuktibayar_t.uangditerima
            WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS rupiah,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN 'TUNAI'::text
            WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN 'NON TUNAI'::text
            ELSE 'PENJAMIN'::text
        END AS transaksi,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN 'UANG MASUK'::text
            WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN (('UANG MASUK'::text || ' - '::text) || (jenisnontunai_m.nama)::text)
            ELSE NULL::text
        END AS keterangan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_pendaftaran.carabayar_nama
            ELSE carabayar_admisi.carabayar_nama
        END AS cara_bayar,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_pendaftaran.penjamin_nama
            ELSE penjamin_admisi.penjamin_nama
        END AS penjamin
   FROM (((((((((((bayaruangmuka_t
     JOIN tandabuktibayar_t ON ((bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id)))
     JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN pendaftaran_t ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN carabayar_m carabayar_pendaftaran ON ((pendaftaran_t.carabayar_id = carabayar_pendaftaran.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
     LEFT JOIN penjamin_m penjamin_pendaftaran ON ((pendaftaran_t.penjamin_id = penjamin_pendaftaran.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
     LEFT JOIN jenisnontunai_m ON ((bayaruangmuka_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
  WHERE ((tandabuktibayar_t.is_deleted = false) AND (tandabuktibayar_t.bayaruangmuka_id IS NOT NULL))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char((closingkasir_t.tgl_closingkasir)::timestamp with time zone, 'YYYY-MM-DD'::text))::date AS tanggal,
    pembayaranpiutang_t.no_pembayaranpiutang AS no_kwitansi,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
        CASE
            WHEN (pemberianpiutang_t.penjualanresep_id IS NULL) THEN pasien_m.nama_pasien
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN penjualanresep_t.nama_pembeli
            ELSE NULL::character varying
        END AS info_pasien,
        CASE
            WHEN (pembayaranpiutang_t.metode_pembayaran = 27) THEN pembayaranpiutang_t.total_bayarpiutang
            WHEN (pembayaranpiutang_t.metode_pembayaran = 28) THEN pembayaranpiutang_t.total_bayarpiutang
            ELSE (0)::double precision
        END AS rupiah,
        CASE
            WHEN (pembayaranpiutang_t.metode_pembayaran = 27) THEN 'TUNAI'::text
            WHEN (pembayaranpiutang_t.metode_pembayaran = 28) THEN 'NON TUNAI'::text
            ELSE 'PENJAMIN'::text
        END AS transaksi,
        CASE
            WHEN (pembayaranpiutang_t.metode_pembayaran = 27) THEN 'PEMBAYARAN PIUTANG'::text
            WHEN (pembayaranpiutang_t.metode_pembayaran = 28) THEN (('PEMBAYARAN PIUTANG'::text || ' - '::text) || (jenisnontunai_m.nama)::text)
            ELSE NULL::text
        END AS keterangan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_m.carabayar_nama
            ELSE carabayar_resep.carabayar_nama
        END AS cara_bayar,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_m.penjamin_nama
            ELSE penjamin_resep.penjamin_nama
        END AS penjamin
   FROM ((((((((((((pembayaranpiutang_t
     JOIN tandabuktibayar_t ON ((pembayaranpiutang_t.pembayaranpiutang_id = tandabuktibayar_t.pembayaranpiutang_id)))
     JOIN closingkasir_t ON ((tandabuktibayar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN pemberianpiutang_t ON ((pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pendaftaran_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN penjualanresep_t ON ((pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_resep ON ((penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_resep ON ((penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id)))
     LEFT JOIN jenisnontunai_m ON ((pembayaranpiutang_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
  WHERE (tandabuktibayar_t.is_deleted = false)
UNION ALL
 SELECT
        CASE
            WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN (ruangan_penerimaan.ruangan_nama)::text
            WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN (ruangan_pengeluaran.ruangan_nama)::text
            ELSE NULL::text
        END AS kasir,
        CASE
            WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN (to_char(closing_bayar.tgl_closingkasir, 'YYYY-MM-DD'::text))::date
            WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN (to_char(closing_keluar.tgl_closingkasir, 'YYYY-MM-DD'::text))::date
            ELSE NULL::date
        END AS tanggal,
    pembayarantransaksi_t.no_transaksi AS no_kwitansi,
    NULL::character varying AS no_registrasi,
        CASE
            WHEN (pembayarantransaksi_t.tipe_transaksi = 700) THEN supplier_m.supplier_nama
            WHEN (pembayarantransaksi_t.tipe_transaksi = 701) THEN pegawai_m.nama_pegawai
            WHEN (pembayarantransaksi_t.tipe_transaksi = 702) THEN pasien_m.nama_pasien
            ELSE NULL::character varying
        END AS info_pasien,
        CASE
            WHEN ((pembayarantransaksi_t.metode_pembayaran = 27) AND (pembayarantransaksi_t.jenis_transaksi = 668)) THEN pembayarantransaksi_t.jumlah
            WHEN ((pembayarantransaksi_t.metode_pembayaran = 27) AND (pembayarantransaksi_t.jenis_transaksi = 669)) THEN (- pembayarantransaksi_t.jumlah)
            WHEN ((pembayarantransaksi_t.metode_pembayaran = 28) AND (pembayarantransaksi_t.jenis_transaksi = 668)) THEN pembayarantransaksi_t.jumlah
            WHEN ((pembayarantransaksi_t.metode_pembayaran = 28) AND (pembayarantransaksi_t.jenis_transaksi = 669)) THEN (- pembayarantransaksi_t.jumlah)
            ELSE (0)::double precision
        END AS rupiah,
        CASE
            WHEN (pembayarantransaksi_t.metode_pembayaran = 27) THEN 'TUNAI'::text
            WHEN (pembayarantransaksi_t.metode_pembayaran = 28) THEN 'NON TUNAI'::text
            ELSE NULL::text
        END AS transaksi,
        CASE
            WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN 'PENERIMAAN'::text
            WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN 'PENGELUARAN'::text
            ELSE NULL::text
        END AS keterangan,
    NULL::character varying AS cara_bayar,
    NULL::character varying AS penjamin
   FROM (((((((((pembayarantransaksi_t
     LEFT JOIN tandabuktibayar_t penerimaan ON ((pembayarantransaksi_t.pembayarantransaksi_id = penerimaan.penerimaanumum_id)))
     LEFT JOIN tandabuktikeluar_t pengeluaran ON ((pembayarantransaksi_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id)))
     LEFT JOIN closingkasir_t closing_bayar ON ((penerimaan.closingkasir_id = closing_bayar.closingkasir_id)))
     LEFT JOIN closingkasir_t closing_keluar ON ((pengeluaran.closingkasir_id = closing_keluar.closingkasir_id)))
     LEFT JOIN pasien_m ON ((pembayarantransaksi_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN supplier_m ON ((pembayarantransaksi_t.supplier_id = supplier_m.supplier_id)))
     LEFT JOIN ruangan_m ruangan_penerimaan ON ((penerimaan.ruangan_id = ruangan_penerimaan.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_pengeluaran ON ((pengeluaran.ruangan_id = ruangan_pengeluaran.ruangan_id)))
  WHERE ((penerimaan.closingkasir_id IS NOT NULL) OR (pengeluaran.closingkasir_id IS NOT NULL))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    returbayarpelayanan_t.no_returbayar AS no_kwitansi,
        CASE
            WHEN (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) THEN pendaftaran_t.no_pendaftaran
            WHEN (pembayaranpelayanan_t.penjualanresep_id IS NOT NULL) THEN penjualanresep_t.noresep
            ELSE NULL::character varying
        END AS no_registrasi,
        CASE
            WHEN (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
            WHEN (pembayaranpelayanan_t.penjualanresep_id IS NOT NULL) THEN penjualanresep_t.nama_pembeli
            ELSE NULL::character varying
        END AS info_pasien,
    (- returbayarpelayanan_t.total_biayaretur) AS rupiah,
    'TUNAI'::text AS transaksi,
    'RETUR PEMBAYARAN'::text AS keterangan,
    NULL::character varying AS cara_bayar,
    NULL::character varying AS penjamin
   FROM ((((((((returbayarpelayanan_t
     JOIN tandabuktikeluar_t ON ((returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id)))
     JOIN closingkasir_t ON ((tandabuktikeluar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
     LEFT JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN ruangan_m ON ((tandabuktikeluar_t.ruangan_id = ruangan_m.ruangan_id)))
  WHERE ((tandabuktikeluar_t.is_deleted = false) AND (returbayarpelayanan_t.total_biayaretur <> (0)::double precision))
UNION ALL
 SELECT ruangan_m.ruangan_nama AS kasir,
    (to_char(closingkasir_t.tgl_closingkasir, 'YYYY-MM-DD'::text))::date AS tanggal,
    returbayarpelayanan_t.no_returbayar AS no_kwitansi,
        CASE
            WHEN (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) THEN pendaftaran_t.no_pendaftaran
            WHEN (pembayaranpelayanan_t.penjualanresep_id IS NOT NULL) THEN penjualanresep_t.noresep
            ELSE NULL::character varying
        END AS no_registrasi,
        CASE
            WHEN (pembayaranpelayanan_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
            WHEN (pembayaranpelayanan_t.penjualanresep_id IS NOT NULL) THEN penjualanresep_t.nama_pembeli
            ELSE NULL::character varying
        END AS info_pasien,
    (- returbayarpelayanan_t.total_nontunai) AS rupiah,
    'NON TUNAI'::text AS transaksi,
    'RETUR PEMBAYARAN'::text AS keterangan,
    NULL::character varying AS cara_bayar,
    NULL::character varying AS penjamin
   FROM ((((((((returbayarpelayanan_t
     JOIN tandabuktikeluar_t ON ((returbayarpelayanan_t.returbayarpelayanan_id = tandabuktikeluar_t.returbayarpelayanan_id)))
     JOIN closingkasir_t ON ((tandabuktikeluar_t.closingkasir_id = closingkasir_t.closingkasir_id)))
     JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
     LEFT JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN ruangan_m ON ((tandabuktikeluar_t.ruangan_id = ruangan_m.ruangan_id)))
  WHERE ((tandabuktikeluar_t.is_deleted = false) AND (returbayarpelayanan_t.total_nontunai <> (0)::double precision));");
        
        $this->execute('ALTER TABLE "public"."laporanpenerimaankasir_v" OWNER TO "postgres";');
        
        $this->execute("
            CREATE VIEW \"public\".\"rinciankelompoktindakan_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    kelompoktindakan_m.kelompoktindakan_nama,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM (((pendaftaran_t
     JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
  WHERE (tindakanpelayanan_t.is_deleted = false)
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, kelompoktindakan_m.kelompoktindakan_nama
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    'obat'::text AS kelompoktindakan_nama,
    sum(obatalkespasien_t.hargajual_oa) AS total
   FROM (pendaftaran_t
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
  WHERE (obatalkespasien_t.is_deleted = false)
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, 'obat'::text;");

     $this->execute('ALTER TABLE "public"."rinciankelompoktindakan_v" OWNER TO "postgres";');    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200409_023504_migrate_20200408 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200409_023504_migrate_20200408 cannot be reverted.\n";

        return false;
    }
    */
}
