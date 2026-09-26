<?php

use yii\db\Migration;

/**
 * Class m250820_102029_RPP1966CreateBarangHistoryV
 */
class m250820_102029_RPP1966CreateBarangHistoryV extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS baranghistory_v');

        $baranghistory_v = file_get_contents(__DIR__ . '/definitions/baranghistory_v.sql');
        $this->execute($baranghistory_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250820_102029_RPP1966CreateBarangHistoryV cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250820_102029_RPP1966CreateBarangHistoryV cannot be reverted.\n";

        return false;
    }
    */
}
