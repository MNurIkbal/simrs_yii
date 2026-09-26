<?php

use yii\db\Migration;

/**
 * Class m250514_065924_migrate_rpp_2125_create_pool_tables
 */
class m250514_065924_migrate_rpp_2125_create_pool_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Obat Alkes Pasien
        $obatalkespasien_calc = file_get_contents(__DIR__ . '/definitions/obatalkespasien_calc.sql');
        $this->execute($obatalkespasien_calc);

        $this->execute('
            CREATE INDEX IF NOT EXISTS obatalkespasien_calc_obatalkespasien_id_idx ON public.obatalkespasien_calc USING btree (obatalkespasien_id);
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS obatalkespasien_calc_penjualanresep_id_idx ON public.obatalkespasien_calc USING btree (penjualanresep_id);
        ');


        // Penjualan Resep
        $penjualanresep_calc = file_get_contents(__DIR__ . '/definitions/penjualanresep_calc.sql');
        $this->execute($penjualanresep_calc);

        $this->execute('
            CREATE INDEX IF NOT EXISTS penjualanresep_calc_penjualanresep_id_idx ON public.penjualanresep_calc USING btree (penjualanresep_id);
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS penjualanresep_calc_reseptur_id_idx ON public.penjualanresep_calc USING btree (reseptur_id);
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS penjualanresep_calc_status_reseptur_idx ON public.penjualanresep_calc USING btree (status_reseptur);
        ');


        // Reseptur Detail
        $resepturdetail_calc = file_get_contents(__DIR__ . '/definitions/resepturdetail_calc.sql');
        $this->execute($resepturdetail_calc);

        $this->execute('
            CREATE INDEX IF NOT EXISTS resepturdetail_calc_reseptur_id_idx ON public.resepturdetail_calc USING btree (reseptur_id);
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS resepturdetail_calc_resepturdetail_id_idx ON public.resepturdetail_calc USING btree (resepturdetail_id);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250514_065924_migrate_rpp_2125_create_pool_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250514_065924_migrate_rpp_2125_create_pool_tables cannot be reverted.\n";

        return false;
    }
    */
}
