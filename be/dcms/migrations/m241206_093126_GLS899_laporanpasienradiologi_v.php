<?php

use yii\db\Migration;

/**
 * Class m241206_093126_GLS899_laporanpasienradiologi_v
 */
class m241206_093126_GLS899_laporanpasienradiologi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporanpasienradiologi_v");
        $laporanpasienradiologi_v = file_get_contents(__DIR__ . '/definitions/laporanpasienradiologi_v.sql');
        $this->execute($laporanpasienradiologi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241206_093126_GLS899_laporanpasienradiologi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241206_093126_GLS899_laporanpasienradiologi_v cannot be reverted.\n";

        return false;
    }
    */
}
