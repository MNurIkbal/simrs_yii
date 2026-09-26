<?php

use yii\db\Migration;

/**
 * Class m210210_033242_migreate_20210210_indexing
 */
class m210210_033242_migreate_20210210_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS "konfigmargin_group_margin" ON "public"."konfigmargin_k" USING btree (
                        "groupmargin_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "konfigmargin_jenisobat" ON "public"."konfigmargin_k" USING btree (
                        "jenisobatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "konfigmargin_kelas" ON "public"."konfigmargin_k" USING btree (
                         "kelaspelayanan_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "obatalkes_jenis_obatalkes" ON "public"."obatalkes_m" USING btree (
                        "jenisobatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "obatalkes_satuan_besar" ON "public"."obatalkes_m" USING btree (
                        "satuanbesar_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "obatalkes_satuan_kecil" ON "public"."obatalkes_m" USING btree (
                        "satuankecil_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "nama_pasien_idx" ON "public"."pasien_m" USING btree (
                        "nama_pasien" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE UNIQUE INDEX IF NOT EXISTS "no_rekam_medik_idx" ON "public"."pasien_m" USING btree (
                        "no_rekam_medik" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "tgl_lahir_idx" ON "public"."pasien_m" USING btree (
                         "tanggal_lahir" "pg_catalog"."date_ops" ASC NULLS LAST)');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210210_033242_migreate_20210210_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210210_033242_migreate_20210210_indexing cannot be reverted.\n";

        return false;
    }
    */
}
