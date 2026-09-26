<?php

use yii\db\Migration;

/**
 * Class m200825_031234_migrate_mhkn_20200825_pindahkamar
 */
class m200825_031234_migrate_mhkn_20200825_pindahkamar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopindahkamar_v";');
     
        $this->execute("
            CREATE VIEW \"public\".\"infopindahkamar_v\" AS  SELECT pindahkamar_t.pindahkamar_id,
    pasienadmisi_t.pasien_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.pegawai_id AS pegawaipendaftaran_id,
    pasienadmisi_t.pegawai_id AS pegawaiadmisi_id,
    pasienadmisi_t.kelaspelayanan_id,
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
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN ruangan_m ruangan_asal ON ((masukkamar_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN kamarruangan_m kamar_asal ON ((masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id)))
     JOIN kamartempattidur_m tempattidur_asal ON ((masukkamar_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id)))
     JOIN ruangan_m ruangan_pindah ON ((pindahkamar_t.ruangan_id = ruangan_pindah.ruangan_id)))
     JOIN kamarruangan_m kamar_pindah ON ((pindahkamar_t.kamarruangan_id = kamar_pindah.kamarruangan_id)))
     JOIN kamartempattidur_m tempattidur_pindah ON ((pindahkamar_t.kamartempattidur_id = tempattidur_pindah.kamartempattidur_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_ditagihkan_pk ON ((pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_pk.kelaspelayanan_id)))
  WHERE ((pindahkamar_t.is_active = true) AND (pindahkamar_t.is_deleted = false));");
        
        $this->execute('ALTER TABLE "public"."infopindahkamar_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200825_031234_migrate_mhkn_20200825_pindahkamar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200825_031234_migrate_mhkn_20200825_pindahkamar cannot be reverted.\n";

        return false;
    }
    */
}
