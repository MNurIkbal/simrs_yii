<?php

use yii\db\Migration;

/**
 * Class m250715_200157_migrate_table_uploaddokumen_r
 */
class m250715_200157_migrate_table_uploaddokumen_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "historydokumenklaim_pendaftaran_id_idx" ON "public"."dokumeneklaim_r" USING btree (
              "pendaftaran_id"
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "historydokumenklaim_type_dokumen_idx" ON "public"."dokumeneklaim_r" USING btree (
              "type_dokumen"
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250715_200157_migrate_table_uploaddokumen_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250715_200157_migrate_table_uploaddokumen_r cannot be reverted.\n";

        return false;
    }
    */
}
