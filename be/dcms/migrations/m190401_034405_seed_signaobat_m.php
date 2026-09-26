<?php

use yii\db\Migration;

/**
 * Class m190401_034405_seed_signaobat_m
 */
class m190401_034405_seed_signaobat_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE signaobat_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."signaobat_m"("signa_id", "signa_nama", "signa_namalainnya", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, \'3 x 1\', \'3 x 1\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (2, \'2 x 1\', \'2 x 1\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (3, \'2 butir sebelum makan\', \'2 butir sebelum makan\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (4, \'2 butir sebelum tidur\', \'2 butir sebelum tidur\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (5, \'1 x 1\', \'1 x 1\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_034405_seed_signaobat_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_034405_seed_signaobat_m cannot be reverted.\n";

        return false;
    }
    */
}
