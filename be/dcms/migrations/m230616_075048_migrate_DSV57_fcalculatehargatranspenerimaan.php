<?php

use yii\db\Migration;

/**
 * Class m230616_075048_migrate_DSV57_fcalculatehargatranspenerimaan
 */
class m230616_075048_migrate_DSV57_fcalculatehargatranspenerimaan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.fcalculatehargatranspenerimaan(use_disc boolean, use_ppn boolean, harga_beli double precision, disc double precision, ppn integer)
            RETURNS double precision
            LANGUAGE plpgsql
        AS \$function\$
            DECLARE 
                vDiscount float8;
                vPpn integer;
            
            begin
                vDiscount := 0;
                vPpn := 0;
            
                if (use_disc = true) then
                    vDiscount := harga_beli * disc::double precision / 100::double precision;
                end if;
            
                if (use_ppn = true) then
                    vPpn := (harga_beli - vDiscount) * ppn::double precision / 100::double precision;
                end if;
            
                RETURN COALESCE((harga_beli - vDiscount + vPpn), 0);
            END;
        \$function\$ ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_075048_migrate_DSV57_fcalculatehargatranspenerimaan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_075048_migrate_DSV57_fcalculatehargatranspenerimaan cannot be reverted.\n";

        return false;
    }
    */
}
