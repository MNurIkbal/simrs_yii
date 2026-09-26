<?php

use yii\db\Migration;

/**
 * Class m210204_103501_migrate_20210204_2988_laporansensusharianri_pasienpindahkan_v
 */
class m210204_103501_migrate_20210204_2988_laporansensusharianri_pasienpindahkan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienpindahkan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienpindahkan_v\" AS
            SELECT pasienadmisi_t.tgl_admisi,
            pindahkamar_t.tgl_pindahkamar,
            pasienadmisi_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            masukkamar_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_skrg.ruangan_nama AS ruangan_skrg,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            (to_char(masukkamar.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            masukkamar_t.lamadirawat_kamar AS lama_rawat,
            masukkamar_t.ruangan_id,
            ruangan_pindah.ruangan_nama AS ruangan_ke,
            instalasi_pindah.instalasi_id,
            instalasi_pindah.instalasi_nama AS instalasi_ke,
            kamar_pindah.kamarruangan_nokamar AS kamar_ke,
            kamar_skrg.kamarruangan_nokamar AS kamar_skrg,
            tempattidur_pindah.no_tempattidur AS tempattidur_ke,
            tempattidur_skrg.no_tempattidur AS tempattidur_skrg,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            masukkamar_t.tgl_masukkamar AS tgl_masukkamar_1,
            masukkamar_t.jam_masukkamar,
            masukkamar_t.tgl_keluarkamar,
            masukkamar_t.jam_keluarkamar
            FROM (((((((((((((((((((pasienadmisi_t
            JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            JOIN ( SELECT masukkamar_t_1.pasienadmisi_id,
            masukkamar_t_1.masukkamar_id,
            masukkamar_t_1.tgl_masukkamar
            FROM (masukkamar_t masukkamar_t_1
            JOIN ( SELECT max(masukkamar_t_2.masukkamar_id) AS max_id,
            masukkamar_t_2.pasienadmisi_id
            FROM masukkamar_t masukkamar_t_2
            GROUP BY masukkamar_t_2.pasienadmisi_id) max_masuk ON (((masukkamar_t_1.pasienadmisi_id = max_masuk.pasienadmisi_id) AND (masukkamar_t_1.masukkamar_id = max_masuk.max_id))))) masukkamar ON ((pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id)))
            JOIN pindahkamar_t ON ((masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id)))
            JOIN pegawai_m dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
            LEFT JOIN pegawai_m dokter_pendaftaran ON ((pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN kelaspelayanan_m ON ((masukkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ruangan_m ruangan_skrg ON ((masukkamar_t.ruangan_id = ruangan_skrg.ruangan_id)))
            JOIN ruangan_m ruangan_pindah ON ((pindahkamar_t.ruangan_id = ruangan_pindah.ruangan_id)))
            JOIN instalasi_m instalasi_pindah ON ((ruangan_pindah.instalasi_id = instalasi_pindah.instalasi_id)))
            JOIN kamarruangan_m kamar_pindah ON ((pindahkamar_t.kamarruangan_id = kamar_pindah.kamarruangan_id)))
            JOIN kamarruangan_m kamar_skrg ON ((masukkamar_t.kamarruangan_id = kamar_skrg.kamarruangan_id)))
            JOIN kamartempattidur_m tempattidur_pindah ON ((masukkamar_t.kamartempattidur_id = tempattidur_pindah.kamartempattidur_id)))
            JOIN kamartempattidur_m tempattidur_skrg ON ((masukkamar_t.kamartempattidur_id = tempattidur_skrg.kamartempattidur_id)))
            LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
            WHERE ((pindahkamar_t.is_active = true) AND (pindahkamar_t.is_deleted = false))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
            $this->execute('
                ALTER TABLE public.laporansensusharianri_pasienpindahkan_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210204_103501_migrate_20210204_2988_laporansensusharianri_pasienpindahkan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210204_103501_migrate_20210204_2988_laporansensusharianri_pasienpindahkan_v cannot be reverted.\n";

        return false;
    }
    */
}
