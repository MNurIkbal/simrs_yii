<?php

use yii\db\Migration;

/**
 * Class m220413_085345_hotfix_funtion_delete_tindakansudahbayar
 */
class m220413_085345_hotfix_funtion_delete_tindakansudahbayar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."delete_tindakansudahbayar"()
              RETURNS "pg_catalog"."trigger" AS $BODY$-- author: Rizqi Febian

                            DECLARE

                            varPembayaranPelayananId INT;
                            varPembayaranId INT;
                            varPendaftaranId INT;
                            isDeleted BOOLEAN;
                            dateDeleted DATE;
                            deletedBy INT;

                            BEGIN
                            varPembayaranPelayananId := NEW.pembayaranpelayanan_id;
                            varPembayaranId := NEW.pembayaran_id;
                            varPendaftaranId := NEW.pendaftaran_id;
                            isDeleted := NEW.is_deleted;
                            dateDeleted := NEW.deleted_date;
                            deletedBy := NEW.deleted_by;

                            IF isDeleted = TRUE THEN
                                
                        ----------tandabuktibayar_t-----------------    
                                     UPDATE tandabuktibayar_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                        WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                            AND is_deleted = false;
                                        
                        ----------piutangasuransi_t-----------------    
                               UPDATE piutangasuransi_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                        WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                            AND is_deleted = false;

                        ----------tindakanpelayanan_t-----------------    
                                     UPDATE tindakanpelayanan_t 
                                            SET tindakansudahbayar_id  = NULL,
                                                    tarif_dijamin = NULL,
                                                    tarif_dibayarkan = NULL,
                                                    tarif_diskon = 0
                                            FROM (SELECT 
                                                            tindakanpelayanan_id, tindakansudahbayar_id 
                                                        FROM tindakansudahbayar_t 
                                                        WHERE pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = FALSE
                                                        ) as subquery 
                                            WHERE tindakanpelayanan_t.tindakanpelayanan_id = subquery.tindakanpelayanan_id OR subquery.tindakanpelayanan_id = tindakanpelayanan_t.parent_id;
                                            
                        ----------obatalkespasien_t-----------------    
                                        UPDATE obatalkespasien_t 
                                            SET obatsudahbayar_id  = NULL 
                                            FROM (SELECT 
                                                            obatalkespasien_id, obatsudahbayar_id 
                                                        FROM obatsudahbayar_t 
                                                        WHERE pembayaranpelayanan_id = varPembayaranPelayananId) as subquery 
                                            WHERE obatalkespasien_t.obatalkespasien_id = subquery.obatalkespasien_id;
                                            
                        ----------obatsudahbayar_t-----------------    
                                        UPDATE obatsudahbayar_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                         WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                             AND is_deleted = false;
                                             
                        ----------tindakansudahbayar_t-----------------    
                                        UPDATE tindakansudahbayar_t SET is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy 
                                         WHERE pembayaranpelayanan_id = varPembayaranPelayananId 
                                           AND is_deleted = false;
                                             
                         ----------bayaruangmuka_t-----------------    
                                                    UPDATE bayaruangmuka_t SET pemakaianuangmuka_id = NULL, jumlah_uangmuka = subquery.jumlahuangmuka FROM (SELECT pemakaianuangmuka_id,SUM(pemakaian_uangmuka) as jumlahuangmuka FROM pemakaianuangmuka_t WHERE pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = FALSE GROUP BY pemakaianuangmuka_id, pemakaian_uangmuka) as subquery WHERE bayaruangmuka_t.pemakaianuangmuka_id = subquery.pemakaianuangmuka_id;
                                                    
                       ----------pemakaianuangmuka_t-----------------      
                                     UPDATE pemakaianuangmuka_t set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                         
                        ----------pendaftaran_t-----------------                     
                                        UPDATE pendaftaran_t set status_bayar = 349  
                                         WHERE pendaftaran_id = varPendaftaranId;
                                         
                        ----------penjualanresep_t-----------------                  
                                        UPDATE penjualanresep_t x
                                           SET status_bayar = 349
                                           FROM obatalkespasien_t AS y,
                                                        obatsudahbayar_t z
                                             WHERE x.penjualanresep_id = y.penjualanresep_id
                                                 AND y.obatalkespasien_id = z.obatalkespasien_id
                                                 AND z.pembayaranpelayanan_id = varPembayaranPelayananId;
                                END IF;

                                    RETURN NEW;

                                    END
                                    $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220413_085345_hotfix_funtion_delete_tindakansudahbayar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220413_085345_hotfix_funtion_delete_tindakansudahbayar cannot be reverted.\n";

        return false;
    }
    */
}
