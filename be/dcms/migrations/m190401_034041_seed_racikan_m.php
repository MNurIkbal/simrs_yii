<?php

use yii\db\Migration;

/**
 * Class m190401_034041_seed_racikan_m
 */
class m190401_034041_seed_racikan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE racikan_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."racikan_m"("racikan_id", "racikan_nama", "racikan_singkatan", "tarif_service", "persen_service", "biaya_kemasan", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, \'Obat Racikan\', \'OR\', 0, 0, 0, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (2, \'Non Racikan\', \'NR\', 1000, 0, 0, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_034041_seed_racikan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_034041_seed_racikan_m cannot be reverted.\n";

        return false;
    }
    */
}
