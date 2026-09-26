<?php

use yii\db\Migration;

/**
 * Class m241004_061031_hotfix_sy_kunjungantagihan_v
 */
class m241004_061031_hotfix_sy_kunjungantagihan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_kunjungantagihan_v");
        $sy_kunjungantagihan_v = file_get_contents(__DIR__ . '/definitions/sy_kunjungantagihan_v.sql');
        $this->execute($sy_kunjungantagihan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241004_061031_hotfix_sy_kunjungantagihan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241004_061031_hotfix_sy_kunjungantagihan_v cannot be reverted.\n";

        return false;
    }
    */
}
