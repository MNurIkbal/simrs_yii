<?php

use yii\db\Migration;

/**
 * Class m210220_142411_migrate_20210220_infokunjungari_V
 */
class m210220_142411_migrate_20210220_infokunjungari_V extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infokunjunganri_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganri_v\" AS  SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
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
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
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
            WHEN pindah_kamar.pindahkamar_id IS NULL THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE pindah_kamar.kelas_ditagihkan_id
        END AS kelas_ditagihkan_id,
        CASE
            WHEN pindah_kamar.pindahkamar_id IS NULL THEN kelas_ditagihkan.kelaspelayanan_nama
            ELSE pindah_kamar.kelas_ditagihkan
        END AS kelas_ditagihkan_nama,
    pasienadmisi_t.kamar_titipan_id,
    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
    pasienadmisi_t.ruangan_titipan_id,
    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
    pasienadmisi_t.is_stoptitipan,
    pindah_kamar.pindahkamar_id,
        CASE
            WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS FALSE THEN false
            WHEN stop_titipan.pindahkamar_id IS NULL AND pasienadmisi_t.is_stoptitipan IS TRUE THEN true
            WHEN stop_titipan.is_pasientitipan IS FALSE AND stop_titipan.is_stoptitipan IS FALSE THEN true
            WHEN stop_titipan.is_pasientitipan IS TRUE AND stop_titipan.is_stoptitipan IS TRUE THEN true
            ELSE false
        END AS is_stoppasientitipan,
    stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
    carakeluar_m.carakeluar_nama,
    fgetnamalookup(pasien_m.agama::integer) AS agama_nama,
    pendaftaran_t.last_modified_date AS tgl_update_terakhir,
    petugas_pemakai.nama_pegawai AS petugas_nama,
    pendaftaran_t.created_date AS tgl_pembuatan,
    petugas_pembuat.nama_pegawai AS pembuat_nama,
    pasien_m.additional_pasien,
    pendaftaran_t.is_stopakomodasi,
    carabayar_m.carabayar_kode_warna,
        CASE
            WHEN antrian_poli.jenisantrian_id = 312 THEN antrian_poli.no_antrian::text
            ELSE '-'::text
        END AS no_antrian_poli,
    pasien_m.catatanpenting_pasien,
    penanggungjawab_m.penanggungjawab_alamat,
    penanggungjawab_m.penanggungjawab_notelp,
    penanggungjawab_m.pj_pekerjaan_id,
    pj_kerja.pekerjaan_nama AS pj_pekerjaan_nama,
    penanggungjawab_m.pj_propinsi_id,
    pj_prop.propinsi_nama AS pj_propinsi_nama,
    penanggungjawab_m.pj_kabupaten_id,
    pj_kab.kabupaten_nama AS pj_kabupaten_nama,
    penanggungjawab_m.pj_kecamatan_id,
    pj_kec.kecamatan_nama AS pj_kecamatan_nama,
    penanggungjawab_m.pj_kelurahan_id,
    pj_kel.kelurahan_nama AS pj_kelurahan_nama,
    pasien_m.bahasa_sehari,
    fgetnamalookup(pasien_m.bahasa_sehari::integer) AS bahasa_sehari_nama,
    penanggungjawab_m.pj_namadepan,
    fgetnamalookup(penanggungjawab_m.pj_namadepan::integer) AS pj_namadepan_nama,
    pasienadmisi_t.limit_tagihan,
    perujuk_m.namaperujuk AS rujukan_dari
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     LEFT JOIN antrian_t antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id AND antrian_poli.jenisantrian_id = 312
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN loginpemakai_k petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
     LEFT JOIN pegawai_m petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
     LEFT JOIN loginpemakai_k pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
     LEFT JOIN pegawai_m petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
     LEFT JOIN pekerjaan_m pj_kerja ON penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id
     LEFT JOIN propinsi_m pj_prop ON penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id
     LEFT JOIN kabupaten_m pj_kab ON penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id
     LEFT JOIN kecamatan_m pj_kec ON penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id
     LEFT JOIN kelurahan_m pj_kel ON penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
     LEFT JOIN kamarruangan_m kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
     LEFT JOIN ruangan_m ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
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
             LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id
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
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienadmisi_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false;");


        $this->execute('ALTER TABLE "public"."infokunjunganri_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210220_142411_migrate_20210220_infokunjungari_V cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210220_142411_migrate_20210220_infokunjungari_V cannot be reverted.\n";

        return false;
    }
    */
}
