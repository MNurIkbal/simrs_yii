<?php

use yii\db\Migration;

/**
 * Class m240102_082848_migrate_GLS_282_improve_infopasienrs_v
 */
class m240102_082848_migrate_GLS_282_improve_infopasienrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienrs_v";');
        $this->execute("
			CREATE OR REPLACE VIEW public.infopasienrs_v
        AS   SELECT worklist.jenis,
    worklist.pendaftaran_id,
    worklist.tgl_pendaftaran,
    worklist.no_pendaftaran,
    worklist.pasien_id,
    worklist.no_rekam_medik,
    worklist.nama_pasien,
    worklist.tanggal_lahir,
    worklist.jk,
    worklist.nama_pegawai,
    worklist.carabayar_nama,
    worklist.penjamin_nama,
    worklist.hak_kelas,
    worklist.kelaspelayanan_nama,
    worklist.kelas_tagihan,
    worklist.is_konsul,
    worklist.is_pasientitipan,
    worklist.carabayar_kode_warna,
    worklist.carabayar_warna,
    worklist.carabayar_id,
    worklist.penjamin_id,
    worklist.jeniskasuspenyakit_id,
    worklist.ruangan_id,
    worklist.kelaspelayanan_id,
    worklist.pegawai_id,
    worklist.status_periksa,
    worklist.status_periksa_nama,
    worklist.is_bayi,
    worklist.is_stoppasientitipan,
    worklist.is_pulang,
    worklist.is_lunas,
    worklist.is_stopakomodasi,
    worklist.pasienmasukpenunjang_id,
    worklist.keterangan_pendaftaran,
    worklist.instalasi_id,
    worklist.ruangan_nama,
    worklist.jeniskasuspenyakit_nama,
    worklist.kamarruangan_nokamar,
    worklist.no_tempattidur,
    worklist.kettempattidur_nama,
    worklist.status_kamar,
    worklist.tgl_pindahkamar,
    worklist.rencana_pulang,
    worklist.antrian_id,
    worklist.no_antrian,
    worklist.no_antrian_global,
    worklist.no_antrian_global_with_date,
    worklist.is_isisoap,
    worklist.peg_create_id,
    worklist.peg_create_nama,
    worklist.konsulpoli_id,
    worklist.alergi,
    worklist.no_telepon_pasien,
    worklist.no_mobile_pasien,
    worklist.no_identitas_pasien,
    worklist.kamartempattidur_id,
    worklist.additional_data,
    worklist.temp_status_periksa,
    worklist.rujukan_id,
    worklist.next_pendaftaran_id,
    worklist.konsulpoli_dokter_nama,
    alergi.riwayat_alergi,
    worklist.catatanpenting_pasien,
    worklist.status_skrining,
    lkp_nama_depan.lookup_name AS nama_depan,
    worklist.nosep
   FROM ( SELECT 'RJ'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE pasien_m.jeniskelamin
                    WHEN '15'::text THEN 'L'::text
                    WHEN '16'::text THEN 'P'::text
                    ELSE 'U'::text
                END AS jk,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            'Kelas '::text || bpjs_t.klsrawat AS hak_kelas,
            kelaspelayanan_m.kelaspelayanan_nama,
            '-'::text AS kelas_tagihan,
                CASE
                    WHEN ruangan_asal.pendaftaranbaru_id IS NULL THEN false
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
            pendaftaran_t.status_periksa::integer AS status_periksa,
            status_pendaftaran.lookup_name AS status_periksa_nama,
                CASE
                    WHEN kelahiranbayi_t.is_bayi > 0 THEN true
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
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
                CASE
                    WHEN soap_rj.soap_id IS NULL THEN false
                    ELSE true
                END AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            NULL::integer AS konsulpoli_id,
            anamnesa_t.alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            NULL::integer AS kamartempattidur_id,
            pendaftaran_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            next_pendaftaran.pendaftaran_id AS next_pendaftaran_id,
            dokter_konsul.dokter_nama AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
                CASE
                    WHEN COALESCE(skrining_pasien_rj_t.skrining_pasien_rj::double precision, 0::double precision) > 0::double precision OR COALESCE(skrining_covid_t.skrining_covid::double precision, 0::double precision) > 0::double precision OR COALESCE(skrining_asesmen_rj_t.skrining_asesmen_rj::double precision, 0::double precision) > 0::double precision THEN 'Sudah'::text::character varying
                    ELSE 'Belum'::text::character varying
                END AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.pekerjaan_id,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    count(a.skrining_pasien_rj_id) AS skrining_pasien_rj
                   FROM skrining_pasien_rj_t a
                  GROUP BY a.pendaftaran_id) skrining_pasien_rj_t ON pendaftaran_t.pendaftaran_id = skrining_pasien_rj_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    count(a.skrining_covid_id) AS skrining_covid
                   FROM skrining_covid_t a
                  GROUP BY a.pendaftaran_id) skrining_covid_t ON pendaftaran_t.pendaftaran_id = skrining_covid_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    count(a.skrining_asesmen_rj_id) AS skrining_asesmen_rj
                   FROM skrining_asesmen_rj_t a
                  GROUP BY a.pendaftaran_id) skrining_asesmen_rj_t ON pendaftaran_t.pendaftaran_id = skrining_asesmen_rj_t.pendaftaran_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_warna,
                    a.carabayar_kode_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.kelompokpegawai_id
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT konsulpoli_t.pendaftaranbaru_id,
                    konsulpoli_t.asalpoliklinikkonsul_id,
                    poli_asal.ruangan_nama
                   FROM konsulpoli_t
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) poli_asal ON konsulpoli_t.asalpoliklinikkonsul_id = poli_asal.ruangan_id) ruangan_asal ON pendaftaran_t.pendaftaran_id = ruangan_asal.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat,
                    a.is_deleted,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT count(*) AS is_bayi,
                    a.pendaftaranbaru_id
                   FROM kelahiranbayi_t a
                  GROUP BY a.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.no_antrian,
                    a.jenisantrian_id,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id AND antrian_t.jenisantrian_id = 312
             LEFT JOIN ( SELECT soaprj_t.pendaftaran_id AS soap_id
                   FROM soaprj_t
                  WHERE soaprj_t.is_deleted = false
                  GROUP BY soaprj_t.pendaftaran_id) soap_rj ON pendaftaran_t.pendaftaran_id = soap_rj.soap_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.alergi
                   FROM anamnesa_t a) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_pendaftaran ON pendaftaran_t.status_periksa::integer = status_pendaftaran.lookup_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.prev_pendaftaran_id
                   FROM pendaftaran_t a
                     JOIN ( SELECT b.pasienadmisi_id,
                            b.status_ranap
                           FROM pasienadmisi_t b) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                  WHERE pasienadmisi_t.status_ranap <> 453) next_pendaftaran ON pendaftaran_t.pendaftaran_id = next_pendaftaran.prev_pendaftaran_id
             LEFT JOIN ( SELECT string_agg(DISTINCT pegawai_m_1.nama_pegawai::text, '##'::text) AS dokter_nama,
                    a.pendaftaran_id
                   FROM konsulpoli_t a
                     LEFT JOIN ( SELECT a_1.pegawai_id,
                            a_1.nama_pegawai
                           FROM pegawai_m a_1) pegawai_m_1 ON a.pegawai_id = pegawai_m_1.pegawai_id
                  WHERE a.status_approve = 565
                  GROUP BY a.pendaftaran_id) dokter_konsul ON pendaftaran_t.pendaftaran_id = dokter_konsul.pendaftaran_id
          WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.is_deleted = false
        UNION ALL
         SELECT 'RJ'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE pasien_m.jeniskelamin
                    WHEN '15'::text THEN 'L'::text
                    WHEN '16'::text THEN 'P'::text
                    ELSE 'U'::text
                END AS jk,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            'Kelas '::text || bpjs_t.klsrawat AS hak_kelas,
            kelaspelayanan_m.kelaspelayanan_nama,
            '-'::text AS kelas_tagihan,
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
            konsulpoli_t.status_periksa::integer AS status_periksa,
            status_konsulpoli.lookup_name AS status_periksa_nama,
                CASE
                    WHEN kelahiranbayi_t.is_bayi > 0 THEN true
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
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
                CASE
                    WHEN soap_rj.soap_id IS NULL THEN false
                    ELSE true
                END AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            konsulpoli_t.konsulpoli_id,
            anamnesa_t.alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            NULL::integer AS kamartempattidur_id,
            pendaftaran_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            next_pendaftaran.pendaftaran_id AS next_pendaftaran_id,
            dokter_konsul.dokter_nama AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM konsulpoli_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    a.no_pendaftaran,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.jeniskasuspenyakit_id,
                    a.kelaspelayanan_id,
                    a.status_bayar,
                    a.is_stopakomodasi,
                    a.keterangan_pendaftaran,
                    a.additional_data,
                    a.rujukan_id,
                    a.caramasuk_id,
                    a.golonganumur_id,
                    a.penanggungjawab_id,
                    a.bpjs_id,
                    a.created_by,
                    a.instalasi_id,
                    a.is_deleted,
                    a.is_active,
                    a.pegawai_id
                   FROM pendaftaran_t a) pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.pekerjaan_id,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    count(a.skrining_pasien_rj_id) AS skrining_pasien_rj
                   FROM skrining_pasien_rj_t a
                  GROUP BY a.pendaftaran_id) skrining_pasien_rj_t ON pendaftaran_t.pendaftaran_id = skrining_pasien_rj_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    count(a.skrining_covid_id) AS skrining_covid
                   FROM skrining_covid_t a
                  GROUP BY a.pendaftaran_id) skrining_covid_t ON pendaftaran_t.pendaftaran_id = skrining_covid_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    count(a.skrining_asesmen_rj_id) AS skrining_asesmen_rj
                   FROM skrining_asesmen_rj_t a
                  GROUP BY a.pendaftaran_id) skrining_asesmen_rj_t ON pendaftaran_t.pendaftaran_id = skrining_asesmen_rj_t.pendaftaran_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_warna,
                    a.carabayar_kode_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
             LEFT JOIN ( SELECT a.golonganumur_id,
                    a.golonganumur_nama
                   FROM golonganumur_m a) golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.kelompokpegawai_id
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruanganasal_m ON konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.no_antrian,
                    a.jenisantrian_id,
                    a.pendaftaran_id,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date,
                    a.pegawai_id
                   FROM antrian_t a
                  WHERE a.is_konsulpoli = true AND a.status_antrian = 0) antrian_t ON antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND antrian_t.jenisantrian_id = 312 AND konsulpoli_t.pegawai_id = antrian_t.pegawai_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat,
                    a.is_deleted,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
             LEFT JOIN ( SELECT count(*) AS soap,
                    soaprj_t.pendaftaran_id
                   FROM soaprj_t
                  WHERE soaprj_t.is_deleted IS FALSE
                  GROUP BY soaprj_t.pendaftaran_id) soaprj ON pendaftaran_t.pendaftaran_id = soaprj.pendaftaran_id
             LEFT JOIN ( SELECT count(*) AS is_bayi,
                    a.pendaftaranbaru_id
                   FROM kelahiranbayi_t a
                  GROUP BY a.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
             LEFT JOIN ( SELECT soaprj_t.pendaftaran_id AS soap_id
                   FROM soaprj_t
                  WHERE soaprj_t.is_deleted = false
                  GROUP BY soaprj_t.pendaftaran_id) soap_rj ON pendaftaran_t.pendaftaran_id = soap_rj.soap_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.alergi
                   FROM anamnesa_t a) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_konsulpoli ON konsulpoli_t.status_konsul::integer = status_konsulpoli.lookup_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.prev_pendaftaran_id
                   FROM pendaftaran_t a
                     JOIN ( SELECT b.pasienadmisi_id,
                            b.status_ranap
                           FROM pasienadmisi_t b) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                  WHERE pasienadmisi_t.status_ranap <> 453) next_pendaftaran ON pendaftaran_t.pendaftaran_id = next_pendaftaran.prev_pendaftaran_id
             LEFT JOIN ( SELECT string_agg(DISTINCT pegawai_m_1.nama_pegawai::text, '##'::text) AS dokter_nama,
                    a.pendaftaran_id
                   FROM konsulpoli_t a
                     LEFT JOIN ( SELECT a_1.pegawai_id,
                            a_1.nama_pegawai
                           FROM pegawai_m a_1) pegawai_m_1 ON a.pegawai_id = pegawai_m_1.pegawai_id
                  WHERE a.status_approve = 565
                  GROUP BY a.pendaftaran_id) dokter_konsul ON pendaftaran_t.pendaftaran_id = dokter_konsul.pendaftaran_id
          WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.is_deleted = false AND pendaftaran_t.is_active = true AND konsulpoli_t.status_approve = 565 AND konsulpoli_t.pendaftaranbaru_id IS NULL
        UNION ALL
         SELECT 'RD'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE pasien_m.jeniskelamin
                    WHEN '15'::text THEN 'L'::text
                    WHEN '16'::text THEN 'P'::text
                    ELSE 'U'::text
                END AS jk,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            'Kelas '::text || bpjs_t.klsrawat AS hak_kelas,
            kelaspelayanan_m.kelaspelayanan_nama,
            '-'::text AS kelas_tagihan,
                CASE
                    WHEN ruangan_asal.pendaftaranbaru_id IS NULL THEN false
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
            pendaftaran_t.status_periksa::integer AS status_periksa,
            status_pendaftaran.lookup_name AS status_periksa_nama,
                CASE
                    WHEN kelahiranbayi_t.is_bayi > 0 THEN true
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
            kamartempattidur_m.no_tempattidur,
            NULL::text AS kettempattidur_nama,
            NULL::text AS status_kamar,
            NULL::timestamp without time zone AS tgl_pindahkamar,
            NULL::timestamp without time zone AS rencana_pulang,
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
                CASE
                    WHEN soap_rd.soap_id IS NULL THEN false
                    ELSE true
                END AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            NULL::integer AS konsulpoli_id,
            asesmenperawatrd_t.is_alergi AS alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            triase_t.kamartempattidur_id,
            pendaftaran_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            next_pendaftaran.pendaftaran_id AS next_pendaftaran_id,
            NULL::text AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.pekerjaan_id,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.carabayar_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT konsulpoli_t.pendaftaranbaru_id,
                    konsulpoli_t.asalpoliklinikkonsul_id,
                    poli_asal.ruangan_nama
                   FROM konsulpoli_t
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) poli_asal ON konsulpoli_t.asalpoliklinikkonsul_id = poli_asal.ruangan_id) ruangan_asal ON pendaftaran_t.pendaftaran_id = ruangan_asal.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT count(*) AS is_bayi,
                    a.pendaftaranbaru_id
                   FROM kelahiranbayi_t a
                  GROUP BY a.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.no_antrian,
                    a.jenisantrian_id,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id AND antrian_t.jenisantrian_id = 312
             LEFT JOIN ( SELECT cppt_t.pendaftaran_id AS soap_id
                   FROM cppt_t
                  WHERE cppt_t.is_deleted = false
                  GROUP BY cppt_t.pendaftaran_id) soap_rd ON pendaftaran_t.pendaftaran_id = soap_rd.soap_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
             LEFT JOIN ( SELECT asesmenperawatrd_t_1.pendaftaran_id,
                    asesmenperawatrd_t_1.is_alergi
                   FROM asesmenperawatrd_t asesmenperawatrd_t_1) asesmenperawatrd_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.kamartempattidur_id
                   FROM triase_t a
                  GROUP BY a.pendaftaran_id, a.kamartempattidur_id) triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON triase_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_pendaftaran ON pendaftaran_t.status_periksa::integer = status_pendaftaran.lookup_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.prev_pendaftaran_id
                   FROM pendaftaran_t a
                  WHERE a.status_periksa::text <> '453'::text) next_pendaftaran ON pendaftaran_t.pendaftaran_id = next_pendaftaran.prev_pendaftaran_id
          WHERE pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.is_deleted = false
        UNION ALL
         SELECT 'RI'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE pasien_m.jeniskelamin
                    WHEN '15'::text THEN 'L'::text
                    WHEN '16'::text THEN 'P'::text
                    ELSE 'U'::text
                END AS jk,
            dokter_dpjp.dokter_nama AS nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN kelas_ditagihkan.kelaspelayanan_nama IS NULL THEN ('Kelas '::text || (((((bpjs_t.additional_data::json -> 'sep'::text) -> 'klsRawat'::text) ->> 'klsRawatHak'::text)::character varying)::text))::character varying
                    ELSE kelaspelayanan_m.kelaspelayanan_nama
                END AS hak_kelas,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, '-'::character varying) AS kelas_tagihan,
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
            status_admisi.lookup_name AS status_periksa_nama,
                CASE
                    WHEN kelahiranbayi_t.is_bayi > 0 THEN true
                    ELSE false
                END AS is_bayi,
                CASE
                    WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS FALSE THEN false
                    WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS TRUE THEN true
                    WHEN stop_titipan.is_pasientitipan IS FALSE AND stop_titipan.is_stoptitipan IS FALSE THEN true
                    WHEN stop_titipan.is_pasientitipan IS TRUE AND stop_titipan.is_stoptitipan IS TRUE THEN true
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
            ket_kamar.lookup_name AS status_kamar,
            pasienadmisi_t.tgl_pindahkamar,
            rencanapulang_t.rencana_pulang,
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
                CASE
                    WHEN soap_ri.soap_id IS NULL THEN false
                    ELSE true
                END AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            NULL::integer AS konsulpoli_id,
            asesmenawal_t.r_alergi AS alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            pasienadmisi_t.kamartempattidur_id,
            pasienadmisi_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            NULL::integer AS next_pendaftaran_id,
            dokter_konsul.dokter_nama AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM pasienadmisi_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.pasien_id,
                    a.no_pendaftaran,
                    a.jeniskasuspenyakit_id,
                    a.pegawai_id,
                    a.status_bayar,
                    a.is_stopakomodasi,
                    a.keterangan_pendaftaran,
                    a.rujukan_id,
                    a.last_modified_by,
                    a.created_by,
                    a.antrian_id,
                    a.is_active,
                    a.is_deleted
                   FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.pekerjaan_id,
                    a.golonganumur_id,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar,
                    a.jeniskasuspenyakit_id,
                    a.keterangan_kamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.carabayar_warna
                   FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur,
                    a.kettempattidur_id
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat,
                    a.additional_data,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar,
                    a.keterangan_kamar
                   FROM kamarruangan_m a) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
             LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                    pindahkamar_t.pasienadmisi_id,
                    pindahkamar_t.kelas_ditagihkan_id,
                    kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
                    pindahkamar_t.is_stoptitipan
                   FROM pindahkamar_t
                     JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                            pk.pasienadmisi_id
                           FROM pindahkamar_t pk
                          GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id
                  WHERE pindahkamar_t.is_deleted = false AND pindahkamar_t.is_pasientitipan = true) pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
             LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                    pindahkamar_t.pasienadmisi_id,
                    pindahkamar_t.is_pasientitipan,
                    pindahkamar_t.is_stoptitipan
                   FROM pindahkamar_t
                     JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                            pk.pasienadmisi_id
                           FROM pindahkamar_t pk
                          GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
                  WHERE pindahkamar_t.is_deleted = false) stop_titipan ON pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.carakeluar_id
                   FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.carakeluar_id
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT count(*) AS is_bayi,
                    kelahiranbayi_t_1.pendaftaranbaru_id
                   FROM kelahiranbayi_t kelahiranbayi_t_1
                  GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.kettempattidur_id,
                    a.kettempattidur_nama
                   FROM kettempattidur_m a) kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.rencana_pulang,
                    a.is_deleted
                   FROM rencanapulang_t a) rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
             LEFT JOIN ( SELECT a.antrian_id,
                    a.jenisantrian_id,
                    a.no_antrian,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id AND antrian_t.jenisantrian_id = 312
             LEFT JOIN ( SELECT cppt_t.pasienadmisi_id AS soap_id
                   FROM cppt_t
                  WHERE cppt_t.is_deleted = false
                  GROUP BY cppt_t.pasienadmisi_id) soap_ri ON pendaftaran_t.pasienadmisi_id = soap_ri.soap_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pasienadmisi_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.r_alergi
                   FROM asesmenawal_t a) asesmenawal_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_admisi ON pasienadmisi_t.status_ranap = status_admisi.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) ket_kamar ON kamarruangan_m.keterangan_kamar::integer = status_admisi.lookup_id
             LEFT JOIN ( SELECT x.pasienadmisi_id,
                    string_agg(x.nama_pegawai::text, '##'::text ORDER BY x.urutan) AS dokter_nama
                   FROM ( SELECT a.pasienadmisi_id,
                            pegawai_m_1.nama_pegawai,
                            1 AS urutan
                           FROM pasienadmisi_t a
                             LEFT JOIN ( SELECT a_1.pegawai_id,
                                    a_1.nama_pegawai
                                   FROM pegawai_m a_1) pegawai_m_1 ON a.pegawai_id = pegawai_m_1.pegawai_id
                        UNION ALL
                         SELECT DISTINCT a.pasienadmisi_id,
                            pegawai_m_1.nama_pegawai,
                            2 AS urutan
                           FROM permintaankonsul_t a
                             LEFT JOIN ( SELECT a_1.pegawai_id,
                                    a_1.nama_pegawai
                                   FROM pegawai_m a_1) pegawai_m_1 ON a.dokter_id = pegawai_m_1.pegawai_id
                          WHERE a.jenis_konsul::text = '435'::text AND a.status_konsul = 437
                          GROUP BY a.pasienadmisi_id, pegawai_m_1.nama_pegawai) x
                  GROUP BY x.pasienadmisi_id) dokter_dpjp ON dokter_dpjp.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT string_agg(DISTINCT pegawai_m_1.nama_pegawai::text, '##'::text) AS dokter_nama,
                    a.pasienadmisi_id
                   FROM permintaankonsul_t a
                     LEFT JOIN ( SELECT a_1.pegawai_id,
                            a_1.nama_pegawai
                           FROM pegawai_m a_1) pegawai_m_1 ON a.dokter_id = pegawai_m_1.pegawai_id
                  WHERE (a.jenis_konsul::text = ANY (ARRAY['434'::character varying::text, '731'::character varying::text])) AND a.status_konsul = 437
                  GROUP BY a.pasienadmisi_id) dokter_konsul ON dokter_konsul.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
          WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false
        UNION ALL
         SELECT 'RI'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE pasien_m.jeniskelamin
                    WHEN '15'::text THEN 'L'::text
                    WHEN '16'::text THEN 'P'::text
                    ELSE 'U'::text
                END AS jk,
            dokter_dpjp.dokter_nama AS nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN kelas_ditagihkan.kelaspelayanan_nama IS NULL THEN ('Kelas '::text || (((((bpjs_t.additional_data::json -> 'sep'::text) -> 'klsRawat'::text) ->> 'klsRawatHak'::text)::character varying)::text))::character varying
                    ELSE kelaspelayanan_m.kelaspelayanan_nama
                END AS hak_kelas,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, '-'::character varying) AS kelas_tagihan,
                CASE
                    WHEN permintaankonsul_t.jenis_konsul::text = '435'::text THEN false
                    ELSE true
                END AS is_konsul,
            pasienadmisi_t.is_pasientitipan,
            carabayar_m.carabayar_kode_warna,
            carabayar_m.carabayar_warna,
            pasienadmisi_t.carabayar_id,
            pasienadmisi_t.penjamin_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            COALESCE(permintaankonsul_t.dokter_id, pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) AS pegawai_id,
            pasienadmisi_t.status_ranap AS status_periksa,
            status_admisi.lookup_name AS status_periksa_nama,
                CASE
                    WHEN kelahiranbayi_t.is_bayi > 0 THEN true
                    ELSE false
                END AS is_bayi,
                CASE
                    WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS FALSE THEN false
                    WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS TRUE THEN true
                    WHEN stop_titipan.is_pasientitipan IS FALSE AND stop_titipan.is_stoptitipan IS FALSE THEN true
                    WHEN stop_titipan.is_pasientitipan IS TRUE AND stop_titipan.is_stoptitipan IS TRUE THEN true
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
            ket_kamar.lookup_name AS status_kamar,
            pasienadmisi_t.tgl_pindahkamar,
            rencanapulang_t.rencana_pulang,
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
                CASE
                    WHEN soap_ri.soap_id IS NULL THEN false
                    ELSE true
                END AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            permintaankonsul_t.permintaankonsul_id AS konsulpoli_id,
            asesmenawal_t.r_alergi AS alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            pasienadmisi_t.kamartempattidur_id,
            pasienadmisi_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            NULL::integer AS next_pendaftaran_id,
            dokter_konsul.dokter_nama AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM pasienadmisi_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.permintaankonsul_id,
                    a.jenis_konsul,
                    a.status_konsul,
                    a.dokter_id
                   FROM permintaankonsul_t a
                     JOIN ( SELECT max(p_max.permintaankonsul_id) AS permintaankonsul_id,
                            p_max.dokter_id,
                            p_max.pasienadmisi_id
                           FROM permintaankonsul_t p_max
                          WHERE (p_max.jenis_konsul::text = ANY (ARRAY['434'::character varying::text, '435'::character varying::text, '731'::text])) AND p_max.status_konsul = 437
                          GROUP BY p_max.dokter_id, p_max.pasienadmisi_id) permintaankonsul_max ON a.permintaankonsul_id = permintaankonsul_max.permintaankonsul_id AND a.pasienadmisi_id = permintaankonsul_max.pasienadmisi_id AND a.dokter_id = permintaankonsul_max.dokter_id) permintaankonsul_t ON pasienadmisi_t.pasienadmisi_id = permintaankonsul_t.pasienadmisi_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.pasien_id,
                    a.no_pendaftaran,
                    a.jeniskasuspenyakit_id,
                    a.pegawai_id,
                    a.status_bayar,
                    a.is_stopakomodasi,
                    a.keterangan_pendaftaran,
                    a.rujukan_id,
                    a.last_modified_by,
                    a.created_by,
                    a.antrian_id,
                    a.is_active,
                    a.is_deleted
                   FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.pekerjaan_id,
                    a.golonganumur_id,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar,
                    a.jeniskasuspenyakit_id,
                    a.keterangan_kamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.carabayar_warna
                   FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur,
                    a.kettempattidur_id
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat,
                    a.additional_data,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar,
                    a.keterangan_kamar
                   FROM kamarruangan_m a) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
             LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                    pindahkamar_t.pasienadmisi_id,
                    pindahkamar_t.kelas_ditagihkan_id,
                    kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
                    pindahkamar_t.is_stoptitipan
                   FROM pindahkamar_t
                     JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                            pk.pasienadmisi_id
                           FROM pindahkamar_t pk
                          GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id
                  WHERE pindahkamar_t.is_deleted = false AND pindahkamar_t.is_pasientitipan = true) pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
             LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                    pindahkamar_t.pasienadmisi_id,
                    pindahkamar_t.is_pasientitipan,
                    pindahkamar_t.is_stoptitipan
                   FROM pindahkamar_t
                     JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                            pk.pasienadmisi_id
                           FROM pindahkamar_t pk
                          GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
                  WHERE pindahkamar_t.is_deleted = false) stop_titipan ON pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.carakeluar_id
                   FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.carakeluar_id
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT count(*) AS is_bayi,
                    kelahiranbayi_t_1.pendaftaranbaru_id
                   FROM kelahiranbayi_t kelahiranbayi_t_1
                  GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.kettempattidur_id,
                    a.kettempattidur_nama
                   FROM kettempattidur_m a) kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.rencana_pulang,
                    a.is_deleted
                   FROM rencanapulang_t a) rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
             LEFT JOIN ( SELECT a.antrian_id,
                    a.jenisantrian_id,
                    a.no_antrian,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id AND antrian_t.jenisantrian_id = 312
             LEFT JOIN ( SELECT cppt_t.pasienadmisi_id AS soap_id
                   FROM cppt_t
                  WHERE cppt_t.is_deleted = false
                  GROUP BY cppt_t.pasienadmisi_id) soap_ri ON pendaftaran_t.pasienadmisi_id = soap_ri.soap_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pasienadmisi_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.r_alergi
                   FROM asesmenawal_t a) asesmenawal_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_admisi ON pasienadmisi_t.status_ranap = status_admisi.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) ket_kamar ON kamarruangan_m.keterangan_kamar::integer = status_admisi.lookup_id
             LEFT JOIN ( SELECT x.pasienadmisi_id,
                    string_agg(x.nama_pegawai::text, '##'::text ORDER BY x.urutan) AS dokter_nama
                   FROM ( SELECT a.pasienadmisi_id,
                            pegawai_m_1.nama_pegawai,
                            1 AS urutan
                           FROM pasienadmisi_t a
                             LEFT JOIN ( SELECT a_1.pegawai_id,
                                    a_1.nama_pegawai
                                   FROM pegawai_m a_1) pegawai_m_1 ON a.pegawai_id = pegawai_m_1.pegawai_id
                        UNION ALL
                         SELECT DISTINCT a.pasienadmisi_id,
                            pegawai_m_1.nama_pegawai,
                            2 AS urutan
                           FROM permintaankonsul_t a
                             LEFT JOIN ( SELECT a_1.pegawai_id,
                                    a_1.nama_pegawai
                                   FROM pegawai_m a_1) pegawai_m_1 ON a.dokter_id = pegawai_m_1.pegawai_id
                          WHERE a.jenis_konsul::text = '435'::text AND a.status_konsul = 437
                          GROUP BY a.pasienadmisi_id, pegawai_m_1.nama_pegawai) x
                  GROUP BY x.pasienadmisi_id) dokter_dpjp ON dokter_dpjp.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT string_agg(DISTINCT pegawai_m_1.nama_pegawai::text, '##'::text) AS dokter_nama,
                    a.pasienadmisi_id
                   FROM permintaankonsul_t a
                     LEFT JOIN ( SELECT a_1.pegawai_id,
                            a_1.nama_pegawai
                           FROM pegawai_m a_1) pegawai_m_1 ON a.dokter_id = pegawai_m_1.pegawai_id
                  WHERE (a.jenis_konsul::text = ANY (ARRAY['434'::character varying::text, '731'::character varying::text])) AND a.status_konsul = 437
                  GROUP BY a.pasienadmisi_id) dokter_konsul ON dokter_konsul.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
          WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND (permintaankonsul_t.jenis_konsul::text = ANY (ARRAY['434'::character varying::text, '435'::character varying::text, '731'::text])) AND permintaankonsul_t.status_konsul = 437
        UNION ALL
         SELECT 'MCU'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE pasien_m.jeniskelamin
                    WHEN '15'::text THEN 'L'::text
                    WHEN '16'::text THEN 'P'::text
                    ELSE 'U'::text
                END AS jk,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            'Kelas '::text || bpjs_t.klsrawat AS hak_kelas,
            kelaspelayanan_m.kelaspelayanan_nama,
            '-'::text AS kelas_tagihan,
            true AS is_konsul,
            false AS is_pasientitipan,
            carabayar_m.carabayar_kode_warna,
            carabayar_m.carabayar_warna,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            ruangan_m.ruangan_id,
            pendaftaran_t.kelaspelayanan_id,
            pegawai_m.pegawai_id,
            konsulpoli_t.status_periksa::integer AS status_periksa,
            status_konsulpoli.lookup_name AS status_periksa_nama,
                CASE
                    WHEN kelahiranbayi_t.is_bayi > 0 THEN true
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
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
            false AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            konsulpoli_t.konsulpoli_id,
            anamnesa_t.is_alergi AS alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            NULL::integer AS kamartempattidur_id,
            pendaftaran_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            NULL::integer AS next_pendaftaran_id,
            NULL::text AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM pendaftaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.status_periksa,
                    a.konsulpoli_id,
                    a.ruangan_id,
                    a.pegawai_id,
                    a.status_approve,
                    a.pendaftaranbaru_id
                   FROM konsulpoli_t a) konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.pekerjaan_id,
                    a.golonganumur_id,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.carabayar_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON COALESCE(konsulpoli_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT bpjs_t_1.bpjs_id,
                    bpjs_t_1.klsrawat,
                    bpjs_t_1.nosep
                   FROM bpjs_t bpjs_t_1) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT count(*) AS is_bayi,
                    kelahiranbayi_t_1.pendaftaranbaru_id
                   FROM kelahiranbayi_t kelahiranbayi_t_1
                  GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.jenisantrian_id,
                    a.no_antrian,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id AND antrian_t.jenisantrian_id = 312
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.is_alergi
                   FROM anamnesa_t a) anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_konsulpoli ON konsulpoli_t.status_periksa::integer = status_konsulpoli.lookup_id
          WHERE pendaftaran_t.instalasi_id = 21 AND konsulpoli_t.status_approve = 565 AND konsulpoli_t.pendaftaranbaru_id IS NOT NULL
        UNION ALL
         SELECT 'OT'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE pasien_m.jeniskelamin
                    WHEN '15'::text THEN 'L'::text
                    WHEN '16'::text THEN 'P'::text
                    ELSE 'U'::text
                END AS jk,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            'Kelas '::text || bpjs_t.klsrawat AS hak_kelas,
            kelaspelayanan_m.kelaspelayanan_nama,
            '-'::text AS kelas_tagihan,
            false AS is_konsul,
            false AS is_pasientitipan,
            carabayar_m.carabayar_kode_warna,
            carabayar_m.carabayar_warna,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            ruangan_m.ruangan_id,
            pendaftaran_t.kelaspelayanan_id,
            pendaftaran_t.pegawai_id,
            pasienmasukpenunjang_t.status_periksa::integer AS status_periksa,
            status_penunjang.lookup_name AS status_periksa_nama,
                CASE
                    WHEN kelahiranbayi_t.is_bayi > 0 THEN true
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
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
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
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
            true AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            NULL::integer AS konsulpoli_id,
            NULL::boolean AS alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            pasienadmisi_t.kamartempattidur_id,
            pendaftaran_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            NULL::integer AS next_pendaftaran_id,
            NULL::text AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM pendaftaran_t
             LEFT JOIN ( SELECT a.tgl_kirimpasien,
                    a.pendaftaran_id,
                    a.ruangan_id,
                    a.instalasi_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.status_periksa,
                    a.pasienmasukpenunjang_id
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.pekerjaan_id,
                    a.golonganumur_id,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.carabayar_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT penjamin_m_1.penjamin_id,
                    penjamin_m_1.penjamin_nama
                   FROM penjamin_m penjamin_m_1) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT kelaspelayanan_m_1.kelaspelayanan_id,
                    kelaspelayanan_m_1.kelaspelayanan_nama
                   FROM kelaspelayanan_m kelaspelayanan_m_1) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT count(*) AS is_bayi,
                    a.pendaftaranbaru_id
                   FROM kelahiranbayi_t a
                  GROUP BY a.pendaftaranbaru_id) kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
             LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.jenisantrian_id,
                    a.no_antrian,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id AND antrian_t.jenisantrian_id = 312
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_penunjang ON pasienmasukpenunjang_t.status_periksa::integer = status_penunjang.lookup_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 12
        UNION ALL
         SELECT 'OL'::text AS jenis,
            pendaftaranol_t.pendaftaran_id,
            pendaftaranol_t.tgl_pendaftaranol AS tgl_pendaftaran,
            pendaftaranol_t.no_pendaftaranol AS no_pendaftaran,
            COALESCE(pasien_m.no_rekam_medik, pendaftaranol_t.no_identitas_pasien) AS no_rekam_medik,
            COALESCE(pasien_m.nama_pasien, pendaftaranol_t.nama_pasien) AS nama_pasien,
            COALESCE(pasien_m.tanggal_lahir, pendaftaranol_t.tanggal_lahir) AS tanggal_lahir,
                CASE COALESCE(pasien_m.jeniskelamin, pendaftaranol_t.jeniskelamin)
                    WHEN '15'::text THEN 'L'::text
                    WHEN '16'::text THEN 'P'::text
                    ELSE 'P'::text
                END AS jk,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::text AS hak_kelas,
            NULL::character varying AS kelaspelayanan_nama,
            '-'::text AS kelas_tagihan,
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
            pendaftaranol_t.status_daftar_ol AS status_periksa,
            status_pendaftaranol.lookup_name AS status_periksa_nama,
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
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
            false AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            NULL::integer AS konsulpoli_id,
            NULL::boolean AS alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            NULL::integer AS kamartempattidur_id,
            pendaftaran_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            NULL::integer AS next_pendaftaran_id,
            NULL::text AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM pendaftaranol_t
             LEFT JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.pekerjaan_id,
                    a.golonganumur_id,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaranol_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaranol_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.carabayar_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaranol_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaranol_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.jenisantrian_id,
                    a.no_antrian,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON pendaftaranol_t.antrian_id = antrian_t.antrian_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pasienpulang_id,
                    a.status_bayar,
                    a.is_stopakomodasi,
                    a.keterangan_pendaftaran,
                    a.instalasi_id,
                    a.additional_data,
                    a.rujukan_id,
                    a.jeniskasuspenyakit_id,
                    a.created_by,
                    a.bpjs_id
                   FROM pendaftaran_t a) pendaftaran_t ON pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_pendaftaranol ON pendaftaranol_t.status_daftar_ol = status_pendaftaranol.lookup_id
        UNION ALL
         SELECT 'OL'::text AS jenis,
            konsulpoli_t.pendaftaran_id,
            konsulpoli_t.tgl_konsulpoli AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE
                    WHEN pasien_m.jeniskelamin::integer = 15 THEN 'L'::text
                    WHEN pasien_m.jeniskelamin::integer = 16 THEN 'P'::text
                    ELSE NULL::text
                END AS jk,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::text AS hak_kelas,
            NULL::character varying AS kelaspelayanan_nama,
            '-'::text AS kelas_tagihan,
            false AS is_konsul,
            false AS is_pasientitipan,
            carabayar_m.carabayar_kode_warna,
            carabayar_m.carabayar_warna,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            konsulpoli_t.ruangan_id,
            NULL::integer AS kelaspelayanan_id,
            konsulpoli_t.pegawai_id,
            konsulpoli_t.status_konsul::integer AS status_periksa,
            look_statuskonsul.lookup_name AS status_periksa_nama,
            false AS is_bayi,
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
            pendaftaran_t.instalasi_id,
            ruangan_m.ruangan_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            NULL::text AS kamarruangan_nokamar,
            NULL::text AS no_tempattidur,
            NULL::text AS kettempattidur_nama,
            NULL::text AS status_kamar,
            NULL::timestamp without time zone AS tgl_pindahkamar,
            NULL::timestamp without time zone AS rencana_pulang,
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
            false AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            konsulpoli_t.konsulpoli_id,
            NULL::boolean AS alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            NULL::integer AS kamartempattidur_id,
            pendaftaran_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            NULL::integer AS next_pendaftaran_id,
            NULL::text AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM konsulpoli_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.is_stopakomodasi,
                    a.keterangan_pendaftaran,
                    a.instalasi_id,
                    a.additional_data,
                    a.rujukan_id,
                    a.jeniskasuspenyakit_id,
                    a.status_bayar,
                    a.bpjs_id
                   FROM pendaftaran_t a) pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON konsulpoli_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.carabayar_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statuskonsul ON konsulpoli_t.status_konsul::integer = look_statuskonsul.lookup_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.no_antrian,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON konsulpoli_t.antrian_id = antrian_t.antrian_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON konsulpoli_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id
        UNION ALL
         SELECT 'OL'::text AS jenis,
            buatjanjipoli_t.pendaftaran_id,
            buatjanjipoli_t.tgl_buatjanji AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
                CASE
                    WHEN pasien_m.jeniskelamin::integer = 15 THEN 'L'::text
                    WHEN pasien_m.jeniskelamin::integer = 16 THEN 'P'::text
                    ELSE NULL::text
                END AS jk,
            pegawai_m.nama_pegawai,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::text AS hak_kelas,
            NULL::character varying AS kelaspelayanan_nama,
            '-'::text AS kelas_tagihan,
            false AS is_konsul,
            false AS is_pasientitipan,
            carabayar_m.carabayar_kode_warna,
            carabayar_m.carabayar_warna,
            buatjanjipoli_t.carabayar_id,
            buatjanjipoli_t.penjamin_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            buatjanjipoli_t.ruangan_id,
            NULL::integer AS kelaspelayanan_id,
            buatjanjipoli_t.pegawai_id,
            buatjanjipoli_t.status_janjipoli::integer AS status_periksa,
            look_statusjanji.lookup_name AS status_periksa_nama,
            false AS is_bayi,
            false AS is_stoppasientitipan,
            NULL::boolean AS is_pulang,
            NULL::boolean AS is_lunas,
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
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.no_antrian_global,
            antrian_t.no_antrian_global_with_date,
            false AS is_isisoap,
            peg_create.pegawai_id AS peg_create_id,
            peg_create.nama_pegawai AS peg_create_nama,
            buatjanjipoli_t.buatjanjipoli_id AS konsulpoli_id,
            NULL::boolean AS alergi,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.no_identitas_pasien,
            NULL::integer AS kamartempattidur_id,
            pendaftaran_t.additional_data,
            false AS temp_status_periksa,
            pendaftaran_t.rujukan_id,
            NULL::integer AS next_pendaftaran_id,
            NULL::text AS konsulpoli_dokter_nama,
            pasien_m.catatanpenting_pasien,
            pasien_m.pasien_id,
            NULL::text AS status_skrining,
            pasien_m.namadepan,
            bpjs_t.nosep
           FROM buatjanjipoli_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id,
                    a.no_pendaftaran,
                    a.jeniskasuspenyakit_id,
                    a.is_stopakomodasi,
                    a.keterangan_pendaftaran,
                    a.instalasi_id,
                    a.additional_data,
                    a.rujukan_id,
                    a.bpjs_id
                   FROM pendaftaran_t a) pendaftaran_t ON buatjanjipoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.no_identitas_pasien,
                    a.catatanpenting_pasien,
                    a.namadepan
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON buatjanjipoli_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.carabayar_warna
                   FROM carabayar_m a) carabayar_m ON buatjanjipoli_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON buatjanjipoli_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusjanji ON buatjanjipoli_t.status_janjipoli::integer = look_statusjanji.lookup_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON buatjanjipoli_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.no_antrian,
                    a.no_antrian_global,
                    a.no_antrian_global_with_date
                   FROM antrian_t a) antrian_t ON buatjanjipoli_t.antrian_id::integer = antrian_t.antrian_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON buatjanjipoli_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_create ON loginpemakai_k.pegawai_id = peg_create.pegawai_id) worklist
     LEFT JOIN ( SELECT x.pasien_id,
            string_agg(x.riwayat_alergi, ', '::text) AS riwayat_alergi
           FROM ( SELECT anamnesa_t.pasien_id,
                    concat(
                        CASE
                            WHEN COALESCE(anamnesa_t.alergi_obat, ''::text) = ''::text THEN ''::text
                            ELSE concat(anamnesa_t.alergi_obat, ', ')
                        END,
                        CASE
                            WHEN COALESCE(anamnesa_t.alergi_lainnya, ''::text) = ''::text THEN ''::text
                            ELSE concat(anamnesa_t.alergi_lainnya, ', ')
                        END,
                        CASE
                            WHEN COALESCE(anamnesa_t.riwayat_alergiobat, ''::text) = ''::text THEN ''::text
                            ELSE concat(anamnesa_t.riwayat_alergiobat, ', ')
                        END,
                        CASE
                            WHEN COALESCE(anamnesa_t.alergi_makanan, ''::text) = ''::text THEN ''::text
                            ELSE concat(anamnesa_t.alergi_makanan, ', ')
                        END) AS riwayat_alergi
                   FROM anamnesa_t
                  WHERE COALESCE(anamnesa_t.alergi_obat, ''::text) <> ''::text OR COALESCE(anamnesa_t.alergi_lainnya, ''::text) <> ''::text OR COALESCE(anamnesa_t.riwayat_alergiobat, ''::text) <> ''::text OR COALESCE(anamnesa_t.alergi_makanan, ''::text) <> ''::text
                UNION ALL
                 SELECT pendaftaran_t.pasien_id,
                    concat(
                        CASE
                            WHEN COALESCE(asesmenperawatrd_t.alergi_obat, ''::text) = ''::text THEN ''::text
                            ELSE concat(asesmenperawatrd_t.alergi_obat, ', ')
                        END,
                        CASE
                            WHEN COALESCE(asesmenperawatrd_t.alergi_lainnya, ''::text) = ''::text THEN ''::text
                            ELSE concat(asesmenperawatrd_t.alergi_lainnya, ', ')
                        END) AS riwayat_alergi
                   FROM asesmenperawatrd_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE COALESCE(asesmenperawatrd_t.alergi_obat, ''::text) <> ''::text OR COALESCE(asesmenperawatrd_t.alergi_lainnya, ''::text) <> ''::text
                UNION ALL
                 SELECT pendaftaran_t.pasien_id,
                    concat(
                        CASE
                            WHEN COALESCE(asesmenawal_t.additional_data::json ->> 'alergi_obat'::text, ''::text) = ''::text THEN ''::text
                            ELSE concat(asesmenawal_t.additional_data::json ->> 'alergi_obat'::text, ', ')
                        END,
                        CASE
                            WHEN COALESCE(asesmenawal_t.additional_data::json ->> 'alergi_lainnya'::text, ''::text) = ''::text THEN ''::text
                            ELSE concat(asesmenawal_t.additional_data::json ->> 'alergi_lainnya'::text)
                        END) AS riwayat_alergi
                   FROM asesmenawal_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE asesmenawal_t.additional_data IS NOT NULL) x
          GROUP BY x.pasien_id) alergi ON worklist.pasien_id = alergi.pasien_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) lkp_nama_depan ON worklist.namadepan::integer = lkp_nama_depan.lookup_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240102_082848_migrate_GLS_282_improve_infopasienrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240102_082848_migrate_GLS_282_improve_infopasienrs_v cannot be reverted.\n";

        return false;
    }
    */
}
