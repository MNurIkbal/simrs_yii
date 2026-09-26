<?php

use yii\db\Migration;

/**
 * Class m190617_032208_create_index_syncakuntansi_r
 */
class m190617_032208_create_index_syncakuntansi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        DROP INDEX IF exists syncakuntansi_adjusmenobatkeluar_id_idx;
        ');

        $this->execute('
        DROP INDEX IF exists syncakuntansi_jenisobatalkes_id_idx;
        ');

        $this->execute('
        DROP INDEX IF exists syncakuntansi_komponentarif_id_idx;
        ');

        $this->execute('
        DROP INDEX IF exists syncakuntansi_obatalkespasien_id_idx;
        ');

        $this->execute('
        DROP INDEX IF exists syncakuntansi_pemakaianobatdetail_id_idx;
        ');

        $this->execute('
        DROP INDEX IF exists syncakuntansi_penjualanresep_id_idx;
        ');

        $this->execute('
        DROP INDEX IF exists syncakuntansi_tindakankomponen_id_idx;
        ');
        

        $this->execute('
    CREATE INDEX "syncakuntansi_adjusmenobatkeluar_id_idx" ON "public"."syncakuntansi_r" USING btree (
  "adjusmenobatkeluar_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
        ');
        

        $this->execute('
CREATE INDEX "syncakuntansi_jenisobatalkes_id_idx" ON "public"."syncakuntansi_r" USING btree (
  "jenisobatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
        ');


 $this->execute('
CREATE INDEX "syncakuntansi_komponentarif_id_idx" ON "public"."syncakuntansi_r" USING btree (
  "komponentarif_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
        ');

  $this->execute('
CREATE INDEX "syncakuntansi_obatalkespasien_id_idx" ON "public"."syncakuntansi_r" USING btree (
  "obatalkespasien_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
        ');

  $this->execute('
CREATE INDEX "syncakuntansi_pemakaianobatdetail_id_idx" ON "public"."syncakuntansi_r" USING btree (
  "pemakaianobatdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
        ');

 $this->execute('
CREATE INDEX "syncakuntansi_penjualanresep_id_idx" ON "public"."syncakuntansi_r" USING btree (
  "penjualanresep_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
        ');


 $this->execute('
CREATE INDEX "syncakuntansi_tindakankomponen_id_idx" ON "public"."syncakuntansi_r" USING btree (
  "tindakankomponen_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);
        ');
  



    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190617_032208_create_index_syncakuntansi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190617_032208_create_index_syncakuntansi_r cannot be reverted.\n";

        return false;
    }
    */
}
