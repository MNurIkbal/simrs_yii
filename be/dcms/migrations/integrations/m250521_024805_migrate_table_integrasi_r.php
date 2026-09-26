<?php

use yii\db\Migration;

/**
 * Class m250521_024805_migrate_table_integrasi_r
 */
class m250521_024805_migrate_table_integrasi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "integrasi_roche_pendaftaran_idx" ON "public"."integrasi_roche_r" (
              "pendaftaran_id" ASC
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS  "integrasi_roche_pasienmasukpenunjang_idx" ON "public"."integrasi_roche_r" (
              "pasienmasukpenunjang_id" ASC
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS  "integrasi_roche_pasienkirimkeunitlain_idx" ON "public"."integrasi_roche_r" (
              "pasienkirimkeunitlain_id" ASC
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250521_024805_migrate_table_integrasi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250521_024805_migrate_table_integrasi_r cannot be reverted.\n";

        return false;
    }
    */
}
