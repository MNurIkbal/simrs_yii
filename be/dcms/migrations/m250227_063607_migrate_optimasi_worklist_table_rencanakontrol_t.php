<?php

use yii\db\Migration;

/**
 * Class m250227_063607_migrate_optimasi_worklist_table_rencanakontrol_t
 */
class m250227_063607_migrate_optimasi_worklist_table_rencanakontrol_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "konsulpoli_id_idx_rencanakontrol" ON "public"."rencanakontrol_t" USING btree (
              konsulpoli_id "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "pendaftaran_id_idx_rencanakontrol" ON "public"."rencanakontrol_t" USING btree (
              pendaftaran_id "pg_catalog"."int4_ops" ASC NULLS LAST
            )
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250227_063607_migrate_optimasi_worklist_table_rencanakontrol_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250227_063607_migrate_optimasi_worklist_table_rencanakontrol_t cannot be reverted.\n";

        return false;
    }
    */
}
