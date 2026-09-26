<?php

use yii\db\Migration;

/**
 * Class m190705_035005_sync_category
 */
class m190705_035005_sync_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           DROP VIEW IF exists public.sync_category;
        ');

        $this->execute("
           CREATE OR REPLACE VIEW public.sync_category AS 
 SELECT 'OBAT'::text AS type,
    jenisobatalkes_m.jenisobatalkes_kode AS code,
    jenisobatalkes_m.jenisobatalkes_nama AS name,
    COALESCE(jenisobatalkes_m.deleted_date, jenisobatalkes_m.last_modified_date, jenisobatalkes_m.created_date) AS date,
        CASE jenisobatalkes_m.is_deleted
            WHEN true THEN 1
            ELSE 0
        END AS deleted
   FROM jenisobatalkes_m
UNION ALL
 SELECT 'KOMPONEN'::text AS type,
    komponentarif_m.komponentarif_kode AS code,
    komponentarif_m.komponentarif_nama AS name,
    COALESCE(komponentarif_m.deleted_date, komponentarif_m.last_modified_date, komponentarif_m.created_date) AS date,
        CASE komponentarif_m.is_deleted
            WHEN true THEN 1
            ELSE 0
        END AS deleted
   FROM komponentarif_m
UNION ALL
 SELECT 'BARANG'::text AS type,
    kelompokbarang_m.kelompokbarang_kode AS code,
    kelompokbarang_m.kelompokbarang_nama AS name,
    COALESCE(kelompokbarang_m.deleted_date, kelompokbarang_m.last_modified_date, kelompokbarang_m.created_date) AS date,
        CASE kelompokbarang_m.is_deleted
            WHEN true THEN 1
            ELSE 0
        END AS deleted
   FROM kelompokbarang_m;
        ");

        $this->execute('
           ALTER TABLE public.sync_category
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190705_035005_sync_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190705_035005_sync_category cannot be reverted.\n";

        return false;
    }
    */
}
