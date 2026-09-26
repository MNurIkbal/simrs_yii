<?php

use yii\db\Migration;

/**
 * Class m250514_070010_migrate_rpp_2125_create_triggers
 */
class m250514_070010_migrate_rpp_2125_create_triggers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TRIGGER penjualanresep_insert_calc
            AFTER INSERT
            ON public.penjualanresep_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.penjualanresep_calc_insert();
        ');

        $this->execute('
            CREATE TRIGGER obatalkespasien_calc_insert
            AFTER INSERT
            ON public.obatalkespasien_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.obatalkespasien_calc_insert();
        ');

        $this->execute('
            CREATE TRIGGER resepturdetail_calc_insert
            AFTER INSERT
            ON public.resepturdetail_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.resepturdetail_calc_insert();
        ');

        $this->execute('
            CREATE TRIGGER penjualanresep_calc_update
            AFTER UPDATE
            ON public.penjualanresep_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.penjualanresep_calc_update();
        ');

        $this->execute('
            CREATE TRIGGER reseptur_calc_update
            AFTER UPDATE
            ON public.reseptur_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.reseptur_calc_update();
        ');

        $this->execute('
            CREATE TRIGGER resepturdetail_calc_update
            AFTER UPDATE
            ON public.resepturdetail_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.resepturdetail_calc_update();
        ');

        $this->execute('
            CREATE TRIGGER obatalkespasien_calc_update
            AFTER UPDATE
            ON public.obatalkespasien_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.obatalkespasien_calc_update();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250514_070010_migrate_rpp_2125_create_triggers cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250514_070010_migrate_rpp_2125_create_triggers cannot be reverted.\n";

        return false;
    }
    */
}
