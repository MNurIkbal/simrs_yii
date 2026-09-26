<?php

use yii\db\Migration;

/**
 * Class m210312_032819_migrate_20210312_trg_reset_penomoran_2
 */
class m210312_032819_migrate_20210312_trg_reset_penomoran_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*-------------------------validasipoobat_t-------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 161;');

        $this->execute("INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(161, 'PO Manual', 'POM', 'POM202103080004', '202103080004', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_poobat\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 161; --> PO Manual(POM)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
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
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_poobat"() OWNER TO "postgres";');

/*-------------------------penerimaansupp_t-------------------------------*/       

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 30;');
        
        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 142;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(30, 'Penerimaan Supplier Barang', 'PSB', 'PSB202103010001', '202103010001', '0');");

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(142, 'No Penerimaan Supplier', 'PSO', 'PSO202103090003', '202103090003', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_penerimaan\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

vPrefix VARCHAR;
vNumber VARCHAR;
vId integer; 
v_tipe int;
         
BEGIN
    v_tipe := NEW.is_tipe;
    if (v_tipe = 1)
    THEN
        vId := 30; --> Penerimaan Supplier Barang (PSB)
    ELSE
        vId := 142; --> Penerimaan Supplier Obat (PSO)
    
    END IF;
        
        
        SELECT 
            (RIGHT('0' || date_part('YEAR',now()),4) ||
            RIGHT('0' || date_part('month',now()),2) ||
            RIGHT('0' || date_part('DAY',now()),2) ||
            CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_penerimaan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
        INTO 
            vNumber
        FROM penomoran_k
            LEFT JOIN penerimaansupp_t ON penerimaansupp_t.created_date::DATE = CURRENT_DATE
                                                                AND penerimaansupp_t.is_tipe = v_tipe
        WHERE penomoran_id = vId;
    
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

    NEW.no_penerimaan := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_penerimaan"() OWNER TO "postgres";');

/*-------------------------purchasereq_t-------------------------------*/       

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 67;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(67, 'Purchase Request', 'PR', 'PR202103030002', '202103030002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_purchasereq\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

vId integer := 67; --> Purchase Request (PR)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pr), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN purchasereq_t ON purchasereq_t.created_date::DATE = CURRENT_DATE
    WHERE penomoran_id = vId; 
        
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
        
    NEW.no_pr := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."generate_purchasereq"() OWNER TO "postgres";');

/*-------------------------pesanobatalkes_t-------------------------------*/       

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 16;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(16, 'Pemesanan Obat Alkes', 'POA', 'POA202103020002', '202103020002', '0');");

         $this->execute('DROP TRIGGER "update_no_pemesanan" ON "public"."pesanobatalkes_t";');

         $this->execute('DROP FUNCTION "public"."update_no_pemesanan"();');


        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"update_no_pemesanan\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 16; --> Pemesanan Obat Alkes(POA)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nopemesanan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN pesanobatalkes_t ON pesanobatalkes_t.created_date::DATE = CURRENT_DATE
    WHERE penomoran_id = vId; 
        
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
        
    NEW.nopemesanan := TRIM(vPrefix) || vNumber;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."update_no_pemesanan"() OWNER TO "postgres";');

         $this->execute('CREATE TRIGGER "update_no_pemesanan" BEFORE INSERT ON "public"."pesanobatalkes_t"
                        FOR EACH ROW
                        EXECUTE PROCEDURE "public"."update_no_pemesanan"();');


/*-------------------------pesanbarang_t-------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 17;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(17, 'Transaksi Pemesanan Barang', 'PBR', 'PBR202103100002', '202103100002', '0');");
        
        $this->execute('DROP TRIGGER "trigger_no_pemesanan" ON "public"."pesanbarang_t";');

        $this->execute('DROP FUNCTION "public"."upt_no_pemesanan_barang"();');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"upt_no_pemesanan_barang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 17; --> Transaksi Pemesanan Barang (PBR)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pemesanan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN pesanbarang_t ON pesanbarang_t.created_date::DATE = CURRENT_DATE
    WHERE penomoran_id = vId; 
        
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
        
    NEW.no_pemesanan := TRIM(vPrefix) || vNumber;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."upt_no_pemesanan_barang"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER "trigger_no_pemesanan" BEFORE INSERT ON "public"."pesanbarang_t"
                        FOR EACH ROW
                        EXECUTE PROCEDURE "public"."upt_no_pemesanan_barang"();');

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_032819_migrate_20210312_trg_reset_penomoran_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_032819_migrate_20210312_trg_reset_penomoran_2 cannot be reverted.\n";

        return false;
    }
    */
}
