<?php

use yii\db\Migration;

/**
 * Class m241205_032041_rpp_1819_lookup_m_buta_warna_parsial
 */
class m241205_032041_rpp_1819_lookup_m_buta_warna_parsial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_type='buta_warna_parsial';
        ");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES
            ('buta_warna_parsial', 'Strong Deuteranomalia', 'Strong Deuteranomalia', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            ('buta_warna_parsial', 'Mild Deuteranomalia', 'Mild Deuteranomalia', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            ('buta_warna_parsial', 'Strong Protanomalia', 'Strong Protanomalia', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            ('buta_warna_parsial', 'Mild Protanomalia', 'Mild Protanomalia', 4, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241205_032041_rpp_1819_lookup_m_buta_warna_parsial cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241205_032041_rpp_1819_lookup_m_buta_warna_parsial cannot be reverted.\n";

        return false;
    }
    */
}
