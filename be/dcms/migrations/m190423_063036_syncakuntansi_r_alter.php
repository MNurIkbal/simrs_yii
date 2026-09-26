<?php

use yii\db\Migration;

use Doco\components\DocoHelpers;
/**
 * Class m190423_063036_syncakuntansi_r_alter
 */
class m190423_063036_syncakuntansi_r_alter extends Migration
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
        $this->execute('
        ALTER TABLE public.syncakuntansi_r 
            DROP COLUMN pemakaianobatdetail_id,
            DROP COLUMN adjusmenobatkeluar_id,
            DROP COLUMN penjualanresep_id;
        ');
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190423_063036_syncakuntansi_r_alter cannot be reverted.\n";

        return false;
    }
    */
}
