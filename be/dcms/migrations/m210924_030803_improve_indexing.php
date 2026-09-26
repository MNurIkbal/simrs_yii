<?php

use yii\db\Migration;

/**
 * Class m210924_030803_improve_indexing
 */
class m210924_030803_improve_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX if not exists "mutasiobatdetail_obatalkes_idx"  ON "public"."mutasiobatdetail_t" USING btree (
  "obatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');
        $this->execute('CREATE INDEX if not exists "mutasiobat_ruanganasal_idx"  ON "public"."mutasiobatruangan_t" USING btree (
  "ruanganasal_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');
        $this->execute('CREATE INDEX if not exists "penjualanresep_ruangan_idx"  ON "public"."penjualanresep_t" USING btree (
  "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210924_030803_improve_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210924_030803_improve_indexing cannot be reverted.\n";

        return false;
    }
    */
}
