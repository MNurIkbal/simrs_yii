<?php

use yii\db\Migration;

/**
 * Class m190401_032818_seed_caramasuk_m
 */
class m190401_032818_seed_caramasuk_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE caramasuk_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."caramasuk_m"("caramasuk_id", "caramasuk_nama", "caramasuk_namalainnya", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, \'Melalui Rawat Jalan\', \'Melalui Rawat Jalan\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (4, \'Melalui Rujukan\', \'Melalui Rujukan\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (6, \'Melalui Askes\', \'Melalui Askes\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (9, \'Melalui Rawat Inap\', \'Melalui Rawat Inap\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_032818_seed_caramasuk_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_032818_seed_caramasuk_m cannot be reverted.\n";

        return false;
    }
    */
}
