<?php

use yii\db\Migration;

/**
 * Class m190520_085620_sync_paymentmethod_update
 */
class m190520_085620_sync_paymentmethod_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
     DROP VIEW sync_paymentmethod;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW sync_paymentmethod AS 
 SELECT \'Cash\'::character varying(10) AS type,
    lookup_m.lookup_id::character varying(10) AS kode,
    \'Kas Rumah Sakit\'::character varying(100) AS name,
    \'0\'::character varying(20) AS number,
    \'\'::character varying(100) AS bank_name,
    \'\'::character varying(20) AS bank_phone,
    \'\'::character varying(200) AS bank_address,
    \'\'::character varying(200) AS notes,
    now()::text::date::timestamp without time zone AS date,
    0 AS deleted
   FROM lookup_m
  WHERE lookup_m.lookup_id = 403
UNION
 SELECT \'Transfer\'::character varying(10) AS type,
    bank_m.bank_id::character varying(10) AS kode,
    bank_m.nama_bank AS name,
    bank_m.no_rekening::character varying(20) AS number,
    bank_m.nama_bank AS bank_name,
    bank_m.no_tlp::character varying(20) AS bank_phone,
    bank_m.alamat_bank::character varying(200) AS bank_address,
    concat(bank_m.nama_bank, \' \', bank_m.nama_pemilikrek)::character varying(200) AS notes,
    COALESCE(bank_m.deleted_date, bank_m.last_modified_date, bank_m.created_date) AS date,
        CASE bank_m.is_deleted
            WHEN true THEN 1
            ELSE 0
        END AS deleted
   FROM bank_m;

        ');

        $this->execute('
ALTER TABLE sync_paymentmethod
  OWNER TO dev;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190520_085620_sync_paymentmethod_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190520_085620_sync_paymentmethod_update cannot be reverted.\n";

        return false;
    }
    */
}
