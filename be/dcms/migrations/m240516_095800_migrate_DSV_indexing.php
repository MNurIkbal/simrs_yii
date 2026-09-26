<?php

use yii\db\Migration;

/**
 * Class m240516_095800_migrate_DSV_indexing
 */
class m240516_095800_migrate_DSV_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE INDEX IF NOT EXISTS idx_obat_sudahbayar_pararel ON public.obatalkespasien_t USING btree (obatsudahbayar_id) WHERE (is_deleted IS FALSE);
        ");

        $this->execute("
            CREATE INDEX IF NOT EXISTS idx_sudahbayar_pararel ON public.tindakanpelayanan_t USING btree (tindakansudahbayar_id) WHERE (is_deleted IS FALSE);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240516_095800_migrate_DSV_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240516_095800_migrate_DSV_indexing cannot be reverted.\n";

        return false;
    }
    */
}
