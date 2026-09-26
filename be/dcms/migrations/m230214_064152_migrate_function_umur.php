<?php

use yii\db\Migration;

/**
 * Class m230214_064152_migrate_function_umur
 */
class m230214_064152_migrate_function_umur extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.umur_fn;');

        $this->execute("
			CREATE OR REPLACE FUNCTION public.umur_fn(xdate date)
			  RETURNS pg_catalog.varchar AS \$BODY\$
			DECLARE vumur VARCHAR;
			BEGIN
				SELECT
			    concat ( A.tahun, ' ', A.bulan, ' ', A.hari ) INTO vumur
			  FROM
			    (
			    SELECT
			      concat(extract(year from age(xdate))::text,' ','Tahun') as tahun,
					 concat(extract(month from age(xdate))::text,' ','Bulan') as bulan,
					 concat(extract(day from age(xdate))::text,' ','Hari') as hari
			    ) A;
	
				RETURN vumur;
		
			END
			\$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100
           ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230214_064152_migrate_function_umur cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230214_064152_migrate_function_umur cannot be reverted.\n";

        return false;
    }
    */
}
