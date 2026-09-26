<?php

use yii\db\Migration;

/**
 * Class m211220_020824_migrate_oddo_servicecategory
 */
class m211220_020824_migrate_oddo_servicecategory extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."servicecategory_m" ADD COLUMN if not exists "servicegroup_id" int8;');

        $this->execute('DROP VIEW if exists "public"."int_servicecategory_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_servicecategory_v\" AS  SELECT
        CASE
            WHEN servicecategory_m.is_obat = false THEN concat('CATEG', servicecategory_m.servicecategory_id)
            ELSE concat('CATEG', servicecategory_m.servicecategory_id)
        END AS sync_id_api,
        CASE
            WHEN servicecategory_m.servicegroup_id IS NULL THEN '-'::text
            ELSE concat('GROUP', servicecategory_m.servicegroup_id)
        END AS parent_id,
    servicecategory_m.servicecategory_nama AS name,
        CASE
            WHEN servicecategory_m.is_obat = false THEN true
            ELSE false
        END AS sync_is_service,
        CASE
            WHEN servicecategory_m.is_obat = false THEN 'normal'::text
            ELSE 'normal'::text
        END AS type,
    true AS active,
    6 AS sync_type,
    servicecategory_m.servicecategory_id,
    servicecategory_m.additional_data,
        CASE
            WHEN servicecategory_m.is_active IS TRUE AND servicecategory_m.is_deleted IS TRUE THEN true
            WHEN servicecategory_m.is_active IS TRUE AND servicecategory_m.is_deleted IS FALSE THEN false
            WHEN servicecategory_m.is_active IS FALSE AND servicecategory_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
        CASE
            WHEN (servicecategory_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (servicecategory_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'MENUNGGU PROSES'::text
        END AS status_proses,
    servicecategory_m.servicegroup_id
   FROM servicecategory_m;");

        $this->execute('DROP VIEW if exists "public"."int_servicegroup_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_servicegroup_v\" AS  SELECT concat('GROUP', servicegroup_m.servicegroup_id) AS sync_id_api,
    '-'::text AS parent_id,
    servicegroup_m.servicegroup_nama AS name,
    false AS sync_is_service,
    'normal'::text AS type,
    true AS active,
    6 AS sync_type,
    servicegroup_m.servicegroup_id,
    servicegroup_m.additional_data,
        CASE
            WHEN servicegroup_m.is_active IS TRUE AND servicegroup_m.is_deleted IS TRUE THEN true
            WHEN servicegroup_m.is_active IS TRUE AND servicegroup_m.is_deleted IS FALSE THEN false
            WHEN servicegroup_m.is_active IS FALSE AND servicegroup_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
        CASE
            WHEN (servicegroup_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (servicegroup_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'MENUNGGU PROSES'::text
        END AS status_proses
   FROM servicegroup_m;");

        $this->execute('DROP VIEW if exists "public"."int_productcategory_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"int_productcategory_v\" AS  SELECT concat('BRG', kelompokbarang_m.kelompokbarang_id) AS sync_id_api,
    kelompokbarang_m.kelompokbarang_nama AS name,
    kelompokbarang_m.kelompokbarang_id AS master_id,
    'kelompokbarang'::text AS jenis,
    kelompokbarang_m.additional_data,
        CASE
            WHEN (kelompokbarang_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (kelompokbarang_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status_proses
   FROM kelompokbarang_m
UNION ALL
 SELECT concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS sync_id_api,
    jenisobatalkes_m.jenisobatalkes_nama AS name,
    jenisobatalkes_m.jenisobatalkes_id AS master_id,
    'jenisobatalkes'::text AS jenis,
    jenisobatalkes_m.additional_data,
        CASE
            WHEN (jenisobatalkes_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (jenisobatalkes_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status_proses
   FROM jenisobatalkes_m
UNION ALL
 SELECT concat('TND', kelompoktindakan_m.kelompoktindakan_id) AS sync_id_api,
    kelompoktindakan_m.kelompoktindakan_nama AS name,
    kelompoktindakan_m.kelompoktindakan_id AS master_id,
    'kelompoktindakan'::text AS jenis,
    kelompoktindakan_m.additional_data,
        CASE
            WHEN (kelompoktindakan_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (kelompoktindakan_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status_proses
   FROM kelompoktindakan_m
UNION ALL
 SELECT concat('CATEG', servicecategory_m.servicecategory_id) AS sync_id_api,
    servicecategory_m.servicecategory_nama AS name,
    servicecategory_m.servicecategory_id AS master_id,
    'servicecategory'::text AS jenis,
    servicecategory_m.additional_data,
        CASE
            WHEN (servicecategory_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (servicecategory_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status_proses
   FROM servicecategory_m
UNION ALL
 SELECT concat('GROUP', servicegroup_m.servicegroup_id) AS sync_id_api,
    servicegroup_m.servicegroup_nama AS name,
    servicegroup_m.servicegroup_id AS master_id,
    'servicegroup'::text AS jenis,
    servicegroup_m.additional_data,
        CASE
            WHEN (servicegroup_m.additional_data::json ->> 'is_error'::text) = 'false'::text THEN 'SUKSES'::text
            WHEN (servicegroup_m.additional_data::json ->> 'is_error'::text) = 'true'::text THEN 'GAGAL'::text
            ELSE 'BELUM KIRIM'::text
        END AS status_proses
   FROM servicegroup_m;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211220_020824_migrate_oddo_servicecategory cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211220_020824_migrate_oddo_servicecategory cannot be reverted.\n";

        return false;
    }
    */
}
