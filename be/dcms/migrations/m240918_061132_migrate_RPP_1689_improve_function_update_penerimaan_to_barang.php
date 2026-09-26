<?php

use yii\db\Migration;

/**
 * Class m240918_061132_migrate_RPP_1689_improve_function_update_penerimaan_to_barang
 */
class m240918_061132_migrate_RPP_1689_improve_function_update_penerimaan_to_barang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER IF EXISTS update_penerimaan_to_barang ON penerimaanbarangdetail_t;');
		$this->execute('DROP FUNCTION IF EXISTS update_penerimaan_to_barang;');
		
		$this->execute("
			CREATE OR REPLACE FUNCTION public.update_penerimaan_to_barang()
			  RETURNS pg_catalog.trigger AS \$BODY\$-- author yaya

			DECLARE
				ruanganGudang INTEGER := 35;  
				validasiDetailId INTEGER;
				barangId  INTEGER;
				konversiId INTEGER;
				nilaiKonversi FLOAT;
				jumlahHarga FLOAT;
				qtyPO FLOAT;

				hargaSatuan FLOAT;

				qtyPenerimaan FLOAT;
				konversiPenerimaan FLOAT;

				penerimaanBarangDetailId INTEGER;
				satuanKecilId INTEGER;
			BEGIN

			IF (NEW.additional_data != 'is_verifikasi')
						THEN
								RETURN NEW;
					END IF;
	
					validasiDetailId := NEW.validasipobarangdetail_id;
					konversiId := NEW.s_konversibrg_id;
					barangId := NEW.barang_id;
					penerimaanBarangDetailId := NEW.penerimaanbarangdetail_id;

					qtyPenerimaan := NEW.qty_diterima;
				-- Konver Ke satuan terkecil
					SELECT 
							nilai_konversi,
							satuankecil_id
					INTO
							nilaiKonversi,
							satuanKecilId
					FROM satuankonversibrg_m
					WHERE satuankonversibrg_id = konversiId;

					-- Prepare untuk mengurangi QTY PO di master obat alkes
					konversiPenerimaan := nilaiKonversi *  qtyPenerimaan;
				  -- Mencari harga netto menggunakan attribute qty_po = qty yang sudah di konversi dan jumlah harga
					SELECT
						qty_po,
						jumlah + COALESCE(discount_rp,0)
					INTO
						qtyPO,
						jumlahHarga
					FROM validasipobarangdetail_t
					WHERE validasipobarangdetail_id = validasiDetailId;

					hargaSatuan := 0;
					IF (qtyPO > 0) THEN
						hargaSatuan := ((jumlahHarga / qtyPO) / COALESCE(nilaiKonversi, 1));
					END IF;

					-- Update ke master obat alkes harga max,min, net dan avg sertan on_po
					UPDATE barang_m SET
						on_po = (on_po - konversiPenerimaan),
						barang_harganetto = hargaSatuan,
						barang_max = (CASE WHEN hargaSatuan > barang_max  
										THEN
												hargaSatuan
										ELSE
												barang_max
						END),
						barang_min = (CASE WHEN barang_min < hargaSatuan  
										THEN
												barang_min
										ELSE
												hargaSatuan
						END)
					WHERE barang_id = barangId;
		
					-- Update validasi detail untuk penerimaan 
					UPDATE validasipobarangdetail_t SET 
					qty_penerimaan = COALESCE(qty_penerimaan,0) + qtyPenerimaan,
					is_completed = (CASE WHEN (COALESCE(qty_penerimaan,0) + qtyPenerimaan) = qty_input 
											THEN 
													true 
											ELSE 
													false 
					END),
					qty_sisa = qty_input - (COALESCE(qty_penerimaan,0) + qtyPenerimaan)
					WHERE validasipobarangdetail_id = validasiDetailId;
		
					-- Insert ke stokobatalkes_t 
						 INSERT INTO stokbarang_t (
									ruangan_id,
									penerimaandetail_id,
									barang_id,
									tglkadaluarsa,
									nobatch,
									tglstok_in,
									qtystok_in,
									harganetto,
									stokbarang_aktif,
									satuankecil_id,
									tglterima
							) VALUES (
									ruanganGudang,
									NEW.penerimaanbarangdetail_id,
									barangId,
									NEW.tgl_kadaluarsa,
									NEW.no_batch,
									NEW.created_date,
									konversiPenerimaan,
									hargaSatuan,
									true,
									satuanKecilId,
									NEW.created_date
							);		

				RETURN NEW;
			END
			\$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100;
			
			");
			
			$this->execute('CREATE TRIGGER "update_penerimaan_to_barang" AFTER UPDATE OF "additional_data" ON "public"."penerimaanbarangdetail_t" FOR EACH ROW EXECUTE PROCEDURE "public".update_penerimaan_to_barang();');
			
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240918_061132_migrate_RPP_1689_improve_function_update_penerimaan_to_barang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240918_061132_migrate_RPP_1689_improve_function_update_penerimaan_to_barang cannot be reverted.\n";

        return false;
    }
    */
}
