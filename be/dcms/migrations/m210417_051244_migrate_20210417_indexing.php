<?php

use yii\db\Migration;

/**
 * Class m210417_051244_migrate_20210417_indexing
 */
class m210417_051244_migrate_20210417_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT exists "idx_tarif_daftartindakan_id" ON "public"."tariftindakan_m" USING btree (
  "daftartindakan_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');

        $this->execute('CREATE INDEX IF NOT exists "idx_tarif_kelaspelayanan_id" ON "public"."tariftindakan_m" USING btree (
  "kelaspelayanan_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');

        $this->execute('CREATE INDEX IF NOT exists "idx_tarif_komponen" ON "public"."tariftindakan_m" USING btree (
  "komponentarif_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');

        $this->execute('CREATE INDEX IF not exists "idx_tarif_penjamin_id" ON "public"."tariftindakan_m" USING btree (
  "penjamin_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX if not exists "idx_daftartindakan_id" ON "public"."tindakanruangan_mp" USING btree (
  "daftartindakan_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX if not exists "idx_ruangan_id" ON "public"."tindakanruangan_mp" USING btree (
  "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210417_051244_migrate_20210417_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210417_051244_migrate_20210417_indexing cannot be reverted.\n";

        return false;
    }
    */
}
