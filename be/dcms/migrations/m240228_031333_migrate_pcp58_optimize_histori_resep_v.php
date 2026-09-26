<?php

use yii\db\Migration;

/**
 * Class m240228_031333_migrate_pcp58_optimize_histori_resep_v
 */
class m240228_031333_migrate_pcp58_optimize_histori_resep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS detailresepobatalkes_v");
        $detailresepobatalkes_v = file_get_contents(__DIR__ . '/definitions/detailresepobatalkes_v.sql');
        $this->execute($detailresepobatalkes_v);

        $this->execute("DROP VIEW IF EXISTS historyresep_v");
        $historyresep_v = file_get_contents(__DIR__ . '/definitions/historyresep_v.sql');
        $this->execute($historyresep_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240228_031333_migrate_pcp58_optimize_histori_resep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240228_031333_migrate_pcp58_optimize_histori_resep_v cannot be reverted.\n";

        return false;
    }
    */
}
