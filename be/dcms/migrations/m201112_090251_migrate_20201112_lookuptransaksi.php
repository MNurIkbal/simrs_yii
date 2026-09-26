<?php

use yii\db\Migration;

/**
 * Class m201112_090251_migrate_20201112_lookuptransaksi
 */
class m201112_090251_migrate_20201112_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
              $this->execute("
                DELETE from lookuptransaksi_m WHERE kode_transaksi in 
                ('ruangan_gigi',
                'ruangan_gizi',
                'ruangan_internis',
                'ruangan_neurologi',
                'ruangan_obsgyn',
                'ruangan_urologi'
                );
                ");

              $this->execute("
                INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi) VALUES 
                ('ruangan_gigi', 0, 'kode ruangan poli gigi (ruangan_m)'),
                ('ruangan_gizi', 0, 'kode ruangan poli gizi (ruangan_m)'),
                ('ruangan_internis', 0, 'kode ruangan penyakit dalam (ruangan_m)'),
                ('ruangan_urologi', 0, 'kode ruangan poli urologi (ruangan_m)'),
                ('ruangan_neurologi', 0, 'kode ruangan neurologi (ruangan_m)'),
                ('ruangan_obsgyn', 0, 'kode ruangan obgyn (ruangan_m)');
                ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201112_090251_migrate_20201112_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_090251_migrate_20201112_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
