<?php

use yii\db\Migration;

/**
 * Class m220322_031632_migrate_BTS194_pendaftaran_t
 */
class m220322_031632_migrate_BTS194_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
          ADD COLUMN IF NOT EXISTS "is_pengajuan_sep" bool DEFAULT false,
          ADD COLUMN IF NOT EXISTS "additional_pengajuan_sep" text;
        ');

        $this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."is_pengajuan_sep" IS \'kebutuhan bpjs\';
        ');

        $this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."additional_pengajuan_sep" IS \'kebutuhan bpjs\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220322_031632_migrate_BTS194_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220322_031632_migrate_BTS194_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
