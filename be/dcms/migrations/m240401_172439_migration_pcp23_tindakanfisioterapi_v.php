<?php

use yii\db\Migration;

/**
 * Class m240401_172439_migration_pcp23_tindakanfisioterapi_v
 */
class m240401_172439_migration_pcp23_tindakanfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS tindakanfisioterapi_v");
        $tindakanfisioterapi_v = file_get_contents(__DIR__ . '/definitions/tindakanfisioterapi_v.sql');
        $this->execute($tindakanfisioterapi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240401_172439_migration_pcp23_tindakanfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240401_172439_migration_pcp23_tindakanfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
