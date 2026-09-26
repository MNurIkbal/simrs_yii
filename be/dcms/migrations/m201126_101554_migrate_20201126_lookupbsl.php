<?php

use yii\db\Migration;

/**
 * Class m201126_101554_migrate_20201126_lookupbsl
 */
class m201126_101554_migrate_20201126_lookupbsl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE from lookuptransaksi_m WHERE kode_transaksi='pemeriksaan_pcr';");

        $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi) VALUES ('pemeriksaan_pcr', 35, 'jenispemeriksaanlab_id');");
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201126_101554_migrate_20201126_lookupbsl cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201126_101554_migrate_20201126_lookupbsl cannot be reverted.\n";

        return false;
    }
    */
}
