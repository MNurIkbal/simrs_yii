<?php

use yii\db\Migration;

/**
 * Class m250918_033243_RPP2236_2241_2242_lapkunjunganpasrs_v
 */
class m250918_033243_RPP2236_2241_2242_lapkunjunganpasrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS lapkunjunganpasrs_v");
        $lapkunjunganpasrs_v = file_get_contents(__DIR__ . '/definitions/lapkunjunganpasrs_v.sql');
        $this->execute($lapkunjunganpasrs_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250918_033243_RPP2236_2241_2242_lapkunjunganpasrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250918_033243_RPP2236_2241_2242_lapkunjunganpasrs_v cannot be reverted.\n";

        return false;
    }
    */
}
