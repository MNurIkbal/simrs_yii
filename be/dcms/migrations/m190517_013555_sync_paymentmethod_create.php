<?php

use yii\db\Migration;

/**
 * Class m190517_013555_sync_paymentmethod_create
 */
class m190517_013555_sync_paymentmethod_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS public.sync_paymentmethod;
        ');

        $this->execute('
            CREATE VIEW public.sync_paymentmethod AS 
            SELECT 
                \'Cash\'::VARCHAR(10) AS type ,
                \'Cash\'::VARCHAR(10) AS kode,
                \'Kas Rumah Sakit\'::VARCHAR(100) AS name , 
                \'0\'::VARCHAR(20) AS number,
                \'\'::VARCHAR(100) AS bank_name , 
                \'\'::VARCHAR(20) AS bank_phone , 
                \'\'::VARCHAR(200) AS bank_address , 
                \'\'::VARCHAR(200) AS notes , 
                CURRENT_TIMESTAMP AS date ,
                0 AS INT
            UNION   
            SELECT 
                \'Transfer\'::VARCHAR(10) AS type ,
                bank_m.bank_id::VARCHAR(10) AS kode,
                bank_m.nama_bank::VARCHAR(100) AS name , 
                bank_m.no_rekening::VARCHAR(20) AS number,
                bank_m.nama_bank::VARCHAR(100) AS bank_name , 
                bank_m.no_tlp::VARCHAR(20) AS bank_phone , 
                bank_m.alamat_bank::VARCHAR(200) AS bank_address , 
                CONCAT(bank_m.nama_bank,\' \', bank_m.nama_pemilikrek)::VARCHAR(200) AS notes , 
                COALESCE(bank_m.deleted_date, bank_m.last_modified_date, bank_m.created_date) AS date,
                CASE bank_m.is_deleted
                    WHEN true THEN 1
                    ELSE 0
                END AS deleted
            FROM bank_m ;
        ');

        $this->execute('
            ALTER TABLE public.sync_paymentmethod OWNER TO postgres;
        ');

        $this->execute('
            ALTER TABLE public.sync_paymentmethod OWNER TO dev;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            DROP VIEW IF EXISTS public.sync_paymentmethod;
        ');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190517_013555_sync_paymentmethod_create cannot be reverted.\n";

        return false;
    }
    */
}
