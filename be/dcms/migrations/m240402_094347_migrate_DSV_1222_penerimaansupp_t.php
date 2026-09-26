<?php

use yii\db\Migration;

/**
 * Class m240402_094347_migrate_DSV_1222_penerimaansupp_t
 */
class m240402_094347_migrate_DSV_1222_penerimaansupp_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."penerimaansupp_t" 
        ADD COLUMN IF NOT EXISTS "sumber_penerimaan" int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240402_094347_migrate_DSV_1222_penerimaansupp_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240402_094347_migrate_DSV_1222_penerimaansupp_t cannot be reverted.\n";

        return false;
    }
    */
}
