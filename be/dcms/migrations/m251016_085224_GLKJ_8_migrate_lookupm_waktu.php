<?php

use yii\db\Migration;

/**
 * Class m251016_085224_GLKJ_8_migrate_lookupm_waktu
 */
class m251016_085224_GLKJ_8_migrate_lookupm_waktu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("UPDATE lookup_m SET additional_data = '{\"expired_time\": \"09:00\"}' WHERE lookup_type = 'waktu' and lookup_name = 'Pagi';");
        $this->execute("UPDATE lookup_m SET additional_data = '{\"expired_time\": \"14:00\"}' WHERE lookup_type = 'waktu' and lookup_name = 'Siang';");
        $this->execute("UPDATE lookup_m SET additional_data = '{\"expired_time\": \"17:00\"}' WHERE lookup_type = 'waktu' and lookup_name = 'Sore';");
        $this->execute("UPDATE lookup_m SET additional_data = '{\"expired_time\": \"19:00\"}' WHERE lookup_type = 'waktu' and lookup_name = 'Malam';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251016_085224_GLKJ_8_migrate_lookupm_waktu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251016_085224_GLKJ_8_migrate_lookupm_waktu cannot be reverted.\n";

        return false;
    }
    */
}
