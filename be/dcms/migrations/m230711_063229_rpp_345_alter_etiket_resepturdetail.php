<?php

use yii\db\Migration;

/**
 * Class m230711_063229_rpp_345_alter_etiket_resepturdetail
 */
class m230711_063229_rpp_345_alter_etiket_resepturdetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS kesimpulanrd_v");

        $this->execute("DROP VIEW IF EXISTS worklistresepdetail_v;");
        
        $this->execute("DROP VIEW IF EXISTS inforesepturdetail_v;");
        
        $this->execute("DROP VIEW IF EXISTS inforesepdetail_v ;");
        
        $this->execute("DROP VIEW IF EXISTS inforesepdetail1_v;");

        $this->execute("DROP VIEW IF EXISTS infopenjualanresepdetail_v;");

        $this->execute("ALTER TABLE public.resepturdetail_t ALTER COLUMN etiket TYPE varchar USING etiket::varchar;");

        $kesimpulanrd_v = file_get_contents(__DIR__ . '/definitions/kesimpulanrd_v.sql');
        $this->execute($kesimpulanrd_v);

        $worklistresepdetail_v = file_get_contents(__DIR__ . '/definitions/worklistresepdetail_v.sql');
        $this->execute($worklistresepdetail_v);

        $inforesepturdetail_v = file_get_contents(__DIR__ . '/definitions/inforesepturdetail_v.sql');
        $this->execute($inforesepturdetail_v);

        $inforesepdetail_v = file_get_contents(__DIR__ . '/definitions/inforesepdetail_v.sql');
        $this->execute($inforesepdetail_v);

        $inforesepdetail1_v = file_get_contents(__DIR__ . '/definitions/inforesepdetail1_v.sql');
        $this->execute($inforesepdetail1_v);

        $infopenjualanresepdetail_v = file_get_contents(__DIR__ . '/definitions/infopenjualanresepdetail_v.sql');
        $this->execute($infopenjualanresepdetail_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230711_063229_rpp_345_alter_etiket_resepturdetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230711_063229_rpp_345_alter_etiket_resepturdetail cannot be reverted.\n";

        return false;
    }
    */
}
