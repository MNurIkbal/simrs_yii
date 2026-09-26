<?php

use yii\db\Migration;

/**
 * Class m201125_050700_migrate_mhkn_20201125_tbl_pasien_m_3025
 */
class m201125_050700_migrate_mhkn_20201125_tbl_pasien_m_3025 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('ALTER TABLE "public"."pasien_m" ADD COLUMN IF NOT EXISTS"catatanpenting_pasien" text COLLATE "pg_catalog"."default";
                ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_050700_migrate_mhkn_20201125_tbl_pasien_m_3025 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_050700_migrate_mhkn_20201125_tbl_pasien_m_3025 cannot be reverted.\n";

        return false;
    }
    */
}
