<?php

use yii\db\Migration;

/**
 * Class m251009_024956_rpp_2262_satusehat_integrasi_t_indexing_pendaftaran_id_type
 */
class m251009_024956_rpp_2262_satusehat_integrasi_t_indexing_pendaftaran_id_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS satusehat_integrasi_t_pendaftaran_id_idx ON public.satusehat_integrasi_t USING btree (pendaftaran_id);');
        $this->execute('CREATE INDEX IF NOT EXISTS satusehat_integrasi_t_type_idx ON public.satusehat_integrasi_t USING btree (type);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251009_024956_rpp_2262_satusehat_integrasi_t_indexing_pendaftaran_id_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251009_024956_rpp_2262_satusehat_integrasi_t_indexing_pendaftaran_id_type cannot be reverted.\n";

        return false;
    }
    */
}
