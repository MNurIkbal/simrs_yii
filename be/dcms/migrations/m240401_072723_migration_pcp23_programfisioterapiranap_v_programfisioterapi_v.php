<?php

use yii\db\Migration;

/**
 * Class m240401_072723_migration_pcp23_programfisioterapiranap_v_programfisioterapi_v
 */
class m240401_072723_migration_pcp23_programfisioterapiranap_v_programfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS programfisioterapi_v");
        $programfisioterapi_v = file_get_contents(__DIR__ . '/definitions/programfisioterapi_v.sql');
        $this->execute($programfisioterapi_v);

        $this->execute("DROP VIEW IF EXISTS programfisioterapiranap_v");
        $programfisioterapiranap_v = file_get_contents(__DIR__ . '/definitions/programfisioterapiranap_v.sql');
        $this->execute($programfisioterapiranap_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240401_072723_migration_pcp23_programfisioterapiranap_v_programfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240401_072723_migration_pcp23_programfisioterapiranap_v_programfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
