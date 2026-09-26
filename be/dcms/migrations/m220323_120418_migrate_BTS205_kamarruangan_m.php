<?php

use yii\db\Migration;

/**
 * Class m220323_120418_migrate_BTS205_kamarruangan_m
 */
class m220323_120418_migrate_BTS205_kamarruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."kamarruangan_m" 
            ADD COLUMN IF NOT EXISTS "is_ttrekaptersedia" bool DEFAULT true;
        ');

        $this->execute('
            COMMENT ON COLUMN "public"."kamarruangan_m"."is_ttrekaptersedia" IS \'untuk membedakan tt tersedia sehingga data tidak minus di lap kinerja\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220323_120418_migrate_BTS205_kamarruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220323_120418_migrate_BTS205_kamarruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
