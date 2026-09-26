<?php

use yii\db\Migration;
use Doco\components\DocoHelpers;

/**
 * Class m190520_034431_syncakuntansi_r_update
 */
class m190520_034431_syncakuntansi_r_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $schema = DocoHelpers::checkSchemaTabel('syncakuntansi_r',[
            'pembayaranpelayanan_id'
        ]);
        if (empty($schema)) {
            $this->execute('
                ALTER TABLE "public"."syncakuntansi_r" 
        ADD COLUMN "pembayaranpelayanan_id" int4;
            ');
        }
     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190520_034431_syncakuntansi_r_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190520_034431_syncakuntansi_r_update cannot be reverted.\n";

        return false;
    }
    */
}
