<?php

use yii\db\Migration;

/**
 * Class m190401_064308_tarifambulan_v
 */
class m190401_064308_tarifambulan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW tarifambulan_v AS 
             SELECT ambulan_m.ambulan_id,
                ambulan_m.no_polisi,
                ambulandetail_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                ambulandetail_m.is_default,
                COALESCE(tariftindakan_m.kelaspelayanan_id, 3) AS kelaspelayanan_id,
                COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'Kelas 3\'::character varying) AS kelaspelayanan_nama,
                COALESCE(tariftindakan_m.penjamin_id, 1) AS penjamin_id,
                COALESCE(penjamin_m.penjamin_nama, \'Perseorangan\'::character varying) AS penjamin_nama,
                COALESCE(tariftindakan_m.komponentarif_id, 6) AS komponentarif_id,
                COALESCE(komponentarif_m.komponentarif_nama, \'Total Tarif\'::character varying) AS komponentarif_nama,
                COALESCE(tariftindakan_m.harga_tariftindakan, 0::double precision) AS harga_tariftindakan
               FROM ambulan_m
                 JOIN ambulandetail_m ON ambulan_m.ambulan_id = ambulandetail_m.ambulan_id
                 JOIN daftartindakan_m ON ambulandetail_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 LEFT JOIN tariftindakan_m ON ambulandetail_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
                 LEFT JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
              WHERE ambulandetail_m.is_deleted = false;
        ');

        $this->execute('
            ALTER TABLE tarifambulan_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_064308_tarifambulan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_064308_tarifambulan_v cannot be reverted.\n";

        return false;
    }
    */
}
