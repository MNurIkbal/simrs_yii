<?php

use yii\db\Migration;

/**
 * Class m210312_041833_migrate_20210312_trg_reset_penomoran_3
 */
class m210312_041833_migrate_20210312_trg_reset_penomoran_3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*------------------------------------------------mutasiobatruangan_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 11;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(11, 'Transaksi Mutasi Obat', 'MOA', 'MOA202103120001', '202103120001', '0');");

        $this->execute('DROP TRIGGER "update no mutasi" ON "public"."mutasiobatruangan_t";');

        $this->execute('DROP FUNCTION "public"."update_no_mutasi"();');


        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"update_no_mutasi\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 11; --> Transaksi Mutasi Obat (MOA)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nomutasioa), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN mutasiobatruangan_t ON mutasiobatruangan_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.nomutasioa := TRIM(vPrefix) || vNumber;

    RETURN NEW;
    
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."update_no_mutasi"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER "update no mutasi" BEFORE INSERT ON "public"."mutasiobatruangan_t"
                        FOR EACH ROW
                        EXECUTE PROCEDURE "public"."update_no_mutasi"();');

/*------------------------------------------------mutasibarang_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 176;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(176, 'Transaksi Mutasi Barang', 'MTB', 'MTB202103100002', '202103100002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_mutasibarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 176; --> Transaksi Mutasi Barang (MTB)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nomutasi_barang), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN mutasibarang_t ON mutasibarang_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.nomutasi_barang := TRIM(vPrefix) || vNumber;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_mutasibarang"() OWNER TO "postgres";');

/*------------------------------------------------terimamutasiobat_t------------------------------------------------*/       

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 15;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(15, 'Terima Mutasi Obat Alkes', 'TMO', 'TMO202103120001', '202103120001', '0');");

        $this->execute('DROP TRIGGER "upd_noterimamutasi" ON "public"."terimamutasiobat_t";');

        $this->execute('DROP FUNCTION "public"."upd_noterimamutasi"();');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"upd_noterimamutasi\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 15; --> Terima Mutasi Obat Alkes (TMO)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(noterimamutasi), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN terimamutasiobat_t ON terimamutasiobat_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.noterimamutasi := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."upd_noterimamutasi"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER "upd_noterimamutasi" BEFORE INSERT ON "public"."terimamutasiobat_t"
                    FOR EACH ROW
                    EXECUTE PROCEDURE "public"."upd_noterimamutasi"();');

/*------------------------------------------------terimamutasiobat_t------------------------------------------------*/      

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 32;');

        $this->execute("INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(32, 'Terima Mutasi Barang', 'TMB', 'TMB202103100002', '202103100002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_terimamutasibarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE
vId integer := 32; --> Terima Mutasi Barang (TMB)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(noterimamutasi), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN terimamutasibarang_t ON terimamutasibarang_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.noterimamutasi := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_terimamutasibarang"() OWNER TO "postgres";');

/*------------------------------------------------penjualanresep_t------------------------------------------------*/      

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 4; ');

        $this->execute("INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(4, 'Transaksi Resep', 'RSP', 'RSP202103100002', '202103100002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"upd_noresep\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 4; --> Transaksi Resep (RSP)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(noresep), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN penjualanresep_t ON penjualanresep_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.noresep := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."upd_noresep"() OWNER TO "postgres";');

/*------------------------------------------------adjusmenobat_t------------------------------------------------*/       

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 138;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(138, 'Adjustment Obat', 'AJO', 'AJO202103100002', '202103100002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_adjusmenobat\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 138; --> Adjustment Obat (AJO)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_adjusmen), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN adjusmenobat_t ON adjusmenobat_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_adjusmen := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_adjusmenobat"() OWNER TO "postgres";');

/*------------------------------------------------adjusmenbarang_t------------------------------------------------*/       


        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 139;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(139, 'Adjustment Barang', 'AJB', 'AJB202103100032', '202103100032', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_adjusmenbarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 139; --> Adjustment Barang (AJB)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_adjusmen), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN adjusmenbarang_t ON adjusmenbarang_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_adjusmen := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_adjusmenbarang"() OWNER TO "postgres";');

/*------------------------------------------------pemakaianobat_t------------------------------------------------*/       

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 29; ');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(29, 'Transaksi Pemakaian Obat', 'PMO', 'PMO202103100002', '202103100002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_no_pemakaianobat\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 29; --> Transaksi Pemakaian Obat (PMO)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nopemakaian_obat), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN pemakaianobat_t ON pemakaianobat_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.nopemakaian_obat := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."generate_no_pemakaianobat"() OWNER TO "postgres";');

/*------------------------------------------------pemakaianobat_t------------------------------------------------*/       


        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 28;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(28, 'Transaksi PO Barang', 'POB', 'POB202103100002', '202103100002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_pobarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 28; -->Transaksi PO Barang (POB)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pobarang), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN validasipobarang_t ON validasipobarang_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_pobarang := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_pobarang"() OWNER TO "postgres";');

 /*------------------------------------------------purchasereqbrg_t------------------------------------------------*/       
       
        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 191; ');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(191, 'No Purchase Req Barang', 'PRB', 'PRB202103100002', '202103100002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_purchasereqbrg\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    DECLARE
vId integer := 191; --> No Purchase Req Barang (PRB)
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
        LEFT JOIN purchasereqbrg_t ON purchasereqbrg_t.created_date::DATE = CURRENT_DATE
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

        $this->execute('ALTER FUNCTION "public"."no_purchasereqbrg"() OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_041833_migrate_20210312_trg_reset_penomoran_3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_041833_migrate_20210312_trg_reset_penomoran_3 cannot be reverted.\n";

        return false;
    }
    */
}
