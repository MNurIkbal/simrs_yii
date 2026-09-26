<?php

use yii\db\Migration;

/**
 * Class m251031_083018_plafonbpjs_v_new_version
 */
class m251031_083018_plafonbpjs_v_new_version extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."plafonbpjs_v";
        ');

        $this->execute('
            CREATE VIEW "public"."plafonbpjs_v" AS SELECT p.plafonbpjs_id,
                i.instalasi_nama AS instalasi,
                k.kelaspelayanan_nama AS kelas,
                p.plafon,
                p.additional_data::json ->> \'plafon_ruangan\'::text AS plafon_ruangan,
                p.is_active,
                p.instalasi_id,
                p.kelaspelayanan_id,
                p.list_ruangan_id,
                string_to_array(NULLIF(p.list_ruangan_id, \'\'::text), \',\'::text)::integer[] AS arr_ruangan_id,
                ( SELECT string_agg(r.ruangan_nama::text, \', \'::text) AS string_agg
                    FROM unnest(string_to_array(NULLIF(p.list_ruangan_id, \'\'::text), \',\'::text)) t(id_str)
                        JOIN ruangan_m r ON r.ruangan_id = t.id_str::integer) AS list_ruangan_nama
            FROM plafonbpjs_m p
                LEFT JOIN instalasi_m i ON i.instalasi_id = p.instalasi_id
                LEFT JOIN kelaspelayanan_m k ON k.kelaspelayanan_id = p.kelaspelayanan_id
            WHERE p.is_deleted = false
            ORDER BY i.instalasi_nama, k.kelaspelayanan_nama
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251031_083018_plafonbpjs_v_new_version cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251031_083018_plafonbpjs_v_new_version cannot be reverted.\n";

        return false;
    }
    */
}
