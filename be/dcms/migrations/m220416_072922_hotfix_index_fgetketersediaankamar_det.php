<?php

use yii\db\Migration;

/**
 * Class m220416_072922_hotfix_index_fgetketersediaankamar_det
 */
class m220416_072922_hotfix_index_fgetketersediaankamar_det extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT exists "idx_tarif_kamarruangan_id" ON "public"."tariftindakan_m" USING btree (
            "kamarruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');
        
        $this->execute('CREATE INDEX IF NOT exists "kamarruangan_m.kamarruangan_id" ON "public"."kamarruangan_m" USING btree (
            "kamarruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');
        
        $this->execute('CREATE INDEX IF NOT exists "kamarruangan_m.kelaspelayanan_id" ON "public"."kamarruangan_m" USING btree (
            "kelaspelayanan_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');

        $this->execute('CREATE INDEX IF NOT exists "kamarruangan_m.ruangan_id" ON "public"."kamarruangan_m" USING btree (
            "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');
        
        $this->execute('CREATE INDEX IF NOT exists  "kamartempattidur_m.kamarraungan_id" ON "public"."kamartempattidur_m" USING btree (
            "kamarruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');
        
        $this->execute('CREATE INDEX IF NOT exists  "kamartempattidur_m.kamartempattidur_id" ON "public"."kamartempattidur_m" USING btree (
            "kamartempattidur_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');
        
        $this->execute('CREATE INDEX IF NOT exists  "pasienadmisi_kamarruangan_id_idx" ON "public"."pasienadmisi_t" USING btree (
            "kamarruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');
        
        $this->execute('CREATE INDEX IF NOT exists  "pasienadmisi_kamartempattidur_id_idx" ON "public"."pasienadmisi_t" USING btree (
            "kamartempattidur_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220416_072922_hotfix_index_fgetketersediaankamar_det cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220416_072922_hotfix_index_fgetketersediaankamar_det cannot be reverted.\n";

        return false;
    }
    */
}
