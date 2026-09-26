<?php

use yii\db\Migration;

/**
 * Class m231228_083225_migrate_GLS_251_infoinstruksi_v
 */
class m231228_083225_migrate_GLS_251_infoinstruksi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infoinstruksi_v");
        $infoinstruksi_v = file_get_contents(__DIR__ . '/definitions/infoinstruksi_v.sql');
        $this->execute($infoinstruksi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231228_083225_migrate_GLS_251_infoinstruksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231228_083225_migrate_GLS_251_infoinstruksi_v cannot be reverted.\n";

        return false;
    }
    */
}
