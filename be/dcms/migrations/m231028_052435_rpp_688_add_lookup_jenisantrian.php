<?php

use yii\db\Migration;

/**
 * Class m231028_052435_rpp_688_add_lookup_jenisantrian
 */
class m231028_052435_rpp_688_add_lookup_jenisantrian extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m where lookup_id = 2121");

        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value)
        VALUES(2121, 'jenis_antrian', 'Pendaftaran Versi 2', 'Pasien Lama BPJS');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231028_052435_rpp_688_add_lookup_jenisantrian cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231028_052435_rpp_688_add_lookup_jenisantrian cannot be reverted.\n";

        return false;
    }
    */
}
