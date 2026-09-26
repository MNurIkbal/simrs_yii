<?php

use yii\db\Migration;

/**
 * Class m191021_040542_stok_opname_barang_1651
 */
class m191021_040542_stok_opname_barang_1651 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."stokopnamebarangdetail_t" 
  ALTER COLUMN "tglkadaluarsa" DROP NOT NULL,
  ALTER COLUMN "kondisibarang" DROP NOT NULL,
  ALTER COLUMN "kondisibarang" SET DEFAULT 9999,
  ALTER COLUMN "tglperiksafisik" DROP NOT NULL;');

        $this->execute('DROP TRIGGER if exists no_sobarang ON public.stokopnamebarang_t;');

        $this->execute('DROP FUNCTION if exists public.no_sobarang();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.no_sobarang()
  RETURNS trigger AS
\$BODY\$
DECLARE
    vId integer := 33; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    
BEGIN
    
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

    NEW.nostokopname = v_Nomor;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.no_sobarang()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER no_sobarang
  BEFORE INSERT
  ON public.stokopnamebarang_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.no_sobarang();
');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191021_040542_stok_opname_barang_1651 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191021_040542_stok_opname_barang_1651 cannot be reverted.\n";

        return false;
    }
    */
}
