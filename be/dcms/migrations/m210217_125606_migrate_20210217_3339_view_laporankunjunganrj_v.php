<?php

use yii\db\Migration;

/**
 * Class m210217_125606_migrate_20210217_3339_view_laporankunjunganrj_v
 */
class m210217_125606_migrate_20210217_3339_view_laporankunjunganrj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporankunjunganrj_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporankunjunganrj_v\" AS
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
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            NULL::integer AS ruanganasal_id,
            NULL::character varying AS ruanganasal_nama,
            fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas,
            fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
            fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan,
            fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
            fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan,
            fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan,
            gelarbelakang.gelarbelakang_nama,
            fgetnamalookup((pasien_m.rhesus)::integer) AS rhesus,
            kondisikeluar_m.kondisikeluar_nama,
            carakeluar_m.carakeluar_nama,
            fgetnamalookup((pasien_m.agama)::integer) AS agama,
            pasienbatalperiksa_t.alasan_batal,
            pendaftaran_t.bpjs_id,
            bpjs_t.nosep,
            pendaftaran_t.status_periksa AS status_periksa_id,
            pendaftaran_t.status_bayar AS status_bayar_id,
            pendaftaran_t.asuransipasien_id,
            pasienpulang_t.carakeluar_id,
            pendaftaran_t.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pendaftaran_t.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            pendaftaran_t.penanggungbiaya_id,
            carabayar_m.carabayar_kode_warna,
            CASE
            WHEN (antrian_poli.jenisantrian_id = 312) THEN (antrian_poli.no_antrian)::text
            ELSE '-'::text
            END AS no_antrian_poli,
            pendaftaran_t.limit_tagihan
            FROM (((((((((((((((((((((((((((((pendaftaran_t
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
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN loginpemakai_k petugas ON ((pendaftaran_t.last_modified_by = petugas.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pemakai ON ((petugas.pegawai_id = petugas_pemakai.pegawai_id)))
            LEFT JOIN loginpemakai_k pembuat ON ((pendaftaran_t.created_by = pembuat.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
            LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
            LEFT JOIN antrian_t antrian_poli ON (((pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id) AND (antrian_poli.jenisantrian_id = 312))))
            LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN gelarbelakang_m gelarbelakang ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang.gelarbelakang_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
            LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted IS FALSE))))
            WHERE ((pendaftaran_t.instalasi_id = 1) AND ((pendaftaran_t.status_periksa)::text <> '402'::text))
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
            fgetnamalookup((pasien_m.agama)::integer) AS jenis_kelamin,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
            ruanganasal_m.ruangan_nama AS ruanganasal_nama,
            fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas,
            fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
            fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan,
            fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
            fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan,
            fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan,
            gelarbelakang.gelarbelakang_nama,
            fgetnamalookup((pasien_m.rhesus)::integer) AS rhesus,
            kondisikeluar_m.kondisikeluar_nama,
            carakeluar_m.carakeluar_nama,
            fgetnamalookup((pasien_m.agama)::integer) AS agama,
            pasienbatalperiksa_t.alasan_batal,
            pendaftaran_t.bpjs_id,
            bpjs_t.nosep,
            pendaftaran_t.status_periksa AS status_periksa_id,
            pendaftaran_t.status_bayar AS status_bayar_id,
            pendaftaran_t.asuransipasien_id,
            pasienpulang_t.carakeluar_id,
            pendaftaran_t.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pendaftaran_t.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            pendaftaran_t.penanggungbiaya_id,
            carabayar_m.carabayar_kode_warna,
            CASE
            WHEN (antrian_poli.jenisantrian_id = 312) THEN (antrian_poli.no_antrian)::text
            ELSE '-'::text
            END AS no_antrian_poli,
            pendaftaran_t.limit_tagihan
            FROM (((((((((((((((((((((((((((((((pendaftaran_t
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
            JOIN konsulpoli_t ON ((pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id)))
            LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN loginpemakai_k petugas ON ((pendaftaran_t.last_modified_by = petugas.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pemakai ON ((petugas.pegawai_id = petugas_pemakai.pegawai_id)))
            LEFT JOIN loginpemakai_k pembuat ON ((pendaftaran_t.created_by = pembuat.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
            JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ruangan_m ruanganasal_m ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
            LEFT JOIN antrian_t antrian_poli ON (((pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id) AND (antrian_poli.jenisantrian_id = 312))))
            LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN gelarbelakang_m gelarbelakang ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang.gelarbelakang_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
            LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted IS FALSE))))
            WHERE ((pendaftaran_t.instalasi_id = 1) AND ((pendaftaran_t.status_periksa)::text <> '402'::text))
            ;");
            $this->execute('
                ALTER TABLE public.laporankunjunganrj_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210217_125606_migrate_20210217_3339_view_laporankunjunganrj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210217_125606_migrate_20210217_3339_view_laporankunjunganrj_v cannot be reverted.\n";

        return false;
    }
    */
}
