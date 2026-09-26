<?php

use yii\db\Migration;

/**
 * Class m211026_120711_improvment_update_master_klasifikasitekanandarah_m
 */
class m211026_120711_improvment_update_master_klasifikasitekanandarah_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            UPDATE "public"."klasifikasitekanandarah_m" SET "sistolik_min" = 0, "sistolik_max" = 85, "diastolik_min" = 0, "diastolik_max" = 55, "last_modified_date" = CURRENT_TIMESTAMP WHERE "klasifikasitekanadarah_id" = 1;
        ');

        $this->execute('
            UPDATE "public"."klasifikasitekanandarah_m" SET "sistolik_min" = 86, "sistolik_max" = 129, "diastolik_min" = 56, "diastolik_max" = 84, "last_modified_date" = CURRENT_TIMESTAMP WHERE "klasifikasitekanadarah_id" = 2;
        ');

        $this->execute('
            UPDATE "public"."klasifikasitekanandarah_m" SET "sistolik_min" = 130, "sistolik_max" = 139, "diastolik_min" = 85, "diastolik_max" = 89, "last_modified_date" = CURRENT_TIMESTAMP WHERE "klasifikasitekanadarah_id" = 3;
        ');

        $this->execute('
            UPDATE "public"."klasifikasitekanandarah_m" SET "sistolik_min" = 140, "sistolik_max" = 159, "diastolik_min" = 90, "diastolik_max" = 99, "last_modified_date" = CURRENT_TIMESTAMP WHERE "klasifikasitekanadarah_id" = 4;
        ');

        $this->execute('
            UPDATE "public"."klasifikasitekanandarah_m" SET "sistolik_min" = 160, "sistolik_max" = 179, "diastolik_min" = 100, "diastolik_max" = 109, "last_modified_date" = CURRENT_TIMESTAMP WHERE "klasifikasitekanadarah_id" = 5;
        ');

        $this->execute('
            UPDATE "public"."klasifikasitekanandarah_m" SET "sistolik_min" = 180, "sistolik_max" = 260, "diastolik_min" = 110, "diastolik_max" = 260, "last_modified_date" = NULL WHERE "klasifikasitekanadarah_id" = 6;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211026_120711_improvment_update_master_klasifikasitekanandarah_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211026_120711_improvment_update_master_klasifikasitekanandarah_m cannot be reverted.\n";

        return false;
    }
    */
}
