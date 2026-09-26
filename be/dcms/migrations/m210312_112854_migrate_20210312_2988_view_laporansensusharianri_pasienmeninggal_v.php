<?php

use yii\db\Migration;

/**
 * Class m210312_112854_migrate_20210312_2988_view_laporansensusharianri_pasienmeninggal_v
 */
class m210312_112854_migrate_20210312_2988_view_laporansensusharianri_pasienmeninggal_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienmeninggal_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienmeninggal_v\" AS
            SELECT pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            kamar_keluar.kamarruangan_nokamar AS kamar,
            tempattidur_keluar.no_tempattidur AS tempattidur,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            penjamin_m.penjamin_nama,
            (to_char(masukkamar.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            CASE
            WHEN (((carakeluar_m.carakeluar_nama)::text = 'MENINGGAL'::text) AND ((kondisikeluar_m.kondisikeluar_nama)::text = 'MENINGGAL < 48 JAM'::text)) THEN (pasienpulang_t.lama_rawat)::integer
            ELSE 0
            END AS lama_rawat_kur48,
            CASE
            WHEN (((carakeluar_m.carakeluar_nama)::text = 'MENINGGAL'::text) AND ((kondisikeluar_m.kondisikeluar_nama)::text = 'MENINGGAL > 48 JAM'::text)) THEN (pasienpulang_t.lama_rawat)::integer
            ELSE 0
            END AS lama_rawat_leb48,
            pegawai_m.nama_pegawai AS nama_dokter,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            pasienpulang_t.tgl_meninggal AS tgl_pasienmeninggal,
            pasienpulang_t.lama_rawat,
            kondisikeluar_m.kondisikeluar_id AS kondisi_keluar_id,
            kondisikeluar_m.kondisikeluar_nama AS kondisi_keluar_nama
            FROM ((((((((((((((pendaftaran_t
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT masukkamar_t_1.pasienadmisi_id,
            masukkamar_t_1.masukkamar_id,
            masukkamar_t_1.tgl_masukkamar
            FROM (masukkamar_t masukkamar_t_1
            JOIN ( SELECT max(masukkamar_t_2.masukkamar_id) AS max_id,
            masukkamar_t_2.pasienadmisi_id
            FROM masukkamar_t masukkamar_t_2
            GROUP BY masukkamar_t_2.pasienadmisi_id) max_masuk ON (((masukkamar_t_1.pasienadmisi_id = max_masuk.pasienadmisi_id) AND (masukkamar_t_1.masukkamar_id = max_masuk.max_id))))) masukkamar ON ((pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id)))
            LEFT JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            LEFT JOIN kamarruangan_m kamar_keluar ON ((pasienadmisi_t.kamarruangan_id = kamar_keluar.kamarruangan_id)))
            LEFT JOIN kamartempattidur_m tempattidur_keluar ON ((pasienadmisi_t.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pasienpulang_t.carakeluar_id = 4))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
            $this->execute('
                ALTER TABLE public.laporansensusharianri_pasienmeninggal_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_112854_migrate_20210312_2988_view_laporansensusharianri_pasienmeninggal_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_112854_migrate_20210312_2988_view_laporansensusharianri_pasienmeninggal_v cannot be reverted.\n";

        return false;
    }
    */
}
