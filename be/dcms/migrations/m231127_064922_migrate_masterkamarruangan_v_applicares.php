<?php

use yii\db\Migration;

/**
 * Class m231127_064922_migrate_masterkamarruangan_v_applicares
 */
class m231127_064922_migrate_masterkamarruangan_v_applicares extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS masterkamarruangan_v");
        $masterkamarruangan_v = file_get_contents(__DIR__ . '/definitions/masterkamarruangan_v.sql');
        $this->execute($masterkamarruangan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231127_064922_migrate_masterkamarruangan_v_applicares cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_064922_migrate_masterkamarruangan_v_applicares cannot be reverted.\n";

        return false;
    }
    */
}
