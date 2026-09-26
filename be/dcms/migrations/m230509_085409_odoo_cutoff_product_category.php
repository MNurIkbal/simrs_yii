<?php

use yii\db\Migration;

/**
 * Class m230509_085409_odoo_cutoff_product_category
 */
class m230509_085409_odoo_cutoff_product_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_productcategory_v");

        $this->execute("
        CREATE OR REPLACE VIEW public.newodoo_productcategory_v
        AS 
        SELECT concat('BRG', kelompokbarang_m.kelompokbarang_id) AS sync_id_api,
            null::character varying  as parent_id,
            kelompokbarang_m.kelompokbarang_nama AS name,
            false as sync_is_service,
            'normal'::text as \"type\",
            true as active,
            CASE
                WHEN kelompokbarang_m.is_active IS TRUE AND kelompokbarang_m.is_deleted IS TRUE THEN true
                WHEN kelompokbarang_m.is_active IS TRUE AND kelompokbarang_m.is_deleted IS FALSE THEN false
                WHEN kelompokbarang_m.is_active IS FALSE AND kelompokbarang_m.is_deleted IS FALSE THEN true
                ELSE true
            END AS wipro_block,
            kelompokbarang_m.kelompokbarang_id as origin_id,
            'kelompokbarang'::text AS jenis
        FROM kelompokbarang_m
        UNION ALL
        SELECT concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS sync_id_api,
            null::character varying  as parent_id,
            jenisobatalkes_m.jenisobatalkes_nama AS name,
            false as sync_is_service,
            'normal'::text as \"type\",
            true as active,
            CASE
                WHEN jenisobatalkes_m.is_active IS TRUE AND jenisobatalkes_m.is_deleted IS TRUE THEN true
                WHEN jenisobatalkes_m.is_active IS TRUE AND jenisobatalkes_m.is_deleted IS FALSE THEN false
                WHEN jenisobatalkes_m.is_active IS FALSE AND jenisobatalkes_m.is_deleted IS FALSE THEN true
                ELSE true
            END AS wipro_block,
            jenisobatalkes_m.jenisobatalkes_id as origin_id,
            'jenisobatalkes'::text AS jenis
        FROM jenisobatalkes_m
        UNION ALL
        SELECT concat('TND', kelompoktindakan_m.kelompoktindakan_id) AS sync_id_api,
            null::character varying  as parent_id,
            kelompoktindakan_m.kelompoktindakan_nama AS name,
            true as sync_is_service,
            'service'::text as \"type\",
            true as active,
            CASE
                WHEN kelompoktindakan_m.is_active IS TRUE AND kelompoktindakan_m.is_deleted IS TRUE THEN true
                WHEN kelompoktindakan_m.is_active IS TRUE AND kelompoktindakan_m.is_deleted IS FALSE THEN false
                WHEN kelompoktindakan_m.is_active IS FALSE AND kelompoktindakan_m.is_deleted IS FALSE THEN true
                ELSE true
            END AS wipro_block,
            kelompoktindakan_m.kelompoktindakan_id as origin_id,
            'kelompoktindakan'::text AS jenis
        FROM kelompoktindakan_m
        UNION ALL
        SELECT concat('CATEG', servicecategory_m.servicecategory_id) AS sync_id_api,
            null::character varying  as parent_id,
            servicecategory_m.servicecategory_nama AS name,
            true as sync_is_service,
            'service'::text as \"type\",
            true as active,
            CASE
                WHEN servicecategory_m.is_active IS TRUE AND servicecategory_m.is_deleted IS TRUE THEN true
                WHEN servicecategory_m.is_active IS TRUE AND servicecategory_m.is_deleted IS FALSE THEN false
                WHEN servicecategory_m.is_active IS FALSE AND servicecategory_m.is_deleted IS FALSE THEN true
                ELSE true
            END AS wipro_block,
            servicecategory_m.servicecategory_id as origin_id,
            'servicecategory'::text AS jenis
        FROM servicecategory_m
        UNION ALL
        SELECT concat('GROUP', servicegroup_m.servicegroup_id) AS sync_id_api,
            null::character varying  as parent_id,
            servicegroup_m.servicegroup_nama AS name,
            true as sync_is_service,
            'service'::text as \"type\",
            true as active,
            CASE
                WHEN servicegroup_m.is_active IS TRUE AND servicegroup_m.is_deleted IS TRUE THEN true
                WHEN servicegroup_m.is_active IS TRUE AND servicegroup_m.is_deleted IS FALSE THEN false
                WHEN servicegroup_m.is_active IS FALSE AND servicegroup_m.is_deleted IS FALSE THEN true
                ELSE true
            END AS wipro_block,
            servicegroup_m.servicegroup_id as origin_id,
            'servicegroup'::text AS jenis
        FROM servicegroup_m;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_085409_odoo_cutoff_product_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_085409_odoo_cutoff_product_category cannot be reverted.\n";

        return false;
    }
    */
}
