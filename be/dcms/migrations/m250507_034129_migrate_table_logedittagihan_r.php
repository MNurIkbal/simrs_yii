<?php

use yii\db\Migration;

/**
 * Class m250507_034129_migrate_table_logedittagihan_r
 */
class m250507_034129_migrate_table_logedittagihan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "logedittagihan_id_idx" ON "public"."logedittagihan_r" USING btree (
              "logedittagihan_id" DESC
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "logedittagihan_pendaftaran_id_idx" ON "public"."logedittagihan_r" USING btree (
              "pendaftaran_id" ASC
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "logedittagihan_pelayanan_id_idx" ON "public"."logedittagihan_r" USING btree (
              "pelayanan_id"
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "logedittagihan_is_obat_idx" ON "public"."logedittagihan_r" USING btree (
              "is_obat"
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250507_034129_migrate_table_logedittagihan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250507_034129_migrate_table_logedittagihan_r cannot be reverted.\n";

        return false;
    }
    */
}
