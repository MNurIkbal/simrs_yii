<?php

use yii\db\Migration;

/**
 * Class m220125_161404_migrate_bank_v_25012022
 */
class m220125_161404_migrate_bank_v_25012022 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.bank_v;');

        $this->execute("
            CREATE VIEW \"public\".\"bank_v\" AS  SELECT bank_m.bank_id,
    bank_m.nama_bank,
    bank_m.cabang,
    bank_m.no_rekening,
    bank_m.nama_pemilikrek,
    bank_m.no_tlp,
    bank_m.no_fax,
    bank_m.email,
    bank_m.alamat_bank,
    bank_m.additional_data,
    propinsi_m.propinsi_nama,
    kabupaten_m.kabupaten_nama,
    bank_m.is_active
   FROM bank_m
     JOIN ( SELECT propinsi_m_1.propinsi_id,
            propinsi_m_1.propinsi_nama
           FROM propinsi_m propinsi_m_1) propinsi_m ON bank_m.propinsi_id = propinsi_m.propinsi_id
     JOIN ( SELECT kabupaten_m_1.kabupaten_id,
            kabupaten_m_1.kabupaten_nama
           FROM kabupaten_m kabupaten_m_1) kabupaten_m ON bank_m.kabupaten_id = kabupaten_m.kabupaten_id
  WHERE bank_m.is_deleted = false;
");
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220125_161404_migrate_bank_v_25012022 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220125_161404_migrate_bank_v_25012022 cannot be reverted.\n";

        return false;
    }
    */
}
