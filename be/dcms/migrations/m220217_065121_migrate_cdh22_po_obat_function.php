<?php

use yii\db\Migration;

/**
 * Class m220217_065121_migrate_cdh22_po_obat_function
 */
class m220217_065121_migrate_cdh22_po_obat_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
			CREATE OR REPLACE FUNCTION public.no_poobat()
			  RETURNS pg_catalog.trigger AS   \$BODY\$

			DECLARE
			vId integer := 161; --> PO Manual(POM)
			vPrefix VARCHAR;
			vNumber VARCHAR;
			vKonfig INTEGER;
	
			BEGIN
			SELECT konfig_penomoran into vKonfig from penomoran_k 	WHERE penomoran_id = vId; 
	
				IF vKonfig = 1152 then -- bulanan
						SELECT 
							(RIGHT('0' || date_part('YEAR',now()),2) ||
							RIGHT('0' || date_part('month',now()),2) ||
							RIGHT('0' || date_part('DAY',now()),2) ||
							CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_poobat), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
								INTO 
									vNumber
						FROM penomoran_k
						LEFT JOIN validasipoobat_t ON to_char(validasipoobat_t.created_date, 'MM/YYYY')  = to_char(CURRENT_DATE, 'MM/YYYY')
						WHERE penomoran_id = vId; 
					ELSE 
						SELECT 
							(RIGHT('0' || date_part('YEAR',now()),4) ||
							RIGHT('0' || date_part('month',now()),2) ||
							RIGHT('0' || date_part('DAY',now()),2) ||
							CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_poobat), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
								INTO 
								vNumber
						FROM penomoran_k
						LEFT JOIN validasipoobat_t ON validasipoobat_t.created_date::DATE = CURRENT_DATE
					WHERE penomoran_id = vId; 
				END IF;
		
		
				SELECT 
					prefix 
				INTO 
					vPrefix 
				FROM penomoran_k 
				WHERE penomoran_id = vId; 
	
				UPDATE penomoran_k SET
			        last_number = vNumber,
			        last_generate = TRIM(vPrefix) || vNumber
			  WHERE penomoran_id = vId;
		
				NEW.no_poobat := TRIM(vPrefix) || vNumber;

				RETURN NEW;

			END
			  \$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100;
			");
				
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220217_065121_migrate_cdh22_po_obat_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220217_065121_migrate_cdh22_po_obat_function cannot be reverted.\n";

        return false;
    }
    */
}
