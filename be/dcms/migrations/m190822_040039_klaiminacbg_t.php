<?php

use yii\db\Migration;

/**
 * Class m190822_040039_klaiminacbg_t
 */
class m190822_040039_klaiminacbg_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."klaiminacbg_t" 
                        ADD COLUMN "tarif_polieksekutif" float4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190822_040039_klaiminacbg_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190822_040039_klaiminacbg_t cannot be reverted.\n";

        return false;
    }
    */
}
