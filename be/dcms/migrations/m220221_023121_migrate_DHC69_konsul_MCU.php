<?php

use yii\db\Migration;

/**
 * Class m220221_023121_migrate_DHC69_konsul_MCU
 */
class m220221_023121_migrate_DHC69_konsul_MCU extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infokunjunganrj_v";
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
                lkp_jeniskelamin.lookup_name AS jenis_kelamin,
                lkp_status_periksa.lookup_name AS status_periksa1,
                ruangan_asal.asalpoliklinikkonsul_id AS ruanganasal_id,
                ruangan_asal.ruangan_nama AS ruanganasal_nama,
                pendaftaran_t.pasienpulang_id,
                pendaftaran_t.status_bayar,
                lkp_status_bayar.lookup_name AS status_bayar_nama,
                antrian_t.jenisantrian_id,
                ruangan_m.ruangan_nama AS poliklinik,
                lkp_status_periksa.lookup_name AS stat_ranap,
                bpjs_t.nosep,
                ruangan_m.lantai_id,
                carakeluar_m.carakeluar_nama,
                    CASE
                        WHEN (COALESCE(soaprj.soap, (0)::bigint) > 0) THEN true
                        ELSE false
                    END AS is_soap,
                carabayar_m.carabayar_warna,
                carabayar_m.carabayar_kode_warna,
                pasien_m.no_telepon_pasien,
                pendaftaran_t.status_bayar AS status_bayar_id
            FROM ((((((((((((((((((((((((((((((pendaftaran_t
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
             LEFT JOIN propinsi_m ON ((pasien_m.propinsi_id = propinsi_m.propinsi_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
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
             LEFT JOIN lookup_m lkp_jeniskelamin ON (((pasien_m.jeniskelamin)::integer = lkp_jeniskelamin.lookup_id)))
             LEFT JOIN lookup_m lkp_status_periksa ON (((pendaftaran_t.status_periksa)::integer = lkp_status_periksa.lookup_id)))
             LEFT JOIN lookup_m lkp_status_bayar ON ((pendaftaran_t.status_bayar = lkp_status_bayar.lookup_id)))
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
            konsulpoli_t.pegawai_id,
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
            lkp_jeniskelamin.lookup_name AS jenis_kelamin,
            lkp_status_periksa.lookup_name AS status_periksa1,
            konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
            ruanganasal_m.ruangan_nama AS ruanganasal_nama,
            konsulpoli_t.pasienpulang_id,
            pendaftaran_t.status_bayar,
            lkp_status_bayar.lookup_name AS status_bayar_nama,
            antrian_t.jenisantrian_id,
            ruangan_m.ruangan_nama AS poliklinik,
            lkp_status_periksa.lookup_name AS stat_ranap,
            bpjs_t.nosep,
            ruangan_m.lantai_id,
            carakeluar_m.carakeluar_nama,
                CASE
                    WHEN (COALESCE(soaprj.soap, (0)::bigint) > 0) THEN true
                    ELSE false
                END AS is_soap,
            carabayar_m.carabayar_warna,
            carabayar_m.carabayar_kode_warna,
            pasien_m.no_telepon_pasien,
            pendaftaran_t.status_bayar AS status_bayar_id
            FROM (((((((((((((((((((((((((((((((konsulpoli_t
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
             LEFT JOIN pasienpulang_t ON ((konsulpoli_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
             LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
             LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
             LEFT JOIN ( SELECT count(*) AS soap,
                    soaprj_t.pendaftaran_id
                   FROM soaprj_t
                  WHERE (soaprj_t.is_deleted IS FALSE)
                  GROUP BY soaprj_t.pendaftaran_id) soaprj ON ((pendaftaran_t.pendaftaran_id = soaprj.pendaftaran_id)))
             LEFT JOIN propinsi_m ON ((pasien_m.propinsi_id = propinsi_m.propinsi_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
             LEFT JOIN lookup_m lkp_jeniskelamin ON (((pasien_m.jeniskelamin)::integer = lkp_jeniskelamin.lookup_id)))
             LEFT JOIN lookup_m lkp_status_periksa ON (((konsulpoli_t.status_periksa)::integer = lkp_status_periksa.lookup_id)))
             LEFT JOIN lookup_m lkp_status_bayar ON ((pendaftaran_t.status_bayar = lkp_status_bayar.lookup_id)))
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
            COALESCE(pegawai_m.pegawai_id, pendaftaran_t.pegawai_id) AS pegawai_id,
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
            lkp_jeniskelamin.lookup_name AS jenis_kelamin,
            lkp_status_periksa.lookup_name AS status_periksa1,
            pendaftaran_t.ruangan_id AS ruanganasal_id,
            ruangan_m.ruangan_nama AS ruanganasal_nama,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.status_bayar,
            lkp_status_bayar.lookup_name AS status_bayar_nama,
            antrian_t.jenisantrian_id,
            ruangan_m.ruangan_nama AS poliklinik,
            lkp_status_periksa.lookup_name AS stat_ranap,
            NULL::character varying AS nosep,
            ruangan_m.lantai_id,
            carakeluar_m.carakeluar_nama,
            CASE
            WHEN (COALESCE(soaprj.soap, (0)::bigint) > 0) THEN true
            ELSE false
            END AS is_soap,
            carabayar_m.carabayar_warna,
            carabayar_m.carabayar_kode_warna,
            pasien_m.no_telepon_pasien,
            pendaftaran_t.status_bayar AS status_bayar_id
            FROM ((((((((((((((((((((((((((((((konsulpoli_t
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
            LEFT JOIN propinsi_m ON ((pasien_m.propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN lookup_m lkp_jeniskelamin ON (((pasien_m.jeniskelamin)::integer = lkp_jeniskelamin.lookup_id)))
            LEFT JOIN lookup_m lkp_status_periksa ON (((konsulpoli_t.status_periksa)::integer = lkp_status_periksa.lookup_id)))
            LEFT JOIN lookup_m lkp_status_bayar ON ((pendaftaran_t.status_bayar = lkp_status_bayar.lookup_id)))
            WHERE ((pendaftaran_t.instalasi_id = 21) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NOT NULL));
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220221_023121_migrate_DHC69_konsul_MCU cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220221_023121_migrate_DHC69_konsul_MCU cannot be reverted.\n";

        return false;
    }
    */
}
