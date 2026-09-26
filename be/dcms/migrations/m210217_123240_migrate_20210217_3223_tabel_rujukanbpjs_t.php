<?php

use yii\db\Migration;

/**
 * Class m210217_123240_migrate_20210217_3223_tabel_rujukanbpjs_t
 */
class m210217_123240_migrate_20210217_3223_tabel_rujukanbpjs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."rujukanbpjs_t" 
            ADD COLUMN IF NOT EXISTS "tglsep" timestamp(6);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210217_123240_migrate_20210217_3223_tabel_rujukanbpjs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210217_123240_migrate_20210217_3223_tabel_rujukanbpjs_t cannot be reverted.\n";

        return false;
    }
    */
}
