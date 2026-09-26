<?php

use yii\db\Migration;

/**
 * Class m210716_041631_improvment_resume_medis_diet_US368
 */
class m210716_041631_improvment_resume_medis_diet_US368 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE resumemedisri_t ADD IF NOT EXISTS catatan_diet TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210716_041631_improvment_resume_medis_diet_US368 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210716_041631_improvment_resume_medis_diet_US368 cannot be reverted.\n";

        return false;
    }
    */
}
