<?php

use yii\db\Migration;

/**
 * Class m240305_093402_hotfix_addlookup_urlicare
 */
class m240305_093402_hotfix_addlookup_urlicare extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m where lookup_id = 2169");
        $this->execute("
        INSERT INTO lookup_m (lookup_id,lookup_type,lookup_name,lookup_value) VALUES
	    (2169,'bpjs','url_icare','https://apijkn-dev.bpjs-kesehatan.go.id/ihs_dev/api/rs/validate');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240305_093402_hotfix_addlookup_urlicare cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240305_093402_hotfix_addlookup_urlicare cannot be reverted.\n";

        return false;
    }
    */
}
