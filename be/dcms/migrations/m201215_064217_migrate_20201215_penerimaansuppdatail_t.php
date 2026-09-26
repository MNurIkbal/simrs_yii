<?php

use yii\db\Migration;

/**
 * Class m201215_064217_migrate_20201215_penerimaansuppdatail_t
 */
class m201215_064217_migrate_20201215_penerimaansuppdatail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."penerimaansuppdetail_t" ADD COLUMN IF NOT exists "harga_netto_satuan" float8;');

        $this->execute('COMMENT ON COLUMN "public"."penerimaansuppdetail_t"."harga_netto" IS \'total harga netto\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201215_064217_migrate_20201215_penerimaansuppdatail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201215_064217_migrate_20201215_penerimaansuppdatail_t cannot be reverted.\n";

        return false;
    }
    */
}
