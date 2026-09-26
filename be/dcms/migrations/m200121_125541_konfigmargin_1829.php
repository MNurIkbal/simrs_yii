<?php

use yii\db\Migration;

/**
 * Class m200121_125541_konfigmargin_1829
 */
class m200121_125541_konfigmargin_1829 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigmargin_k" 
                        ALTER COLUMN "perda_margin" DROP NOT NULL;');

        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" 
                      ADD COLUMN "embalase_racikan" float8,
                      ADD COLUMN "embalase_nonracikan" float8;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200121_125541_konfigmargin_1829 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200121_125541_konfigmargin_1829 cannot be reverted.\n";

        return false;
    }
    */
}
