<?php

use yii\db\Migration;

/**
 * Class m220607_060549_migrate_BHD12_BTS361_laporankinerjaprofesional_los_v
 */
class m220607_060549_migrate_BHD12_BTS361_laporankinerjaprofesional_los_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporankinerjaprofesional_los_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporankinerjaprofesional_los_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi AS tgl_masukkamar,
            pasienadmisi_t.tgl_pulang AS tgl_keluarkamar
            FROM ((pendaftaran_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.tgl_pulang,
            a.ruangan_id,
            a.kelaspelayanan_id,
            a.status_ranap
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasienadmisi_id,
            (a.tgl_masukkamar)::date AS tgl_masukkamar,
            a.jam_masukkamar,
            a.tgl_keluarkamar,
            a.jam_keluarkamar,
            a.ruangan_id,
            a.kelaspelayanan_id,
            a.pindahkamar_id
            FROM masukkamar_t a) masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            WHERE ((pasienadmisi_t.status_ranap <> 453) AND (pasienadmisi_t.tgl_admisi >= ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)))
            GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pasienadmisi_t.tgl_admisi, pasienadmisi_t.tgl_pulang
            ;");
        $this->execute('
            ALTER TABLE public.laporankinerjaprofesional_los_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220607_060549_migrate_BHD12_BTS361_laporankinerjaprofesional_los_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220607_060549_migrate_BHD12_BTS361_laporankinerjaprofesional_los_v cannot be reverted.\n";

        return false;
    }
    */
}
