<?php

use yii\db\Migration;

/**
 * Class m230713_012901_migrate_RPP339_infokunjunganrj_v
 */
class m230810_152200_migrate_RPP510_laporankunjunganrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
     {
         $this->execute("DROP VIEW IF EXISTS laporankunjunganrs_v");
         $laporankunjunganrs_v = file_get_contents(__DIR__ . '/definitions/laporankunjunganrs_v.sql');
         $this->execute($laporankunjunganrs_v);
     }

     /**
      * {@inheritdoc}
      */
     public function safeDown()
     {
         echo "m230810_152200_migrate_RPP510_laporankunjunganrs_v cannot be reverted.\n";

         return false;
     }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230713_012901_migrate_RPP339_infokunjunganrj_v cannot be reverted.\n";

        return false;
    }
    */
}
