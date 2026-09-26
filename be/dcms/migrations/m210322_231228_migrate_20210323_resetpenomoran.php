<?php

use yii\db\Migration;

/**
 * Class m210322_231228_migrate_20210323_resetpenomoran
 */
class m210322_231228_migrate_20210323_resetpenomoran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*------------------------------------------------reseptur_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 27;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(27, 'Transaksi Reseptur', 'RST', 'RST202103220019', '202103220019', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_noreseptur\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    vId integer := 27; --> Transaksi Reseptur (RST)
    vPrefix VARCHAR;
    vNumber VARCHAR;

    -- perubahan stok qty dipesan
    var_status_reseptur INTEGER;
    var_ruangan_id INTEGER;
    var_reseptur_id INTEGER;
    
BEGIN
------------------------------------------no reseptur-----------------------------------------------------
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(noresep), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN reseptur_t ON reseptur_t.created_date::DATE = CURRENT_DATE
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


------------------------------------------penambahan update qty_dipesan-----------------------------------------------------

    var_reseptur_id := new.reseptur_id;
    var_status_reseptur := new.status_reseptur;
    var_ruangan_id := NEW.ruangan_id;
    
    IF (var_status_reseptur = 347)
            THEN                                        
                    UPDATE stokobatalkes_r
                    SET qty_dipesan = (qty_dipesan - resepturdetail.total_qty  ), 
                            qty_tersedia = (qty_sisa - (qty_dipesan- resepturdetail.total_qty ))
                    FROM (
                            SELECT 
                                    reseptur_id as resid,
                                    obatalkes_id, 
                                    SUM(qty_konversi) as total_qty 
                            FROM resepturdetail_t 
                            WHERE reseptur_id = var_reseptur_id 
                                AND is_deleted = FALSE 
                            GROUP BY 
                                reseptur_id, 
                                obatalkes_id
                    ) as resepturdetail
                    WHERE resepturdetail.resid = var_reseptur_id 
                        AND (stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) = (resepturdetail.obatalkes_id, var_ruangan_id);
        END IF;
    
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."generate_noreseptur"() OWNER TO "postgres";');

/*------------------------------------------------stokopname_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 5;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(5, 'Transaksi Stok Opname', 'SOP', 'SOP202103220002', '202103220002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_nostokopname\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
    vId integer := 5; --> Transaksi Stok Opname (SOP)
    vPrefix VARCHAR;
    vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nostokopname), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN stokopname_t ON stokopname_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.nostokopname := TRIM(vPrefix) || vNumber;
    
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
        
        $this->execute('ALTER FUNCTION "public"."generate_nostokopname"() OWNER TO "postgres";');


/*------------------------------------------------pemusnahanobat_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 118;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(118, 'Pemusnahan Obat Alkes', 'MUS', 'MUS202103220002', '202103220002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_pemusnahan_obat_alkes\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
    vId integer := 118; --> Pemusnahan Obat Alkes (MUS)
    vPrefix VARCHAR;
    vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nopemusnahan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN pemusnahanobat_t ON pemusnahanobat_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.nopemusnahan := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_pemusnahan_obat_alkes"() OWNER TO "postgres";');



/*------------------------------------------------penerimaanbarang_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 165;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(165, 'Penerimaan Barang', 'TRB', 'TRB202103210001', '202103210001', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_penerimaanbarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    vId integer := 165; --> Penerimaan Barang (TRB)
        vPrefix VARCHAR;
        vNumber VARCHAR;
        
BEGIN
---------------------------------------------no. penerimaan barang---------------------------------------------------       
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_penerimaan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN penerimaanbarang_t ON penerimaanbarang_t.created_date::DATE = CURRENT_DATE
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
    
    -----------------------------------------------------------------------------------------------------   
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
    END IF;
    
     RETURN NEW;    
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
       
        $this->execute('ALTER FUNCTION "public"."no_penerimaanbarang"() OWNER TO "postgres";');



/*------------------------------------------------penerimaanobat_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 164;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES
(164, 'Penerimaan Obat', 'TRO', 'TRO202103210002', '202103210002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_penerimaanobat\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    vId integer := 164; --> Penerimaan Obat (TRO)
    vPrefix VARCHAR;
    vNumber VARCHAR;
    
BEGIN
    IF (NEW.is_verifikasi = 1) 
            THEN
                    -- Update Validasi untuk bisa melakukan penerimaan lagi
                        UPDATE validasipoobat_t SET 
                            is_verifikasi = FALSE
                        WHERE validasipoobat_id = NEW.validasipoobat_id;

                    -- Ini Update Trigger untuk menyatakan bahwa parentnya sudah di verifikasi
                        UPDATE penerimaanobatdetail_t SET
                                additional_data  = 'is_verifikasi'
                        WHERE penerimaanobat_id = NEW.penerimaanobat_id AND is_deleted = false;
        RETURN NEW;
    END IF;
    
---------------------------------------------no.Penerimaan Obat-----------------------------------------------  
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_penerimaan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN penerimaanobat_t ON penerimaanobat_t.created_date::DATE = CURRENT_DATE
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
        
        $this->execute('ALTER FUNCTION "public"."no_penerimaanobat"() OWNER TO "postgres";');

/*------------------------------------------------returpenerimaanbarang_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 177;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(177, 'Retur Penerimaan Barang', 'RPB', 'RPB202103220002', '202103220002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_returpenerimaanbarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
    vId integer := 177; --> Retur Penerimaan Barang (RPB)
    vPrefix VARCHAR;
    vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_returpenerimaanbarang), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN returpenerimaanbarang_t ON returpenerimaanbarang_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_returpenerimaanbarang := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
       
        $this->execute('ALTER FUNCTION "public"."no_returpenerimaanbarang"() OWNER TO "postgres";');

/*------------------------------------------------returpenerimaanobat_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 144;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(144, 'Retur Penerimaan Obat', 'RPO', 'RPO202103220002', '202103220002', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_returpenerimaanobat\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
    vId integer := 144; --> Retur Penerimaan Obat (RPO)
    vPrefix VARCHAR;
    vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_returpenerimaanobat), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN returpenerimaanobat_t ON returpenerimaanobat_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_returpenerimaanobat := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_returpenerimaanobat"() OWNER TO "postgres";');

/*------------------------------------------------formulirstokopname_t------------------------------------------------*/

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 111;');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(111, 'Transaksi Form Stok Opname', 'FSO', 'FSO202103220002', '202103220002', '0');");

        $this->execute('DROP TRIGGER "update_no_formulir" ON "public"."formulirstokopname_t";');

        $this->execute('DROP FUNCTION "public"."upd_no_formulir_so"();');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"upd_no_formulir_so\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
    vId integer := 111; --> Transaksi Form Stok Opname (FSO)
    vPrefix VARCHAR;
    vNumber VARCHAR;
    
BEGIN
    
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(noformulir), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN formulirstokopname_t ON formulirstokopname_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.noformulir := TRIM(vPrefix) || vNumber;

    RETURN NEW;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
       
        $this->execute('ALTER FUNCTION "public"."upd_no_formulir_so"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER "update_no_formulir" BEFORE INSERT ON "public"."formulirstokopname_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."upd_no_formulir_so"();');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210322_231228_migrate_20210323_resetpenomoran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210322_231228_migrate_20210323_resetpenomoran cannot be reverted.\n";

        return false;
    }
    */
}
