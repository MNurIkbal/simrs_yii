<?php

use yii\db\Migration;

/**
 * Class m201124_043257_migrate_mhkn_20201124_trg_f_gethp_20201124_2989
 */
class m201124_043257_migrate_mhkn_20201124_trg_f_gethp_20201124_2989 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_gethp\"(\"xfirstdate\" date, \"xlastdate\" date, \"xruangan_id\" int4)
  RETURNS TABLE(\"jumlah_jadwal\" int4, \"vidhari\" int4) AS \$BODY\$

DECLARE
    vdays int4;
    vdate int4;
    tanggal date;
    vhari varchar;

BEGIN
SELECT (xlastdate::date - xfirstdate::date) + 1 INTO vdays;
vdate := 0;

WHILE vdate < vdays
LOOP
    IF vdate = 0
    THEN
        SELECT DATE_TRUNC('day', xfirstdate::TIMESTAMP) INTO tanggal;
    ELSE
        SELECT DATE_TRUNC('day', tanggal::TIMESTAMP) + '1 DAY'::INTERVAL INTO tanggal;
    END IF;
    
    SELECT to_char(tanggal::date, 'day') INTO vhari;
    
    SELECT CASE vhari
        WHEN 'sunday   ' THEN
            81
        WHEN 'monday   ' THEN
            75
        WHEN 'tuesday  ' THEN
            76
        WHEN 'wednesday' THEN
            77
        WHEN 'thursday ' THEN
            78
        WHEN 'friday   ' THEN
            79
        WHEN 'saturday ' THEN
            80
        END INTO vidhari;
        
    SELECT COUNT(jadwalbukapoli_id) INTO jumlah_jadwal
    FROM jadwalbukapoli_m
    WHERE hari = vidhari
    AND ruangan_id = xruangan_id
    GROUP BY ruangan_id;
    
    
    -- RETURN DATA ROW
    RETURN NEXT;
    
    vdate  := vdate + 1;    
END LOOP;

  

END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000
                ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201124_043257_migrate_mhkn_20201124_trg_f_gethp_20201124_2989 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201124_043257_migrate_mhkn_20201124_trg_f_gethp_20201124_2989 cannot be reverted.\n";

        return false;
    }
    */
}
