<style type="text/css">
    .tbl-bordered {
        border:1px;
    }
    .tbl-bordered th {
        border: 0px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: px solid black;
        padding: 5px; 
    }
    .tbl-bordered tr#colored {
        background-color: #fdfd96;
    }
</style>
<?php $frontend = Yii::$app->params['frontend']; //dummy buat test local ?>
<table class="tbl-bordered">
   <tbody>
      
         <?php  if($data){ for($i=0; $i<count($data[0]); $i++){ 
          if ($i % 2 == 0){ echo '<tr>'; }    
         ?>
            <td cellpadding="4" style="border:0px;">  
             <img src="<?=$frontend?><?=$data[0][$i]?>" style="width:350px;"> 
             </td>
         <?php } } ?> 
      </tr> 
   </tbody>
</table>
 

 