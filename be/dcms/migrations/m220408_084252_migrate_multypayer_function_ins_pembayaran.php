<?php

use yii\db\Migration;

/**
 * Class m220408_084252_migrate_multypayer_function_ins_pembayaran
 */
class m220408_084252_migrate_multypayer_function_ins_pembayaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."ins_pembayaran"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                            
            DECLARE
                    paramJson VARCHAR;
                    vPembayaranpelayanan json;
                    vPembayaranpenjamin json;
                    vPembayaranmetode json;
                    vPembayarandiskon json;
                    vPembayaran_id INTEGER;
                    vrow json;
                    vNumber VARCHAR; 
                    vNopembayaran VARCHAR;
                    vId integer := 1; --> Transaksi Penomoran Pembayaran (BYR)
                    vPrefix VARCHAR;
                    vCount INTEGER;
                    
            BEGIN
                    paramJson := NEW.additional_data;
                    vPembayaranpelayanan := paramJson::json->>\'pembayaran_pelayanan\';
                    vPembayaranpenjamin := paramJson::json->>\'pembayaran_penjamin\';
                    vPembayaranmetode := paramJson::json->>\'pembayaran_jenis_pembayaran\';
                    vPembayarandiskon := paramJson::json->>\'pembayaran_diskon\';
                    vPembayaran_id := NEW.pembayaran_id;
                    vCount  := 1;
                    
            -------------------------Penomoran-------------------------------
            SELECT 
                    (RIGHT(\'0\' || date_part(\'YEAR\',now()),4) ||
                    RIGHT(\'0\' || date_part(\'month\',now()),2) ||
                    RIGHT(\'0\' || date_part(\'DAY\',now()),2) ||
                    CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pembayaran), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, \'0\'))) last_no
                INTO 
                    vNumber
                FROM penomoran_k
                    LEFT JOIN pembayaran_t ON pembayaran_t.created_date::DATE = CURRENT_DATE
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
                    
                NEW.no_pembayaran := TRIM(vPrefix) || vNumber;
            ----------------------------end Penomoran----------------------------


                    -- insert ke pembayaranpelayanan_t
                        FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpelayanan)
                        LOOP
                        IF (vrow->>\'penjamin_id\' IS NOT NULL) THEN
                            INSERT INTO pembayaranpelayanan_t (
                                                            pembayaran_id,
                                                            carabayar_id,
                                                            ruangan_id,
                                                            penjamin_id,
                                                            pendaftaran_id,
                                                            tandabuktibayar_id,
                                                            pasien_id,
                                                            pasienadmisi_id,
                                                            ruangan_pelakhir_id,
                                                            tgl_pembayaran, 
                                                            total_biayaoa,
                                                            total_biayatindakan,
                                                            total_biayapelayanan,
                                                            total_subsidiasuransi,
                                                            total_subsidipemerintah,
                                                            total_subsidirs,
                                                            total_iurbiaya,
                                                            total_bayartindakan,
                                                            total_discount,
                                                            total_pembebasan,
                                                            total_sisatagihan,
                                                            statusbayar,
                                                            penjualanresep_id,  
                                                            biaya_administrasi,
                                                            e_collection,
                                                            no_rekening,
                                                            nama_pemrekening,
                                                            penggunaan_uangmuka,
                                                            total_terbayar,
                                                            pembulatan,
                                                            additional_data,
                                                            no_pembayaran,
                                                            is_penjaminutama
                                                            

                                     ) VALUES (
                                                            vPembayaran_id,
                                                            (vrow->>\'carabayar_id\')::INTEGER,
                                                            (vrow->>\'ruangan_id\')::INTEGER,
                                                            (vrow->>\'penjamin_id\')::INTEGER,
                                                            (vrow->>\'pendaftaran_id\')::INTEGER,
                                                            (vrow->>\'tandabuktibayar_id\')::INTEGER,
                                                            (vrow->>\'pasien_id\')::INTEGER,
                                                            (vrow->>\'pasienadmisi_id\')::INTEGER,
                                                            (vrow->>\'ruangan_pelakhir_id\')::INTEGER,
                                                            (vrow->>\'tgl_pembayaran\')::TIMESTAMP,
                                                            (vrow->>\'total_biayaoa\')::FLOAT,
                                                            (vrow->>\'total_biayatindakan\')::FLOAT,
                                                            (vrow->>\'total_biayapelayanan\')::FLOAT,
                                                            (vrow->>\'total_subsidiasuransi\')::FLOAT,
                                                            (vrow->>\'total_subsidipemerintah\')::FLOAT,
                                                            (vrow->>\'total_subsidirs\')::FLOAT,
                                                            (vrow->>\'total_iurbiaya\')::FLOAT,
                                                            (vrow->>\'total_bayartindakan\')::FLOAT,
                                                            (vrow->>\'total_discount\')::FLOAT,
                                                            (vrow->>\'total_pembebasan\')::FLOAT,
                                                            (vrow->>\'total_sisatagihan\')::FLOAT,
                                                            (vrow->>\'statusbayar\')::VARCHAR,
                                                            CASE WHEN vrow->>\'pendaftaran_id\' IS NOT NULL THEN
                                                                    NULL
                                                            ELSE
                                                                    (vrow->>\'penjualanresep_id\')::INTEGER
                                                            END,
                                                            (vrow->>\'biaya_administrasi\')::FLOAT,
                                                            (vrow->>\'e_collection\')::BOOLEAN,
                                                            (vrow->>\'no_rekening\')::VARCHAR,
                                                            (vrow->>\'nama_pemrekening\')::VARCHAR,
                                                            (vrow->>\'penggunaan_uangmuka\')::FLOAT,
                                                            (vrow->>\'total_terbayar\')::FLOAT,
                                                            (vrow->>\'pembulatan\')::FLOAT,
                                                            (vrow->>\'additional_data\')::TEXT,
                                                            CONCAT(NEW.no_pembayaran,\'-\',vCount),
                                                            (vrow->>\'is_penjaminutama\')::BOOLEAN
                                            );
                                            
                                            vCount  := vCount  + 1;
                END IF;
              END LOOP;
                            
                                            NEW.no_invoicepasien := CONCAT(NEW.no_pembayaran,\'-\',vCount);
                                
                        -- insert ke pembayaranpenjamin_t
                            FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpenjamin)
                          LOOP
                                IF (vrow->>\'penjamin_id\' IS NOT NULL) THEN
                                    INSERT INTO pembayaranpenjamin_t (
                                                    pembayaran_id,
                                                    penjamin_id,
                                                    penjamin_nama,
                                                    no_kartu,
                                                    total_dijamin

                                 ) VALUES (
                                                    vPembayaran_id,
                                                    (vrow->>\'penjamin_id\')::INTEGER,
                                                    (vrow->>\'penjamin_nama\')::VARCHAR,
                                                    (vrow->>\'no_kartu\')::VARCHAR,
                                                    (vrow->>\'total_dijamin\')::FLOAT                         
                                            );
                                END IF;
                            END LOOP;
                            
                            -- insert ke pembayaranmetode_t
                            FOR vrow IN SELECT * FROM json_array_elements(vPembayaranmetode)
                          LOOP
                                IF (vrow->>\'metode_bayar\' IS NOT NULL) THEN
                                    INSERT INTO pembayaranmetode_t (
                                                    pembayaran_id,
                                                    metode_bayar,
                                                    no_kartu,
                                                    total_dibayar,
                                                                                            jenisnontunai_id,
                                                                                            edclist_id,
                                                                                            nama_edc,
                                                                                            created_by,
                                                                                            pendaftaran_id

                                 ) VALUES (
                                                    vPembayaran_id,
                                                    (vrow->>\'metode_bayar\')::VARCHAR,
                                                    (vrow->>\'no_kartu\')::VARCHAR,
                                                    (vrow->>\'total_dibayar\')::FLOAT,                     
                                                    (vrow->>\'jenisnontunai_id\')::INTEGER,                     
                                                    (vrow->>\'edclist_id\')::INTEGER,                     
                                                    (vrow->>\'label_edc\')::VARCHAR,
                                                                                            NEW.created_by,
                                                                                            NEW.pendaftaran_id
                                            );
                                END IF;
                            END LOOP;


                                -- insert ke pembayarandiskon_t
                                FOR vrow IN SELECT * FROM json_array_elements(vPembayarandiskon)
                                LOOP 
                                    IF (vrow->>\'total_diskon\' IS NOT NULL) THEN
                                        INSERT INTO pembayarandiskon_t (
                                                                pembayaran_id,
                                                                pegawai_id,
                                                                komponentarif_id,
                                                                total_komponentarif,
                                                                total_diskon,
                                                                                                                    alasan
                                        ) VALUES (
                                                                vPembayaran_id,
                                                                (vrow->>\'pegawai_id\')::INTEGER,
                                                                (vrow->>\'komponentarif_id\')::INTEGER,
                                                                (vrow->>\'total_komponentarif\')::FLOAT,
                                                                (vrow->>\'total_diskon\')::FLOAT,
                                                                (vrow->>\'alasan\')::TEXT
                                        );
                                        END IF;
                                END LOOP;
                                
                                
                            RETURN NEW;
                        END
                        $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_084252_migrate_multypayer_function_ins_pembayaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_084252_migrate_multypayer_function_ins_pembayaran cannot be reverted.\n";

        return false;
    }
    */
}
