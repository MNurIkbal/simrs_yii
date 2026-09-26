<?php

use yii\db\Migration;

/**
 * Class m210114_095516_migrate_20210114_lookuptransaksi_m
 */
class m210114_095516_migrate_20210114_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi) VALUES
('kecamatan_id', 99999, 'default kecamatan pasien baru'),
('kelurahan_id', 99999, 'default kelurahan pasien baru'),
('kabupaten_id', 3174, 'default kabupaten pasien baru'),
('provinsi_id', 31, 'default provinsi pasien baru');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210114_095516_migrate_20210114_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210114_095516_migrate_20210114_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
