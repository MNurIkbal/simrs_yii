<?php

use yii\db\Migration;

/**
 * Class m220314_125848_migrate_BTS87_bpjs_t
 */
class m220314_125848_migrate_BTS87_bpjs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bpjs_t"   
            ADD COLUMN IF NOT EXISTS "kode_dpjp_spri" varchar(50) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "nama_dpjp_spri" varchar(255) COLLATE "pg_catalog"."default";
        ');

        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."kode_dpjp_spri" IS \'nampung spri\';
          ');

        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."nama_dpjp_spri" IS \'nampung spri\';
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220314_125848_migrate_BTS87_bpjs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220314_125848_migrate_BTS87_bpjs_t cannot be reverted.\n";

        return false;
    }
    */
}
