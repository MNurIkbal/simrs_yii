<?php

use yii\db\Migration;

/**
 * Class m230918_023319_inforesep_v_statusracikan_optimize
 */
class m230918_023319_inforesep_v_statusracikan_optimize extends Migration
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
        echo "m230918_023319_inforesep_v_statusracikan_optimize cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230918_023319_inforesep_v_statusracikan_optimize cannot be reverted.\n";

        return false;
    }
    */
}
