<?php

use yii\db\Migration;

/**
 * Class m220921_094458_view_gateway_tariftindakan
 */
class m220921_094458_view_gateway_tariftindakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_tariftindakan_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_tariftindakan_v\"
        AS SELECT daftartindakan_m.daftartindakan_kode,
            daftartindakan_m.daftartindakan_nama,
            kelompoktindakan_m.kelompoktindakan_nama,
            daftartindakan_m.daftartindakan_namalainnya,
            tarif_tindakan.tarif
        FROM daftartindakan_m
            LEFT JOIN ( SELECT a.kelompoktindakan_id,
                    a.kelompoktindakan_nama
                FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            JOIN ( SELECT tindakan.daftartindakan_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                        FROM ( SELECT kelaspelayanan_m.kelaspelayanan_nama AS kelas,
                                    penjamin_m.penjamin_nama AS penjamin,
                                    perdatarif_m.perdanama_sk AS perda,
                                    tariftindakan_m.harga_tariftindakan,
                                    tariftindakan_m.persencyto_tindakan,
                                    tariftindakan_m.persen_penyulit
                                FROM tariftindakan_m
                                    JOIN ( SELECT a.kelaspelayanan_id,
                                            a.kelaspelayanan_nama
                                        FROM kelaspelayanan_m a) kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                                    LEFT JOIN ( SELECT a.penjamin_id,
                                            a.penjamin_nama
                                        FROM penjamin_m a) penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    LEFT JOIN ( SELECT a.perdatarif_id,
                                            a.perdanama_sk
                                        FROM perdatarif_m a) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                WHERE tariftindakan_m.komponentarif_id = 6 AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true AND tindakan.daftartindakan_id = tariftindakan_m.daftartindakan_id) d) AS tarif
                FROM tariftindakan_m tindakan
                WHERE tindakan.is_deleted IS FALSE AND tindakan.is_active IS TRUE
                GROUP BY tindakan.daftartindakan_id) tarif_tindakan ON daftartindakan_m.daftartindakan_id = tarif_tindakan.daftartindakan_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_094458_view_gateway_tariftindakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_094458_view_gateway_tariftindakan cannot be reverted.\n";

        return false;
    }
    */
}
