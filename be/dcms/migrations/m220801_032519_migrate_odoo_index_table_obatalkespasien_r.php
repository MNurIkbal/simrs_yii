<?php

use yii\db\Migration;

/**
 * Class m220801_032519_migrate_odoo_index_table_obatalkespasien_r
 */
class m220801_032519_migrate_odoo_index_table_obatalkespasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "ix_kelaspelayanan_obatalkespasien_r";
        ');

        $this->execute('
            CREATE INDEX "ix_kelaspelayanan_obatalkespasien_r" ON "public"."obatalkespasien_r" USING btree (
              "kelaspelayanan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_opr_obatalkespasien_id";
        ');

        $this->execute('
            CREATE INDEX "ix_opr_obatalkespasien_id" ON "public"."obatalkespasien_r" USING btree (
              "obatalkespasien_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_opr_pendaftaran_id";
        ');

        $this->execute('
            CREATE INDEX "ix_opr_pendaftaran_id" ON "public"."obatalkespasien_r" USING btree (
              "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_032519_migrate_odoo_index_table_obatalkespasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_032519_migrate_odoo_index_table_obatalkespasien_r cannot be reverted.\n";

        return false;
    }
    */
}
