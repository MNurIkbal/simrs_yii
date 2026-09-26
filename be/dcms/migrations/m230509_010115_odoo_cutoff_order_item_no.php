<?php

use yii\db\Migration;

/**
 * Class m230509_010115_odoo_cutoff_order_item_no
 */
class m230509_010115_odoo_cutoff_order_item_no extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE tindakanpelayanan_t ADD COLUMN IF NOT EXISTS order_item_no varchar(50) null;");

        $this->execute("ALTER TABLE tindakanpelayanan_r ADD COLUMN IF NOT EXISTS order_item_no varchar(50) null;");

        $this->execute("DROP TRIGGER IF EXISTS generate_order_item_no_penunjang ON tindakanpelayanan_t");

        $this->execute("DROP FUNCTION IF EXISTS generate_order_item_no");

        $this->execute("
        CREATE OR REPLACE FUNCTION public.generate_order_item_no()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function\$
            
        DECLARE
        vNumber VARCHAR;
            
        BEGIN
            IF(new.pasienmasukpenunjang_id is not null) THEN
                select 
                concat(pt.no_masukpenunjang,'-',
                lpad(cast((count(tt.pasienmasukpenunjang_id)+1) as varchar(3)),3,'0'))
                into vNumber
                from tindakanpelayanan_t tt 
                left join pasienmasukpenunjang_t pt on pt.pasienmasukpenunjang_id = tt.pasienmasukpenunjang_id 
                where tt.pasienmasukpenunjang_id =new.pasienmasukpenunjang_id
                group by pt.no_masukpenunjang;
            
                
                new.order_item_no := vNumber;
            end if;

            RETURN NEW;
        END
        \$function\$;
        ");

        $this->execute("
        create trigger generate_order_item_no_penunjang before
        insert
            on
            public.tindakanpelayanan_t for each row execute procedure generate_order_item_no();
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_010115_odoo_cutoff_order_item_no cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_010115_odoo_cutoff_order_item_no cannot be reverted.\n";

        return false;
    }
    */
}
