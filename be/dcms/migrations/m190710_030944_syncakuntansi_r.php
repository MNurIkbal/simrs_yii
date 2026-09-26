<?php

use yii\db\Migration;

/**
 * Class m190710_030944_syncakuntansi_r
 */
class m190710_030944_syncakuntansi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW IF exists public.sync_penerimaansupplier;
              ');

        $this->execute('
          DROP VIEW IF exists public.sync_penerimaansupplier_de;
              ');             

             $this->execute('
         ALTER TABLE "public"."syncakuntansi_r" 
        DROP COLUMN IF exists "adjusmenobat_id";
          ');

            $this->execute('
         ALTER TABLE "public"."syncakuntansi_r" 
        DROP COLUMN IF exists "adjusmenbarang_id";
          ');

            $this->execute('
         ALTER TABLE "public"."syncakuntansi_r" 
        DROP COLUMN IF exists "adjusmenbarangkeluar_id";
          ');

            


          $this->execute('
         ALTER TABLE syncakuntansi_r ADD adjusmenobat_id int4;
          ');

            $this->execute('
         ALTER TABLE syncakuntansi_r ADD adjusmenbarang_id int4;
             ');

              $this->execute('
          ALTER TABLE syncakuntansi_r ADD adjusmenbarangkeluar_id int4;
              ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190710_030944_syncakuntansi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190710_030944_syncakuntansi_r cannot be reverted.\n";

        return false;
    }
    */
}
