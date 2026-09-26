<?php

use yii\db\Migration;

/**
 * Class m210127_123145_migrate_20210127_int_penjamin_v
 */
class m210127_123145_migrate_20210127_int_penjamin_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  $this->execute('DROP VIEW if exists "public"."int_penjamin_v";');

  $this->execute("
    CREATE VIEW \"public\".\"int_penjamin_v\" AS  SELECT concat('PEN', penjamin_m.penjamin_id) AS sync_id_api,
    penjamin_m.penjamin_kode AS vendor_code,
    penjamin_m.penjamin_nama AS name,
    penjamin_m.penjamin_nama AS display_name,
    carabayar_m.carabayar_nama AS customer_type_api,
    '-'::text AS contact_person,
    '-'::text AS phone,
    '-'::text AS mobile,
    '-'::text AS fax,
    '-'::text AS email,
    '-'::text AS website,
    penjamin_m.alamat_penjamin AS street,
    penjamin_m.alamat_penjamin AS street2,
    penjamin_m.alamat_penjamin AS street3,
    '-'::text AS city,
    '-'::text AS zip,
    NULL::text AS credit_days,
    NULL::text AS opdiscountid,
    NULL::text AS ipdiscountid,
    NULL::text AS taxid,
        CASE
            WHEN penjamin_m.is_active IS TRUE AND penjamin_m.is_deleted IS TRUE THEN true
            WHEN penjamin_m.is_active IS TRUE AND penjamin_m.is_deleted IS FALSE THEN false
            WHEN penjamin_m.is_active IS FALSE AND penjamin_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
        CASE
            WHEN carabayar_m.groupcarabayar_id = 417 THEN false
            ELSE true
        END AS insurance,
    true AS customer,
    true AS active,
    6 AS sync_type,
    penjamin_m.penjamin_id,
    penjamin_m.additional_data
   FROM penjamin_m
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id AND carabayar_m.is_deleted = false;");
  
  $this->execute('ALTER TABLE "public"."int_penjamin_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_123145_migrate_20210127_int_penjamin_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_123145_migrate_20210127_int_penjamin_v cannot be reverted.\n";

        return false;
    }
    */
}
