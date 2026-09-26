<?php

use yii\db\Migration;

/**
 * Class m200810_102400_migrate_mhkn_20200810_4
 */
class m200810_102400_migrate_mhkn_20200810_4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if  exists "public"."laporanorderanrad_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanorderanrad_v\" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id
   FROM ((((((((pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON ((pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 5) AND ((pasienkirimkeunitlain_t.status_penunjang)::integer <> 472))
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id
   FROM (((((((((((pasienkirimkeunitlain_t
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
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 5) AND ((pasienkirimkeunitlain_t.status_penunjang)::integer <> 472));");

        $this->execute('DROP VIEW if exists "public"."rl1_3_fasilitastempattidurdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rl1_3_fasilitastempattidurdetail_v\" AS  SELECT kamarruangan_m.jeniskasuspenyakit_id,
    kamarruangan_m.kelaspelayanan_id,
    kamartempattidur_m.created_date,
    kamartempattidur_m.kamartempattidur_id
   FROM (kamarruangan_m
     JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
  WHERE ((kamarruangan_m.is_deleted = false) AND (kamartempattidur_m.is_deleted = false));");



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200810_102400_migrate_mhkn_20200810_4 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200810_102400_migrate_mhkn_20200810_4 cannot be reverted.\n";

        return false;
    }
    */
}
