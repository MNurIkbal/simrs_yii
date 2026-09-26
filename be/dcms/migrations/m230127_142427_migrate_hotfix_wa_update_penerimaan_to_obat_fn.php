<?php

use yii\db\Migration;

/**
 * Class m230127_142427_migrate_hotfix_wa_update_penerimaan_to_obat_fn
 */
class m230127_142427_migrate_hotfix_wa_update_penerimaan_to_obat_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
			DROP TRIGGER IF EXISTS update_penerimaan_to_obat  ON "public"."penerimaanobatdetail_t";
        ');
		
        $this->execute('
            DROP FUNCTION update_penerimaan_to_obat();
        ');
		
        $this->execute('
			CREATE OR REPLACE FUNCTION "public"."update_penerimaan_to_obat"()
			  RETURNS "pg_catalog"."trigger" AS $BODY$-- author yaya

			DECLARE
			    ruanganGudang INTEGER := 25;  
			    validasiDetailId INTEGER;
			    obatAlkesId  INTEGER;
			    konversiId INTEGER;
			    nilaiKonversi FLOAT;
			    jumlahHarga FLOAT;
			    qtyPO FLOAT;
			    hargaSatuan FLOAT;
			    qtyPenerimaan FLOAT;
			    konversiPenerimaan FLOAT;
			    penerimaanObatDetailId INTEGER;
			    satuanKecilId INTEGER;
			    vhargaRataRata FLOAT;
			BEGIN
        

			    IF (NEW.additional_data != \'is_verifikasi\')
			        THEN RETURN NEW;
			    END IF;

			    NEW.additional_data := NULL;
			    validasiDetailId := NEW.validasipoobatdetail_id;
			    konversiId := NEW.s_konversiobt_id;
			    obatAlkesId := NEW.obatalkes_id;
			    penerimaanObatDetailId := NEW.penerimaanobatdetail_id;
			    qtyPenerimaan := NEW.qty_diterima;
    
			    -- Konver Ke satuan terkecil
			    SELECT 
			        nilai_konversi,
			        satuankecil_id
			    INTO
			        nilaiKonversi,
			        satuanKecilId
			    FROM satuankonversi_m
			    WHERE satuankonversi_id = konversiId;

			    -- Prepare untuk mengurangi QTY PO di master obat alkes
			    konversiPenerimaan := nilaiKonversi *  qtyPenerimaan;
			    -- Mencari harga netto menggunakan attribute qty_po = qty yang sudah di konversi dan jumlah harga
			    SELECT
			        qty_po * nilaiKonversi,
			        jumlah + COALESCE(discount_rp,0)
			    INTO
			        qtyPO,
			        jumlahHarga
			    FROM validasipoobatdetail_t
			    WHERE validasipoobatdetail_id = validasiDetailId;

			    hargaSatuan := 0;
    
			    IF (qtyPO > 0) THEN
			        hargaSatuan := jumlahHarga / qtyPO;
			    END IF;

			    -- Update ke master obat alkes harga max,min, net dan avg sertan on_po

			    IF(qtyPenerimaan > 0) THEN   
			        UPDATE obatalkes_m 
			        SET
			            on_po = (on_po - konversiPenerimaan),
			            -- 2019-03-08 Perubahan atas best price dan update jika nilah 0 maka set harga satuan
			            -- harganetto = 
			            -- CASE WHEN harganetto = 0 THEN 0 ELSE hargaSatuan END, 
			            hargamaksimum = (
			                CASE WHEN hargamaksimum = 0 
			                    THEN hargaSatuan
			                    ELSE 
			                        CASE WHEN hargaSatuan > hargamaksimum  
			                        THEN hargaSatuan ELSE hargamaksimum
			                        END
			                END
			            ),
			            hargaminimum = (
			                CASE WHEN hargaminimum = 0 THEN hargaSatuan
			                ELSE
			                    CASE WHEN hargaminimum < hargaSatuan  
			                    THEN hargaminimum ELSE hargaSatuan
			                    END
			                END
			            ),
			            hargaratarata = (CASE WHEN hargaratarata = 0 THEN hargaSatuan ELSE (hargaterakhir + hargaSatuan) / 2 END),
			            hargaterakhir = hargaSatuan 
			        WHERE obatalkes_id = obatAlkesId;
            
			        -- Update validasi detail untuk penerimaan 
			        UPDATE validasipoobatdetail_t 
			        SET 
			            qty_penerimaan = COALESCE(qty_penerimaan,0) + qtyPenerimaan,
			            is_completed = (
			                CASE WHEN (COALESCE(qty_penerimaan,0) + qtyPenerimaan) = qty_input 
			                    THEN true ELSE false 
			                END
			            ),
			            qty_sisa = qty_input - (COALESCE(qty_penerimaan,0) + qtyPenerimaan)
			        WHERE validasipoobatdetail_id = validasiDetailId;
            
			        SELECT hargaratarata
			        INTO vhargaRataRata
			        FROM obatalkes_m 
			        WHERE obatalkes_id = obatAlkesId;
        
			        -- Insert ke stokobatalkes_t 
			        INSERT INTO stokobatalkes_t (
			            ruangan_id,
			            penerimaanobatdetail_id,
			            obatalkes_id,
			            tglkadaluarsa,
			            nobatch,
			            tglstok_in,
			            qtystok_in,
			            harganetto,
			            stokoa_aktif,
			            satuankecil_id,
			            tglterima,
			            total_persediaan,
			            harga_netto_avg
			        ) VALUES (
			            ruanganGudang,
			            NEW.penerimaanobatdetail_id,
			            obatAlkesId,
			            NEW.tgl_kadaluarsa,
			            NEW.no_batch,
			            NEW.created_date,
			            konversiPenerimaan,
			            hargaSatuan,
			            true,
			            satuanKecilId,
			            NEW.created_date,
			            konversiPenerimaan * hargaSatuan,
			            COALESCE(vhargaRataRata,0)
			        ); 
			    END IF;     

			    RETURN NEW;
			END
			$BODY$
			  LANGUAGE plpgsql VOLATILE
			  COST 100;
        ');
		
        $this->execute('
			CREATE TRIGGER "update_penerimaan_to_obat" AFTER UPDATE OF "additional_data" ON "public"."penerimaanobatdetail_t"
			FOR EACH ROW
			EXECUTE PROCEDURE "public"."update_penerimaan_to_obat"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230127_142427_migrate_hotfix_wa_update_penerimaan_to_obat_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230127_142427_migrate_hotfix_wa_update_penerimaan_to_obat_fn cannot be reverted.\n";

        return false;
    }
    */
}
