<?php

use yii\db\Migration;

/**
 * Class m200811_033154_migrate_mhkn_20200811_trig_tindakanpelayanan
 */
class m200811_033154_migrate_mhkn_20200811_trig_tindakanpelayanan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"tindakanpelayanan_penunjang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$-- author yaya

DECLARE
    vTindakanPelayananId INT;
    paramJson VARCHAR;
    vKompenen VARCHAR;
BEGIN

  paramJson := NEW.additional_data;  
    vTindakanPelayananId := paramJson::json->>'permintaankepenunjang_id';
  vKompenen := paramJson::json->>'list_komponen';
    
    -- Ini untuk Kondisi dimana mengupdate permintaaan kepenunjang
    IF(vTindakanPelayananId > 0) THEN
        UPDATE permintaankepenunjang_t set tindakanpelayanan_id = NEW.tindakanpelayanan_id where permintaankepenunjang_id = vTindakanPelayananId;
    NEW.additional_data = NULL;
    END IF;

  IF (json_array_length(vKompenen::json) > 0)  THEN
            INSERT INTO tindakankomponen_t (
                            komponentarif_id,
                            tindakanpelayanan_id,
                            tarif_kompsatuan,
                            tarif_tindakankomp,
                            tarifcyto_tindakankomp,
                            tarifpenyulit_komponen,
                            subsidiasuransikomp,
                            subsidipemerintahkomp,
                            iurbiayakomp,
                            created_by
         ) SELECT 
                            komponentarif_id,
                            NEW.tindakanpelayanan_id as tindakanpelayanan_id, 
                            tarif_kompsatuan,
                            tarif_tindakankomp,
                            tarifcyto_tindakankomp,
                            tarifpenyulit_komponen,
                            subsidiasuransikomp,
                            subsidipemerintahkomp,
                            iurbiayakomp,
              NEW.created_by as created_by
            FROM json_populate_recordset(null::tindakankomponen_t,vKompenen::json);
    END IF;
    -- Ini untuk insert ke tindakankomponen_t

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_033154_migrate_mhkn_20200811_trig_tindakanpelayanan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_033154_migrate_mhkn_20200811_trig_tindakanpelayanan cannot be reverted.\n";

        return false;
    }
    */
}
