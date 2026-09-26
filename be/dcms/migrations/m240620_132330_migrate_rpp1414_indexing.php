<?php

use yii\db\Migration;

/**
 * Class m240620_132330_migrate_rpp1414_indexing
 */
class m240620_132330_migrate_rpp1414_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "obatalkespasien_t_deleted_true" ON "public"."obatalkespasien_t" (
            "is_deleted"
            ) WHERE is_deleted IS TRUE;
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "obatalkespasien_t_instruksitindakanbmhp_id_idx" ON "public"."obatalkespasien_t" (
            "instruksitindakanbmhp_id"
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "resepturdetail_tgl_resepturdetail" ON "public"."resepturdetail_t" USING btree (
                "created_date" "pg_catalog"."timestamp_ops" DESC NULLS LAST
                );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "public"."instruksi_tgl_instruksi_id_idx";
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "instruksi_tgl_instruksi_id_idx" ON "public"."instruksi_t" USING btree (
            "tgl_instruksi" "pg_catalog"."timestamp_ops" DESC NULLS LAST
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "reseptur_instruksi_id_idx" ON "public"."reseptur_t" (
            "instruksi_id"
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240620_132330_migrate_rpp1414_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240620_132330_migrate_rpp1414_indexing cannot be reverted.\n";

        return false;
    }
    */
}
