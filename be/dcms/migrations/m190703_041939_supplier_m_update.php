<?php

use yii\db\Migration;

/**
 * Class m190703_041939_supplier_m_update
 */
class m190703_041939_supplier_m_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
   DROP VIEW IF exists public.sync_supplier;
        ');

             $this->execute('
   ALTER TABLE "public"."supplier_m" 
  ALTER COLUMN "no_npwp" TYPE varchar(100) USING "no_npwp"::varchar(100);
        ');


             $this->execute("
   CREATE OR REPLACE VIEW public.sync_supplier AS 
 SELECT supplier_m.supplier_id::character varying(20) AS supplier_kode,
    supplier_m.supplier_nama,
    supplier_m.supplier_alamat,
    supplier_m.no_tlp,
    supplier_m.email,
    supplier_m.no_npwp AS tax_number,
    ''::text AS website,
    supplier_m.no_rekening AS account_bank_no,
    supplier_m.nama_pemilikrek AS account_bank_holder,
    bank_m.nama_bank AS account_bank_name,
    COALESCE(supplier_m.deleted_date, supplier_m.last_modified_date, supplier_m.created_date) AS date,
        CASE supplier_m.is_deleted
            WHEN true THEN 1
            ELSE 0
        END AS deleted
   FROM supplier_m
     LEFT JOIN bank_m ON supplier_m.bank_id = bank_m.bank_id;
        ");

$this->execute('
  ALTER TABLE public.sync_supplier
  OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190703_041939_supplier_m_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190703_041939_supplier_m_update cannot be reverted.\n";

        return false;
    }
    */
}
