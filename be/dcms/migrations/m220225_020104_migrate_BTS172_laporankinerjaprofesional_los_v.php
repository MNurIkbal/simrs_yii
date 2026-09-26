<?php

use yii\db\Migration;

/**
 * Class m220225_020104_migrate_BTS172_laporankinerjaprofesional_los_v
 */
class m220225_020104_migrate_BTS172_laporankinerjaprofesional_los_v extends Migration
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
            pasienadmisi_t.tgl_admisi,
            pasienadmisi_t.tgl_pulang,
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id
            FROM (pendaftaran_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.tgl_pulang,
            a.ruangan_id,
            a.kelaspelayanan_id,
            a.status_ranap
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            WHERE (pasienadmisi_t.status_ranap <> 453)
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
        echo "m220225_020104_migrate_BTS172_laporankinerjaprofesional_los_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220225_020104_migrate_BTS172_laporankinerjaprofesional_los_v cannot be reverted.\n";

        return false;
    }
    */
}
