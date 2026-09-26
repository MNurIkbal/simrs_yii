<?php

use yii\db\Migration;

/**
 * Class m220306_045911_migrate_BTS137_bpjs_t
 */
class m220306_045911_migrate_BTS137_bpjs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bpjs_t" 
          ADD COLUMN IF NOT EXISTS "status_pulang" varchar(50),
          ADD COLUMN IF NOT EXISTS "no_surat_meninggal" varchar(50),
          ADD COLUMN IF NOT EXISTS "tgl_meninggal_bpjs" timestamp(0),
          ADD COLUMN IF NOT EXISTS "no_lp_manual" varchar(50);
        ');

        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."status_pulang" IS \'ambil dari lookup carapulang_inacbg\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220306_045911_migrate_BTS137_bpjs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220306_045911_migrate_BTS137_bpjs_t cannot be reverted.\n";

        return false;
    }
    */
}
