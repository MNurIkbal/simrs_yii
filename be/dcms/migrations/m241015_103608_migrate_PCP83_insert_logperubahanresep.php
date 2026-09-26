<?php

use yii\db\Migration;

/**
 * Class m241015_103608_migrate_PCP83_insert_logperubahanresep
 */
class m241015_103608_migrate_PCP83_insert_logperubahanresep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TRIGGER insert_logperubahanresep BEFORE
INSERT
    OR
UPDATE
    ON
    public.resepturdetail_t FOR EACH ROW EXECUTE PROCEDURE insert_logperubahanresep_r_reseptur()");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241015_103608_migrate_PCP83_insert_logperubahanresep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241015_103608_migrate_PCP83_insert_logperubahanresep cannot be reverted.\n";

        return false;
    }
    */
}
