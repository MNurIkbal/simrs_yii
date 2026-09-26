<?php

use yii\db\Migration;

/**
 * Class m240109_233059_migrate_gls_317_sy_infopasienbpjsklaim_v
 */
class m240109_233059_migrate_gls_317_sy_infopasienbpjsklaim_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_infopasienbpjsklaim_v");
        $sy_infopasienbpjsklaim_v = file_get_contents(__DIR__ . '/definitions/sy_infopasienbpjsklaim_v.sql');
        $this->execute($sy_infopasienbpjsklaim_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240109_233059_migrate_gls_317_sy_infopasienbpjsklaim_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240109_233059_migrate_gls_317_sy_infopasienbpjsklaim_v cannot be reverted.\n";

        return false;
    }
    */
}
