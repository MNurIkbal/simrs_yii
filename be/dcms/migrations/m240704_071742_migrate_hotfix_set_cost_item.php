<?php

use yii\db\Migration;

/**
 * Class m240704_071742_migrate_hotfix_set_cost_item
 */
class m240704_071742_migrate_hotfix_set_cost_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER if exists set_cost ON public.obatalkespasien_t;');
		
        $this->execute('DROP FUNCTION if exists public.set_cost_item();');
		
		
        $this->execute('
			CREATE OR REPLACE FUNCTION "public"."set_cost_item"()
			  RETURNS "pg_catalog"."trigger" AS $BODY$
					DECLARE
						wa_cost float8;
						base_price_item float8;
    
						BEGIN
			-- 				select weighted_avg 
			-- 				into wa_cost
			-- 				from logasetobat_r 
			-- 				where ruangan_id = new.ruangan_id and obatalkes_id = new.obatalkes_id
			-- 				order by stokobatalkes_id desc limit 1;
			-- 	
			-- 		if wa_cost is null then
			-- 			select weighted_avg
			-- 			into wa_cost
			-- 			from logasetobat_r 
			-- 			where ruangan_id = 25 and logasetobat_r.obatalkes_id = new.obatalkes_id
			-- 			order by stokobatalkes_id desc limit 1;
			-- 		end IF;
	
	
				SELECT weighted_avg
				into wa_cost
				from stokobatalkes_r WHERE ruangan_id = new.ruangan_id and obatalkes_id = new.obatalkes_id;
	
	
					select harganetto
					into base_price_item
					from obatalkes_m
					where obatalkes_id = new.obatalkes_id;
	
					new.cost = coalesce(wa_cost,base_price_item);
					new.baseprice = base_price_item;

			    RETURN NEW;
			END
			 $BODY$
			  LANGUAGE plpgsql VOLATILE
			  COST 100;
			');
			
			$this->execute('
				CREATE TRIGGER "set_cost" BEFORE INSERT ON "public"."obatalkespasien_t"
				FOR EACH ROW
				EXECUTE PROCEDURE "public"."set_cost_item"();
				');


			
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240704_071742_migrate_hotfix_set_cost_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240704_071742_migrate_hotfix_set_cost_item cannot be reverted.\n";

        return false;
    }
    */
}
