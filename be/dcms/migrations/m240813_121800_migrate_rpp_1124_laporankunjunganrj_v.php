<?php

use yii\db\Migration;

/**
 * Class m240813_121800_migrate_rpp_1124_laporankunjunganrj_v
 */
class m240813_121800_migrate_rpp_1124_laporankunjunganrj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporankunjunganrj_v");
        $laporankunjunganrj_v = file_get_contents(__DIR__ . '/definitions/laporankunjunganrj_v.sql');
        $this->execute($laporankunjunganrj_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240813_121800_migrate_rpp_1124_laporankunjunganrj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240813_121800_migrate_rpp_1124_laporankunjunganrj_v cannot be reverted.\n";

        return false;
    }
    */
}
