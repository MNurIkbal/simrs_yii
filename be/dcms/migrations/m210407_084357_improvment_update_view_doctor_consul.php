<?php

use yii\db\Migration;

/**
 * Class m210407_084357_improvment_update_view_doctor_consul
 */
class m210407_084357_improvment_update_view_doctor_consul extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konsulpoli_t ADD IF NOT EXISTS pasienpulang_id int4 ;
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS konsulpoli_id int4 ;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienrs_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienrs_v" AS  SELECT \'RJ\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                    CASE pasien_m.jeniskelamin
                        WHEN \'15\'::text THEN \'L\'::text
                        ELSE \'P\'::text
                    END AS jk,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
                kelaspelayanan_m.kelaspelayanan_nama,
                \'-\'::text AS kelas_tagihan,
                    CASE
                        WHEN (ruangan_asal.pendaftaranbaru_id IS NULL) THEN false
                        ELSE true
                    END AS is_konsul,
                false AS is_pasientitipan,
                carabayar_m.carabayar_kode_warna,
                carabayar_m.carabayar_warna,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.kelaspelayanan_id,
                pendaftaran_t.pegawai_id,
                (pendaftaran_t.status_periksa)::integer AS status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
                        ELSE false
                    END AS is_bayi,
                false AS is_stoppasientitipan,
                    CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_pulang,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_lunas,
                pendaftaran_t.is_stopakomodasi,
                NULL::integer AS pasienmasukpenunjang_id,
                pendaftaran_t.keterangan_pendaftaran,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                NULL::text AS kamarruangan_nokamar,
                NULL::text AS no_tempattidur,
                NULL::text AS kettempattidur_nama,
                NULL::text AS status_kamar,
                NULL::timestamp without time zone AS tgl_pindahkamar,
                NULL::timestamp without time zone AS rencana_pulang,
                antrian_t.no_antrian,
                    CASE
                        WHEN (soap_rj.soap_id IS NULL) THEN false
                        ELSE true
                    END AS is_isisoap,
                peg_create.pegawai_id AS peg_create_id,
                peg_create.nama_pegawai AS peg_create_nama,
                NULL::integer AS konsulpoli_id
               FROM (((((((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN ( SELECT konsulpoli_t.pendaftaranbaru_id,
                        konsulpoli_t.asalpoliklinikkonsul_id,
                        poli_asal.ruangan_nama
                       FROM (konsulpoli_t
                         LEFT JOIN ruangan_m poli_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = poli_asal.ruangan_id)))) ruangan_asal ON ((pendaftaran_t.pendaftaran_id = ruangan_asal.pendaftaranbaru_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
                 LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
                 LEFT JOIN ( SELECT soaprj_t.pendaftaran_id AS soap_id
                       FROM soaprj_t
                      WHERE (soaprj_t.is_deleted = false)
                      GROUP BY soaprj_t.pendaftaran_id) soap_rj ON ((pendaftaran_t.pendaftaran_id = soap_rj.soap_id)))
                 LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
              WHERE (pendaftaran_t.instalasi_id = 1)
            UNION ALL
             SELECT \'RJ\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                    CASE pasien_m.jeniskelamin
                        WHEN \'15\'::text THEN \'L\'::text
                        ELSE \'P\'::text
                    END AS jk,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
                kelaspelayanan_m.kelaspelayanan_nama,
                \'-\'::text AS kelas_tagihan,
                true AS is_konsul,
                false AS is_pasientitipan,
                carabayar_m.carabayar_kode_warna,
                carabayar_m.carabayar_warna,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                ruangan_m.ruangan_id,
                pendaftaran_t.kelaspelayanan_id,
                konsulpoli_t.pegawai_id,
                (konsulpoli_t.status_periksa)::integer AS status_periksa,
                fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status_periksa_nama,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
                        ELSE false
                    END AS is_bayi,
                false AS is_stoppasientitipan,
                    CASE COALESCE(konsulpoli_t.pasienpulang_id, 0)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_pulang,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_lunas,
                pendaftaran_t.is_stopakomodasi,
                NULL::integer AS pasienmasukpenunjang_id, 
                pendaftaran_t.keterangan_pendaftaran,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                NULL::text AS kamarruangan_nokamar,
                NULL::text AS no_tempattidur,
                NULL::text AS kettempattidur_nama,
                NULL::text AS status_kamar,
                NULL::timestamp without time zone AS tgl_pindahkamar,
                NULL::timestamp without time zone AS rencana_pulang,
                antrian_t.no_antrian,
                    CASE
                        WHEN (soap_rj.soap_id IS NULL) THEN false
                        ELSE true
                    END AS is_isisoap,
                peg_create.pegawai_id AS peg_create_id,
                peg_create.nama_pegawai AS peg_create_nama,
                konsulpoli_t.konsulpoli_id
               FROM ((((((((((((((((((((((((((((konsulpoli_t
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
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
                 LEFT JOIN ( SELECT soaprj_t.pendaftaran_id AS soap_id
                       FROM soaprj_t
                      WHERE (soaprj_t.is_deleted = false)
                      GROUP BY soaprj_t.pendaftaran_id) soap_rj ON ((pendaftaran_t.pendaftaran_id = soap_rj.soap_id)))
                 LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
              WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NULL))
            UNION ALL
             SELECT \'RD\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                    CASE pasien_m.jeniskelamin
                        WHEN \'15\'::text THEN \'L\'::text
                        ELSE \'P\'::text
                    END AS jk,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
                kelaspelayanan_m.kelaspelayanan_nama,
                \'-\'::text AS kelas_tagihan,
                    CASE
                        WHEN (ruangan_asal.pendaftaranbaru_id IS NULL) THEN false
                        ELSE true
                    END AS is_konsul,
                false AS is_pasientitipan,
                carabayar_m.carabayar_kode_warna,
                carabayar_m.carabayar_warna,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.kelaspelayanan_id,
                pendaftaran_t.pegawai_id,
                (pendaftaran_t.status_periksa)::integer AS status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
                        ELSE false
                    END AS is_bayi,
                false AS is_stoppasientitipan,
                    CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_pulang,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_lunas,
                pendaftaran_t.is_stopakomodasi,
                NULL::integer AS pasienmasukpenunjang_id,
                pendaftaran_t.keterangan_pendaftaran,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                NULL::text AS kamarruangan_nokamar,
                NULL::text AS no_tempattidur,
                NULL::text AS kettempattidur_nama,
                NULL::text AS status_kamar,
                NULL::timestamp without time zone AS tgl_pindahkamar,
                NULL::timestamp without time zone AS rencana_pulang,
                antrian_t.no_antrian,
                    CASE
                        WHEN (soap_rd.soap_id IS NULL) THEN false
                        ELSE true
                    END AS is_isisoap,
                peg_create.pegawai_id AS peg_create_id,
                peg_create.nama_pegawai AS peg_create_nama,
                NULL::integer AS konsulpoli_id
               FROM (((((((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN ( SELECT konsulpoli_t.pendaftaranbaru_id,
                        konsulpoli_t.asalpoliklinikkonsul_id,
                        poli_asal.ruangan_nama
                       FROM (konsulpoli_t
                         LEFT JOIN ruangan_m poli_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = poli_asal.ruangan_id)))) ruangan_asal ON ((pendaftaran_t.pendaftaran_id = ruangan_asal.pendaftaranbaru_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
                 LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
                 LEFT JOIN ( SELECT cppt_t.pendaftaran_id AS soap_id
                       FROM cppt_t
                      WHERE (cppt_t.is_deleted = false)
                      GROUP BY cppt_t.pendaftaran_id) soap_rd ON ((pendaftaran_t.pendaftaran_id = soap_rd.soap_id)))
                 LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
              WHERE (pendaftaran_t.instalasi_id = 2)
            UNION ALL
             SELECT \'RI\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                pasienadmisi_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                    CASE pasien_m.jeniskelamin
                        WHEN \'15\'::text THEN \'L\'::text
                        ELSE \'P\'::text
                    END AS jk,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
                kelaspelayanan_m.kelaspelayanan_nama,
                \'-\'::text AS kelas_tagihan,
                false AS is_konsul,
                pasienadmisi_t.is_pasientitipan,
                carabayar_m.carabayar_kode_warna,
                carabayar_m.carabayar_warna,
                pasienadmisi_t.carabayar_id,
                pasienadmisi_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pasienadmisi_t.ruangan_id,
                pasienadmisi_t.kelaspelayanan_id,
                COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) AS pegawai_id,
                pasienadmisi_t.status_ranap AS status_periksa,
                fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa_nama,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
                        ELSE false
                    END AS is_bayi,
                    CASE
                        WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
                        WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
                        WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
                        WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
                        ELSE false
                    END AS is_stoppasientitipan,
                    CASE COALESCE(pasienadmisi_t.pasienpulang_id, 0)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_pulang,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_lunas,
                pendaftaran_t.is_stopakomodasi,
                NULL::integer AS pasienmasukpenunjang_id,
                pendaftaran_t.keterangan_pendaftaran,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.no_tempattidur,
                kettempattidur_m.kettempattidur_nama,
                fgetnamalookup((kamarruangan_m.keterangan_kamar)::integer) AS status_kamar,
                pasienadmisi_t.tgl_pindahkamar,
                rencanapulang_t.rencana_pulang,
                antrian_t.no_antrian,
                    CASE
                        WHEN (soap_ri.soap_id IS NULL) THEN false
                        ELSE true
                    END AS is_isisoap,
                peg_create.pegawai_id AS peg_create_id,
                peg_create.nama_pegawai AS peg_create_nama,
                NULL::integer AS konsulpoli_id
               FROM ((((((((((((((((((((((((((((((((((((((pendaftaran_t
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                 LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
                 LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
                 JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 LEFT JOIN caramasuk_m ON ((pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id)))
                 JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN loginpemakai_k petugas ON ((pendaftaran_t.last_modified_by = petugas.loginpemakai_id)))
                 LEFT JOIN pegawai_m petugas_pemakai ON ((petugas.pegawai_id = petugas_pemakai.pegawai_id)))
                 LEFT JOIN loginpemakai_k pembuat ON ((pendaftaran_t.created_by = pembuat.loginpemakai_id)))
                 LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
                 LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
                 LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
                 LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
                 LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
                 JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                 LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
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
                 LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienadmisi_id)))
                 LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
                 LEFT JOIN kettempattidur_m ON ((kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id)))
                 LEFT JOIN rencanapulang_t ON (((pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id) AND (rencanapulang_t.is_deleted = false))))
                 LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
                 LEFT JOIN ( SELECT cppt_t.pasienadmisi_id AS soap_id
                       FROM cppt_t
                      WHERE (cppt_t.is_deleted = false)
                      GROUP BY cppt_t.pasienadmisi_id) soap_ri ON ((pendaftaran_t.pasienadmisi_id = soap_ri.soap_id)))
                 LEFT JOIN loginpemakai_k ON ((pasienadmisi_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
              WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false))
            UNION ALL
             SELECT \'MCU\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                    CASE pasien_m.jeniskelamin
                        WHEN \'15\'::text THEN \'L\'::text
                        ELSE \'P\'::text
                    END AS jk,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
                kelaspelayanan_m.kelaspelayanan_nama,
                \'-\'::text AS kelas_tagihan,
                true AS is_konsul,
                false AS is_pasientitipan,
                carabayar_m.carabayar_kode_warna,
                carabayar_m.carabayar_warna,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                ruangan_m.ruangan_id,
                pendaftaran_t.kelaspelayanan_id,
                pendaftaran_t.pegawai_id,
                (pendaftaran_t.status_periksa)::integer AS status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
                        ELSE false
                    END AS is_bayi,
                false AS is_stoppasientitipan,
                    CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_pulang,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_lunas,
                pendaftaran_t.is_stopakomodasi,
                NULL::integer AS pasienmasukpenunjang_id,
                pendaftaran_t.keterangan_pendaftaran,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                NULL::text AS kamarruangan_nokamar,
                NULL::text AS no_tempattidur,
                NULL::text AS kettempattidur_nama,
                NULL::text AS status_kamar,
                NULL::timestamp without time zone AS tgl_pindahkamar,
                NULL::timestamp without time zone AS rencana_pulang,
                antrian_t.no_antrian,
                false AS is_isisoap,
                peg_create.pegawai_id AS peg_create_id,
                peg_create.nama_pegawai AS peg_create_nama,
                konsulpoli_t.konsulpoli_id
               FROM ((((((((((((((((pendaftaran_t
                 JOIN konsulpoli_t ON ((pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
                 JOIN ruangan_m ON ((COALESCE(konsulpoli_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
                 LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
                 LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
              WHERE ((pendaftaran_t.instalasi_id = 21) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NOT NULL))
            UNION ALL
             SELECT \'OT\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                    CASE pasien_m.jeniskelamin
                        WHEN \'15\'::text THEN \'L\'::text
                        ELSE \'P\'::text
                    END AS jk,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
                kelaspelayanan_m.kelaspelayanan_nama,
                \'-\'::text AS kelas_tagihan,
                false AS is_konsul,
                false AS is_pasientitipan,
                carabayar_m.carabayar_kode_warna,
                carabayar_m.carabayar_warna,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pendaftaran_t.ruangan_id,
                pendaftaran_t.kelaspelayanan_id,
                pendaftaran_t.pegawai_id,
                (pasienmasukpenunjang_t.status_periksa)::integer AS status_periksa,
                fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status_periksa_nama,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
                        ELSE false
                    END AS is_bayi,
                false AS is_stoppasientitipan,
                    CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_pulang,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_lunas,
                pendaftaran_t.is_stopakomodasi,
                pasienkirimkeunitlain_t.pasienmasukpenunjang_id,
                pendaftaran_t.keterangan_pendaftaran,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                NULL::text AS kamarruangan_nokamar,
                NULL::text AS no_tempattidur,
                NULL::text AS kettempattidur_nama,
                NULL::text AS status_kamar,
                NULL::timestamp without time zone AS tgl_pindahkamar,
                NULL::timestamp without time zone AS rencana_pulang,
                antrian_t.no_antrian,
                false AS is_isisoap,
                peg_create.pegawai_id AS peg_create_id,
                peg_create.nama_pegawai AS peg_create_nama,
                NULL::integer AS konsulpoli_id
               FROM ((((((((((((((((pasienkirimkeunitlain_t
                 JOIN pendaftaran_t ON ((pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
                 LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 12)
            UNION ALL
             SELECT \'OT\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                    CASE pasien_m.jeniskelamin
                        WHEN \'15\'::text THEN \'L\'::text
                        ELSE \'P\'::text
                    END AS jk,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
                kelaspelayanan_m.kelaspelayanan_nama,
                \'-\'::text AS kelas_tagihan,
                false AS is_konsul,
                false AS is_pasientitipan,
                carabayar_m.carabayar_kode_warna,
                carabayar_m.carabayar_warna,
                pasienadmisi_t.carabayar_id,
                pasienadmisi_t.penjamin_id,
                pendaftaran_t.jeniskasuspenyakit_id,
                pasienadmisi_t.ruangan_id,
                pasienadmisi_t.kelaspelayanan_id,
                COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) AS pegawai_id,
                (pasienmasukpenunjang_t.status_periksa)::integer AS status_periksa,
                fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status_periksa_nama,
                    CASE
                        WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
                        ELSE false
                    END AS is_bayi,
                false AS is_stoppasientitipan,
                    CASE COALESCE(pasienadmisi_t.pasienpulang_id, 0)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_pulang,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_lunas,
                pendaftaran_t.is_stopakomodasi,
                pasienkirimkeunitlain_t.pasienmasukpenunjang_id,
                pendaftaran_t.keterangan_pendaftaran,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.no_tempattidur,
                NULL::text AS kettempattidur_nama,
                NULL::text AS status_kamar,
                NULL::timestamp without time zone AS tgl_pindahkamar,
                NULL::timestamp without time zone AS rencana_pulang,
                antrian_t.no_antrian,
                false AS is_isisoap,
                peg_create.pegawai_id AS peg_create_id,
                peg_create.nama_pegawai AS peg_create_nama,
                NULL::integer AS konsulpoli_id
               FROM ((((((((((((((((((pasienkirimkeunitlain_t
                 JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                 JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                 LEFT JOIN ( SELECT count(*) AS is_bayi,
                        kelahiranbayi_t_1.pendaftaranbaru_id
                       FROM kelahiranbayi_t kelahiranbayi_t_1
                      GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
                 LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 12)
            UNION ALL
             SELECT \'OL\'::text AS jenis,
                pendaftaranol_t.pendaftaran_id,
                pendaftaranol_t.tgl_pendaftaranol AS tgl_pendaftaran,
                pendaftaranol_t.no_pendaftaranol AS no_pendaftaran,
                COALESCE(pasien_m.no_rekam_medik, pendaftaranol_t.no_identitas_pasien) AS no_rekam_medik,
                COALESCE(pasien_m.nama_pasien, pendaftaranol_t.nama_pasien) AS nama_pasien,
                COALESCE(pasien_m.tanggal_lahir, pendaftaranol_t.tanggal_lahir) AS tanggal_lahir,
                    CASE COALESCE(pasien_m.jeniskelamin, pendaftaranol_t.jeniskelamin)
                        WHEN \'15\'::text THEN \'L\'::text
                        ELSE \'P\'::text
                    END AS jk,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                NULL::text AS hak_kelas,
                NULL::character varying AS kelaspelayanan_nama,
                \'-\'::text AS kelas_tagihan,
                false AS is_konsul,
                false AS is_pasientitipan,
                carabayar_m.carabayar_kode_warna,
                carabayar_m.carabayar_warna,
                pendaftaranol_t.carabayar_id,
                pendaftaranol_t.penjamin_id,
                NULL::integer AS jeniskasuspenyakit_id,
                pendaftaranol_t.ruangan_id,
                NULL::integer AS kelaspelayanan_id,
                pendaftaranol_t.pegawai_id,
                COALESCE((pendaftaran_t.status_periksa)::integer, pendaftaranol_t.status_daftar_ol) AS status_periksa,
                fgetnamalookup(COALESCE((pendaftaran_t.status_periksa)::integer, pendaftaranol_t.status_daftar_ol)) AS status_periksa_nama,
                false AS is_bayi,
                false AS is_stoppasientitipan,
                    CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_pulang,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_lunas,
                pendaftaran_t.is_stopakomodasi,
                NULL::integer AS pasienmasukpenunjang_id,
                pendaftaran_t.keterangan_pendaftaran,
                pendaftaran_t.instalasi_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                NULL::text AS kamarruangan_nokamar,
                NULL::text AS no_tempattidur,
                NULL::text AS kettempattidur_nama,
                NULL::text AS status_kamar,
                NULL::timestamp without time zone AS tgl_pindahkamar,
                NULL::timestamp without time zone AS rencana_pulang,
                antrian_t.no_antrian,
                false AS is_isisoap,
                peg_create.pegawai_id AS peg_create_id,
                peg_create.nama_pegawai AS peg_create_nama,
                NULL::integer AS konsulpoli_id
               FROM (((((((((((((pendaftaranol_t
                 LEFT JOIN pasien_m ON ((pendaftaranol_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ruangan_m ON ((pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaranol_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN carabayar_m ON ((pendaftaranol_t.carabayar_id = carabayar_m.carabayar_id)))
                 LEFT JOIN penjamin_m ON ((pendaftaranol_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
                 LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
                 LEFT JOIN jenispasien_m ON ((pendaftaranol_t.klasifikasipasien_id = jenispasien_m.jenispasien_id)))
                 LEFT JOIN antrian_t ON ((pendaftaranol_t.antrian_id = antrian_t.antrian_id)))
                 LEFT JOIN pendaftaran_t ON ((pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
              WHERE (pendaftaranol_t.status_daftar_ol = 564);
        ');

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
            konsulpoli_t.pasienpulang_id,
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
             LEFT JOIN pasienpulang_t ON ((konsulpoli_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
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
            DROP VIEW IF EXISTS "public"."infopasienpulangrjrd_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienpulangrjrd_v" AS  SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.tgl_pendaftaran,
                pasienpulang_t.tglpasienpulang, 
                pasien_m.no_rekam_medik,
                pendaftaran_t.no_pendaftaran,
                pasien_m.nama_pasien,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                kelaspelayanan_m.kelaspelayanan_nama,
                pasienpulang_t.ruanganakhir_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                pendaftaran_t.pegawai_id,
                pegawai_m.nama_pegawai AS dokter,
                carakeluar_m.carakeluar_nama,
                pendaftaran_t.instalasi_id,
                pasienpulang_t.kondisikeluar_id,
                kondisikeluar_m.kondisikeluar_nama,
                pasienpulang_t.pasienpulang_id,
                pendaftaran_t.umur,
                pasien_m.tanggal_lahir,
                pendaftaran_t.status_bayar,
                fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
                pendaftaran_t.status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama
               FROM (((((((((pendaftaran_t
                 JOIN pasienpulang_t ON (((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id) AND (pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id))))
                 JOIN pasien_m ON ((pasienpulang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
              WHERE (pasienpulang_t.pasienbatalpulang_id IS NULL)
            UNION
             SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.tgl_pendaftaran,
                pasienpulang_t.tglpasienpulang,
                pasien_m.no_rekam_medik,
                pendaftaran_t.no_pendaftaran,
                pasien_m.nama_pasien,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                kelaspelayanan_m.kelaspelayanan_nama,
                pasienpulang_t.ruanganakhir_id,
                ruangan_m.ruangan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                pegawai_m.pegawai_id,
                pegawai_m.nama_pegawai AS dokter,
                carakeluar_m.carakeluar_nama,
                pendaftaran_t.instalasi_id,
                pasienpulang_t.kondisikeluar_id,
                kondisikeluar_m.kondisikeluar_nama,
                pasienpulang_t.pasienpulang_id,
                pendaftaran_t.umur,
                pasien_m.tanggal_lahir,
                pendaftaran_t.status_bayar,
                fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
                konsulpoli_t.status_periksa,
                fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status_periksa_nama
               FROM ((((((((((konsulpoli_t
                 JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasienpulang_t ON (((konsulpoli_t.pendaftaran_id = pasienpulang_t.pendaftaran_id) AND (konsulpoli_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (konsulpoli_t.konsulpoli_id = pasienpulang_t.konsulpoli_id))))
                 JOIN pasien_m ON ((pasienpulang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
              WHERE (pasienpulang_t.pasienbatalpulang_id IS NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_084357_improvment_update_view_doctor_consul cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_084357_improvment_update_view_doctor_consul cannot be reverted.\n";

        return false;
    }
    */
}
