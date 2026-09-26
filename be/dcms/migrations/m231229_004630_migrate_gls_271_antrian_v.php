<?php

use yii\db\Migration;

/**
 * Class m231229_004630_migrate_gls_271_antrian_v
 */
class m231229_004630_migrate_gls_271_antrian_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS antrian_v");
        $antrian_v = file_get_contents(__DIR__ . '/definitions/antrian_v.sql');
        $this->execute($antrian_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231229_004630_migrate_gls_271_antrian_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231229_004630_migrate_gls_271_antrian_v cannot be reverted.\n";

        return false;
    }
    */
}
