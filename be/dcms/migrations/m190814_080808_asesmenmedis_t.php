<?php

use yii\db\Migration;

/**
 * Class m190814_080808_asesmenmedis_t
 */
class m190814_080808_asesmenmedis_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienri_v;');

        $this->execute('DROP VIEW if exists public.asuhangizi_v;');

        $this->execute('DROP VIEW if exists public.infopasiengizi_v;');

        $this->execute('DROP VIEW if exists public.infopasiengizi_v_old;');


        $this->execute('ALTER TABLE "public"."asesmenmedis_t" 
                        ALTER COLUMN "diagnosa_id" TYPE text USING "diagnosa_id"::text;');

        $this->execute("CREATE OR REPLACE VIEW public.infopasienri_v AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
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
    status_ranap.lookup_name AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
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
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
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
    COALESCE(tagihan.sub_total, 0::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, 0::double precision) AS tarif_inacbg,
    carabayar_m.groupcarabayar_id AS group_carabayar,
        CASE
            WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     JOIN lookup_m jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     LEFT JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN lookup_m status_ranap ON pasienadmisi_t.status_ranap = status_ranap.lookup_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id
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
           FROM monitorsetdiagnosa_t
             JOIN diagnosa_m ON monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id
          WHERE monitorsetdiagnosa_t.is_deleted = false) monitorsetdiagnosa ON pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false;");



        $this->execute("CREATE OR REPLACE VIEW public.asuhangizi_v AS 
 SELECT pasien.no_rekam_medik,
    pasien.pendaftaran_id,
    pasien.tgl_pendaftaran,
    pasien.no_pendaftaran,
    pasien.nama_pasien,
    pasien.jenis_kelamin,
    pasien.jeniskasuspenyakit_nama,
    pasien.tanggal_lahir,
    pasien.umur,
    pasien.dokter_admisi,
    pasien.kelaspelayanan_nama,
    pasien.carabayar_nama,
    pasien.penjamin_nama,
    pasien.r_penyakitkeluarga,
    pasien.is_merokok,
    pasien.jml_rokok,
    t.gizi_makanan,
    t.antropometri,
    t.biokimia,
    t.fisikklinis_gizi,
    t.diagnosa_gizi,
    t.intervensi_gizi,
    t.rencana_gizi,
    t.asuhangizi_id,
    t.created_date,
    pasien.ruangan_nama,
    pasien.kamarruangan_nokamar,
    pasien.no_tempattidur,
    t.peg_gizi_id AS dietisen_id,
    dietisen.nama_pegawai AS dietisen_nama,
    asesmenmedis_t.r_peskk,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama
   FROM asuhangizi_t t
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            kelaspelayanan_m.kelaspelayanan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            asesmenmedis_t_1.r_penyakitkeluarga,
            asesmenmedis_t_1.is_merokok,
            asesmenmedis_t_1.jml_rokok,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN asesmenmedis_t asesmenmedis_t_1 ON pendaftaran_t.pendaftaran_id = asesmenmedis_t_1.pendaftaran_id AND asesmenmedis_t_1.is_deleted = false
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id) pasien ON t.pendaftaran_id = pasien.pendaftaran_id
     LEFT JOIN asesmenmedis_t ON t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     JOIN pegawai_m dietisen ON t.peg_gizi_id = dietisen.pegawai_id;");


         $this->execute('ALTER TABLE public.asuhangizi_v
                        OWNER TO postgres;');

          $this->execute("CREATE OR REPLACE VIEW public.infopasiengizi_v AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kls_bpjs.kelaspelayanan_nama AS hak_kelas_nama,
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
    status_ranap.lookup_name AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
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
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    skrininggizi_t.skrininggizi_id,
    skrininggizi_t.skor,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 80
            ELSE skrininggizi_t.status_asesmen
        END AS status_asesmen,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 'Tidak Asesmen'::character varying
            ELSE asmen_gizi.lookup_name
        END AS stat_asesmen_gizi,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenawal_t.asmen_riwayat,
    asesmenmedis_t.r_peskk
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_bpjs ON bpjs_t.klsrawat = kls_bpjs.bpjs_kelas
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN lookup_m status_ranap ON pasienadmisi_t.status_ranap = status_ranap.lookup_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN lookupkeperawatan_m asmen_gizi ON skrininggizi_t.status_asesmen = asmen_gizi.lookupkeperawatan_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453;
");

           $this->execute('ALTER TABLE public.infopasiengizi_v
                        OWNER TO postgres;');



        $this->execute("CREATE OR REPLACE VIEW public.infopasiengizi_v_old AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kls_bpjs.kelaspelayanan_nama AS hak_kelas_nama,
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
    status_ranap.lookup_name AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
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
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    skrininggizi_t.skrininggizi_id,
    skrininggizi_t.skor,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 80
            ELSE skrininggizi_t.status_asesmen
        END AS status_asesmen,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 'Tidak Asesmen'::character varying
            ELSE asmen_gizi.lookup_name
        END AS stat_asesmen_gizi,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    ( SELECT count(*) AS count
           FROM asuhangizi_t
          WHERE asuhangizi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AS jml_data_asuhan,
    asesmenawalgizi_t.asesmenawalgizi_id
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_bpjs ON bpjs_t.klsrawat = kls_bpjs.bpjs_kelas
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN lookup_m status_ranap ON pasienadmisi_t.status_ranap = status_ranap.lookup_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN lookupkeperawatan_m asmen_gizi ON skrininggizi_t.status_asesmen = asmen_gizi.lookupkeperawatan_id
     LEFT JOIN asesmenawalgizi_t ON asesmenawalgizi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453;
");

        $this->execute('ALTER TABLE public.infopasiengizi_v_old
                        OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190814_080808_asesmenmedis_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190814_080808_asesmenmedis_t cannot be reverted.\n";

        return false;
    }
    */
}
