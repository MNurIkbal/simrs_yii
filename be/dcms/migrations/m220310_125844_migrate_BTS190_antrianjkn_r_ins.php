<?php

use yii\db\Migration;

/**
 * Class m220310_125844_migrate_BTS190_antrianjkn_r_ins
 */
class m220310_125844_migrate_BTS190_antrianjkn_r_ins extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"antrianjkn_r_ins\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    DECLARE
    dataAntrianJkn VARCHAR;
    paramJson VARCHAR;
    
    BEGIN
    paramJson := NEW.additional_data; 
    dataAntrianJkn := paramJson::json->>'jkn';
    
    -- di comment kebutuhan reservasi onsite
--  IF(new.jenis_reservasi = 1102) THEN
        INSERT INTO antrianjkn_r (
                                pendaftaranol_id,
                                antrian_id,
                                tanggal_periksa,
                                nomorkartu,
                                jenis_cara_bayar,
                                jeniskunjungan,
                                nomorreferensi,
                                keterangan,
                                no_rekam_medik
                                                                ) VALUES (
                                                                new.pendaftaranol_id,
                                                                new.antrian_id,
                                                                new.tgl_pendaftaranol,
                                                                (dataAntrianJkn::json->>'nomorkartu')::VARCHAR,
                                                                (dataAntrianJkn::json->>'jenis_cara_bayar')::INTEGER,
                                                                (dataAntrianJkn::json->>'jeniskunjungan')::INTEGER,
                                                                (dataAntrianJkn::json->>'nomorreferensi')::VARCHAR,
                                                                (dataAntrianJkn::json->>'keterangan')::VARCHAR,
                                                                (dataAntrianJkn::json->>'no_rekam_medik')::VARCHAR
                                                                );
--  END IF;
    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220310_125844_migrate_BTS190_antrianjkn_r_ins cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220310_125844_migrate_BTS190_antrianjkn_r_ins cannot be reverted.\n";

        return false;
    }
    */
}
