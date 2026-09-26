<?php

use yii\db\Migration;

/**
 * Class m211008_125623_migrate_oddo_int_ruangan_v
 */
class m211008_125623_migrate_oddo_int_ruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_ruangan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_ruangan_v\" AS  SELECT ruangan_m.ruangan_id AS sync_id_api,
    '-'::text AS parent_id,
    ruangan_m.ruangan_nama AS name,
    true AS active,
    6 AS sync_type,
    ruangan_m.ruangan_id,
        CASE ruangan_m.additional_data
            WHEN 'EMA'::text THEN NULL::text
            ELSE ruangan_m.additional_data
        END AS additional_data,
    ruangan_m.is_store,
    ruangan_m.is_mainstore,
    ruangan_m.is_substore,
    ruangan_m.is_cartstore,
        CASE
            WHEN ruangan_m.is_active IS TRUE AND ruangan_m.is_deleted IS TRUE THEN true
            WHEN ruangan_m.is_active IS TRUE AND ruangan_m.is_deleted IS FALSE THEN false
            WHEN ruangan_m.is_active IS FALSE AND ruangan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
        CASE
            WHEN ruangan_m.additional_data <> 'EMA'::text AND (ruangan_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN ruangan_m.additional_data <> 'EMA'::text AND (ruangan_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'MENUNGGU PROSES'::text
        END AS status_proses
   FROM ruangan_m;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211008_125623_migrate_oddo_int_ruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211008_125623_migrate_oddo_int_ruangan_v cannot be reverted.\n";

        return false;
    }
    */
}
