<?php

use yii\db\Migration;

/**
 * Class m190725_041946_syncakuntansi_r_alter
 */
class m190725_041946_syncakuntansi_r_alter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          ALTER TABLE "public"."syncakuntansi_r" 
                ADD COLUMN "returpenerimaanobat_id" int4,
                ADD COLUMN "returpenerimaanbarang_id" int4;
        ');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_041946_syncakuntansi_r_alter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_041946_syncakuntansi_r_alter cannot be reverted.\n";

        return false;
    }
    */
}
