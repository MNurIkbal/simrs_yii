<?php

use yii\db\Migration;

/**
 * Class m210312_112409_migrate_20210312_2988_view_laporansensusharianri_pasienkeluar_v
 */
class m210312_112409_migrate_20210312_2988_view_laporansensusharianri_pasienkeluar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienkeluar_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienkeluar_v\" AS
            SELECT pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienpulang_t.ruanganakhir_id AS ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            kamar_keluar.kamarruangan_nokamar AS kamar,
            tempattidur_keluar.no_tempattidur AS tempattidur,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            penjamin_m.penjamin_nama,
            (to_char(masukkamar_t.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            masukkamar_t.lamadirawat_kamar AS lama_rawat,
            pegawai_m.nama_pegawai AS nama_dokter,
            masukkamar_t.tgl_masukkamar AS tgl_masukkamar_1,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            masukkamar_t.jam_masukkamar,
            masukkamar_t.tgl_keluarkamar,
            masukkamar_t.jam_keluarkamar
            FROM ((((((((((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            JOIN kamarruangan_m kamar_keluar ON ((masukkamar_t.kamarruangan_id = kamar_keluar.kamarruangan_id)))
            JOIN kamartempattidur_m tempattidur_keluar ON ((masukkamar_t.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pasienpulang_t.pasienadmisi_id IS NOT NULL) AND (masukkamar_t.jam_keluarkamar IS NULL) AND (pasienpulang_t.carakeluar_id = ANY (ARRAY[1, 3, 5, 6, 7])))
            ORDER BY pasienpulang_t.tglpasienpulang DESC
            ;");
            $this->execute('
                ALTER TABLE public.laporansensusharianri_pasienkeluar_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_112409_migrate_20210312_2988_view_laporansensusharianri_pasienkeluar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_112409_migrate_20210312_2988_view_laporansensusharianri_pasienkeluar_v cannot be reverted.\n";

        return false;
    }
    */
}
