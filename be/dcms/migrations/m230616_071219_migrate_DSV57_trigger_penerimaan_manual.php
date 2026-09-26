<?php

use yii\db\Migration;

/**
 * Class m230616_071219_migrate_DSV57_trigger_penerimaan_manual
 */
class m230616_071219_migrate_DSV57_trigger_penerimaan_manual extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TRIGGER IF EXISTS trigger_insert_penerimaansuppdetail_t ON public.penerimaansuppdetail_t;");
        
        $this->execute("DROP FUNCTION IF EXISTS penerimaansuppdetail_t_insert();");

        $this->execute("CREATE OR REPLACE FUNCTION public.update_penerimaansupp_to_obat()
                RETURNS trigger
                LANGUAGE plpgsql
            AS \$function\$ --author Novia 2023-05-10
            
                DECLARE
                    vpenerimaansupp_id int4;
                    vobatalkes_id  int4;
                    vqty int4;
                    vhargasatuan float8;
                    vharganetto float8;
                    vis_verifikasi bool;
                    konfigPenerimaan bool;
                    useDiscount bool;
                    usePpn bool;
                    pajakId int4;
                    persenPajak int4;
                    vdiscount float8;
                    vppn float8;
                    res record;
                
                BEGIN
                    vpenerimaansupp_id := new.penerimaansupp_id;
                
                    -- Get value is_verifikasi dan penerimaansupp_id
                    SELECT 
                        penerimaansupp_id,
                        is_verifikasi,
                        pajak_id
                    INTO
                        vpenerimaansupp_id,
                        vis_verifikasi,
                        pajakId
                    FROM penerimaansupp_t
                    WHERE penerimaansupp_id = new.penerimaansupp_id;
                        
                    -- Get persen pajak
                    SELECT 
                            pajak_persen
                    INTO
                        persenPajak
                    FROM pajak_m 
                    WHERE pajak_id = pajakId;
                
                    -- Get konfig verifikasi penerimaan
                    SELECT 
                        is_verifpenerimaan,
                        use_discount,
                        use_ppn
                    INTO 
                        konfigPenerimaan,
                        useDiscount,
                        usePpn
                    FROM konfigfarmasi_k 
                    WHERE konfigfarmasi_id = 1;
                
                    IF(TG_OP = 'INSERT' AND konfigPenerimaan = false) THEN
                        -- on insert penerimaansuppdetail_t (penerimaan manual tanpa verifikasi)
                        vobatalkes_id := new.obatalkes_id;
                        vqty := new.qty_kecil;
                        vharganetto := new.harga_netto;
                        
                        vhargasatuan := 0;
                        vhargasatuan := vharganetto / vqty;
                    
                        vdiscount := 0;
                        IF (useDiscount = true) THEN 
                            vdiscount := new.harga_netto_satuan * new.diskon / 100;
                        END IF;
                    
                        vppn := 0;
                        IF (usePpn = true) THEN 
                            vppn := ((vhargasatuan - vdiscount) * persenPajak / 100);
                        END IF;
                    
                        IF (vqty > 0) THEN
                            vhargasatuan := (vhargasatuan - vdiscount) + vppn;
                            vhargasatuan := ROUND(vhargasatuan::NUMERIC, 2);
                        END IF;
                            
                        IF(vis_verifikasi = true) THEN
                            UPDATE obatalkes_m 
                            SET	
                                hargamaksimum = (
                                    CASE 
                                    WHEN hargamaksimum = 0 THEN vhargaSatuan
                                    ELSE
                                        CASE WHEN vhargaSatuan > hargamaksimum  
                                        THEN
                                            vhargaSatuan
                                        ELSE
                                            hargamaksimum
                                        END
                                    END
                                ),
                                hargaminimum = (
                                    CASE WHEN hargaminimum = 0 THEN vhargaSatuan
                                    ELSE
                                        CASE 
                                            WHEN hargaminimum < vhargaSatuan  
                                            THEN
                                                hargaminimum
                                            ELSE
                                                vhargaSatuan
                                        END
                                    END
                                ),
                                hargaratarata = (
                                    CASE 
                                        WHEN hargaratarata = 0 THEN vhargaSatuan 
                                        ELSE (hargaterakhir + vhargaSatuan) / 2 
                                    END
                                ),
                                hargaterakhir = vhargasatuan 
                            WHERE obatalkes_id = vobatalkes_id;
                        END IF;
                    ELSE 
                        -- on update is_verifikasi penerimaansupp_t (penerimaan manual dengan verifikasi)
                        FOR res IN
                            SELECT 
                                obatalkes_id,
                                qty_kecil,
                                harga_netto,
                                harga_netto_satuan * (diskon / 100) AS diskon_terkecil
                            FROM penerimaansuppdetail_t 
                            WHERE penerimaansupp_id = vpenerimaansupp_id
                        LOOP
                            vhargasatuan := 0;
                            vhargasatuan := res.harga_netto / res.qty_kecil;
                    
                            vdiscount := 0;
                            IF (useDiscount = true) THEN 
                                vdiscount := res.diskon_terkecil;
                            END IF;
                        
                            vppn := 0;
                            IF (usePpn = true) THEN 
                                vppn := ((vhargasatuan - vdiscount) * persenPajak / 100);
                            END IF;
                        
                            IF (res.qty_kecil > 0) THEN
                                vhargasatuan := (vhargasatuan - vdiscount) + vppn;
                            END IF;
                            
                            IF(vis_verifikasi = true) THEN
                                UPDATE obatalkes_m 
                                SET	
                                    hargamaksimum = (
                                        CASE 
                                        WHEN hargamaksimum = 0 THEN vhargaSatuan
                                        ELSE
                                            CASE WHEN vhargaSatuan > hargamaksimum  
                                            THEN
                                                vhargaSatuan
                                            ELSE
                                                hargamaksimum
                                            END
                                        END
                                    ),
                                    hargaminimum = (
                                        CASE WHEN hargaminimum = 0 THEN vhargaSatuan
                                        ELSE
                                            CASE 
                                                WHEN hargaminimum < vhargaSatuan  
                                                THEN
                                                    hargaminimum
                                                ELSE
                                                    vhargaSatuan
                                            END
                                        END
                                    ),
                                    hargaratarata = (
                                        CASE 
                                            WHEN hargaratarata = 0 THEN vhargaSatuan 
                                            ELSE (hargaterakhir + vhargaSatuan) / 2 
                                        END
                                    ),
                                    hargaterakhir = vhargasatuan 
                                WHERE obatalkes_id = res.obatalkes_id;
                            END IF;
                        END LOOP;
                    END IF;
                
                    RETURN NEW;
                END;
            \$function\$ ;
        ");

       $this->execute("
            CREATE TRIGGER penerimaansuppdetail_t_trigger_insert BEFORE
            INSERT ON public.penerimaansuppdetail_t 
            FOR EACH ROW EXECUTE PROCEDURE update_penerimaansupp_to_obat()");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_071219_migrate_DSV57_trigger_penerimaan_manual cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_071219_migrate_DSV57_trigger_penerimaan_manual cannot be reverted.\n";

        return false;
    }
    */
}
