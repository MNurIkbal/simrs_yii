<?php

use yii\db\Migration;

/**
 * Class m190401_063424_alter_tariftindakan_m
 */
class m190401_063424_alter_tariftindakan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW mastertariftindakan_v;
        ');

        $this->execute('
          DROP VIEW infotarifrs_v;
        ');

        $this->execute('
          DROP VIEW tarifambulan_v;
        ');

        $this->execute('
          DROP VIEW tariftindakanruangan_v;
        ');

        $this->execute('
          DROP VIEW tariftindakanruangandetail_v;
        ');

        $this->execute('
          DROP VIEW tariftindakan_v;
        ');

        $this->execute('
          DROP VIEW tariftindakanrad_v;
        ');

        $this->execute('
          DROP VIEW tariftindakanlab_v;
        ');
        
        $this->execute('
          DROP VIEW tarifpaketpenunjang_v;
        ');

        $this->execute('
          DROP VIEW infotarifpenunjang_v;
        ');

        $this->execute('
          DROP VIEW tariftindakanoperasi_v;
        ');

        $this->execute('
          DROP VIEW pakettindakanrad_v;
        ');

        $this->execute('
          DROP VIEW pakettindakanlab_v;
        ');

        $this->execute('
              ALTER TABLE "public"."tariftindakan_m" 
              ALTER COLUMN "harga_tariftindakan" TYPE decimal(18,2) USING "harga_tariftindakan"::decimal(18,2),
              ALTER COLUMN "persendiskon_tindakan" TYPE decimal(18,2) USING "persendiskon_tindakan"::decimal(18,2),
              ALTER COLUMN "hargadiskon_tindakan" TYPE decimal(18,2) USING "hargadiskon_tindakan"::decimal(18,2),
              ALTER COLUMN "persencyto_tindakan" TYPE decimal(18,2) USING "persencyto_tindakan"::decimal(18,2);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_063424_alter_tariftindakan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_063424_alter_tariftindakan_m cannot be reverted.\n";

        return false;
    }
    */
}
