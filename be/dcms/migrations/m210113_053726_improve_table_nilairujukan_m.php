<?php

use yii\db\Migration;

/**
 * Class m210113_053726_improve_table_nilairujukan_m
 */
class m210113_053726_improve_table_nilairujukan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."nilairujukan_m" 
              ADD IF NOT EXISTS "umur_awal" int4,
              ADD IF NOT EXISTS "umur_akhir" int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210113_053726_improve_table_nilairujukan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210113_053726_improve_table_nilairujukan_m cannot be reverted.\n";

        return false;
    }
    */
}
