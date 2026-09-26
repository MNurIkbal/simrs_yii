<?php

use yii\db\Migration;

/**
 * Class m210928_124633_improvment_asesmen_medis_US1494
 */
class m210928_124633_improvment_asesmen_medis_US1494 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS kepala TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS mulut TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS mata TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS tht TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS leher TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS toraks TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS jantung TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS paru TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS abdomen TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS genitalia_anus TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS ekstremitas TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS kulit TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS rencana TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210928_124633_improvment_asesmen_medis_US1494 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210928_124633_improvment_asesmen_medis_US1494 cannot be reverted.\n";

        return false;
    }
    */
}
