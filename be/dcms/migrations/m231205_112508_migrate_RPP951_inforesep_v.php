<?php

use yii\db\Migration;

/**
 * Class m231205_112508_migrate_RPP951_inforesep_v
 */
class m231205_112508_migrate_RPP951_inforesep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS inforesep_v");
        $inforesep_v = file_get_contents(__DIR__ . '/definitions/inforesep_v.sql');
        $this->execute($inforesep_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231205_112508_migrate_RPP951_inforesep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231205_112508_migrate_RPP951_inforesep_v cannot be reverted.\n";

        return false;
    }
    */
}
