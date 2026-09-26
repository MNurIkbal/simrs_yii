<?php

use yii\db\Migration;

/**
 * Class m190523_030214_sync_datesync_create
 */
class m190523_030214_sync_datesync_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW public.sync_datesync AS  SELECT \'categories\'::text AS jenis,
                max(categories.date) AS date
               FROM ( SELECT COALESCE(jenisobatalkes_m.deleted_date, jenisobatalkes_m.last_modified_date, jenisobatalkes_m.created_date) AS date
                       FROM jenisobatalkes_m
                    UNION ALL
                     SELECT COALESCE(komponentarif_m.deleted_date, komponentarif_m.last_modified_date, komponentarif_m.created_date) AS date
                       FROM komponentarif_m) categories
            UNION ALL
             SELECT \'vendors\'::text AS jenis,
                max(COALESCE(supplier_m.deleted_date, supplier_m.last_modified_date, supplier_m.created_date)) AS date
               FROM supplier_m
            UNION ALL
             SELECT \'payment\'::text AS jenis,
                max(COALESCE(bank_m.deleted_date, bank_m.last_modified_date, bank_m.created_date)) AS date
               FROM bank_m;

        ');

        $this->execute('
            ALTER TABLE public.sync_datesync OWNER TO postgres;
        ');

        $this->execute('
            ALTER TABLE public.sync_datesync OWNER TO dev;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            DROP VIEW IF EXISTS sync_datesync;
        ');
    }
    
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190523_030214_sync_datesync_create cannot be reverted.\n";

        return false;
    }
    */
}
