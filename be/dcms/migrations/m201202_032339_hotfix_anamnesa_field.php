<?php

use yii\db\Migration;

/**
 * Class m201202_032339_hotfix_anamnesa_field
 */
class m201202_032339_hotfix_anamnesa_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."anamnesa_t" ALTER COLUMN "nadi" TYPE varchar(30) USING "nadi"::varchar(30);
        ');

        $this->execute('
            ALTER TABLE "public"."anamnesa_t" ALTER COLUMN "berat_badan" TYPE varchar(30) USING "berat_badan"::varchar(30);
        ');

        $this->execute('
            ALTER TABLE "public"."anamnesa_t" ALTER COLUMN "tinggi_badan" TYPE varchar(30) USING "tinggi_badan"::varchar(30);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201202_032339_hotfix_anamnesa_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201202_032339_hotfix_anamnesa_field cannot be reverted.\n";

        return false;
    }
    */
}
