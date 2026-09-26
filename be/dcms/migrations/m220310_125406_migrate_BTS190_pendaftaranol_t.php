<?php

use yii\db\Migration;

/**
 * Class m220310_125406_migrate_BTS190_pendaftaranol_t
 */
class m220310_125406_migrate_BTS190_pendaftaranol_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaranol_t" 
          ADD COLUMN IF NOT EXISTS "jeniskunjungan" varchar(50);
        ');

        $this->execute('COMMENT ON COLUMN "public"."pendaftaranol_t"."jeniskunjungan" IS \'ambil dari lookup_m jeniskunjungan\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220310_125406_migrate_BTS190_pendaftaranol_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220310_125406_migrate_BTS190_pendaftaranol_t cannot be reverted.\n";

        return false;
    }
    */
}
