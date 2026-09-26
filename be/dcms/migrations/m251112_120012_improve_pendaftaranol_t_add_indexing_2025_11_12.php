<?php

use yii\db\Migration;

/**
 * Class m251112_120012_improve_pendaftaranol_t_add_indexing_2025_11_12
 */
class m251112_120012_improve_pendaftaranol_t_add_indexing_2025_11_12 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE INDEX IF NOT EXISTS pendaftaranol_t_antrian_id_idx 
            ON public.pendaftaranol_t USING btree (antrian_id);
        ");

        $this->execute("
            CREATE INDEX IF NOT EXISTS antrianjkn_r_pendaftaranol_id_idx 
            ON public.antrianjkn_r USING btree (pendaftaranol_id);
        ");

        $this->execute("
            CREATE INDEX IF NOT EXISTS pendaftaranol_t_pegawai_id_idx 
            ON public.pendaftaranol_t USING btree (pegawai_id);
        ");

        $this->execute("
            CREATE INDEX IF NOT EXISTS pendaftaranol_t_jadwalbukapoli_id_idx 
            ON public.pendaftaranol_t USING btree (jadwalbukapoli_id);
        ");

        $this->execute("
            CREATE INDEX IF NOT EXISTS pendaftaranol_t_jadwaldokter_id_idx 
            ON public.pendaftaranol_t USING btree (jadwaldokter_id);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251112_120012_improve_pendaftaranol_t_add_indexing_2025_11_12 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251112_120012_improve_pendaftaranol_t_add_indexing_2025_11_12 cannot be reverted.\n";

        return false;
    }
    */
}
