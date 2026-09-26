<?php

use yii\db\Migration;

/**
 * Class m250731_105410_GLBJ778_indexing_created_date_pendaftaran_pembayaran_pasienadmisi
 */
class m250731_105410_GLBJ778_indexing_created_date_pendaftaran_pembayaran_pasienadmisi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE INDEX IF NOT EXISTS pendaftaran_t_created_date_idx ON public.pendaftaran_t USING btree (created_date)");
        $this->execute("CREATE INDEX IF NOT EXISTS pembayaran_t_created_date_idx ON public.pembayaran_t USING btree (created_date)");
        $this->execute("CREATE INDEX IF NOT EXISTS pasienadmisi_t_created_date_idx ON public.pasienadmisi_t USING btree (created_date)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250731_105410_GLBJ778_indexing_created_date_pendaftaran_pembayaran_pasienadmisi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250731_105410_GLBJ778_indexing_created_date_pendaftaran_pembayaran_pasienadmisi cannot be reverted.\n";

        return false;
    }
    */
}
