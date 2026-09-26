<?php

use yii\db\Migration;

/**
 * Class m241104_115034_migrate_PCP94_infoprogramterapiapprove_v
 */
class m241104_115034_migrate_PCP94_infoprogramterapiapprove_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infoprogramterapiapprove_v");
        $infoprogramterapiapprove_v = file_get_contents(__DIR__ . '/definitions/infoprogramterapiapprove_v.sql');
        $this->execute($infoprogramterapiapprove_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241104_115034_migrate_PCP94_infoprogramterapiapprove_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241104_115034_migrate_PCP94_infoprogramterapiapprove_v cannot be reverted.\n";

        return false;
    }
    */
}
