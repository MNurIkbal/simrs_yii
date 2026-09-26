<?php

use yii\db\Migration;

/**
 * Class m240221_082206_migrate_DSV_1130_laporancarapulang_v
 */
class m240221_082206_migrate_DSV_1130_laporancarapulang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporancarapulang_v");
        $laporancarapulang_v = file_get_contents(__DIR__ . '/definitions/laporancarapulang_v.sql');
        $this->execute($laporancarapulang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240221_082206_migrate_DSV_1130_laporancarapulang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240221_082206_migrate_DSV_1130_laporancarapulang_v cannot be reverted.\n";

        return false;
    }
    */
}
