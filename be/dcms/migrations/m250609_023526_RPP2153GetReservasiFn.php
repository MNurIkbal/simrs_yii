<?php

use yii\db\Migration;

/**
 * Class m250609_023526_RPP2153GetReservasiFn
 */
class m250609_023526_RPP2153GetReservasiFn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS get_reservasi");
        $get_reservasi = file_get_contents(__DIR__ . '/definitions/get_reservasi_fn.sql');
        $this->execute($get_reservasi);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250609_023526_RPP2153GetReservasiFn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250609_023526_RPP2153GetReservasiFn cannot be reverted.\n";

        return false;
    }
    */
}
