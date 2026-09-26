<?php

use yii\db\Migration;

/**
 * Class m240610_093941_migrate_RPP_1444_programfisioterapi_v
 */
class m240610_093941_migrate_RPP_1444_programfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS programfisioterapi_v");
        $programfisioterapi_v = file_get_contents(__DIR__ . '/definitions/programfisioterapi_v.sql');
        $this->execute($programfisioterapi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240610_093941_migrate_RPP_1444_programfisioterapi_v cannot be reverted.\n";

        return false;
    }
}
