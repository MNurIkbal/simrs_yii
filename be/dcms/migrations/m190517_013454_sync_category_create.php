<?php

use yii\db\Migration;

/**
 * Class m190517_013454_sync_category_create
 */
class m190517_013454_sync_category_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->execute('
    		DROP VIEW IF EXISTS public.sync_category;
		');

		$this->execute('
			CREATE VIEW public.sync_category AS  
			SELECT 
				\'OBAT\' AS type,
				jenisobatalkes_m.jenisobatalkes_kode AS code,
				jenisobatalkes_m.jenisobatalkes_nama AS name, 
				COALESCE(jenisobatalkes_m.deleted_date, jenisobatalkes_m.last_modified_date, jenisobatalkes_m.created_date) AS date,
				CASE jenisobatalkes_m.is_deleted
					WHEN TRUE THEN 1
					ELSE 0
				END AS deleted
			FROM jenisobatalkes_m
			UNION ALL 
			SELECT \'KOMPONEN\' AS type,
				komponentarif_m.komponentarif_kode AS code,
				komponentarif_m.komponentarif_nama AS name,
				COALESCE(komponentarif_m.deleted_date ,komponentarif_m.last_modified_date, komponentarif_m.created_date) AS date,
				CASE komponentarif_m.is_deleted
					WHEN TRUE THEN 1
					ELSE 0
				END AS deleted
			FROM komponentarif_m;
		');

		$this->execute('
			ALTER TABLE public.sync_category OWNER TO postgres;
		');

		$this->execute('
			ALTER TABLE public.sync_category OWNER TO dev;
		');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
        	DROP VIEW IF EXISTS public.sync_category;
		');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190517_013454_sync_category_create cannot be reverted.\n";

        return false;
    }
    */
}
