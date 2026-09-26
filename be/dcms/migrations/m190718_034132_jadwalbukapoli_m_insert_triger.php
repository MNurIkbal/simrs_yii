<?php

use yii\db\Migration;

/**
 * Class m190718_034132_jadwalbukapoli_m_insert_triger
 */
class m190718_034132_jadwalbukapoli_m_insert_triger extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
              $this->execute('
                DROP TRIGGER if exists trigger_insert_jadwalbukapoli_m ON public.jadwalbukapoli_m;
             ');

             $this->execute('
                DROP FUNCTION if exists public.jadwalbukapoli_m_insert();
             ');

              $this->execute("
                CREATE OR REPLACE FUNCTION public.jadwalbukapoli_m_insert()
  RETURNS trigger AS
\$BODY\$
DECLARE
vjadwalbukapoli_id int4;
vkuotaoffline int4;
vkuotaonline int4;
vcreated_by int4;
vdate TIMESTAMP;

BEGIN
  vjadwalbukapoli_id := NEW.jadwalbukapoli_id;
  vkuotaoffline := NEW.maxantrian_poli;
  vkuotaonline := NEW.kuota_online;
  vcreated_by := NEW.created_by;
  vdate := CURRENT_DATE;
  
  IF(COALESCE(vkuotaoffline,0) > 0)
  THEN
    INSERT INTO public.stokkuotadokter_t (
      jadwalbukapoli_id , tgltransaksi_in, kuota_in, kuota_out, flag, is_online, created_by, created_date
    )VALUES(
      vjadwalbukapoli_id, vdate, vkuotaoffline, 0, TRUE, FALSE, vcreated_by, vdate
    );
    
    INSERT INTO public.stokkuotadokter_t  (
      jadwalbukapoli_id , tgltransaksi_in, kuota_in, kuota_out, flag, is_online, created_by, created_date
    )VALUES(
      vjadwalbukapoli_id, vdate, vkuotaonline, 0, TRUE, TRUE, vcreated_by, vdate
    );
  END IF;
  
  RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

               $this->execute('
                ALTER FUNCTION public.jadwalbukapoli_m_insert()
  OWNER TO postgres;');

               $this->execute('
                CREATE TRIGGER trigger_insert_jadwalbukapoli_m
  BEFORE INSERT
  ON public.jadwalbukapoli_m
  FOR EACH ROW
  EXECUTE PROCEDURE public.jadwalbukapoli_m_insert();');
               
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190718_034132_jadwalbukapoli_m_insert_triger cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190718_034132_jadwalbukapoli_m_insert_triger cannot be reverted.\n";

        return false;
    }
    */
}
