<?php

use yii\db\Migration;

/**
 * Class m240530_081842_migrate_rpp_1240_peformance_so_function_weighted_avg_stok
 */
class m240530_081842_migrate_rpp_1240_peformance_so_function_weighted_avg_stok extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute("ALTER TABLE \"public\".\"stokobatalkes_r\" ADD COLUMN \"weighted_avg\" float8;");
		
		$this->execute("
			CREATE OR REPLACE FUNCTION \"public\".\"weighted_avg_stokobatalkes_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$   
 DECLARE v_weighted_avg float8;
       
BEGIN

if new.tipe = 'IN' then

SELECT weighted_avg into v_weighted_avg 
from logasetobat_r WHERE ruangan_id = 25 and obatalkes_id = NEW.obatalkes_id
and tipe = 'IN' ORDER BY logasetobat_id desc limit 1;


UPDATE stokobatalkes_r set weighted_avg = v_weighted_avg
WHERE obatalkes_id = NEW.obatalkes_id;
end if;
RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
;");

$this->execute("CREATE TRIGGER \"weighted_avg_stokobatalkes_r_update\" AFTER INSERT ON \"public\".\"logasetobat_r\"
FOR EACH ROW
EXECUTE PROCEDURE \"public\".\"weighted_avg_stokobatalkes_r_update\"();");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240530_081842_migrate_rpp_1240_peformance_so_function_weighted_avg_stok cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240530_081842_migrate_rpp_1240_peformance_so_function_weighted_avg_stok cannot be reverted.\n";

        return false;
    }
    */
}
