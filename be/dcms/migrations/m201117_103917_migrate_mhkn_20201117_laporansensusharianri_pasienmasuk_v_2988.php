<?php

use yii\db\Migration;

/**
 * Class m201117_103917_migrate_mhkn_20201117_laporansensusharianri_pasienmasuk_v_2988
 */
class m201117_103917_migrate_mhkn_20201117_laporansensusharianri_pasienmasuk_v_2988 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienmasuk_v;');
        $this->execute("CREATE VIEW \"public\".\"laporansensusharianri_pasienmasuk_v\" AS
             SELECT (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date AS tgl_admisi,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    kamar_asal.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    kamar_asal.kamarruangan_nokamar AS kamar,
    tempattidur_asal.no_tempattidur AS tempattidur,
    penjamin_m.penjamin_nama,
    pegawai_m.nama_pegawai AS nama_dokter,
    diagnosa_m.diagnosa_nama
   FROM (((((((((((((pasienadmisi_t
     JOIN pendaftaran_t ON ((pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
     LEFT JOIN pindahkamar_t ON ((masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN kamarruangan_m kamar_asal ON ((masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m tempattidur_asal ON ((masukkamar_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id)))
     JOIN kelaspelayanan_m ON ((kamar_asal.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
     LEFT JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
  WHERE ((pasienadmisi_t.pasienpulang_id IS NULL) AND (pendaftaran_t.is_deleted IS FALSE))
  ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            
            ;");
            $this->execute('ALTER TABLE public.laporansensusharianri_pasienmasuk_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201117_103917_migrate_mhkn_20201117_laporansensusharianri_pasienmasuk_v_2988 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201117_103917_migrate_mhkn_20201117_laporansensusharianri_pasienmasuk_v_2988 cannot be reverted.\n";

        return false;
    }
    */
}
