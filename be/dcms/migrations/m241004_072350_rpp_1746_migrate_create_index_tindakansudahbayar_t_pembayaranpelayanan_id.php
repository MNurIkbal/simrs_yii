<?php

use yii\db\Migration;

/**
 * Class m241004_072350_rpp_1746_migrate_create_index_tindakansudahbayar_t_pembayaranpelayanan_id
 */
class m241004_072350_rpp_1746_migrate_create_index_tindakansudahbayar_t_pembayaranpelayanan_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS tindakansudahbayar_t_pembayaranpelayanan_id_idx ON public.tindakansudahbayar_t USING btree (pembayaranpelayanan_id);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241004_072350_rpp_1746_migrate_create_index_tindakansudahbayar_t_pembayaranpelayanan_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241004_072350_rpp_1746_migrate_create_index_tindakansudahbayar_t_pembayaranpelayanan_id cannot be reverted.\n";

        return false;
    }
    */
}
