<?php

use yii\db\Migration;

/**
 * Class m191016_025629_trigger_penerimaan_barang
 */
class m191016_025629_trigger_penerimaan_barang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP TRIGGER if exists no_penerimaanbarang ON public.penerimaanbarang_t;');

         $this->execute('DROP FUNCTION if exists public.no_penerimaanbarang();');

         $this->execute("
            CREATE OR REPLACE FUNCTION public.no_penerimaanbarang()
  RETURNS trigger AS
\$BODY\$
DECLARE
    vId integer := 165; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    
BEGIN

    IF (NEW.is_verifikasi = 1) 
            THEN
                    -- Update Validasi untuk bisa melakukan penerimaan lagi
                        UPDATE validasipobarang_t SET 
                            is_verifikasi = FALSE
                        WHERE validasipobarang_id = NEW.validasipobarang_id;

                    -- Ini Update Trigger untuk menyatakan bahwa parentnya sudah di verifikasi
                        UPDATE penerimaanbarangdetail_t SET
                                additional_data  = 'is_verifikasi'
                        WHERE penerimaanbarang_id = NEW.penerimaanbarang_id AND is_deleted = false;
        RETURN NEW;
    ELSEIF (NEW.is_verifikasi = 2)
            THEN
            RETURN NEW;
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

         $this->execute('ALTER FUNCTION public.no_penerimaanbarang()
  OWNER TO postgres;');
    
        $this->execute('CREATE TRIGGER no_penerimaanbarang
  BEFORE INSERT OR UPDATE OF is_verifikasi
  ON public.penerimaanbarang_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.no_penerimaanbarang();');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191016_025629_trigger_penerimaan_barang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191016_025629_trigger_penerimaan_barang cannot be reverted.\n";

        return false;
    }
    */
}
