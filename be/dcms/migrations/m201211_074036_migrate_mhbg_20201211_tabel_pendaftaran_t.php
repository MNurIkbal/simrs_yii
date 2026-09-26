<?php

use yii\db\Migration;

/**
 * Class m201211_074036_migrate_mhbg_20201211_tabel_pendaftaran_t
 */
class m201211_074036_migrate_mhbg_20201211_tabel_pendaftaran_t extends Migration
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
        echo "m201211_074036_migrate_mhbg_20201211_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201211_074036_migrate_mhbg_20201211_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
