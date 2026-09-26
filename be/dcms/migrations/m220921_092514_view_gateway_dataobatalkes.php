<?php

use yii\db\Migration;

/**
 * Class m220921_092514_view_gateway_dataobatalkes
 */
class m220921_092514_view_gateway_dataobatalkes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_data_obatalkes_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_data_obatalkes_v\"
        AS SELECT stokobatalkes_t.obatalkes_id,
            obatalkes_m.obatalkes_kode,
            obatalkes_m.obatalkes_nama,
            jenisobatalkes_m.jenisobatalkes_nama,
            stokobatalkes_t.ruangan_id,
            ruangan_m.ruangan_nama,
            COALESCE(sum(stokobatalkes_t.qtystok_in), 0::double precision) - COALESCE(sum(stokobatalkes_t.qtystok_out), 0::double precision) AS qty,
            COALESCE(obatalkes_m.harganetto, 0::double precision) AS harganetto,
            COALESCE(sum(stokobatalkes_t.qtystok_in), 0::double precision) - COALESCE(sum(stokobatalkes_t.qtystok_out), 0::double precision) * COALESCE(obatalkes_m.harganetto, 0::double precision) AS sub_total
           FROM stokobatalkes_t
             LEFT JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama,
                    a.obatalkes_kode,
                    a.jenisobatalkes_id,
                    a.harganetto
                   FROM obatalkes_m a) obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.jenisobatalkes_id,
                    a.jenisobatalkes_nama
                   FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
          GROUP BY stokobatalkes_t.obatalkes_id, obatalkes_m.obatalkes_kode, obatalkes_m.obatalkes_nama, jenisobatalkes_m.jenisobatalkes_nama, stokobatalkes_t.ruangan_id, ruangan_m.ruangan_nama, obatalkes_m.harganetto;        
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_092514_view_gateway_dataobatalkes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_092514_view_gateway_dataobatalkes cannot be reverted.\n";

        return false;
    }
    */
}
