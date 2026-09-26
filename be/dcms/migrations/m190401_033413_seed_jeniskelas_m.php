<?php

use yii\db\Migration;

/**
 * Class m190401_033413_seed_jeniskelas_m
 */
class m190401_033413_seed_jeniskelas_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE jeniskelas_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."jeniskelas_m"("jeniskelas_id", "jeniskelas_nama", "jeniskelas_namalainnya", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, \'Reguler\', \'Reguler\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (2, \'Eksekutif\', \'Eksekutif\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_033413_seed_jeniskelas_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_033413_seed_jeniskelas_m cannot be reverted.\n";

        return false;
    }
    */
}
