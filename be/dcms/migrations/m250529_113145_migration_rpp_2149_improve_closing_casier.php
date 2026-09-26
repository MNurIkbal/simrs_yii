<?php

use yii\db\Migration;

/**
 * Class m250529_113145_migration_rpp_2149_improve_closing_casier
 */
class m250529_113145_migration_rpp_2149_improve_closing_casier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS public.closing_kasir_view;');
        $closing_kasir_view = file_get_contents(__DIR__ . '/definitions/closing_kasir_view.sql');
        $this->execute($closing_kasir_view);

        $this->execute('
            CREATE INDEX pembayaran_t_deleted_date_idx ON public.pembayaran_t USING btree (deleted_date);
        ');

        $this->execute('
            CREATE INDEX pembayaran_t_pasienadmisi_id_idx ON public.pembayaran_t USING btree (pasienadmisi_id);
        ');

        $this->execute('
            CREATE INDEX pembayaran_t_pemberianpiutang_id_idx ON public.pembayaran_t USING btree (pemberianpiutang_id);
        ');

        $this->execute('
            CREATE INDEX pembayaranpelayanan_t_penjualanresep_id_idx ON public.pembayaranpelayanan_t USING btree (penjualanresep_id);
        ');

        $this->execute('
            CREATE INDEX idx_penjualanresep_status ON public.penjualanresep_t USING btree (status_reseptur) WHERE (is_deleted = false);
        ');

        $this->execute('
            CREATE INDEX penjamin_m_is_deleted_idx ON public.penjamin_m USING btree (is_deleted);
        ');

        $this->execute('
            CREATE INDEX pasienadmisi_t_carabayar_id_idx ON public.pasienadmisi_t USING btree (carabayar_id);
        ');

        $this->execute('
            CREATE INDEX tandabuktibayar_t_pembayaran_id_idx ON public.tandabuktibayar_t USING btree (pembayaran_id);
        ');

        $this->execute('
            CREATE INDEX loginpemakai_k_pegawai_id_idx ON public.loginpemakai_k USING btree (pegawai_id);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250529_113145_migration_rpp_2149_improve_closing_casier cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250529_113145_migration_rpp_2149_improve_closing_casier cannot be reverted.\n";

        return false;
    }
    */
}
