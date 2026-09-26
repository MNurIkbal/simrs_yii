<?php

use yii\db\Migration;

/**
 * Class m250417_075732_migrate_improve_view_laporankunjunganri_v
 */
class m250417_075732_migrate_improve_view_laporankunjunganri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporankunjunganri_v");
        $laporankunjunganri_v = file_get_contents(__DIR__ . '/definitions/laporankunjunganri_v.sql');
        $this->execute($laporankunjunganri_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250417_075732_migrate_improve_view_laporankunjunganri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250417_075732_migrate_improve_view_laporankunjunganri_v cannot be reverted.\n";

        return false;
    }
    */
}
