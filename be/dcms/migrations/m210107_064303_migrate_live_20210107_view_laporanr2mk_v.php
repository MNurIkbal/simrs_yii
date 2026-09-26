<?php

use yii\db\Migration;

/**
 * Class m210107_064303_migrate_live_20210107_view_laporanr2mk_v
 */
class m210107_064303_migrate_live_20210107_view_laporanr2mk_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanr2mk_v;');
        $this->execute("CREATE VIEW \"public\".\"laporanr2mk_v\" AS
             SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
    pegawai_m.nama_pegawai AS nama_dokter,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.tempat_lahir,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    fgetnamalookup((pasien_m.warga_negara)::integer) AS kebangsaan,
    suku_m.suku_nama,
        CASE
            WHEN (pasien_m.no_identitas_pasien IS NULL) THEN (pasien_m.additional_pasien)::character varying
            ELSE pasien_m.no_identitas_pasien
        END AS no_identitas_pasien,
    fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan,
    fgetnamalookup((pasien_m.agama)::integer) AS agama,
    asuransipasien_m.nokartuasuransi AS no_asuransi_pasien,
    pasien_m.no_telepon_pasien,
    NULL::text AS pemberitahuan,
    penanggungjawab_m.penanggungjawab_nama,
    penanggungjawab_m.penanggungjawab_alamat,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.no_identitas AS no_identitas_penanggung,
    pendidikan_m.pendidikan_nama AS pendidikan_pasien,
        CASE
            WHEN (pendaftaran_t.instalasi_id = 2) THEN 'Dari Rawat Darurat'::text
            ELSE 'Dari Rawat Jalan'::text
        END AS prosedurmasuk_rs,
    pekerjaan_m.pekerjaan_nama,
    NULL::text AS alamat_kantor,
        CASE
            WHEN (pendaftaran_t.instalasi_id <> 2) THEN (NULL::text)::character varying
            ELSE dokter_pengirim.nama_pegawai
        END AS dokter_pengirim,
    fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas,
    ruangan_m.ruangan_nama,
    kamartempattidur_m.no_tempattidur,
    ((asesmenmedis_t.diagnosa_id ->> 'text'::text))::character varying AS diagnosa_masuk,
    d_utama.diag_utama_kode,
    d_utama.diag_utama,
    d_penyerta.diag_penyerta_kode,
    d_penyerta.diag_penyerta,
    carakeluar_m.carakeluar_nama,
    NULL::text AS penyulit,
    NULL::text AS penyebab_kematian,
    NULL::text AS tgl_jam,
    NULL::text AS operasi_tindakan,
    pasienpulang_t.lama_rawat,
    NULL::text AS infeksi_nosokomial,
    pasienpulang_t.tglpasienpulang AS tgl_keluar,
    asesmenawal_t.nama_alergi AS alergi_obat,
    NULL::text AS alergi_lainnya,
        CASE
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
            ELSE false
        END AS is_stoppasientitipan,
        CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE pindah_kamar.kelas_ditagihkan_id
        END AS kelas_ditagihkan_id,
        CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN kelas_ditagihkan.kelaspelayanan_nama
            ELSE pindah_kamar.kelas_ditagihkan
        END AS kelas_ditagihkan_nama,
    pasienadmisi_t.is_pasientitipan,
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.additional_data
   FROM (((((((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dokter_pengirim ON ((pendaftaran_t.pegawai_id = dokter_pengirim.pegawai_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN asesmenmedis_t ON ((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id)))
     LEFT JOIN asesmenawal_t ON ((pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id)))
     LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            koreksidiagnosa_t.koreksidiagnosa_id,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_namalainnya AS diag_utama
           FROM ((koreksidiagnosa_t
             JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    max(koreksidiagnosa_t_1.koreksidiagnosa_id) AS utamamax_id
                   FROM (koreksidiagnosa_t koreksidiagnosa_t_1
                     JOIN pendaftaran_t pendaftaran_t_1 ON ((koreksidiagnosa_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
                  WHERE ((koreksidiagnosa_t_1.is_deleted = false) AND (koreksidiagnosa_t_1.kelompokdiagnosa_id = 2))
                  GROUP BY pendaftaran_t_1.pendaftaran_id) max_utama ON (((koreksidiagnosa_t.pendaftaran_id = max_utama.pendaftaran_id) AND (koreksidiagnosa_t.koreksidiagnosa_id = max_utama.utamamax_id))))) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
     LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            koreksidiagnosa_t.koreksidiagnosa_id,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_namalainnya AS diag_penyerta
           FROM ((koreksidiagnosa_t
             JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    max(koreksidiagnosa_t_1.koreksidiagnosa_id) AS utamamax_id
                   FROM (koreksidiagnosa_t koreksidiagnosa_t_1
                     JOIN pendaftaran_t pendaftaran_t_1 ON ((koreksidiagnosa_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
                  WHERE ((koreksidiagnosa_t_1.is_deleted = false) AND (koreksidiagnosa_t_1.kelompokdiagnosa_id = 3))
                  GROUP BY pendaftaran_t_1.pendaftaran_id) max_utama ON (((koreksidiagnosa_t.pendaftaran_id = max_utama.pendaftaran_id) AND (koreksidiagnosa_t.koreksidiagnosa_id = max_utama.utamamax_id))))) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
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
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false))
            ;");
            $this->execute('ALTER TABLE public.laporanr2mk_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_064303_migrate_live_20210107_view_laporanr2mk_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_064303_migrate_live_20210107_view_laporanr2mk_v cannot be reverted.\n";

        return false;
    }
    */
}
