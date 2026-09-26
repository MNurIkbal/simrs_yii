<?php

use yii\db\Migration;

/**
 * Class m220729_075327_migrate_odoo_view_int_roomname_v
 */
class m220729_075327_migrate_odoo_view_int_roomname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_roomname_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_roomname_v" AS  
            SELECT kamarruangan_m.kamarruangan_id::text AS sync_id_api,
                kamarruangan_m.kamarruangan_id,
                kamarruangan_m.kamarruangan_nokamar AS name,
                kamarruangan_m.kamarruangan_kode AS code,
                6 AS sync_type,
                kamarruangan_m.additional_data,
                    CASE
                        WHEN kamarruangan_m.is_active IS TRUE AND kamarruangan_m.is_deleted IS TRUE THEN false
                        WHEN kamarruangan_m.is_active IS TRUE AND kamarruangan_m.is_deleted IS FALSE THEN true
                        WHEN kamarruangan_m.is_active IS FALSE AND kamarruangan_m.is_deleted IS FALSE THEN false
                        ELSE false
                    END AS active,
                    CASE
                        WHEN kamarruangan_m.is_active IS TRUE AND kamarruangan_m.is_deleted IS TRUE THEN true
                        WHEN kamarruangan_m.is_active IS TRUE AND kamarruangan_m.is_deleted IS FALSE THEN false
                        WHEN kamarruangan_m.is_active IS FALSE AND kamarruangan_m.is_deleted IS FALSE THEN true
                        ELSE true
                    END AS wipro_block,
                    CASE
                        WHEN (kamarruangan_m.additional_data::json ->> \'is_error\'::text) = \'false\'::text THEN \'SUKSES\'::text
                        WHEN (kamarruangan_m.additional_data::json ->> \'is_error\'::text) = \'true\'::text THEN \'GAGAL\'::text
                        ELSE \'BELUM PROSES\'::text
                    END AS status_proses,
                kamarruangan_m.ruangan_id::text AS ward_id,
                kamarruangan_m.kelaspelayanan_id::text AS bed_type_id
               FROM kamarruangan_m;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_075327_migrate_odoo_view_int_roomname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_075327_migrate_odoo_view_int_roomname_v cannot be reverted.\n";

        return false;
    }
    */
}
