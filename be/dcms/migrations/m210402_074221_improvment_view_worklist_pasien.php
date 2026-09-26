<?php

use yii\db\Migration;

/**
 * Class m210402_074221_improvment_view_worklist_pasien
 */
class m210402_074221_improvment_view_worklist_pasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS infopasienrs_v;
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
                peg_create.nama_pegawai AS peg_create_nama
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
                peg_create.nama_pegawai AS peg_create_nama
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
                peg_create.nama_pegawai AS peg_create_nama
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
                peg_create.nama_pegawai AS peg_create_nama
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
                peg_create.nama_pegawai AS peg_create_nama
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
                peg_create.nama_pegawai AS peg_create_nama
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
                peg_create.nama_pegawai AS peg_create_nama
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210402_074221_improvment_view_worklist_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210402_074221_improvment_view_worklist_pasien cannot be reverted.\n";

        return false;
    }
    */
}
