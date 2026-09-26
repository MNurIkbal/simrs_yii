<?php

use yii\db\Migration;

/**
 * Class m230303_154621_migrate_GM24_function_fgetketersediaankamar
 */
class m230303_154621_migrate_GM24_function_fgetketersediaankamar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."fgetketersediaankamar"("xruangan_id" int4=0, "xkelaspelayanan_id" int4=0, "xkamarruangan_id" int4=0)
  RETURNS TABLE("kamartempattidur_id" int4, "kamarruangan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "kamarruangan_nokamar" varchar, "harga_tariftindakan" float8, "kamarruangan_jenis" varchar, "no_tempattidur" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "status_isi" varchar, "total_isi" float8, "total_kosong" float8, "kode_warna" varchar, "kettempattidur_warna" varchar, "additional_data" varchar) AS $BODY$ 
            BEGIN
            IF(xruangan_id = 0)
            THEN
                xruangan_id := NULL;
            END IF;

            IF(xkelaspelayanan_id = 0)
            THEN
                xkelaspelayanan_id := NULL;
            END IF;

            IF(xkamarruangan_id = 0)
            THEN
                xkamarruangan_id := NULL;
            END IF;

            RETURN QUERY
            SELECT 
            ktt.kamartempattidur_id::int4,
            ktt.kamarruangan_id::int4,
            krm.ruangan_id::int4,
            rm.ruangan_nama::varchar,
            krm.kamarruangan_nokamar::varchar,
            tk.harga_tariftindakan::float8,
            krm.kamarruangan_jenis::varchar,
            ktt.no_tempattidur::varchar,
            krm.kelaspelayanan_id::int4,
            kp.kelaspelayanan_nama::varchar,
            ktt.status_isi::varchar,
            COALESCE(isi.total_isi::float8, 0::int) as total_isi,
            COALESCE(kosong.total_kosong::float8, 0::int) as total_kosong,
                kettempattidur_m.kode_warna::varchar,
                kettempattidur_m.kettempattidur_warna::varchar,
                kettempattidur_m.additional_data::varchar
            FROM kamartempattidur_m ktt
             JOIN kamarruangan_m krm ON ktt.kamarruangan_id = krm.kamarruangan_id AND krm.is_deleted = false AND krm.is_active = true
             JOIN ruangan_m rm ON krm.ruangan_id = rm.ruangan_id AND rm.is_deleted = false
             JOIN kelaspelayanan_m kp ON krm.kelaspelayanan_id = kp.kelaspelayanan_id AND kp.is_deleted = false 
             JOIN tariftindakan_m tk ON krm.kamarruangan_id = tk.kamarruangan_id AND tk.komponentarif_id=6 and tk.is_deleted=false AND tk.kelaspelayanan_id = krm.kelaspelayanan_id AND tk.penjamin_id = 1
                         JOIN penjamin_m  pm ON tk.penjamin_id = pm.penjamin_id AND tk.penjamin_id = pm.penjamin_id
             LEFT JOIN kettempattidur_m ON ktt.kettempattidur_id = kettempattidur_m.kettempattidur_id 
             LEFT JOIN jeniskasuspenyakit_m jkpkamar ON krm.jeniskasuspenyakit_id = jkpkamar.jeniskasuspenyakit_id AND jkpkamar.is_deleted = false
             LEFT JOIN (SELECT
                          kamartempattidur_m.kamarruangan_id,
                          count(kamartempattidur_m.kamartempattidur_id) as total_isi
                    FROM kamartempattidur_m
                    WHERE kamartempattidur_m.is_deleted=FALSE and kamartempattidur_m.status_isi=true and kamartempattidur_m.is_active = TRUE
                    GROUP BY kamartempattidur_m.kamarruangan_id) isi ON ktt.kamarruangan_id = isi.kamarruangan_id
              LEFT JOIN (SELECT
                          kamartempattidur_m.kamarruangan_id,
                          count(kamartempattidur_m.kamartempattidur_id) as total_kosong
                    FROM kamartempattidur_m
                    WHERE kamartempattidur_m.is_deleted=FALSE and  kamartempattidur_m.status_isi=false and kamartempattidur_m.is_active = TRUE
                    GROUP BY kamartempattidur_m.kamarruangan_id) kosong ON ktt.kamarruangan_id = kosong.kamarruangan_id 
                 WHERE ktt.is_deleted=FALSE AND ktt.is_active=TRUE AND pm.penjamin_id=1 AND 
                 krm.ruangan_id = COALESCE(xruangan_id, krm.ruangan_id) AND 
                 krm.kelaspelayanan_id = COALESCE(xkelaspelayanan_id, krm.kelaspelayanan_id) AND 
                 ktt.kamarruangan_id = COALESCE(xkamarruangan_id, ktt.kamarruangan_id) ;
                 

            END; 
            $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230303_154621_migrate_GM24_function_fgetketersediaankamar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230303_154621_migrate_GM24_function_fgetketersediaankamar cannot be reverted.\n";

        return false;
    }
    */
}
