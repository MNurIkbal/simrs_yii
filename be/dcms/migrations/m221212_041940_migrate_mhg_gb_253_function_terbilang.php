<?php

use yii\db\Migration;

/**
 * Class m221212_041940_migrate_mhg_gb_253_function_terbilang
 */
class m221212_041940_migrate_mhg_gb_253_function_terbilang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DROP FUNCTION if exists public.terbilang;');
		
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"terbilang\"(\"nilai\" int8)
  RETURNS \"pg_catalog\".\"varchar\" AS \$BODY\$

DECLARE

    pembagi    bigint ;
    nilai_besar   bigint;
    nilai_kecil    bigint;
    kata    varchar(250);
    hasil_bagi    numeric;
    dummy        bigint;
    awalan_kata    varchar(100);
    unit        varchar(30);
    cek_koma    varchar(50);
    awalan    varchar(10);
    akhiran        varchar(10);

begin

 kata = '';
 nilai_besar = FLOOR(ABS(nilai) );
 nilai_kecil = ROUND((ABS(nilai) - nilai_besar ) * 100);
 --nilai_kecil     = ROUND(cast(((ABS(nilai)-ABS(nilai_besar))*100) as numeric),0);

 pembagi = 1000000000000;
if nilai_besar > pembagi * 1000 then kata = 'OUT OF RANGE'; end if;
WHILE pembagi >= 1
loop
  hasil_bagi = FLOOR(nilai_besar / pembagi);
  nilai_besar = cast(nilai_besar as bigint) % pembagi;
  unit = '';
  unit = (case when
        hasil_bagi >0
     then
         (case when pembagi = 1000000000000 THEN 'TRILYUN ' else
         (case WHEN pembagi=1000000000 THEN 'MILYAR ' else
         (case WHEN pembagi=1000000 THEN 'JUTA ' else
         (case WHEN pembagi=1000 THEN 'RIBU '
       else
       unit
             end)
         end)
         END)
         END)
     else
        unit
     end)  ;
     awalan_kata = '';
     dummy = hasil_bagi;
     if dummy>=100 then
       awalan_kata = (case when floor(dummy/100) = 1 then 'SE' else
        (case when floor(dummy/100) = 2 then 'DUA ' else
        (case when floor(dummy/100) = 3 then 'TIGA ' else
        (case when floor(dummy/100) = 4 then 'EMPAT ' else
        (case when floor(dummy/100) = 5 then 'LIMA ' else
        (case when floor(dummy/100) = 6 then 'ENAM ' else
        (case when floor(dummy/100) = 7 then 'TUJUH ' else
        (case when floor(dummy/100) = 8 then 'DELAPAN ' else
        'SEMBILAN '
        END)
        END)
        END)
        END)
        END)
        END)
        END)
        END) || 'RATUS ';
     end if;
     dummy = cast(hasil_bagi as bigint) % 100;

     if dummy < 10 then if dummy=1 and unit = 'RIBU ' then
         if  hasil_bagi=dummy then awalan_kata = awalan_kata || 'SE';
         else
         awalan_kata = awalan_kata ||'SATU ';
         end if;
       else
         if dummy >0 then
            awalan_kata =awalan_kata ||
            (case when dummy = 1 then 'SATU ' else
        (case when dummy = 2 then 'DUA ' else
        (case when dummy = 3 then 'TIGA ' else
        (case when dummy = 4 then 'EMPAT ' else
        (case when dummy = 5 then 'LIMA ' else
        (case when dummy = 6 then 'ENAM ' else
        (case when dummy = 7 then 'TUJUH ' else
        (case when dummy = 8 then 'DELAPAN ' else
        'SEMBILAN '
        END)
        END)
        END)
        END)
        END)
        END)
        END)
        END);
        end if;
       end if;
      else
			
    if dummy >10 AND dummy <20 then
         awalan_kata = awalan_kata ||
         (case when cast(dummy as bigint)%10 = 1 then 'SE' else
        (case when cast(dummy as bigint)%10 = 2 then 'DUA ' else
        (case when cast(dummy as bigint)%10 = 3 then 'TIGA ' else
        (case when cast(dummy as bigint)%10 = 4 then 'EMPAT ' else
        (case when cast(dummy as bigint)%10 = 5 then 'LIMA ' else
        (case when cast(dummy as bigint)%10 = 6 then 'ENAM ' else
        (case when cast(dummy as bigint)%10 = 7 then 'TUJUH ' else
        (case when cast(dummy as bigint)%10 = 8 then 'DELAPAN ' else
        'SEMBILAN '
        END)
        END)
        END)
        END)
        END)
        END)
        END)
        END)||'BELAS ';
    else
        awalan_kata = awalan_kata ||
         (case when floor(dummy/10) = 1 then 'SE' else
        (case when floor(dummy/10) = 2 then 'DUA ' else
        (case when floor(dummy/10) = 3 then 'TIGA ' else
        (case when floor(dummy/10) = 4 then 'EMPAT ' else
        (case when floor(dummy/10) = 5 then 'LIMA ' else
        (case when floor(dummy/10)= 6 then 'ENAM ' else
        (case when floor(dummy/10) = 7 then 'TUJUH ' else
        (case when floor(dummy/10) = 8 then 'DELAPAN ' else
        'SEMBILAN '
        END)
        END)
        END)
        END)
        END)
        END)
        END)
        END)||'PULUH ';

   IF cast(dummy as bigint)% 10 > 0 then
             awalan_kata = awalan_kata ||
 --(case when cast(dummy as bigint)%10  = 1 then 'SE' else
 (case when cast(dummy as bigint)%10  = 1 then 'SATU' else
        (case when cast(dummy as bigint)%10 = 2 then 'DUA ' else
        (case when cast(dummy as bigint)%10 = 3 then 'TIGA ' else
        (case when cast(dummy as bigint)%10 = 4 then 'EMPAT ' else
        (case when cast(dummy as bigint)%10 = 5 then 'LIMA ' else
        (case when cast(dummy as bigint)%10 = 6 then 'ENAM ' else
        (case when cast(dummy as bigint)%10 = 7 then 'TUJUH ' else
        (case when cast(dummy as bigint)%10 = 8 then 'DELAPAN ' else
        'SEMBILAN '
        END)
        END)
        END)
        END)
        END)
        END)
        END)
        END);

         end if; 
         end if;     
     end if;
     kata = kata || awalan_kata || unit;
     pembagi = pembagi / 1000;

end loop;

if FLOOR(nilai) = 0 then kata = 'NOL '; end if;
cek_koma = '';


if nilai_kecil <10  then  if nilai_kecil > 0 then
     cek_koma = 'KOMA NOL '||
               (case when nilai_kecil = 1 then 'SATU ' else
        (case when nilai_kecil = 2 then 'DUA ' else
        (case when nilai_kecil = 3 then 'TIGA ' else
        (case when nilai_kecil = 4 then 'EMPAT ' else
        (case when nilai_kecil = 5 then 'LIMA ' else
        (case when nilai_kecil = 6 then 'ENAM ' else
        (case when nilai_kecil = 7 then 'TUJUH ' else
        (case when nilai_kecil = 8 then 'DELAPAN ' else
                'SEMBILAN '
            ENd)
         end)
         end)
         end)
         end)
         end)
         end)
         end);
		
  end if ;
 else

 cek_koma = 'KOMA '||
               (case when floor(nilai_kecil/10) = 1 then 'SATU ' else
        (case when floor(nilai_kecil/10) = 2 then 'DUA ' else
        (case when floor(nilai_kecil/10) = 3 then 'TIGA ' else
        (case when floor(nilai_kecil/10) = 4 then 'EMPAT ' else
        (case when floor(nilai_kecil/10) = 5 then 'LIMA ' else
        (case when floor(nilai_kecil/10) = 6 then 'ENAM ' else
        (case when floor(nilai_kecil/10) = 7 then 'TUJUH ' else
        (case when floor(nilai_kecil/10) = 8 then 'DELAPAN ' else
                'SEMBILAN '
            ENd)
         end)
         end)
         end)
         end)
         end)
         end)
         end);

      

    if (Cast(nilai_kecil as bigint)%10>0) then
         cek_koma = cek_koma ||
         (case when cast(nilai_kecil as bigint)%10 = 1 then 'SATU ' else
        (case when  cast(nilai_kecil as bigint)%10  = 2 then 'DUA ' else
        (case when  cast(nilai_kecil as bigint)%10  = 3 then 'TIGA ' else
        (case when  cast(nilai_kecil as bigint)%10  = 4 then 'EMPAT ' else
        (case when  cast(nilai_kecil as bigint)%10  = 5 then 'LIMA ' else
        (case when  cast(nilai_kecil as bigint)%10  = 6 then 'ENAM ' else
        (case when  cast(nilai_kecil as bigint)%10  = 7 then 'TUJUH ' else
        (case when  cast(nilai_kecil as bigint)%10  = 8 then 'DELAPAN ' else

                'SEMBILAN '
            ENd)
         end)
         end)
         end)
         end)
         end)
         end)
         end);
     end if;

     

 end if ;
kata = kata || cek_koma;

RETURN kata;

end;

\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
					 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221212_041940_migrate_mhg_gb_253_function_terbilang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221212_041940_migrate_mhg_gb_253_function_terbilang cannot be reverted.\n";

        return false;
    }
    */
}
