<?php

use yii\db\Migration;

/**
 * Class m240405_083901_migrate_GLBJ470_infopermintaankonsul_v
 */
class m240405_083901_migrate_GLBJ470_infopermintaankonsul_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopermintaankonsul_v");
        $infopermintaankonsul_v = file_get_contents(__DIR__ . '/definitions/infopermintaankonsul_v.sql');
        $this->execute($infopermintaankonsul_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240405_083901_migrate_GLBJ470_infopermintaankonsul_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240405_083901_migrate_GLBJ470_infopermintaankonsul_v cannot be reverted.\n";

        return false;
    }
    */
}
