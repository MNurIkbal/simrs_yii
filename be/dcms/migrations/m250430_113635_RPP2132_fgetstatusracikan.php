<?php

use yii\db\Migration;

/**
 * Class m250430_113635_RPP2132_fgetstatusracikan
 */
class m250430_113635_RPP2132_fgetstatusracikan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS public.fgetstatusracikan;');
        $fgetstatusracikan = file_get_contents(__DIR__ . '/definitions/fgetstatusracikan.sql');
        $this->execute($fgetstatusracikan);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250430_113635_RPP2132_fgetstatusracikan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250430_113635_RPP2132_fgetstatusracikan cannot be reverted.\n";

        return false;
    }
    */
}
