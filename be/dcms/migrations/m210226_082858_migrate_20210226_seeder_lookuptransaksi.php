<?php

use yii\db\Migration;

/**
 * Class m210226_082858_migrate_20210226_seeder_lookuptransaksi
 */
class m210226_082858_migrate_20210226_seeder_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from lookuptransaksi_m WHERE kode_transaksi=\'kelompok_tindakan\';');

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES 
('kelompok_tindakan', 0, 'list kelompok tindakan ', '[28]');");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_082858_migrate_20210226_seeder_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_082858_migrate_20210226_seeder_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
