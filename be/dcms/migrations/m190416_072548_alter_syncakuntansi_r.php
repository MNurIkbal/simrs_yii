<?php

use yii\db\Migration;

use Doco\components\DocoHelpers;

/**
 * Class m190416_072548_alter_syncakuntansi_r
 */
class m190416_072548_alter_syncakuntansi_r extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $schema = DocoHelpers::checkSchemaTabel('syncakuntansi_r',[
            'pemakaianobatdetail_id',
            'adjusmenobatkeluar_id',
            'penjualanresep_id',
        ]);
        if (empty($schema)) {
            $this->execute('
                ALTER TABLE "public"."syncakuntansi_r" 
                 ADD COLUMN "pemakaianobatdetail_id" int4,
                 ADD COLUMN "adjusmenobatkeluar_id" int4,
                 ADD COLUMN "penjualanresep_id" int4,
                 ADD PRIMARY KEY ("syncakuntansi_id");
            ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190416_072548_alter_syncakuntansi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190416_072548_alter_syncakuntansi_r cannot be reverted.\n";

        return false;
    }
    */
}
