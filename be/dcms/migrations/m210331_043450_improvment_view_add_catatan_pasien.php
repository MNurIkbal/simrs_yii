<?php

use yii\db\Migration;

/**
 * Class m210331_043450_improvment_view_add_catatan_pasien
 */
class m210331_043450_improvment_view_add_catatan_pasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS infokunjunganrj_v;
        ');

        $this->execute('
            CREATE VIEW "public"."infokunjunganrj_v" AS ( 
             SELECT \'PENDAFTARAN\'::text AS jenis,
                pasien_m.pasien_id,
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
                pasien_m.catatanpenting_pasien,
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
                ruangan_asal.asalpoliklinikkonsul_id AS ruanganasal_id,
                ruangan_asal.ruangan_nama AS ruanganasal_nama,
                pendaftaran_t.pasienpulang_id,
                pendaftaran_t.status_bayar,
                fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
                antrian_t.jenisantrian_id,
                ruangan_m.ruangan_nama AS poliklinik,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
                bpjs_t.nosep,
                ruangan_m.lantai_id,
                carakeluar_m.carakeluar_nama,
                    CASE
                        WHEN (COALESCE(soaprj.soap, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_soap,
                carabayar_m.carabayar_warna,
                carabayar_m.carabayar_kode_warna
               FROM (((((((((((((((((((((((pendaftaran_t
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
                 LEFT JOIN ( SELECT konsulpoli_t.pendaftaranbaru_id,
                        konsulpoli_t.asalpoliklinikkonsul_id,
                        poli_asal.ruangan_nama
                       FROM (konsulpoli_t
                         LEFT JOIN ruangan_m poli_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = poli_asal.ruangan_id)))) ruangan_asal ON ((pendaftaran_t.pendaftaran_id = ruangan_asal.pendaftaranbaru_id)))
                 LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
                 LEFT JOIN ( SELECT count(*) AS soap,
                        soaprj_t.pendaftaran_id
                       FROM soaprj_t
                      WHERE (soaprj_t.is_deleted IS FALSE)
                      GROUP BY soaprj_t.pendaftaran_id) soaprj ON ((pendaftaran_t.pendaftaran_id = soaprj.pendaftaran_id)))
              WHERE (pendaftaran_t.instalasi_id = 1)
            UNION
             SELECT \'KONSUL\'::text AS jenis,
                pasien_m.pasien_id,
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
                pasien_m.catatanpenting_pasien,
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
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa1,
                konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
                ruanganasal_m.ruangan_nama AS ruanganasal_nama,
                pendaftaran_t.pasienpulang_id,
                pendaftaran_t.status_bayar,
                fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
                antrian_t.jenisantrian_id,
                ruangan_m.ruangan_nama AS poliklinik,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
                bpjs_t.nosep,
                ruangan_m.lantai_id,
                carakeluar_m.carakeluar_nama,
                    CASE
                        WHEN (COALESCE(soaprj.soap, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_soap,
                carabayar_m.carabayar_warna,
                carabayar_m.carabayar_kode_warna
               FROM ((((((((((((((((((((((((konsulpoli_t
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
                 LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
                 LEFT JOIN ( SELECT count(*) AS soap,
                        soaprj_t.pendaftaran_id
                       FROM soaprj_t
                      WHERE (soaprj_t.is_deleted IS FALSE)
                      GROUP BY soaprj_t.pendaftaran_id) soaprj ON ((pendaftaran_t.pendaftaran_id = soaprj.pendaftaran_id)))
              WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NULL))
    ) UNION ALL
     SELECT \'MCU\'::text AS jenis,
        pasien_m.pasien_id,
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
        pasien_m.catatanpenting_pasien,
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
        konsulpoli_t.ruangan_id,
        ruangan_m.ruangan_nama,
        ruangan_m.ruangan_singkatan,
        ruangan_m.instalasi_id,
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
        pendaftaran_t.ruangan_id AS ruanganasal_id,
        ruangan_m.ruangan_nama AS ruanganasal_nama,
        pendaftaran_t.pasienpulang_id,
        pendaftaran_t.status_bayar,
        fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
        antrian_t.jenisantrian_id,
        ruangan_m.ruangan_nama AS poliklinik,
        fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
        NULL::character varying AS nosep,
        ruangan_m.lantai_id,
        carakeluar_m.carakeluar_nama,
            CASE
                WHEN (COALESCE(soaprj.soap, (0)::bigint) > 0) THEN true
                ELSE false
            END AS is_soap,
        carabayar_m.carabayar_warna,
        carabayar_m.carabayar_kode_warna
       FROM (((((((((((((((((((((((konsulpoli_t
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
         JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
         LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
         LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
         LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
         LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
         LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
         LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
         LEFT JOIN ( SELECT count(*) AS soap,
                soaprj_t.pendaftaran_id
               FROM soaprj_t
              WHERE (soaprj_t.is_deleted IS FALSE)
              GROUP BY soaprj_t.pendaftaran_id) soaprj ON ((pendaftaran_t.pendaftaran_id = soaprj.pendaftaran_id)))
      WHERE ((pendaftaran_t.instalasi_id = 21) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NOT NULL));
        ');

        $this->execute('
            DROP VIEW IF EXISTS infopasienri_v;
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienri_v" AS  SELECT pasienadmisi_t.pasienadmisi_id,
                pendaftaran_t.pendaftaran_id,
                pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
                pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
                pasienadmisi_t.pegawai_id AS dokter_admisi_id,
                pasienadmisi_t.carabayar_id,
                pasienadmisi_t.penjamin_id, 
                bpjs_t.klsrawat,
                pasienadmisi_t.kelaspelayanan_id,
                pasienadmisi_t.ruangan_id,
                pasienadmisi_t.tgl_admisi,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.bpjs_id,
                pendaftaran_t.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.namadepan,
                fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
                pasien_m.nama_pasien,
                pasien_m.alamat_pasien,
                pasien_m.jeniskelamin AS jeniskelamin_id,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
                dokter_admisi.nama_pegawai AS dokter_admisi,
                bpjs_t.klsrawat AS hak_kelas,
                kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                ruangan_m.ruangan_nama,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.no_tempattidur,
                pasienadmisi_t.tgl_pulang,
                rencanapulang_t.rencana_pulang,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                pasienadmisi_t.status_ranap,
                fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
                kamarruangan_m.jeniskasuspenyakit_id,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                pendaftaran_t.golonganumur_id,
                pasienadmisi_t.tgl_pindahkamar,
                asesmenmedis_t.r_alergiobat,
                asesmenmedis_t.is_hamil,
                asesmenmedis_t.sumber_info,
                asesmenmedis_t.sumber_hubungan,
                asesmenmedis_t.luas_permukaantubuh,
                asesmenmedis_t.tinggi_badan,
                asesmenmedis_t.berat_badan,
                asesmenmedis_t.r_penyakitkeluarga,
                asesmenmedis_t.r_imunisasi,
                asesmenmedis_t.diagnosa_id,
                asesmenmedis_t.diagnosa_id AS diagnosa_nama,
                pasienadmisi_t.kamarruangan_id,
                pasienadmisi_t.kamartempattidur_id,
                pasien_m.photopasien,
                pendaftaran_t.caramasuk_id,
                caramasuk_m.caramasuk_nama,
                asesmenmedis_t.discharge_plan,
                asesmenawal_t.obatan_rumah,
                asesmenawal_t.obat_darirumah,
                    CASE
                        WHEN (( SELECT count(*) AS count
                           FROM cppt_t x
                          WHERE ((x.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (x.is_instruksi_pulang = true))) > 0) THEN true
                        ELSE false
                    END AS instruksi_pulang,
                kelaspelayanan_m.kelaspelayanan_nama,
                pasienadmisi_t.pasienpulang_id,
                pasien_m.jeniskelamin,
                pekerjaan_m.pekerjaan_nama,
                pendidikan_m.pendidikan_nama,
                asesmenmedis_t.r_peskk,
                asesmenmedis_t.is_merokok,
                asesmenmedis_t.jml_rokok,
                COALESCE(tagihan.sub_total, (0)::double precision) AS tagihan_rs,
                COALESCE(monitorsetdiagnosa.total, (0)::double precision) AS tarif_inacbg,
                carabayar_m.groupcarabayar_id AS group_carabayar,
                    CASE
                        WHEN (monitorsetdiagnosa.diag_utama_id IS NULL) THEN \'BELUM DIMONITOR\'::text
                        ELSE \'SUDAH DIMONITOR\'::text
                    END AS status_monitor,
                bpjs_t.nosep,
                pasienadmisi_t.is_aps,
                kelaspelayanan_m.urutankelas,
                kelaspelayanan_m.bpjs_kelas,
                pendaftaran_t.keterangan_pendaftaran,
                pasienadmisi_t.asuransipasien_id,
                pendaftaran_t.is_stopakomodasi,
                pendaftaran_t.tgl_stopakomodasi,
                    CASE
                        WHEN (implementasi.sisa = 0) THEN true
                        WHEN (implementasi.sisa <> 0) THEN false
                        ELSE false
                    END AS status_implementasi,
                pasienadmisi_t.is_pasientitipan,
                    CASE
                        WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN pasienadmisi_t.kelas_ditagihkan_id
                        ELSE pindah_kamar.kelas_ditagihkan_id
                    END AS kelas_ditagihkan_id,
                    CASE
                        WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN kelas_ditagihkan.kelaspelayanan_nama
                        ELSE pindah_kamar.kelas_ditagihkan
                    END AS kelas_ditagihkan_nama,
                pasienadmisi_t.kamar_titipan_id,
                kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
                pasienadmisi_t.ruangan_titipan_id,
                ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
                pasienadmisi_t.is_stoptitipan,
                pindah_kamar.pindahkamar_id,
                    CASE
                        WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
                        WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
                        WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
                        WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
                        ELSE false
                    END AS is_stoppasientitipan,
                stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
                        ELSE false
                    END AS is_bayi,
                pendaftaran_t.status_bayar,
                carabayar_m.carabayar_warna,
                carabayar_m.carabayar_kode_warna,
                pendaftaran_t.penanggungbiaya_id,
                pasienadmisi_t.limit_tagihan,
                pasien_m.catatanpenting_pasien
               FROM (((((((((((((((((((((((((((pendaftaran_t
                 JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
                 LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
                 LEFT JOIN pegawai_m dokter_pendaftaran ON ((pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id)))
                 JOIN pegawai_m dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
                 JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id)))
                 JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                 JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (asesmenmedis_t.is_deleted = false))))
                 LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
                 LEFT JOIN asesmenawal_t ON ((pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id)))
                 LEFT JOIN rencanapulang_t ON (((pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id) AND (rencanapulang_t.is_deleted = false))))
                 LEFT JOIN ( SELECT x.pendaftaran_id,
                        x.pasienadmisi_id,
                        sum(x.sub_total) AS sub_total
                       FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                                pendaftaran_t_1.pasienadmisi_id,
                                sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                               FROM (pendaftaran_t pendaftaran_t_1
                                 JOIN tindakanpelayanan_t ON (((pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
                              GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                            UNION ALL
                             SELECT pendaftaran_t_1.pendaftaran_id,
                                pendaftaran_t_1.pasienadmisi_id,
                                sum(obatalkespasien_t.hargajual_oa) AS sub_total
                               FROM (pendaftaran_t pendaftaran_t_1
                                 JOIN obatalkespasien_t ON (((pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
                              GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
                      GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON (((pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id))))
                 LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
                        monitorsetdiagnosa_t.pendaftaran_id,
                        monitorsetdiagnosa_t.pasienadmisi_id,
                        monitorsetdiagnosa_t.diag_utama_id,
                        diagnosa_m.diagnosa_kode,
                        diagnosa_m.diagnosa_nama,
                        monitorsetdiagnosa_t.diag_penyerta,
                        monitorsetdiagnosa_t.diag_tindakan,
                        monitorsetdiagnosa_t.total,
                        monitorsetdiagnosa_t.is_dokter
                       FROM (monitorsetdiagnosa_t
                         JOIN diagnosa_m ON ((monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id)))
                      WHERE (monitorsetdiagnosa_t.is_deleted = false)) monitorsetdiagnosa ON ((pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id)))
                 LEFT JOIN ( SELECT cppt_t.pendaftaran_id,
                        (count(instruksitindakan_t.status_implementasi) + count(instruksitindakanbmhp_t.status_implementasi)) AS sisa
                       FROM (((cppt_t
                         LEFT JOIN instruksi_t ON (((cppt_t.cppt_id = instruksi_t.cppt_id) AND (instruksi_t.is_deleted = false))))
                         LEFT JOIN instruksitindakan_t ON (((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id) AND (instruksitindakan_t.is_deleted = false) AND ((instruksitindakan_t.status_implementasi)::text <> \'455\'::text))))
                         LEFT JOIN instruksitindakanbmhp_t ON (((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id) AND (instruksitindakanbmhp_t.is_deleted = false) AND ((instruksitindakanbmhp_t.status_implementasi)::text <> \'455\'::text))))
                      WHERE (cppt_t.is_deleted = false)
                      GROUP BY cppt_t.pendaftaran_id) implementasi ON ((pendaftaran_t.pendaftaran_id = implementasi.pendaftaran_id)))
                 LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
                 LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
                 LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
                 LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                        pindahkamar_t.pasienadmisi_id,
                        pindahkamar_t.kelas_ditagihkan_id,
                        kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
                        pindahkamar_t.is_stoptitipan
                       FROM ((pindahkamar_t
                         JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                                pk.pasienadmisi_id
                               FROM pindahkamar_t pk
                              GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
                         LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON ((pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id)))
                      WHERE ((pindahkamar_t.is_deleted = false) AND (pindahkamar_t.is_pasientitipan = true))) pindah_kamar ON ((pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id)))
                 LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                        pindahkamar_t.pasienadmisi_id,
                        pindahkamar_t.is_pasientitipan,
                        pindahkamar_t.is_stoptitipan
                       FROM (pindahkamar_t
                         JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                                pk.pasienadmisi_id
                               FROM pindahkamar_t pk
                              GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
                      WHERE (pindahkamar_t.is_deleted = false)) stop_titipan ON ((pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id)))
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
              WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false));

        ');

        $this->execute('
            DROP VIEW IF EXISTS infopasienrd_v;
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienrd_v" AS  SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.no_rekam_medik,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                pendaftaran_t.pegawai_id AS dokter_jaga_id,
                dok_jaga.nama_pegawai AS dokter_jaga,
                ( SELECT dokpj.dokterbaru_id
                       FROM gantidokterpj_t dokpj
                      WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
                     LIMIT 1) AS dokter_id,
                ( SELECT dokter.nama_pegawai
                       FROM (gantidokterpj_t dokpj
                         JOIN ( SELECT pegawai_m.pegawai_id,
                                pegawai_m.nama_pegawai
                               FROM (pegawai_m
                                 JOIN ruanganpegawai_mp ON ((pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id)))
                              WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_active = true) AND (pegawai_m.is_deleted = false))) dokter ON ((dokpj.dokterbaru_id = dokter.pegawai_id)))
                      WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
                     LIMIT 1) AS dokter,
                ( SELECT dokpj.jenis_dokter
                       FROM gantidokterpj_t dokpj
                      WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
                     LIMIT 1) AS jenis_dokter_id,
                ( SELECT fgetnamalookup(gantidokterpj_t.jenis_dokter) AS lookup_name
                       FROM gantidokterpj_t dokpj
                      WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
                     LIMIT 1) AS jenis_dokter,
                pendaftaran_t.status_periksa AS status_periksa_id,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.ruangan_id,
                ruangan_m.ruangan_nama,
                pasien_m.jeniskelamin,
                pendaftaran_t.*::pendaftaran_t AS pendaftaran_t,
                pendaftaran_t.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.jeniskasuspenyakit_id,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pendaftaran_t.carabayar_id,
                carabayar_m.carabayar_nama,
                pendaftaran_t.umur,
                pasien_m.tanggal_lahir,
                pasien_m.photopasien,
                bpjs_t.nosep,
                carabayar_m.carabayar_warna, 
                carabayar_m.carabayar_kode_warna,
                pendaftaran_t.pasienpulang_id,
                pekerjaan_m.pekerjaan_nama,
                pasien_m.catatanpenting_pasien
               FROM ((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pegawai_m dok_jaga ON ((pendaftaran_t.pegawai_id = dok_jaga.pegawai_id)))
                 LEFT JOIN gantidokterpj_t ON ((pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id)))
                 LEFT JOIN pegawai_m dok_dpjp ON ((gantidokterpj_t.dokterbaru_id = dok_dpjp.pegawai_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                 LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
                 LEFT JOIN pekerjaan_m ON ((COALESCE(asesmenperawatrd_t.pekerjaan_id, pasien_m.pekerjaan_id) = pekerjaan_m.pekerjaan_id)))
              WHERE (pendaftaran_t.instalasi_id = 2);
        ');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210331_043450_improvment_view_add_catatan_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210331_043450_improvment_view_add_catatan_pasien cannot be reverted.\n";

        return false;
    }
    */
}
