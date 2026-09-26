<?php

use yii\db\Migration;

/**
 * Class m250506_102240_rpp_2124_hasilpemeriksaan_m
 */
class m250506_102240_rpp_2124_hasilpemeriksaan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS \"public\".\"hasilpemeriksaan_m\" (
                \"hasilpemeriksaan_id\" serial4 PRIMARY KEY,
                hasilpemeriksaan_kode VARCHAR(10),
                \"hasilpemeriksaan_nama\" varchar(200) COLLATE \"pg_catalog\".\"default\",
                \"additional_data\" text COLLATE \"pg_catalog\".\"default\",
                \"created_date\" timestamp(6) DEFAULT ('now'::text)::date,
                \"created_by\" int4,
                \"modified_count\" int4,
                \"last_modified_date\" timestamp(6),
                \"last_modified_by\" int4,
                \"is_deleted\" bool NOT NULL DEFAULT false,
                \"is_active\" bool NOT NULL DEFAULT true,
                \"deleted_date\" timestamp(6),
                \"deleted_by\" int4
            );
        ");

        $this->execute("
            INSERT INTO hasilpemeriksaan_m(hasilpemeriksaan_id, hasilpemeriksaan_kode, hasilpemeriksaan_nama, created_date)
            SELECT lookup_id, lookup_id, lookup_name, created_date
            FROM lookup_m 
            WHERE lookup_type = 'hasil_pemeriksaan'
            ORDER BY lookup_id;
        ");

        $this->execute("
            SELECT setval('\"public\".\"hasilpemeriksaan_m_hasilpemeriksaan_id_seq\"', (SELECT MAX(hasilpemeriksaan_id) FROM hasilpemeriksaan_m), true);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250506_102240_rpp_2124_hasilpemeriksaan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250506_102240_rpp_2124_hasilpemeriksaan_m cannot be reverted.\n";

        return false;
    }
    */
}
