<?php

use yii\db\Migration;

/**
 * Class m191016_042150_trigger_penerimaan_barang_supp
 */
class m191016_042150_trigger_penerimaan_barang_supp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP TRIGGER if exists no_penerimaan ON public.penerimaansupp_t;');

         $this->execute('DROP FUNCTION if exists public.no_penerimaan();');

         $this->execute("
            CREATE OR REPLACE FUNCTION public.no_penerimaan()
  RETURNS trigger AS
\$BODY\$
DECLARE
    vId integer; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    v_tipe int;
    
BEGIN

    v_tipe := NEW.is_tipe;
    if (v_tipe = 1)
    THEN
        vId := 30;
    ELSE
        vId := 142;
    
    END IF;
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;

    NEW.no_penerimaan = v_Nomor;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

         $this->execute('ALTER FUNCTION public.no_penerimaan()
  OWNER TO postgres;');

         $this->execute('CREATE TRIGGER no_penerimaan
  BEFORE INSERT
  ON public.penerimaansupp_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.no_penerimaan();');

       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191016_042150_trigger_penerimaan_barang_supp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191016_042150_trigger_penerimaan_barang_supp cannot be reverted.\n";

        return false;
    }
    */
}
