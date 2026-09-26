<?php

use yii\db\Migration;

/**
 * Class m221207_113536_migrate_gb_108_alter_soaprj_t_cppt_t_addolumn_is_icd_x
 */
class m221207_113536_migrate_gb_108_alter_soaprj_t_cppt_t_addolumn_is_icd_x extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE cppt_t ADD IF NOT EXISTS is_icd_x bool default true;
        ');
		
        $this->execute('
            ALTER TABLE soaprj_t ADD IF NOT EXISTS is_icd_x bool default true;
        ');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221207_113536_migrate_gb_108_alter_soaprj_t_cppt_t_addolumn_is_icd_x cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221207_113536_migrate_gb_108_alter_soaprj_t_cppt_t_addolumn_is_icd_x cannot be reverted.\n";

        return false;
    }
    */
}
