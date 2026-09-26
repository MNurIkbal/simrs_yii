<?php

use yii\db\Migration;

/**
 * Class m201117_103606_migrate_mhkn_20201117_laporansensusharianri_pasienkeluarpindahrslain_v_2988
 */
class m201117_103606_migrate_mhkn_20201117_laporansensusharianri_pasienkeluarpindahrslain_v_2988 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienkeluarpindahrslain_v;');
        $this->execute("CREATE VIEW \"public\".\"laporansensusharianri_pasienkeluarpindahrslain_v\" AS
             SELECT pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    kamar_keluar.kamarruangan_nokamar AS kamar,
    tempattidur_keluar.no_tempattidur AS tempattidur,
    diagnosa_m.diagnosa_nama,
    penjamin_m.penjamin_nama,
    (to_char(masukkamar_t.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
    masukkamar_t.lamadirawat_kamar AS lama_rawat,
    pegawai_m.nama_pegawai AS nama_dokter,
    rujukankeluar_m.rumahsakit_rujukan,
    pasienpulang_t.tglpasienpulang AS tgl_pasienplg
   FROM (((((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
     LEFT JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
     JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
     JOIN kamarruangan_m kamar_keluar ON ((masukkamar_t.kamarruangan_id = kamar_keluar.kamarruangan_id)))
     JOIN kamartempattidur_m tempattidur_keluar ON ((masukkamar_t.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
     LEFT JOIN pasiendirujukkeluar_t ON ((pasienpulang_t.pasiendirujukkeluar_id = pasiendirujukkeluar_t.pasiendirujukkeluar_id)))
     LEFT JOIN rujukankeluar_m ON ((pasiendirujukkeluar_t.rujukankeluar_id = rujukankeluar_m.rujukankeluar_id)))
  WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pasienpulang_t.carakeluar_id = 2) AND (pasienpulang_t.kondisikeluar_id = 3))
  ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
        
        $this->execute('ALTER TABLE public.laporansensusharianri_pasienkeluarpindahrslain_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201117_103606_migrate_mhkn_20201117_laporansensusharianri_pasienkeluarpindahrslain_v_2988 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201117_103606_migrate_mhkn_20201117_laporansensusharianri_pasienkeluarpindahrslain_v_2988 cannot be reverted.\n";

        return false;
    }
    */
}
