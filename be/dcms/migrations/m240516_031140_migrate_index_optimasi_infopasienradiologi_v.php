<?php

use yii\db\Migration;

/**
 * Class m240516_031140_migrate_index_optimasi_infopasienradiologi_v
 */
class m240516_031140_migrate_index_optimasi_infopasienradiologi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE INDEX IF NOT EXISTS idx_pasienmasukpenunjang_pararel ON public.tindakanpelayanan_t  USING btree (pasienmasukpenunjang_id) WHERE ((tindakanpelayananasal_id IS NULL) AND (NOT is_deleted) AND (instalasi_id = 5));");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_pasienkirimkeunitlain_pararel ON public.pasienkirimkeunitlain_t  USING btree (pasienkirimkeunitlain_id) WHERE ((pasienadmisi_id IS NULL) AND (instalasi_id = 5));");
        $this->execute("CREATE INDEX IF NOT EXISTS idx_pasienmasukpenunjang_pararel_withoutdeleted ON public.tindakanpelayanan_t USING btree (pasienmasukpenunjang_id) WHERE ((tindakanpelayananasal_id IS NULL)  AND (instalasi_id = 5));");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240516_031140_migrate_index_optimasi_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240516_031140_migrate_index_optimasi_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }
    */
}
