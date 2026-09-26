<?php

use yii\db\Migration;

/**
 * Class m201205_152026_improve_asesmenperawatrd_add_column
 */
class m201205_152026_improve_asesmenperawatrd_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS asesmen_allo_text TEXT;
        ');
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201205_152026_improve_asesmenperawatrd_add_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201205_152026_improve_asesmenperawatrd_add_column cannot be reverted.\n";

        return false;
    }
    */
}
