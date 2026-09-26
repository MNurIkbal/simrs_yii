<?php

use yii\db\Migration;

/**
 * Class m201102_065024_migrate_mhkn_20201102_pasientitipanlive_view
 */
class m201102_065024_migrate_mhkn_20201102_pasientitipanlive_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infokunjunganri_v;');
        $this->execute("CREATE VIEW \"public\".\"infokunjunganri_v\" AS
    SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
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
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
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
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.tgl_pulang,
    pasienadmisi_t.kunjungan,
    pasienadmisi_t.status_keluar,
    pasienadmisi_t.rawat_gabung,
    kamarruangan_m.kamarruangan_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pegawai_m.kelompokpegawai_id,
    pasien_m.is_deleted,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    golonganumur_m.golonganumur_nama,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasienadmisi_t.created_by,
    kamartempattidur_m.kamartempattidur_id,
    pasienadmisi_t.bpjs_id,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    bpjs_t.nosep,
    kelaspelayanan_m.urutankelas,
    kelaspelayanan_m.bpjs_kelas,
    bpjs_t.klsrawat,
    pasienadmisi_t.is_aps,
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
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk
   FROM (((((((((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN caramasuk_m ON ((pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
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
  WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false));
            ");
        
        $this->execute('ALTER TABLE public.infokunjunganri_v
    OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopasienpulangri_v;');
        $this->execute("CREATE VIEW \"public\".\"infopasienpulangri_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
        CASE
            WHEN (pasienadmisi_t.pasienpulang_id IS NULL) THEN pendaftaran_t.tgl_stopakomodasi
            ELSE pasienpulang_t.tglpasienpulang
        END AS tglpasienpulang,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.ruangan_id AS ruanganakhir_id,
    ruangan_m.ruangan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.lama_rawat,
    pasienpulang_t.pasienpulang_id,
    pendaftaran_t.umur,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jns_kelamin,
    pasienadmisi_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    pasienadmisi_t.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    pendaftaran_t.is_stopakomodasi,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pk.pindahkamar_id,
    pk.is_stoptitipan AS pk_is_stoptitipan,
    pk.is_pasientitipan AS pk_is_pasientitipan,
    pk.kelas_ditagihkan_id AS pk_kelas_ditagihkan_id,
    pk.kelaspelayanan_nama AS pk_kelas_ditagihkan_nama
   FROM (((((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id))))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
     LEFT JOIN ( SELECT pindahkamar_t.pasienadmisi_id,
            pindahkamar_t.pindahkamar_id,
            pindahkamar_t.is_stoptitipan,
            pindahkamar_t.is_pasientitipan,
            pindahkamar_t.kelas_ditagihkan_id,
            kelas_ditagihkan_1.kelaspelayanan_nama
           FROM ((pindahkamar_t
             JOIN ( SELECT pindahkamar_t_1.pasienadmisi_id,
                    max(pindahkamar_t_1.pindahkamar_id) AS pindahkamar_id
                   FROM pindahkamar_t pindahkamar_t_1
                  GROUP BY pindahkamar_t_1.pasienadmisi_id) nilai_max ON (((pindahkamar_t.pindahkamar_id = nilai_max.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = nilai_max.pasienadmisi_id))))
             LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON ((pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id)))) pk ON ((pasienadmisi_t.pasienadmisi_id = pk.pasienadmisi_id)))
  WHERE (pendaftaran_t.is_stopakomodasi = true);
            ");
    $this->execute('ALTER TABLE public.infopasienpulangri_v
    OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopasienri_v;');
        $this->execute("CREATE VIEW \"public\".\"infopasienri_v\" AS
            SELECT pasienadmisi_t.pasienadmisi_id,
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
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
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
            WHEN (monitorsetdiagnosa.diag_utama_id IS NULL) THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
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
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk
   FROM ((((((((((((((((((((((((((pendaftaran_t
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
             LEFT JOIN instruksitindakan_t ON (((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id) AND (instruksitindakan_t.is_deleted = false) AND ((instruksitindakan_t.status_implementasi)::text <> '455'::text))))
             LEFT JOIN instruksitindakanbmhp_t ON (((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id) AND (instruksitindakanbmhp_t.is_deleted = false) AND ((instruksitindakanbmhp_t.status_implementasi)::text <> '455'::text))))
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
  WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false));
            ");
    $this->execute('ALTER TABLE public.infopasienri_v
    OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopindahkamar_v;');
        $this->execute("CREATE VIEW \"public\".\"infopindahkamar_v\" AS
            SELECT pindahkamar_t.pindahkamar_id,
    pasienadmisi_t.pasien_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.pegawai_id AS pegawaipendaftaran_id,
    pasienadmisi_t.pegawai_id AS pegawaiadmisi_id,
    pindahkamar_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id AS ruangan_sekarang_id,
    pindahkamar_t.ruangan_id AS ruangan_pindah_id,
    pasienadmisi_t.tgl_admisi,
    pindahkamar_t.tgl_pindahkamar,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_asal.ruangan_nama AS ruangan_sekarang,
    kamar_asal.kamarruangan_nokamar AS kamar_sekarang,
    tempattidur_asal.no_tempattidur AS tempattidur_sekarang,
    ruangan_pindah.ruangan_nama AS ruangan_pindah,
    kamar_pindah.kamarruangan_nokamar AS kamar_pindah,
    tempattidur_pindah.no_tempattidur AS tempattidur_pindah,
    pasienadmisi_t.is_stoptitipan,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pindahkamar_t.is_stoptitipan AS is_stoptitipan_pk,
    pindahkamar_t.is_pasientitipan AS is_pasientitipan_pk,
    pindahkamar_t.kelas_ditagihkan_id AS kelas_ditagihkan_id_pk,
    kelas_ditagihkan_pk.kelaspelayanan_nama AS kelas_ditagihkan_nama_pk
   FROM ((((((((((((((((((pasienadmisi_t
     JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
     JOIN pindahkamar_t ON ((masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id)))
     JOIN pegawai_m dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
     LEFT JOIN pegawai_m dokter_pendaftaran ON ((pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pindahkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN ruangan_m ruangan_asal ON ((masukkamar_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN kamarruangan_m kamar_asal ON ((masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id)))
     JOIN kamartempattidur_m tempattidur_asal ON ((masukkamar_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id)))
     JOIN ruangan_m ruangan_pindah ON ((pindahkamar_t.ruangan_id = ruangan_pindah.ruangan_id)))
     JOIN kamarruangan_m kamar_pindah ON ((pindahkamar_t.kamarruangan_id = kamar_pindah.kamarruangan_id)))
     JOIN kamartempattidur_m tempattidur_pindah ON ((pindahkamar_t.kamartempattidur_id = tempattidur_pindah.kamartempattidur_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan_pk ON ((pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_pk.kelaspelayanan_id)))
  WHERE ((pindahkamar_t.is_active = true) AND (pindahkamar_t.is_deleted = false));
            ");
        $this->execute('ALTER TABLE public.infopindahkamar_v
    OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.laporankunjunganri_v;');
        $this->execute("CREATE VIEW \"public\".\"laporankunjunganri_v\" AS
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
    pasien_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
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
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.tgl_pulang,
    pasienadmisi_t.status_keluar,
    pasienadmisi_t.rawat_gabung,
    kamarruangan_m.kamarruangan_id,
    pegawai_m.nama_pegawai,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pegawai_m.kelompokpegawai_id,
    pasien_m.is_deleted,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienpulang_t.pasienpulang_id,
    pasienpulang_t.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_namalain,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((pasien_m.agama)::integer) AS agama,
    fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas,
    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
    fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
    fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan,
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
    fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan,
    fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup((pasien_m.rhesus)::integer) AS rhesus,
    fgetnamalookup((pendaftaran_t.status_masuk)::integer) AS status_masuk,
    pasien_m.jeniskelamin,
    pasienbatalperiksa_t.alasan_batal,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_ranap_nama,
    golonganumur_m.golonganumur_nama,
    carakeluar_m.carakeluar_nama,
    pasienadmisi_t.kamartempattidur_id,
    bpjs_t.nosep,
    bpjs_t.bpjs_id,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.is_stoptitipan,
    pindah_kamar.pindahkamar_id,
        CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE pindah_kamar.kelas_ditagihkan_id
        END AS kelas_ditagihkan_id,
        CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN kelas_ditagihkan.kelaspelayanan_nama
            ELSE pindah_kamar.kelas_ditagihkan
        END AS kelas_ditagihkan_nama,
        CASE
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
            ELSE false
        END AS is_stoppasientitipan,
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk
   FROM ((((((((((((((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN pasienadmisi_t ON (((pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id))))
     LEFT JOIN caramasuk_m ON ((pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN gelarbelakang_m gelarbelakang ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang.gelarbelakang_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
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
  WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false));
            ");
        $this->execute('ALTER TABLE public.laporankunjunganri_v
    OWNER TO postgres;');


        $this->execute('DROP VIEW if exists public.laporankunjunganrs_v;');
        $this->execute("CREATE VIEW \"public\".\"laporankunjunganrs_v\" AS
            SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pendaftaran_t.pendaftaran_id,
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
    pendaftaran_t.shift_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.rujukan_id,
    pendaftaran_t.pasienpulang_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pasien_m.kecamatan_id,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.is_active,
    pasien_m.is_deleted,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((pasien_m.agama)::integer) AS agama,
    fgetnamalookup((pasien_m.statusperkawinan)::integer) AS status_perkawinan,
    fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas,
    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
    fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
    fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan,
    fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup((pasien_m.rhesus)::integer) AS rhesus,
    bpjs_t.nosep,
    NULL::integer AS kelas_ditagihkan_id,
    NULL::character varying AS kelas_ditagihkan_nama,
    NULL::boolean AS is_pasientitipan,
    NULL::boolean AS is_stoptitipan,
    NULL::integer AS pindahkamar_id,
    NULL::boolean AS is_stoppasientitipan,
    NULL::boolean AS is_pasientitipan_pk
   FROM (((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN gelarbelakang_m gelarbelakang ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang.gelarbelakang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
  WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2]))
UNION ALL
 SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
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
    pendaftaran_t.shift_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.rujukan_id,
    pendaftaran_t.pasienpulang_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pasien_m.kecamatan_id,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.is_active,
    pasien_m.is_deleted,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    fgetnamalookup((pasien_m.agama)::integer) AS agama,
    fgetnamalookup((pasien_m.statusperkawinan)::integer) AS status_perkawinan,
    fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas,
    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
    fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
    fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
    fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan,
    fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup((pasien_m.rhesus)::integer) AS rhesus,
    bpjs_t.nosep,
        CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE pindah_kamar.kelas_ditagihkan_id
        END AS kelas_ditagihkan_id,
        CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN kelas_ditagihkan.kelaspelayanan_nama
            ELSE pindah_kamar.kelas_ditagihkan
        END AS kelas_ditagihkan_nama,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.is_stoptitipan,
    pindah_kamar.pindahkamar_id,
        CASE
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
            ELSE false
        END AS is_stoppasientitipan,
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk
   FROM (((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN gelarbelakang_m gelarbelakang ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang.gelarbelakang_id)))
     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
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
          WHERE (pindahkamar_t.is_deleted = false)) stop_titipan ON ((pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id)));
            ");
$this->execute('ALTER TABLE public.laporankunjunganrs_v
    OWNER TO postgres;');


        $this->execute('DROP VIEW if exists public.laporanpasienri_v;');
        $this->execute("CREATE VIEW \"public\".\"laporanpasienri_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pasienadmisi_t.pegawai_id,
    pasienadmisi_t.pasienpulang_id,
    pasienadmisi_t.tgl_admisi AS \"Tanggal Masuk\",
    pasienpulang_t.tglpasienpulang AS \"Tanggal Keluar\",
    pasien_m.no_rekam_medik AS \"No. Rekam Medik\",
    pendaftaran_t.no_pendaftaran AS \"No. Pendaftaran\",
    pasien_m.nama_pasien AS \"Nama Pasien\",
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS \"Jenis Kelamin\",
    pegawai_m.nama_pegawai AS \"Dokter\",
    carabayar_m.carabayar_nama AS \"Cara Bayar\",
    penjamin_m.penjamin_nama AS \"Penjamin\",
    kelaspelayanan_m.kelaspelayanan_nama AS \"Kelas Pelayanan\",
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS \"Jenis Kasus Penyakit\",
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_nama AS \"Ruangan\",
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamartempattidur_m.no_tempattidur,
    pasienpulang_t.lama_rawat AS \"Lama Rawat\",
    pasienbatalperiksa_t.alasan_batal,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_ranap_nama,
    pasienadmisi_t.is_stoptitipan,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pindah_kamar.is_stoptitipan AS is_stoptitipan_pk,
    pindah_kamar.is_pasientitipan AS is_pasientitipan_pk,
    pindah_kamar.kelas_ditagihkan_id AS kelas_ditagihkan_id_pk,
    pindah_kamar.kelaspelayanan_nama AS kelas_ditagihkan_nama_pk,
    pindah_kamar.pindahkamar_id
   FROM (((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
            pindahkamar_t.pasienadmisi_id,
            pindahkamar_t.is_pasientitipan,
            pindahkamar_t.is_stoptitipan,
            pindahkamar_t.kelas_ditagihkan_id,
            kelaspelayanan_m_1.kelaspelayanan_nama
           FROM ((pindahkamar_t
             JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                    pk.pasienadmisi_id
                   FROM pindahkamar_t pk
                  GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
             LEFT JOIN kelaspelayanan_m kelaspelayanan_m_1 ON ((pindahkamar_t.kelas_ditagihkan_id = kelaspelayanan_m_1.kelaspelayanan_id)))
          WHERE (pindahkamar_t.is_deleted = false)) pindah_kamar ON ((pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id)));
            ");
$this->execute('ALTER TABLE public.laporanpasienri_v
    OWNER TO postgres;');


        $this->execute('DROP VIEW if exists public.rincianpasien_v;');
        $this->execute("CREATE VIEW \"public\".\"rincianpasien_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    carabayar_admisi.carabayar_nama AS carabayar_admisi,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_admisi.penjamin_nama AS penjamin_admisi,
    pendaftaran_t.kelaspelayanan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN kelas_admisi.kelaspelayanan_nama
            ELSE kelaspelayanan_m.kelaspelayanan_nama
        END AS kelaspelayanan_nama,
    kelas_admisi.kelaspelayanan_nama AS kelas_admisi,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_lunas,
    pendaftaran_t.is_stopakomodasi,
        CASE
            WHEN (pendaftaran_t.is_stopakomodasi <> true) THEN false
            WHEN (pendaftaran_t.is_stopakomodasi = true) THEN true
            ELSE NULL::boolean
        END AS is_pulang,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text)
            ELSE to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)
        END AS tgl_masuk,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pasienadmisi_t.tgl_admisi, 'HH24:MI:SS'::text)
            ELSE to_char(pendaftaran_t.tgl_pendaftaran, 'HH24:MI:SS'::text)
        END AS jam_masuk,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'YYYY-MM-DD'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'YYYY-MM-DD'::text)
        END AS tgl_keluar,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'HH24:MI:SS'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'HH24:MI:SS'::text)
        END AS jam_keluar,
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
        CASE
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
            ELSE false
        END AS is_stoppasientitipan,
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk
   FROM (((((((((((((((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_admisi ON ((pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     LEFT JOIN bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
     LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
     LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
     LEFT JOIN pasienpulang_t pulang_pendaftaran ON ((pendaftaran_t.pasienpulang_id = pulang_pendaftaran.pasienpulang_id)))
     LEFT JOIN pasienpulang_t pulang_admisi ON ((pasienadmisi_t.pasienpulang_id = pulang_admisi.pasienpulang_id)))
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
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasienadmisi_t.tgl_admisi, pendaftaran_t.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, pendaftaran_t.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, pendaftaran_t.ruangan_id, r_pendaftaran.ruangan_nama, pasienadmisi_t.ruangan_id, r_admisi.ruangan_nama, pasienadmisi_t.kamarruangan_id, kamarruangan_m.kamarruangan_nokamar, pasienadmisi_t.kamartempattidur_id, kamartempattidur_m.no_tempattidur, pendaftaran_t.status_bayar, (fgetnamalookup(pendaftaran_t.status_bayar)), penjamin_admisi.penjamin_nama, carabayar_admisi.carabayar_nama, kelas_admisi.kelaspelayanan_nama, pasien_m.namadepan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'YYYY-MM-DD'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'YYYY-MM-DD'::text)
        END,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'HH24:MI:SS'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'HH24:MI:SS'::text)
        END, pasienadmisi_t.is_pasientitipan,
        CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE pindah_kamar.kelas_ditagihkan_id
        END,
        CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN kelas_ditagihkan.kelaspelayanan_nama
            ELSE pindah_kamar.kelas_ditagihkan
        END, pasienadmisi_t.kamar_titipan_id, kamar_ditagihkan.kamarruangan_nokamar, pasienadmisi_t.ruangan_titipan_id, ruangan_ditagihkan.ruangan_nama, pasienadmisi_t.is_stoptitipan,
        CASE
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
            ELSE false
        END, stop_titipan.is_pasientitipan;");

$this->execute('ALTER TABLE public.rincianpasien_v
    OWNER TO postgres;');


        $this->execute('DROP VIEW if exists public.infokunjunganrj_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrj_v\" AS (
         SELECT 'PENDAFTARAN'::text AS jenis,
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
            ruangan_m.lantai_id
           FROM (((((((((((((((((((((pendaftaran_t
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
          WHERE (pendaftaran_t.instalasi_id = 1)
        UNION
         SELECT 'KONSUL'::text AS jenis,
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
            ruangan_m.lantai_id
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
          WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NULL))
) UNION ALL
 SELECT 'MCU'::text AS jenis,
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
    paket_mcu.ruangan_id,
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
    ruangan_asal.ruangan_nama AS ruanganasal_nama,
    pendaftaran_t.pasienpulang_id,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    antrian_t.jenisantrian_id,
    ruangan_m.ruangan_nama AS poliklinik,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS stat_ranap,
    NULL::character varying AS nosep,
    ruangan_m.lantai_id
   FROM ((((((((((((((((((((pendaftaran_t
     JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            paketpelayanan_mp.ruangan_id
           FROM (((tindakanpelayanan_t
             JOIN tipepaket_m ON (((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id) AND (tipepaket_m.is_deleted = false))))
             JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
             JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 1))))
          GROUP BY tindakanpelayanan_t.pendaftaran_id, paketpelayanan_mp.ruangan_id) paket_mcu ON ((pendaftaran_t.pendaftaran_id = paket_mcu.pendaftaran_id)))
     JOIN ruangan_m ON ((paket_mcu.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
  WHERE (pendaftaran_t.instalasi_id = 21);");

    $this->execute('ALTER TABLE public.infokunjunganrj_v
    OWNER TO postgres;');


        $this->execute('DROP VIEW if exists public.infodatapendaftaran_v;');
        $this->execute("CREATE VIEW \"public\".\"infodatapendaftaran_v\" AS
            SELECT data_info.pendaftaran_id,
    data_info.instalasi_id AS ins_id,
    data_info.ruangan_id AS rua_id,
    data_info.pasien_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS pen_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS car_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.kelaspelayanan_id
            ELSE data_info.kelaspelayananri_id
        END AS kelaspelayanan_id,
    data_info.pasienpulang_id,
    data_info.no_pendaftaran,
    data_info.tgl_pendaftaran,
    data_info.no_rekam_medik,
    data_info.nama_pasien,
    data_info.no_mobile_pasien,
    data_info.instalasi_nama AS ins_nama,
    data_info.ruangan_nama AS rua_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS car,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS pen,
    data_info.kelaspelayanan_nama,
    data_info.jumlah_uangmuka,
    data_info.pasienpulangri_id,
    data_info.pasienadmisi_id,
    data_info.status_pasien,
    data_info.pasienmasukpenunjang_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.tglpasienpulang
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.tglpasienpulang_ri IS NULL)) THEN data_info.tgl_stopakomodasi
            ELSE data_info.tglpasienpulang_ri
        END AS tglpasienpulang,
    data_info.dokterrj_id,
    data_info.nama_dok_rj_rd,
    data_info.dokterri_id,
    data_info.nama_dok_ri,
    data_info.jeniskasuspenyakit_nama,
    data_info.umur,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS carabayar_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS penjamin_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS carabayar_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS penjamin_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.instalasi_id
            ELSE data_info.instalasiri_id
        END AS instalasi_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.ruangan_id
            ELSE data_info.ruanganri_id
        END AS ruangan_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.instalasi_nama
            ELSE data_info.instalasi_nama_ri
        END AS instalasi_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.ruangan_nama
            ELSE data_info.ruangan_nama_ri
        END AS ruangan_nama,
    data_info.status_bayar,
    data_info.jeniskasuspenyakit_id,
    data_info.tanggal_lahir,
    data_info.penjualanresep_id,
    data_info.jasa,
    data_info.administrasi,
    data_info.obat,
    data_info.totalharga_jual,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.kelas_bpjspendaftaran
            ELSE data_info.kelas_bpjsadmisi
        END AS hak_kelas,
        CASE
            WHEN ((data_info.pasienadmisi_id IS NULL) AND (data_info.bpjs_idpendaftaran IS NOT NULL)) THEN data_info.no_bpjspendaftaran
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.bpjs_idadmisi IS NOT NULL)) THEN data_info.no_bpjsadmisi
            WHEN ((data_info.pasienadmisi_id IS NULL) AND (data_info.bpjs_idadmisi IS NULL)) THEN data_info.no_asuransipendaftaran
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.bpjs_idadmisi IS NULL)) THEN data_info.no_asuransiadmisi
            ELSE NULL::character varying
        END AS no_kartu,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.groupcarabayar_pendaftaran
            ELSE data_info.groupcarabayar_admisi
        END AS group_carabayar,
    data_info.total_piutang,
    data_info.keadaanmasuk_id,
    data_info.keadaan_masuk,
    data_info.transportasi_id,
    data_info.transportasi,
    data_info.keterangan_pendaftaran,
    (data_info.tagihan_belumbayar)::integer AS tagihan_belumbayar,
    (data_info.sisa_penunjang)::integer AS sisa_penunjang,
    (data_info.sisa_karcis)::integer AS sisa_karcis,
    (data_info.sisa_obat)::integer AS sisa_obat,
    data_info.status_periksa,
    data_info.tinggi_badan,
    data_info.berat_badan,
    data_info.nama_depan,
    data_info.jenisidentitas,
    data_info.no_identitas_pasien,
    data_info.no_telepon_pasien,
    data_info.alamatemail,
    data_info.alamat_pasien,
    data_info.alamat_sekarang,
    data_info.additional_pasien,
    data_info.jenis_kelamin
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.kelaspelayanan_id,
            pasienadmisi_t.kelaspelayanan_id AS kelaspelayananri_id,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            concat(fgetnamalookup((pasien_m.namadepan)::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.jenisidentitas,
            pasien_m.no_identitas_pasien,
            pasien_m.no_telepon_pasien,
            pasien_m.alamatemail,
            pasien_m.alamat_pasien,
            pasien_m.alamat_sekarang,
            pasien_m.additional_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
                    ELSE kelaspelayanan_ri.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            pasienpulang_t.tglpasienpulang,
            pulang_ri.tglpasienpulang AS tglpasienpulang_ri,
            ((COALESCE(bayaruangmuka_t.jumlah_uangmuka, (0)::double precision) - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, (0)::double precision)) - COALESCE(pengembalianuangmuka_t.total_pengembalian, (0)::double precision)) AS jumlah_uangmuka,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.status_pasien,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            dok_rj_rd.nama_pegawai AS nama_dok_rj_rd,
            dok_ri.nama_pegawai AS nama_dok_ri,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.umur,
            pasienadmisi_t.carabayar_id AS carabayarri_id,
            carabayar_ri.carabayar_nama AS carabayar_nama_ri,
            pasienadmisi_t.penjamin_id AS penjaminri_id,
            penjamin_ri.penjamin_nama AS penjamin_nama_ri,
            pasienadmisi_t.ruangan_id AS ruanganri_id,
            ruang_ri.instalasi_id AS instalasiri_id,
            ruang_ri.ruangan_nama AS ruangan_nama_ri,
            ins_ri.instalasi_nama AS instalasi_nama_ri,
            pendaftaran_t.status_bayar,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            NULL::integer AS penjualanresep_id,
            0 AS jasa,
                CASE
                    WHEN (penjualan_resep.biaya_adm IS NULL) THEN (0)::double precision
                    ELSE penjualan_resep.biaya_adm
                END AS administrasi,
            0 AS obat,
            0 AS totalharga_jual,
            bpjs_pendaftaran.klsrawat AS kelas_bpjspendaftaran,
            bpjs_admisi.klsrawat AS kelas_bpjsadmisi,
            bpjs_pendaftaran.bpjs_id AS bpjs_idpendaftaran,
            bpjs_admisi.bpjs_id AS bpjs_idadmisi,
            bpjs_pendaftaran.nokartuasuransi AS no_bpjspendaftaran,
            bpjs_admisi.nokartuasuransi AS no_bpjsadmisi,
            asuransi_pendaftaran.nokartuasuransi AS no_asuransipendaftaran,
            asuransi_admisi.nokartuasuransi AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_ri.groupcarabayar_id AS groupcarabayar_admisi,
            COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision) AS total_piutang,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pendaftaran_t.keadaan_masuk AS keadaanmasuk_id,
            fgetnamalookup((pendaftaran_t.keadaan_masuk)::integer) AS keadaan_masuk,
            pendaftaran_t.transportasi AS transportasi_id,
            fgetnamalookup((pendaftaran_t.transportasi)::integer) AS transportasi,
            pendaftaran_t.keterangan_pendaftaran,
            COALESCE(belum_bayar.total_tagihan, (0)::double precision) AS tagihan_belumbayar,
            COALESCE(sisa_penunjang.total_tagihan, (0)::double precision) AS sisa_penunjang,
            COALESCE(sisa_karcis.total_tagihan, (0)::double precision) AS sisa_karcis,
            COALESCE(sisa_obat.total_tagihan, (0)::double precision) AS sisa_obat,
            pendaftaran_t.status_periksa,
                CASE
                    WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN periksa_fisik_rj.tinggi
                    WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (periksa_fisik_rd.tinggi)::double precision
                    WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (periksa_fisik_ri.tinggi)::double precision
                    ELSE NULL::double precision
                END AS tinggi_badan,
                CASE
                    WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN periksa_fisik_rj.berat
                    WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (periksa_fisik_rd.berat)::double precision
                    WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (periksa_fisik_ri.berat)::double precision
                    ELSE NULL::double precision
                END AS berat_badan,
            pendaftaran_t.tgl_stopakomodasi,
            fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin
           FROM ((((((((((((((((((((((((((((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN ruangan_m ruang_ri ON ((pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id)))
             LEFT JOIN instalasi_m ins_ri ON ((ruang_ri.instalasi_id = ins_ri.instalasi_id)))
             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN carabayar_m carabayar_ri ON ((pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id)))
             LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             LEFT JOIN kelaspelayanan_m kelaspelayanan_ri ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
             LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
             LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                    sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                   FROM bayaruangmuka_t bayaruangmuka_t_1
                  WHERE (bayaruangmuka_t_1.is_deleted = false)
                  GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
             LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT pengembalianuangmuka_t_1.pendaftaran_id,
                    sum(pengembalianuangmuka_t_1.total_pengembalian) AS total_pengembalian
                   FROM pengembalianuangmuka_t pengembalianuangmuka_t_1
                  WHERE (pengembalianuangmuka_t_1.is_deleted = false)
                  GROUP BY pengembalianuangmuka_t_1.pendaftaran_id) pengembalianuangmuka_t ON ((pendaftaran_t.pendaftaran_id = pengembalianuangmuka_t.pendaftaran_id)))
             LEFT JOIN ( SELECT pemakaianuangmuka_t_1.pendaftaran_id,
                    sum(pemakaianuangmuka_t_1.pemakaian_uangmuka) AS pemakaian_uangmuka
                   FROM pemakaianuangmuka_t pemakaianuangmuka_t_1
                  WHERE (pemakaianuangmuka_t_1.is_deleted = false)
                  GROUP BY pemakaianuangmuka_t_1.pendaftaran_id) pemakaianuangmuka_t ON ((pendaftaran_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
             LEFT JOIN pegawai_m dok_rj_rd ON ((pendaftaran_t.pegawai_id = dok_rj_rd.pegawai_id)))
             LEFT JOIN pegawai_m dok_ri ON ((pasienadmisi_t.pegawai_id = dok_ri.pegawai_id)))
             LEFT JOIN bpjs_t bpjs_pendaftaran ON ((pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id)))
             LEFT JOIN bpjs_t bpjs_admisi ON ((pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id)))
             LEFT JOIN asuransipasien_m asuransi_pendaftaran ON ((pendaftaran_t.asuransipasien_id = asuransi_pendaftaran.asuransipasien_id)))
             LEFT JOIN asuransipasien_m asuransi_admisi ON ((pasienadmisi_t.asuransipasien_id = asuransi_admisi.asuransipasien_id)))
             LEFT JOIN pemberianpiutang_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT sum(pt.biayaadministrasi) AS biaya_adm,
                    pt.pendaftaran_id
                   FROM penjualanresep_t pt
                  WHERE ((pt.status_bayar = 349) AND (pt.is_deleted = false))
                  GROUP BY pt.pendaftaran_id) penjualan_resep ON ((penjualan_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM tindakanpelayanan_t
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan
                           FROM obatalkespasien_t
                          WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) belum_bayar ON ((pendaftaran_t.pendaftaran_id = belum_bayar.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM (tindakanpelayanan_t
                             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL) AND (daftartindakan_m.kelompoktindakan_id <> 17))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_penunjang ON ((pendaftaran_t.pendaftaran_id = sisa_penunjang.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM (tindakanpelayanan_t
                             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (daftartindakan_m.kelompoktindakan_id = 17))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_karcis ON ((pendaftaran_t.pendaftaran_id = sisa_karcis.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT obatalkespasien_t.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan
                           FROM obatalkespasien_t
                          WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_obat ON ((pendaftaran_t.pendaftaran_id = sisa_obat.pendaftaran_id)))
             LEFT JOIN ( SELECT pemeriksaanfisik_t.pendaftaran_id,
                    pemeriksaanfisik_t.tinggibadan_cm AS tinggi,
                    pemeriksaanfisik_t.beratbadan_kg AS berat
                   FROM pemeriksaanfisik_t
                  WHERE (pemeriksaanfisik_t.is_deleted = false)) periksa_fisik_rj ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id)))
             LEFT JOIN ( SELECT asesmenperawatrd_t.pendaftaran_id,
                    asesmenperawatrd_t.tinggi_badan AS tinggi,
                    asesmenperawatrd_t.berat_badan AS berat
                   FROM asesmenperawatrd_t
                  WHERE (asesmenperawatrd_t.is_deleted = false)) periksa_fisik_rd ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id)))
             LEFT JOIN ( SELECT asesmenmedis_t.pendaftaran_id,
                    asesmenmedis_t.tinggi_badan AS tinggi,
                    asesmenmedis_t.berat_badan AS berat
                   FROM asesmenmedis_t
                  WHERE (asesmenmedis_t.is_deleted = false)) periksa_fisik_ri ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id)))
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir, bpjs_pendaftaran.klsrawat, bpjs_admisi.klsrawat, bpjs_pendaftaran.bpjs_id, bpjs_admisi.bpjs_id, bpjs_pendaftaran.nokartuasuransi, bpjs_admisi.nokartuasuransi, asuransi_pendaftaran.nokartuasuransi, asuransi_admisi.nokartuasuransi, kelaspelayanan_ri.kelaspelayanan_nama, carabayar_m.groupcarabayar_id, carabayar_ri.groupcarabayar_id, pemberianpiutang_t.total_piutang, pendaftaran_t.keadaan_masuk, pendaftaran_t.transportasi, pendaftaran_t.keterangan_pendaftaran, penjualan_resep.biaya_adm, belum_bayar.total_tagihan, sisa_penunjang.total_tagihan, sisa_karcis.total_tagihan, COALESCE(sisa_obat.total_tagihan, (0)::double precision), pendaftaran_t.status_periksa, periksa_fisik_rj.tinggi, periksa_fisik_rj.berat, periksa_fisik_rd.tinggi, periksa_fisik_rd.berat, periksa_fisik_ri.tinggi, periksa_fisik_ri.berat, pasien_m.namadepan, pasien_m.jenisidentitas, pasien_m.additional_pasien, pasien_m.no_identitas_pasien, pasien_m.no_telepon_pasien, pasien_m.alamatemail, pasien_m.alamat_sekarang, pasien_m.alamat_pasien, pasien_m.jeniskelamin
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            ruangan_m.instalasi_id,
            penjualanresep_t.ruangan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.kelaspelayanan_id AS kelaspelayananri_id,
            0 AS pasienpulang_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
                CASE
                    WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
                    WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN (concat(fgetnamalookup((pasien_m.namadepan)::integer), ' ', pasien_m.nama_pasien))::character varying
                    WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN (concat(fgetnamalookup((pegawai_m.gelardepan)::integer), ' ', karyawan.nama_pegawai))::character varying
                    ELSE NULL::character varying
                END AS nama_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.jenisidentitas,
            pasien_m.no_identitas_pasien,
            pasien_m.no_telepon_pasien,
            pasien_m.alamatemail,
            pasien_m.alamat_pasien,
            pasien_m.alamat_sekarang,
            pasien_m.additional_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::character varying AS kelaspelayanan_nama,
            penjualanresep_t.tglresep AS tglpasienpulang,
            penjualanresep_t.tglresep AS tglpasienpulang_ri,
            0 AS jumlah_uangmuka,
            0 AS pasienpulangri_id,
            0 AS pasienadmisi_id,
            NULL::character varying AS status_pasien,
            0 AS pasienmasukpenunjang_id,
            pegawai_m.nama_pegawai AS nama_dok_rj_rd,
            pegawai_m.nama_pegawai AS nama_dok_ri,
            NULL::character varying AS jeniskasuspenyakit_nama,
            NULL::character varying AS umur,
            penjualanresep_t.carabayar_id AS carabayarri_id,
            carabayar_m.carabayar_nama AS carabayar_nama_ri,
            penjualanresep_t.penjamin_id AS penjaminri_id,
            penjamin_m.penjamin_nama AS penjamin_nama_ri,
            penjualanresep_t.ruangan_id AS ruanganri_id,
            ruangan_m.instalasi_id AS instalasiri_id,
            ruangan_m.ruangan_nama AS ruangan_nama_ri,
            instalasi_m.instalasi_nama AS instalasi_nama_ri,
            penjualanresep_t.status_bayar,
            0 AS jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            penjualanresep_t.penjualanresep_id,
            COALESCE(penjualanresep_t.totaltarifservice, (0)::double precision) AS jasa,
            COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision) AS administrasi,
            COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) AS obat,
            ((COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.totaltarifservice, (0)::double precision)) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totalharga_jual,
            NULL::integer AS kelas_bpjspendaftaran,
            NULL::integer AS kelas_bpjsadmisi,
            NULL::integer AS bpjs_idpendaftaran,
            NULL::integer AS bpjs_idadmisi,
            NULL::character varying AS no_bpjspendaftaran,
            NULL::character varying AS no_bpjsadmisi,
            NULL::character varying AS no_asuransipendaftaran,
            NULL::character varying AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_m.groupcarabayar_id AS groupcarabayar_admisi,
            pemberianpiutang_t.total_piutang,
            NULL::integer AS dokterrj_id,
            NULL::integer AS dokterri_id,
            NULL::character varying AS keadaanmasuk_id,
            NULL::character varying AS keadaan_masuk,
            NULL::character varying AS transportasi_id,
            NULL::character varying AS transportasi,
            NULL::text AS keterangan_pendaftaran,
            (tagihan_resep.tagihan_obat)::integer AS tagihan_belumbayar,
            0 AS sisa_penunjang,
            0 AS sisa_karcis,
            (tagihan_resep.tagihan_obat)::integer AS sisa_obat,
            NULL::character varying AS status_periksa,
            NULL::double precision AS tinggi,
            NULL::double precision AS berat,
            NULL::timestamp without time zone AS tgl_stopakomodasi,
                CASE
                    WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN NULL::character varying
                    WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN fgetnamalookup((pasien_m.namadepan)::integer)
                    WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN fgetnamalookup((pegawai_m.gelardepan)::integer)
                    ELSE NULL::character varying
                END AS nama_depan,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin
           FROM (((((((((penjualanresep_t
             LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN ruangan_m ON ((penjualanresep_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
             LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
             LEFT JOIN pemberianpiutang_t ON ((penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id)))
             LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
                   FROM obatalkespasien_t
                  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                  GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON ((penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id)))
          WHERE (((penjualanresep_t.jenispenjualan)::text = ANY (ARRAY['343'::text, '345'::text])) AND (penjualanresep_t.is_deleted = false))) data_info;
            ");
$this->execute('ALTER TABLE public.infodatapendaftaran_v
    OWNER TO postgres;');


        $this->execute('DROP VIEW if exists public.infotindakanpenatajasa_v;');
        $this->execute("CREATE VIEW \"public\".\"infotindakanpenatajasa_v\" AS
            SELECT 'tindakan'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS obatalkes_id,
    NULL::character varying AS obatalkes_nama,
    NULL::double precision AS qty_oa,
    NULL::double precision AS hargajual_oa,
    NULL::integer AS satuanobat_id,
    NULL::character varying AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    tindakanpelayanan_t.tarifpenyulit_tindakan
   FROM ((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          WHERE (tindakansudahbayar_t.is_deleted = false)
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
  WHERE (tindakanpelayanan_t.is_deleted = false)
UNION ALL
 SELECT 'paket'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tipepaket_m.tipepaket_id AS daftartindakan_id,
    tipepaket_m.tipepaket_nama AS daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    tindakanpelayanan_t.tarifpenyulit_tindakan
   FROM (((((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          WHERE (tindakansudahbayar_t.is_deleted = false)
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
     LEFT JOIN obatalkespasien_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id) AND (obatalkespasien_t.is_deleted = false))))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
  WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
UNION ALL
 SELECT 'obat'::text AS jenis,
    NULL::integer AS tindakanpelayanan_id,
    obatalkespasien_t.pendaftaran_id,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    obatalkespasien_t.ruangan_id,
    ruangan_m.ruangan_nama,
    obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    obatalkespasien_t.qty_oa AS qty_tindakan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan,
    obatalkespasien_t.is_penatajasa,
        CASE COALESCE(obatsudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    obatalkespasien_t.is_deleted,
    NULL::text AS keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    NULL::integer AS kelaspelayanan_id,
    NULL::character varying AS kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_masukpenunjang,
    NULL::double precision AS tarifpenyulit_tindakan
   FROM (((((((((obatalkespasien_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT obatsudahbayar_t.obatalkespasien_id,
            count(*) AS telahbayar
           FROM obatsudahbayar_t
          WHERE (obatsudahbayar_t.is_deleted = false)
          GROUP BY obatsudahbayar_t.obatsudahbayar_id) obatsudahbayar ON ((obatalkespasien_t.obatalkespasien_id = obatsudahbayar.obatalkespasien_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN tindakanpelayanan_t ON (((obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id) AND (tindakanpelayanan_t.is_deleted = false))))
     LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
  WHERE (obatalkespasien_t.is_deleted IS FALSE);
            ");
    $this->execute('ALTER TABLE public.infotindakanpenatajasa_v
    OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopasienpenunjang_v;');
        $this->execute("CREATE VIEW \"public\".\"infopasienpenunjang_v\" AS
            SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_penunjang,
    ruangasal.ruangan_nama AS ruangan_asal,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienmasukpenunjang_t.status_periksa,
    ruangan_m.instalasi_id,
    pendaftaran_t.created_by,
    ruangan_m.ruangan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pegawai_m.nama_pegawai,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS nama_status_periksa,
    pasien_m.tanggal_lahir,
    pendaftaran_t.keterangan_pendaftaran,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    pegawai_m.pegawai_id,
    kelaspelayanan_m.kelaspelayanan_id,
    pendaftaran_t.asuransipasien_id,
    pendaftaran_t.status_periksa AS status_periksa_id,
    pendaftaran_t.instalasi_id AS instalasiasal_id
   FROM (((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN ruangan_m ruangasal ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangasal.ruangan_id)))
     JOIN kelaspelayanan_m ON ((pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pasienmasukpenunjang_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
  WHERE ((pasienmasukpenunjang_t.is_active = true) AND (pasienmasukpenunjang_t.is_deleted = false));
            ");
        $this->execute('ALTER TABLE public.infopasienpenunjang_v
    OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201102_065024_migrate_mhkn_20201102_pasientitipanlive_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201102_065024_migrate_mhkn_20201102_pasientitipanlive_view cannot be reverted.\n";

        return false;
    }
    */
}
