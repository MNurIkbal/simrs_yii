<?php

use yii\db\Migration;

/**
 * Class m210312_112540_migrate_20210312_2988_view_laporansensusharianri_pasienkeluarpindahrslain_v
 */
class m210312_112540_migrate_20210312_2988_view_laporansensusharianri_pasienkeluarpindahrslain_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienkeluarpindahrslain_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienkeluarpindahrslain_v\" AS
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
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            penjamin_m.penjamin_nama,
            (to_char(masukkamar.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            masukkamar.lamadirawat_kamar AS lama_rawat,
            pegawai_m.nama_pegawai AS nama_dokter,
            rujukankeluar_m.rumahsakit_rujukan,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            masukkamar.tgl_masukkamar AS tgl_masukkamar_1,
            masukkamar.jam_masukkamar,
            masukkamar.tgl_keluarkamar,
            masukkamar.jam_keluarkamar
            FROM ((((((((((((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            JOIN ( SELECT masukkamar_t_1.pasienadmisi_id,
            masukkamar_t_1.masukkamar_id,
            masukkamar_t_1.tgl_masukkamar,
            masukkamar_t_1.kamarruangan_id,
            masukkamar_t_1.kamartempattidur_id,
            masukkamar_t_1.lamadirawat_kamar,
            masukkamar_t_1.jam_masukkamar,
            masukkamar_t_1.tgl_keluarkamar,
            masukkamar_t_1.jam_keluarkamar
            FROM (masukkamar_t masukkamar_t_1
            JOIN ( SELECT max(masukkamar_t_2.masukkamar_id) AS max_id,
            masukkamar_t_2.pasienadmisi_id
            FROM masukkamar_t masukkamar_t_2
            GROUP BY masukkamar_t_2.pasienadmisi_id) max_masuk ON (((masukkamar_t_1.pasienadmisi_id = max_masuk.pasienadmisi_id) AND (masukkamar_t_1.masukkamar_id = max_masuk.max_id))))) masukkamar ON ((pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id)))
            JOIN kamarruangan_m kamar_keluar ON ((masukkamar.kamarruangan_id = kamar_keluar.kamarruangan_id)))
            JOIN kamartempattidur_m tempattidur_keluar ON ((masukkamar.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
            LEFT JOIN pasiendirujukkeluar_t ON ((pasienpulang_t.pasiendirujukkeluar_id = pasiendirujukkeluar_t.pasiendirujukkeluar_id)))
            LEFT JOIN rujukankeluar_m ON ((pasiendirujukkeluar_t.rujukankeluar_id = rujukankeluar_m.rujukankeluar_id)))
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pasienpulang_t.carakeluar_id = 2) AND (pasienpulang_t.kondisikeluar_id = 3))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
            $this->execute('
                ALTER TABLE public.laporansensusharianri_pasienkeluarpindahrslain_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_112540_migrate_20210312_2988_view_laporansensusharianri_pasienkeluarpindahrslain_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_112540_migrate_20210312_2988_view_laporansensusharianri_pasienkeluarpindahrslain_v cannot be reverted.\n";

        return false;
    }
    */
}
