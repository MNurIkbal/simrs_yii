<?php

use yii\db\Migration;

/**
 * Class m190912_051005_infokunjunganri_v
 */
class m190912_051005_infokunjunganri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infokunjunganri_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infokunjunganri_v AS 
 SELECT pasien_m.pasien_id,
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
    pendaftaran_t.tgl_pendaftaran,
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
    pasienadmisi_t.status_ranap AS status_periksa_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
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
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false;
");

        $this->execute('ALTER TABLE public.infokunjunganri_v
  OWNER TO postgres;');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190912_051005_infokunjunganri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190912_051005_infokunjunganri_v cannot be reverted.\n";

        return false;
    }
    */
}
