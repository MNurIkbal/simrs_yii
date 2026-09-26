<?php

use yii\db\Migration;

/**
 * Class m190619_101214_sync_datesync_update
 */
class m190619_101214_sync_datesync_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
     DROP VIEW public.sync_datesync;
        ');
        $this->execute('
     CREATE OR REPLACE VIEW public.sync_datesync AS 
 SELECT \'categories\'::text AS jenis,
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
   FROM bank_m
UNION ALL
 SELECT \'customers\'::text AS jenis,
    max(COALESCE(penjamin_m.deleted_date, penjamin_m.last_modified_date, penjamin_m.created_date)) AS date
   FROM penjamin_m;

        ');
        $this->execute('
     ALTER TABLE public.sync_datesync
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190619_101214_sync_datesync_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190619_101214_sync_datesync_update cannot be reverted.\n";

        return false;
    }
    */
}
