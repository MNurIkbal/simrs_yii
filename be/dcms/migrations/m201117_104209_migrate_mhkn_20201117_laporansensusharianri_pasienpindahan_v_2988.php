<?php

use yii\db\Migration;

/**
 * Class m201117_104209_migrate_mhkn_20201117_laporansensusharianri_pasienpindahan_v_2988
 */
class m201117_104209_migrate_mhkn_20201117_laporansensusharianri_pasienpindahan_v_2988 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienpindahan_v;');
        $this->execute("CREATE VIEW \"public\".\"laporansensusharianri_pasienpindahan_v\" AS
             SELECT pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pindahkamar_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_skrg.ruangan_nama AS ruangan_skrg,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    diagnosa_m.diagnosa_nama,
    (to_char(masukkamar_t.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
    pasienpulang_t.lama_rawat,
    ruangan_asal.ruangan_id,
    ruangan_asal.ruangan_nama AS ruangan_dari,
    instalasi_asal.instalasi_id,
    instalasi_asal.instalasi_nama AS instalasi_dari,
    kamar_asal.kamarruangan_nokamar AS kamar_dari,
    tempattidur_asal.no_tempattidur AS tempattidur_dari,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    pasienpulang_t.tglpasienpulang AS tgl_pasienplg
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
     JOIN ruangan_m ruangan_skrg ON ((pasienadmisi_t.ruangan_id = ruangan_skrg.ruangan_id)))
     JOIN ruangan_m ruangan_asal ON ((masukkamar_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN kamarruangan_m kamar_asal ON ((masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id)))
     JOIN kamartempattidur_m tempattidur_asal ON ((masukkamar_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id)))
     LEFT JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
     LEFT JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
  WHERE ((pindahkamar_t.is_active = true) AND (pindahkamar_t.is_deleted = false))
  ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
            $this->execute('ALTER TABLE public.laporansensusharianri_pasienpindahan_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201117_104209_migrate_mhkn_20201117_laporansensusharianri_pasienpindahan_v_2988 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201117_104209_migrate_mhkn_20201117_laporansensusharianri_pasienpindahan_v_2988 cannot be reverted.\n";

        return false;
    }
    */
}
