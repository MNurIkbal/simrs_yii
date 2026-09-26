<?php

use yii\db\Migration;

/**
 * Class m190830_095159_pasienadmisi_t
 */
class m190830_095159_pasienadmisi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."pasienadmisi_t" 
                        ADD COLUMN "is_pasientitipan" bool DEFAULT false;
                        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190830_095159_pasienadmisi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190830_095159_pasienadmisi_t cannot be reverted.\n";

        return false;
    }
    */
}
