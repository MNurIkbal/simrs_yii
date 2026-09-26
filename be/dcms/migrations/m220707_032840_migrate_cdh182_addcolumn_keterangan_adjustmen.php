<?php

use yii\db\Migration;

/**
 * Class m220707_032840_migrate_cdh182_addcolumn_keterangan_adjustmen
 */
class m220707_032840_migrate_cdh182_addcolumn_keterangan_adjustmen extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
			ALTER TABLE "public"."adjusmenbarangmasuk_t" ADD COLUMN IF NOT EXISTS "keterangan" text;
        ');
		
        $this->execute('
			ALTER TABLE "public"."adjusmenbarangkeluar_t" ADD COLUMN IF NOT EXISTS  "keterangan" text;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220707_032840_migrate_cdh182_addcolumn_keterangan_adjustmen cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_032840_migrate_cdh182_addcolumn_keterangan_adjustmen cannot be reverted.\n";

        return false;
    }
    */
}
