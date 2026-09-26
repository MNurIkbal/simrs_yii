<?php

use yii\db\Migration;

/**
 * Class m201026_075248_migrate_mhkn_20201026_lookup_m_tipe_pembayaran
 */
class m201026_075248_migrate_mhkn_20201026_lookup_m_tipe_pembayaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE from lookup_m where lookup_type =\'tipe_pembayaran\';
        ');
        $this->execute('
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (681, \'tipe_pembayaran\', \'Voucher\', \'Voucher\', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (682, \'tipe_pembayaran\', \'Cash\', \'Cash\', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (683, \'tipe_pembayaran\', \'CreditCard\', \'CreditCard\', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (684, \'tipe_pembayaran\', \'BankTransfer\', \'BankTransfer\', 4, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (685, \'tipe_pembayaran\', \'DebitCard\', \'DebitCard\', 5, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201026_075248_migrate_mhkn_20201026_lookup_m_tipe_pembayaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201026_075248_migrate_mhkn_20201026_lookup_m_tipe_pembayaran cannot be reverted.\n";

        return false;
    }
    */
}
