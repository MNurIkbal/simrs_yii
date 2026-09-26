<?php

use yii\db\Migration;

/**
 * Class m221128_083105_migrate_hotfix_no_masuk_penunjang
 */
class m221128_083105_migrate_hotfix_no_masuk_penunjang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.no_masuk_penunjang()
        RETURNS trigger
        LANGUAGE plpgsql
       AS \$function\$DECLARE
           vId integer; 
           vPrefix varchar;
           vLast varchar;
           vYear varchar;
           vMonth varchar;
           v_Nomor varchar;
         vInstalasi varchar;
         v_day varchar;
         v_reset varchar;
         vNumber varchar;
         
       BEGIN
           SELECT i.instalasi_singkatan into vInstalasi from instalasi_m i right join ruangan_m r ON r.instalasi_id = i.instalasi_id where r.ruangan_id = NEW.ruangan_id;
           IF(trim(vInstalasi) = 'LAB') THEN
                   vId := 121;
           ELSIF(trim(vInstalasi) = 'RAD') THEN
                   vId := 136;
           ELSEIF (trim(vInstalasi) = 'IBS') THEN
               vId := 140;
           ELSEIF (trim(vInstalasi) = 'FIS') THEN
               vId := 193;
           ELSEIF (trim(vInstalasi) = 'MCU') THEN
               vId := 185;
           ELSE
               vId := 166;
           END IF;
           
       -- 	SELECT date_part('DAY',now()) INTO v_day;
       -- 	
       -- 	IF(v_day = '1') 
       -- 	THEN 
       -- 		SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) AS VARCHAR(5)), 5, '0')) 		INTO v_reset
       -- 		FROM penomoran_k where penomoran_id = vId;
       -- 		
       -- 		IF(v_reset <> '00001')
       -- 		THEN 
       -- 			UPDATE penomoran_k SET
       -- 				last_generate = '00001'
       -- 			WHERE penomoran_id = vId;
       -- 		END IF;
       -- 	
       -- 	END IF;
       -- 	SELECT 
       -- 		prefix,
       -- 		date_part('YEAR',now()) as year, 
       -- 		RIGHT('0'|| date_part('month',now()),2) as month,
       -- 		(
       -- 			SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(5)), 5, '0')) last_no
       -- 				FROM penomoran_k where penomoran_id = vId
       -- 		)
       -- 	INTO
       -- 		vPrefix,
       -- 		vYear,
       -- 		vMonth,
       -- 		vLast
       -- 	FROM penomoran_k WHERE penomoran_id = vId;
       
           SELECT penomoran_k.prefix,
               (RIGHT('0' || date_part('YEAR',now()),4) ||
               RIGHT('0' || date_part('month',now()),2) ||
               RIGHT('0' || date_part('DAY',now()),2) ||
               CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_masukpenunjang), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
           INTO 
               vPrefix,
               vNumber
           FROM penomoran_k
               LEFT JOIN pasienmasukpenunjang_t ON pasienmasukpenunjang_t.created_date::DATE = CURRENT_DATE
               LEFT JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
               LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id 
           WHERE penomoran_id = vId
           AND instalasi_singkatan = vInstalasi
           GROUP BY penomoran_k.prefix;
       
           IF(COALESCE(vPrefix,'') = '' )
           THEN
               SELECT prefix INTO vPrefix
               FROM penomoran_k
               WHERE penomoran_id = vId;
               
               SELECT 
                   CONCAT(RIGHT('0' || date_part('YEAR',now()),4) ||
                   RIGHT('0' || date_part('month',now()),2) ||
                   RIGHT('0' || date_part('DAY',now()),2),
                   CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_masukpenunjang), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
               INTO vNumber
               from pasienmasukpenunjang_t;
           END IF;
       
           
       -- 	v_Nomor = vPrefix || vYear || vMonth || vLast;
           v_Nomor = vPrefix || vNumber;
       
           UPDATE penomoran_k SET
               last_number = vLast,
               last_generate = V_Nomor
           WHERE penomoran_id = vId;
       
           NEW.no_masukpenunjang = v_Nomor;
       
           RETURN NEW;
       END
       \$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221128_083105_migrate_hotfix_no_masuk_penunjang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221128_083105_migrate_hotfix_no_masuk_penunjang cannot be reverted.\n";

        return false;
    }
    */
}
