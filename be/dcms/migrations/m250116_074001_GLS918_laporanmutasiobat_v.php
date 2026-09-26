<?php

use yii\db\Migration;

/**
 * Class m250116_074001_GLS918_laporanmutasiobat_v
 */
class m250116_074001_GLS918_laporanmutasiobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute("DROP VIEW IF EXISTS laporanmutasiobat_v");
            $laporanmutasiobat_v = file_get_contents(__DIR__ . '/definitions/laporanmutasiobat_v.sql');
            $this->execute($laporanmutasiobat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250116_074001_GLS918_laporanmutasiobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250116_074001_GLS918_laporanmutasiobat_v cannot be reverted.\n";

        return false;
    }
    */
}
