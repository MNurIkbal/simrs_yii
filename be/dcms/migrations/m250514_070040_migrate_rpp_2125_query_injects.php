<?php

use yii\db\Migration;

/**
 * Class m250514_070040_migrate_rpp_2125_query_injects
 */
class m250514_070040_migrate_rpp_2125_query_injects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            INSERT INTO obatalkespasien_calc (
                obatalkespasien_id, 
                qty_konversi, 
                det_konversi, 
                obatalkes_id, 
                penjualanresep_id, 
                is_deleted
            )
            SELECT
                obatalkespasien_id, 
                qty_konversi, 
                det_konversi,
                obatalkes_id,
                penjualanresep_id,
                is_deleted
            FROM obatalkespasien_t
            WHERE penjualanresep_id IN (
                SELECT penjualanresep_id 
                FROM penjualanresep_t 
                WHERE is_deleted IS FALSE 
                AND status_reseptur <> 660 
                AND status_reseptur <> 432
            )
            AND is_deleted IS FALSE;
        ');

        $this->execute('
            INSERT INTO penjualanresep_calc (
                penjualanresep_id, 
                ruangan_id, 
                noresep, 
                reseptur_id, 
                status_reseptur, 
                is_deleted
            )
            SELECT
                penjualanresep_id, 
                ruangan_id, 
                noresep, 
                reseptur_id, 
                status_reseptur, 
                is_deleted
            FROM penjualanresep_t
            WHERE is_deleted IS FALSE 
            AND status_reseptur <> 660 
            AND status_reseptur <> 432;
        ');

        $this->execute('
            INSERT INTO resepturdetail_calc (
                resepturdetail_id, 
                obatalkes_id, 
                qty_konversi, 
                det_konversi, 
                reseptur_id, 
                is_deleted
            )
            SELECT
                resepturdetail_id, 
                obatalkes_id, 
                qty_konversi, 
                det_konversi, 
                reseptur_id, 
                is_deleted
            FROM resepturdetail_t
            WHERE is_deleted IS FALSE 
            AND reseptur_id IN (
                SELECT reseptur_id 
                FROM reseptur_t 
                WHERE status_reseptur <> 660 
                AND status_reseptur <> 432
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250514_070040_migrate_rpp_2125_query_injects cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250514_070040_migrate_rpp_2125_query_injects cannot be reverted.\n";

        return false;
    }
    */
}
