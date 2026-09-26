<?php

use yii\db\Migration;

/**
 * Class m250226_040601_migrate_dsv_1687_laporanpengirimanklaim_v
 */
class m250226_040601_migrate_dsv_1687_laporanpengirimanklaim_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpengirimanklaim_v");
        $laporanpengirimanklaim_v = file_get_contents(__DIR__ . '/definitions/laporanpengirimanklaim_v.sql');
        $this->execute($laporanpengirimanklaim_v);

        $this->execute('
            CREATE INDEX IF NOT EXISTS sy_kunjungan_tgl_pendaftaran_idx ON public.sy_kunjungan USING btree (date(tgl_pendaftaran));
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS sy_kunjungan_tgl_pulang_idx ON public.sy_kunjungan USING btree (date(tgl_pulang));
        ');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250226_040601_migrate_dsv_1687_laporanpengirimanklaim_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250226_040601_migrate_dsv_1687_laporanpengirimanklaim_v cannot be reverted.\n";

        return false;
    }
    */
}
