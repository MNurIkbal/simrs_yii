<?php

use yii\db\Migration;

/**
 * Class m250827_065458_gls_1029_sy_kunjungan_alter_dokter_nama_text
 */
class m250827_065458_gls_1029_sy_kunjungan_alter_dokter_nama_text extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // View terikat
        $this->execute('DROP VIEW if exists public.sy_infopasienbpjsklaimlist_v;');
        $this->execute('DROP VIEW if exists public.sy_koreksidiagnosa_v;');
        $this->execute('DROP VIEW if exists public.koreksidiagnosa_new_v;');
        $this->execute('DROP VIEW if exists public.sy_infopasienbpjs_v;');
        $this->execute('DROP VIEW if exists public.laporanjasapelayananrajal_v;');
        $this->execute('DROP VIEW if exists public.sy_infopasienbpjsklaim_v;');
        $this->execute('DROP VIEW if exists public.dh_kunjungan_new_v;');

        // alter table
        $this->execute('ALTER TABLE "public"."sy_kunjungan" ALTER COLUMN "dokter_nama" TYPE text COLLATE "pg_catalog"."default" USING "dokter_nama"::text;');
        
        // sy_infopasienbpjsklaimlist_v
        $this->execute("
            CREATE OR REPLACE VIEW public.sy_infopasienbpjsklaimlist_v
            AS SELECT sy_kunjungan.kunjungan_id,
                pendaftaran_t.pendaftaran_id,
                sy_kunjungan.no_pendaftaran,
                sy_kunjungan.no_rekammedik,
                sy_kunjungan.nama_pasien,
                sy_kunjungan.jenis_kelamin,
                sy_kunjungan.tgl_lahir,
                sy_kunjungan.umur,
                sy_kunjungan.tgl_pendaftaran,
                COALESCE(pendaftaran_t.tgl_pulang, sy_kunjungan.tgl_pulang) AS tgl_pulang,
                sy_kunjungan.instalasi_kode,
                sy_kunjungan.instalasi_nama,
                sy_kunjungan.ruangan_kode,
                sy_kunjungan.ruangan_nama,
                sy_kunjungan.carabayar_kode,
                sy_kunjungan.carabayar_nama,
                sy_kunjungan.penjamin_kode,
                sy_kunjungan.penjamin_nama,
                sy_kunjungan.kelas_kode,
                sy_kunjungan.kelas_nama,
                sy_kunjungan.dokter_kode,
                sy_kunjungan.dokter_nama,
                sy_kunjungan.no_sep,
                sy_kunjungan.status_kunjungan,
                sy_kunjungan.no_kamar,
                sy_kunjungan.no_tempattidur,
                sy_kunjungan.hak_kelasbpjs,
                sy_kunjungan.carakeluar_kode,
                sy_kunjungan.lama_rawat,
                sy_kunjungan.no_asuransi,
                sy_kunjungan.is_verifikasi,
                sy_kunjungan.total_verifikasi,
                sy_kunjungan.tgl_verifikasi,
                sy_kunjungan.identitas_id,
                sy_kunjungan.identitas_nama,
                sy_kunjungan.identitas_value,
                sy_kunjungan.no_klaimcovid,
                sy_kunjungan.pasien_id,
                sy_kunjungan.additional_data,
                sy_kunjungan.created_date,
                sy_kunjungan.created_by,
                sy_kunjungan.modified_count,
                sy_kunjungan.last_modified_date,
                sy_kunjungan.last_modified_by,
                sy_kunjungan.is_deleted,
                sy_kunjungan.is_active,
                sy_kunjungan.deleted_date,
                sy_kunjungan.deleted_by,
                sy_kunjungan.nosep,
                sy_kunjungan.jeniskasuspenyakit_id,
                sy_kunjungan.jeniskasuspenyakit_nama,
                sy_kunjungan.instalasi_id,
                sy_kunjungan.ruangan_id,
                sy_kunjungan.status_unduh_dokumen,
                    CASE
                        WHEN (EXISTS ( SELECT sy_kunjungantagihan.kunjungan_id
                        FROM sy_kunjungantagihan
                        WHERE sy_kunjungantagihan.kunjungan_id = sy_kunjungan.kunjungan_id
                        LIMIT 1)) THEN ( SELECT sum(sy_kunjungantagihan.layanan_tarif) AS sum
                        FROM sy_kunjungantagihan
                        WHERE sy_kunjungantagihan.kunjungan_id = sy_kunjungan.kunjungan_id)
                        ELSE 0::numeric
                    END AS tarif_rs,
                    CASE
                        WHEN (EXISTS ( SELECT sy_klaiminacbg.kunjungan_id,
                            tagihan.tarif_inacbg
                        FROM sy_klaiminacbg
                            JOIN ( SELECT sy_klaimgroup_t.sy_klaimgroup_id,
                                    sum(sy_klaimgroup_t.total) AS tarif_inacbg
                                FROM sy_klaimgroup_t
                                GROUP BY sy_klaimgroup_t.sy_klaimgroup_id) tagihan ON sy_klaiminacbg.klaimgroup_id = tagihan.sy_klaimgroup_id
                        WHERE sy_klaiminacbg.is_deleted IS FALSE AND sy_klaiminacbg.is_active IS TRUE AND sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id
                        LIMIT 1)) THEN ( SELECT sum(tagihan.tarif_inacbg) AS sum
                        FROM sy_klaiminacbg
                            JOIN ( SELECT sy_klaimgroup_t.sy_klaimgroup_id,
                                    sum(sy_klaimgroup_t.total) AS tarif_inacbg
                                FROM sy_klaimgroup_t
                                GROUP BY sy_klaimgroup_t.sy_klaimgroup_id) tagihan ON sy_klaiminacbg.klaimgroup_id = tagihan.sy_klaimgroup_id
                        WHERE sy_klaiminacbg.is_deleted IS FALSE AND sy_klaiminacbg.is_active IS TRUE AND sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id)
                        ELSE 0::double precision
                    END AS plafon,
                sy_kunjungan.no_pembayaran,
                    CASE
                        WHEN sy_kunjungan.instalasi_kode::text = 'RI'::text THEN pasienadmisi_t.bpjs_id
                        ELSE pendaftaran_t.bpjs_id
                    END AS bpjs_id
            FROM sy_kunjungan
                JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.tgl_stopakomodasi AS tgl_pulang,
                        a.pasienadmisi_id,
                        a.bpjs_id,
                        a.tgl_pendaftaran
                    FROM pendaftaran_t a
                    ORDER BY a.pasienadmisi_id) pendaftaran_t ON sy_kunjungan.no_pendaftaran::text = pendaftaran_t.no_pendaftaran::text
                LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.bpjs_id
                    FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            WHERE sy_kunjungan.is_deleted IS FALSE AND sy_kunjungan.is_active IS TRUE;
        ");

        // sy_koreksidiagnosa_v
        $this->execute("
            CREATE OR REPLACE VIEW public.sy_koreksidiagnosa_v
            AS SELECT sy_koreksidiagnosa.sy_koreksidiagnosa_id AS koreksidiagnosa_id,
                sy_koreksidiagnosa.kunjungan_id,
                sy_kunjungan.no_pendaftaran,
                sy_kunjungan.no_rekammedik,
                sy_kunjungan.nama_pasien,
                sy_kunjungan.dokter_nama AS dokter_dpjp,
                sy_koreksidiagnosa.tgl_koreksidiagnosa,
                sy_koreksidiagnosa.kelompokdiagnosa_id,
                kelompokdiagnosa_m.kelompokdiagnosa_nama,
                sy_koreksidiagnosa.diagnosa_id,
                diagnosa_m.diagnosa_kode,
                diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
                sy_koreksidiagnosa.diagnosaasal_id,
                sy_koreksidiagnosa.diag_asal_masuk,
                sy_koreksidiagnosa.diag_asal_utama,
                sy_koreksidiagnosa.diag_asal_penyerta,
                sy_koreksidiagnosa.diag_asal_terapi,
                sy_koreksidiagnosa.is_inacbg,
                sy_koreksidiagnosa.is_icdprimer,
                sy_koreksidiagnosa.is_deleted,
                sy_koreksidiagnosa.is_active,
                kelompokdiagnosa_m.kelompokdiagnosa_namalainnya AS kelompok_diagnosa
            FROM sy_koreksidiagnosa
                JOIN sy_kunjungan ON sy_koreksidiagnosa.kunjungan_id = sy_kunjungan.kunjungan_id
                JOIN kelompokdiagnosa_m ON sy_koreksidiagnosa.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
                JOIN diagnosa_m ON sy_koreksidiagnosa.diagnosa_id = diagnosa_m.diagnosa_id
            WHERE sy_koreksidiagnosa.is_deleted = false;
        ");

        // koreksidiagnosa_new_v
        $this->execute("
            CREATE OR REPLACE VIEW public.koreksidiagnosa_new_v
            AS SELECT sy_koreksidiagnosa.sy_koreksidiagnosa_id,
                sy_koreksidiagnosa.kunjungan_id,
                sy_kunjungan.no_pendaftaran,
                sy_kunjungan.pasien_id,
                sy_kunjungan.no_rekammedik,
                sy_kunjungan.nama_pasien,
                sy_kunjungan.dokter_kode,
                sy_kunjungan.dokter_nama AS dokter_dpjp,
                sy_koreksidiagnosa.tgl_koreksidiagnosa,
                sy_koreksidiagnosa.kelompokdiagnosa_id,
                kelompokdiagnosa_m.kelompokdiagnosa_nama,
                sy_koreksidiagnosa.diagnosa_id,
                diagnosa_m.diagnosa_kode,
                diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
                sy_koreksidiagnosa.diagnosaasal_id,
                sy_koreksidiagnosa.diag_asal_masuk,
                sy_koreksidiagnosa.diag_asal_utama,
                sy_koreksidiagnosa.diag_asal_penyerta,
                sy_koreksidiagnosa.diag_asal_terapi,
                sy_koreksidiagnosa.is_inacbg,
                sy_koreksidiagnosa.is_icdprimer,
                sy_koreksidiagnosa.is_deleted,
                sy_koreksidiagnosa.is_active,
                kelompokdiagnosa_m.kelompokdiagnosa_namalainnya AS kelompok_diagnosa,
                sy_koreksidiagnosa.is_inagrouper
            FROM sy_koreksidiagnosa
                JOIN sy_kunjungan ON sy_koreksidiagnosa.kunjungan_id = sy_kunjungan.kunjungan_id
                JOIN kelompokdiagnosa_m ON sy_koreksidiagnosa.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
                JOIN diagnosa_m ON sy_koreksidiagnosa.diagnosa_id = diagnosa_m.diagnosa_id
            WHERE sy_koreksidiagnosa.is_deleted = false
            ORDER BY sy_koreksidiagnosa.sy_koreksidiagnosa_id;
        ");

        // sy_infopasienbpjs_v
        $this->execute("
            CREATE OR REPLACE VIEW public.sy_infopasienbpjs_v
            AS SELECT sy_kunjungan.kunjungan_id,
                sy_kunjungan.no_pendaftaran,
                sy_kunjungan.no_rekammedik,
                sy_kunjungan.pasien_id,
                sy_kunjungan.nama_pasien,
                sy_kunjungan.jenis_kelamin,
                sy_kunjungan.tgl_lahir,
                sy_kunjungan.umur,
                sy_kunjungan.tgl_pendaftaran,
                COALESCE(akomodasi_ranap.tgl_pulang, sy_kunjungan.tgl_pulang) AS tgl_pulang,
                sy_kunjungan.instalasi_kode,
                sy_kunjungan.instalasi_nama,
                sy_kunjungan.ruangan_kode,
                sy_kunjungan.ruangan_nama,
                sy_kunjungan.carabayar_kode,
                sy_kunjungan.carabayar_nama,
                sy_kunjungan.penjamin_kode,
                sy_kunjungan.penjamin_nama,
                sy_kunjungan.kelas_kode,
                sy_kunjungan.kelas_nama,
                sy_kunjungan.dokter_kode,
                sy_kunjungan.dokter_nama,
                sy_kunjungan.no_sep,
                sy_kunjungan.status_kunjungan,
                sy_kunjungan.no_kamar,
                sy_kunjungan.no_tempattidur,
                sy_kunjungan.hak_kelasbpjs,
                sy_kunjungan.carakeluar_kode,
                sy_kunjungan.lama_rawat,
                sy_kunjungan.no_asuransi,
                sy_kunjungan.is_verifikasi,
                sy_kunjungan.total_verifikasi,
                sy_kunjungan.tgl_verifikasi,
                sy_kunjungan.identitas_id,
                sy_kunjungan.identitas_nama,
                sy_kunjungan.identitas_value,
                sy_kunjungan.no_klaimcovid,
                sy_kunjungan.nosep,
                look_verifikasi.verifikasi,
                COALESCE(tagihan.layanan_tarif, 0::numeric) AS layanan_tarif,
                COALESCE(tagihan.jasa_rs, 0::numeric) AS jasa_rs,
                COALESCE(tagihan.jasa_dokter, 0::numeric) AS jasa_dokter,
                ((((sy_kunjungan.info_response_bpjs::json -> 'peserta'::text) ->> 'hakKelas'::text)::json) -> 'kode'::text)::character varying AS peserta_hakkelas,
                sy_kunjungan.sistole,
                sy_kunjungan.diastole,
                sy_kunjungan.caramasuk
            FROM sy_kunjungan
                JOIN ( SELECT a.lookup_id,
                        a.lookup_name AS verifikasi
                    FROM lookup_m a) look_verifikasi ON sy_kunjungan.status_kunjungan = look_verifikasi.lookup_id
                LEFT JOIN ( SELECT a.kunjungan_id,
                        sum(a.layanan_tarif) AS layanan_tarif,
                        sum(a.jasa_rs) AS jasa_rs,
                        sum(a.jasa_dokter) AS jasa_dokter
                    FROM sy_kunjungantagihan a
                    WHERE a.is_deleted = false
                    GROUP BY a.kunjungan_id) tagihan ON sy_kunjungan.kunjungan_id = tagihan.kunjungan_id
                LEFT JOIN ( SELECT DISTINCT pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_stopakomodasi AS tgl_pulang
                    FROM pendaftaran_t
                    WHERE pendaftaran_t.pasienadmisi_id IS NOT NULL) akomodasi_ranap ON sy_kunjungan.no_pendaftaran::text = akomodasi_ranap.no_pendaftaran::text
            WHERE sy_kunjungan.is_active = true AND sy_kunjungan.is_deleted = false;
        ");

        // laporanjasapelayananrajal_v
        $this->execute("
            CREATE OR REPLACE VIEW public.laporanjasapelayananrajal_v
            AS WITH pelayanan_rajal AS (
                    SELECT sy_kunjungan.nosep,
                        sy_kunjungan.no_pendaftaran,
                        sy_kunjungan.tgl_pendaftaran,
                        sy_klaimgroup_t.total AS total_tarif_jkn,
                        sy_klaimgroup_t.total * 44::double precision / 100::double precision AS tarif_sarana,
                        sy_klaimgroup_t.total * 56::double precision / 100::double precision AS tarif_pelayanan,
                        sy_klaiminacbg.total_tarifrs AS total_tarif_rs,
                        sy_kunjungan.no_rekammedik,
                        sy_kunjungan.nama_pasien,
                        sy_kunjungan.dokter_nama AS dpjp,
                        konsul.dokter_konsul,
                        konsul.num_konsul,
                        radiologi.dokter_radiologi,
                        radiologi.num_radiologi,
                        operasi.tindakan_operasi,
                        sy_kunjungan.ruangan_nama
                    FROM sy_klaiminacbg
                        JOIN sy_kunjungan ON sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id AND (sy_kunjungan.instalasi_nama::text = ANY (ARRAY['Rawat Jalan'::character varying::text, 'IGD'::character varying::text])) AND sy_kunjungan.status_kunjungan = 551
                        JOIN sy_klaimgroup_t ON sy_klaiminacbg.klaimgroup_id = sy_klaimgroup_t.sy_klaimgroup_id
                        JOIN pendaftaran_t ON sy_kunjungan.no_pendaftaran::text = pendaftaran_t.no_pendaftaran::text AND pendaftaran_t.status_periksa::integer <> 433
                        LEFT JOIN ( SELECT a.pendaftaran_id,
                                pegawai_m.nama_pegawai AS dokter_konsul,
                                row_number() OVER (PARTITION BY a.pendaftaran_id ORDER BY a.konsulpoli_id) AS num_konsul
                            FROM konsulpoli_t a
                                JOIN pegawai_m ON a.pegawai_id = pegawai_m.pegawai_id
                            WHERE (a.status_periksa::integer <> ALL (ARRAY[402, 411])) AND a.status_approve = 565) konsul ON pendaftaran_t.pendaftaran_id = konsul.pendaftaran_id
                        LEFT JOIN ( SELECT a.pendaftaran_id,
                                pegawai_m.nama_pegawai AS dokter_radiologi,
                                row_number() OVER (PARTITION BY a.pendaftaran_id ORDER BY a.pasienmasukpenunjang_id) AS num_radiologi
                            FROM pasienmasukpenunjang_t a
                                JOIN ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5 AND ruangan_m.is_deleted = false AND ruangan_m.is_active = true
                                JOIN tindakanpelayanan_t ON a.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
                                JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
                            WHERE a.status_periksa::integer <> 476) radiologi ON pendaftaran_t.pendaftaran_id = radiologi.pendaftaran_id
                        LEFT JOIN ( SELECT a.pendaftaran_id,
                                string_agg(daftartindakan_m.daftartindakan_nama::text, ', '::text) AS tindakan_operasi
                            FROM pasienmasukpenunjang_t a
                                JOIN ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 12 AND ruangan_m.is_deleted = false AND ruangan_m.is_active = true
                                JOIN tindakanpelayanan_t ON a.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
                                JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                            WHERE a.status_periksa::integer <> 476
                            GROUP BY a.pendaftaran_id) operasi ON pendaftaran_t.pendaftaran_id = operasi.pendaftaran_id
                    WHERE sy_klaiminacbg.is_deleted = false AND sy_klaiminacbg.is_active = true
                    )
            SELECT pelayanan_rajal.nosep AS \"No SEP\",
                pelayanan_rajal.no_pendaftaran AS \"No Pendaftaran\",
                pelayanan_rajal.tgl_pendaftaran AS \"Tgl Pendaftaran\",
                pelayanan_rajal.total_tarif_jkn AS \"Total Tarif JKN\",
                pelayanan_rajal.tarif_sarana AS \"Sarana\",
                pelayanan_rajal.tarif_pelayanan AS \"Pelayanan\",
                pelayanan_rajal.total_tarif_rs AS \"Total Tarif RS\",
                pelayanan_rajal.no_rekammedik AS \"No RM\",
                pelayanan_rajal.nama_pasien AS \"Nama Pasien\",
                pelayanan_rajal.dpjp AS \"DPJP\",
                max(
                    CASE
                        WHEN pelayanan_rajal.num_konsul = 1 THEN pelayanan_rajal.dokter_konsul
                        ELSE NULL::character varying
                    END::text) AS \"Konsul 1\",
                max(
                    CASE
                        WHEN pelayanan_rajal.num_konsul = 2 THEN pelayanan_rajal.dokter_konsul
                        ELSE NULL::character varying
                    END::text) AS \"Konsul 2\",
                max(
                    CASE
                        WHEN pelayanan_rajal.num_konsul = 3 THEN pelayanan_rajal.dokter_konsul
                        ELSE NULL::character varying
                    END::text) AS \"Konsul 3\",
                max(
                    CASE
                        WHEN pelayanan_rajal.num_konsul = 4 THEN pelayanan_rajal.dokter_konsul
                        ELSE NULL::character varying
                    END::text) AS \"Konsul 4\",
                max(
                    CASE
                        WHEN pelayanan_rajal.num_konsul = 5 THEN pelayanan_rajal.dokter_konsul
                        ELSE NULL::character varying
                    END::text) AS \"Konsul 5\",
                max(
                    CASE
                        WHEN pelayanan_rajal.num_radiologi = 1 THEN pelayanan_rajal.dokter_radiologi
                        ELSE NULL::character varying
                    END::text) AS \"Dokter Radiologi 1\",
                max(
                    CASE
                        WHEN pelayanan_rajal.num_radiologi = 2 THEN pelayanan_rajal.dokter_radiologi
                        ELSE NULL::character varying
                    END::text) AS \"Dokter Radiologi 2\",
                pelayanan_rajal.tindakan_operasi AS \"Operasi\",
                pelayanan_rajal.ruangan_nama AS \"Unit\"
            FROM pelayanan_rajal
            GROUP BY pelayanan_rajal.nosep, pelayanan_rajal.no_pendaftaran, pelayanan_rajal.tgl_pendaftaran, pelayanan_rajal.total_tarif_jkn, pelayanan_rajal.tarif_sarana, pelayanan_rajal.tarif_pelayanan, pelayanan_rajal.total_tarif_rs, pelayanan_rajal.no_rekammedik, pelayanan_rajal.nama_pasien, pelayanan_rajal.dpjp, pelayanan_rajal.tindakan_operasi, pelayanan_rajal.ruangan_nama;
        ");

        // sy_infopasienbpjsklaim_v
        $this->execute("
            CREATE OR REPLACE VIEW public.sy_infopasienbpjsklaim_v
            AS SELECT sy_kunjungantagihan.kunjungantagihan_id,
                sy_kunjungantagihan.kunjungan_id,
                sy_kunjungan.no_pendaftaran,
                sy_kunjungan.no_rekammedik,
                sy_kunjungan.instalasi_kode,
                sy_kunjungan.tgl_pendaftaran,
                COALESCE(akomodasi_ranap.tgl_pulang, sy_kunjungan.tgl_pulang) AS tgl_pulang,
                sy_kunjungantagihan.ruangan_kode,
                sy_kunjungantagihan.ruangan_nama,
                sy_kunjungantagihan.dokter_kode,
                sy_kunjungantagihan.dokter_nama,
                sy_kunjungantagihan.layanan_qty,
                sy_kunjungantagihan.layanan_kode,
                sy_kunjungantagihan.layanan_nama,
                sy_kunjungantagihan.layanan_tarif,
                sy_kunjungantagihan.jasa_rs,
                sy_kunjungantagihan.jasa_dokter,
                sy_kunjungantagihan.created_date,
                sy_kunjungantagihan.groupinacbg_nama
            FROM sy_kunjungantagihan
                JOIN ( SELECT a.kunjungan_id,
                        a.no_pendaftaran,
                        a.no_rekammedik,
                        a.instalasi_kode,
                        a.tgl_pendaftaran,
                        a.tgl_pulang
                    FROM sy_kunjungan a) sy_kunjungan ON sy_kunjungan.kunjungan_id = sy_kunjungantagihan.kunjungan_id
                LEFT JOIN ( SELECT DISTINCT pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_stopakomodasi AS tgl_pulang
                    FROM pendaftaran_t
                    WHERE pendaftaran_t.pasienadmisi_id IS NOT NULL) akomodasi_ranap ON sy_kunjungan.no_pendaftaran::text = akomodasi_ranap.no_pendaftaran::text
            WHERE sy_kunjungantagihan.is_active = true AND sy_kunjungantagihan.is_deleted = false;
        ");

        // dh_kunjungan_new_v
        $this->execute("
            CREATE OR REPLACE VIEW public.dh_kunjungan_new_v
            AS SELECT sy_kunjungan.kunjungan_id,
                sy_kunjungan.no_pendaftaran,
                sy_kunjungan.no_rekammedik,
                sy_kunjungan.nama_pasien,
                sy_kunjungan.jenis_kelamin,
                sy_kunjungan.tgl_lahir,
                sy_kunjungan.umur,
                sy_kunjungan.tgl_pendaftaran,
                sy_kunjungan.tgl_pulang,
                sy_kunjungan.instalasi_kode,
                sy_kunjungan.instalasi_nama,
                sy_kunjungan.ruangan_kode,
                sy_kunjungan.ruangan_nama,
                sy_kunjungan.carabayar_kode,
                sy_kunjungan.carabayar_nama,
                sy_kunjungan.penjamin_kode,
                sy_kunjungan.penjamin_nama,
                sy_kunjungan.kelas_kode,
                sy_kunjungan.kelas_nama,
                sy_kunjungan.dokter_kode,
                sy_kunjungan.dokter_nama,
                sy_kunjungan.no_sep,
                sy_kunjungan.status_kunjungan,
                sy_kunjungan.no_kamar,
                sy_kunjungan.no_tempattidur,
                sy_kunjungan.hak_kelasbpjs,
                sy_kunjungan.carakeluar_kode,
                sy_kunjungan.lama_rawat,
                sy_kunjungan.no_asuransi,
                sy_kunjungan.is_verifikasi,
                sy_kunjungan.total_verifikasi,
                sy_kunjungan.tgl_verifikasi,
                sy_kunjungan.identitas_id,
                sy_kunjungan.identitas_nama,
                sy_kunjungan.identitas_value,
                sy_kunjungan.no_klaimcovid,
                sy_kunjungan.pasien_id,
                sy_kunjungan.additional_data,
                sy_kunjungan.created_date,
                sy_kunjungan.created_by,
                sy_kunjungan.modified_count,
                sy_kunjungan.last_modified_date,
                sy_kunjungan.last_modified_by,
                sy_kunjungan.is_deleted,
                sy_kunjungan.is_active,
                sy_kunjungan.deleted_date,
                sy_kunjungan.deleted_by,
                sy_kunjungan.nosep,
                look_verifikasi.lookup_id,
                look_verifikasi.verifikasi
            FROM sy_kunjungan
                JOIN ( SELECT a.lookup_id,
                        a.lookup_name AS verifikasi
                    FROM lookup_m a) look_verifikasi ON sy_kunjungan.status_kunjungan = look_verifikasi.lookup_id
            WHERE sy_kunjungan.is_active = true AND sy_kunjungan.is_deleted = false AND (sy_kunjungan.status_kunjungan = ANY (ARRAY[549, 550, 551, 556]));
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250827_065458_gls_1029_sy_kunjungan_alter_dokter_nama_text cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250827_065458_gls_1029_sy_kunjungan_alter_dokter_nama_text cannot be reverted.\n";

        return false;
    }
    */
}
