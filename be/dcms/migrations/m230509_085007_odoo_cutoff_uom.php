<?php

use yii\db\Migration;

/**
 * Class m230509_085007_odoo_cutoff_uom
 */
class m230509_085007_odoo_cutoff_uom extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_uom_v");

        $this->execute("
        CREATE OR REPLACE VIEW public.newodoo_uom_v
        AS 
        select 
        satuanunit_m.satuanunit_id AS sync_id_api,
        satuanunit_m.satuanunit_nama AS name,
        satuanunit_m.satuanunit_namalain AS code,
        true AS active,
        1 AS factor,
        'reference'::text AS uom_type,
        1 AS category_id,
        CASE
            WHEN satuanunit_m.is_active IS TRUE AND satuanunit_m.is_deleted IS TRUE THEN true
            WHEN satuanunit_m.is_active IS TRUE AND satuanunit_m.is_deleted IS FALSE THEN false
            WHEN satuanunit_m.is_active IS FALSE AND satuanunit_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
        from satuanunit_m;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_085007_odoo_cutoff_uom cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_085007_odoo_cutoff_uom cannot be reverted.\n";

        return false;
    }
    */
}
