<?php

use yii\db\Migration;

/**
 * Class m240730_104419_migrate_RPP1407_detailresepobatalkes_v
 */
class m240730_104419_migrate_RPP1407_detailresepobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS detailresepobatalkes_v");
        $detailresepobatalkes_v = file_get_contents(__DIR__ . '/definitions/detailresepobatalkes_v.sql');
        $this->execute($detailresepobatalkes_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240730_104419_migrate_RPP1407_detailresepobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240730_104419_migrate_RPP1407_detailresepobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
