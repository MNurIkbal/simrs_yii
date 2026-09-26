<?php

use yii\db\Migration;

/**
 * Class m221228_102920_migrate_function_bulan
 */
class m221228_102920_migrate_function_bulan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.bulan;');

        $this->execute("
			CREATE OR REPLACE FUNCTION \"public\".\"bulan\"(\"tgl\" date)
			  RETURNS \"pg_catalog\".\"varchar\" AS \$BODY\$
			DECLARE
			BULAN CHARACTER VARYING(12);
			BEGIN
			   IF    EXTRACT(MONTH FROM TGL) = 1 THEN
			         BULAN = 'Jan';
			   ELSIF EXTRACT(MONTH FROM TGL) = 2 THEN
			         BULAN = 'Feb';
			   ELSIF EXTRACT(MONTH FROM TGL) = 3 THEN
			         BULAN = 'Mar';
			   ELSIF EXTRACT(MONTH FROM TGL) = 4 THEN
			         BULAN = 'Apr';
			   ELSIF EXTRACT(MONTH FROM TGL) = 5 THEN
			         BULAN = 'Mei';
			   ELSIF EXTRACT(MONTH FROM TGL) = 6 THEN
			         BULAN = 'Jun';
			   ELSIF EXTRACT(MONTH FROM TGL) = 7 THEN
			         BULAN = 'Jul';
			   ELSIF EXTRACT(MONTH FROM TGL) = 8 THEN
			         BULAN = 'Agu';
			   ELSIF EXTRACT(MONTH FROM TGL) = 9 THEN
			         BULAN = 'Sep';
			   ELSIF EXTRACT(MONTH FROM TGL) = 10 THEN
			         BULAN = 'Okt';
			   ELSIF EXTRACT(MONTH FROM TGL) = 11 THEN
			         BULAN = 'Nop';
			   ELSIF EXTRACT(MONTH FROM TGL) = 12 THEN
			         BULAN = 'Des';
			   END IF;
   
			RETURN BULAN;
			END;
			\$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100
			
           ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221228_102920_migrate_function_bulan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221228_102920_migrate_function_bulan cannot be reverted.\n";

        return false;
    }
    */
}
