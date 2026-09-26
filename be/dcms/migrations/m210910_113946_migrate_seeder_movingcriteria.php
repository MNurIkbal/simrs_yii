<?php

use yii\db\Migration;

/**
 * Class m210910_113946_migrate_seeder_movingcriteria
 */
class m210910_113946_migrate_seeder_movingcriteria extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('TRUNCATE TABLE movingcriteria_m RESTART IDENTITY;');
        
        $this->execute("
            INSERT INTO public.movingcriteria_m(movingcriteria_id, min, max, criteria, factor, ss_min, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(1, 26, 30, 'FAST', 'MAX', '5', NULL, '2021-07-22 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(2, 20, 25, 'MED FAST', 'MAX', '4', NULL, '2021-07-22 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(3, 13, 19, 'MED', 'AVG', '3', NULL, '2021-07-22 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(4, 8, 12, 'MED SLOW', 'AVG', '2', NULL, '2021-07-22 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(5, 4, 7, 'SLOW', 'MIN', '2', NULL, '2021-07-22 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(6, 0, 3, 'VERY SLOW', 'MIN_RESEP', '0', NULL, '2021-07-22 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210910_113946_migrate_seeder_movingcriteria cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210910_113946_migrate_seeder_movingcriteria cannot be reverted.\n";

        return false;
    }
    */
}
