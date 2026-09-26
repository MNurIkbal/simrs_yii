<?php

use yii\db\Migration;

/**
 * Class m210107_072915_migrate_live_20210107_tabel_pendaftaran_t
 */
class m210107_072915_migrate_live_20210107_tabel_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
  ADD COLUMN IF NOT EXISTS "limit_tagihan" float8 DEFAULT 0;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_072915_migrate_live_20210107_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_072915_migrate_live_20210107_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
