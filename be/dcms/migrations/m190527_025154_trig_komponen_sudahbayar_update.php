<?php

use yii\db\Migration;

/**
 * Class m190527_025154_trig_komponen_sudahbayar_update
 */
class m190527_025154_trig_komponen_sudahbayar_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
     ALTER TABLE "public"."tindakansudahbayar_t" DISABLE TRIGGER "komponen_sudahbayar";
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190527_025154_trig_komponen_sudahbayar_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190527_025154_trig_komponen_sudahbayar_update cannot be reverted.\n";

        return false;
    }
    */
}
