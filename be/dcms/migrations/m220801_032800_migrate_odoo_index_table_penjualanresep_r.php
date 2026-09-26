<?php

use yii\db\Migration;

/**
 * Class m220801_032800_migrate_odoo_index_table_penjualanresep_r
 */
class m220801_032800_migrate_odoo_index_table_penjualanresep_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "ix_pr_karyawan";
        ');

        $this->execute('
            CREATE INDEX "ix_pr_karyawan" ON "public"."penjualanresep_r" USING btree (
              "karyawan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_pr_pasien";
        ');

        $this->execute('
            CREATE INDEX "ix_pr_pasien" ON "public"."penjualanresep_r" USING btree (
              "pasien_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_pr_penjamin";
        ');

        $this->execute('
            CREATE INDEX "ix_pr_penjamin" ON "public"."penjualanresep_r" USING btree (
              "penjamin_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_032800_migrate_odoo_index_table_penjualanresep_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_032800_migrate_odoo_index_table_penjualanresep_r cannot be reverted.\n";

        return false;
    }
    */
}
