<?php

use yii\db\Migration;

/**
 * Class m230811_090245_migrate_RPP285_infopasienbelumbayar_v
 */
class m230811_090245_migrate_RPP285_infopasienbelumbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienbelumbayar_v");
        $infopasienbelumbayar_v = file_get_contents(__DIR__ . '/definitions/infopasienbelumbayar_v.sql');
        $this->execute($infopasienbelumbayar_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230811_090245_migrate_RPP285_infopasienbelumbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230811_090245_migrate_RPP285_infopasienbelumbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
