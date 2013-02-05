<?php
if(count($_POST) > 0) {

     $postData =$_POST;
     $i = 0;
    foreach ($postData as $key => $value) {

       if(substr_compare($key, "field", 0, 5)==0){
           $chunkarray[] = $i;
           //$i=0;
       }
       $i++;
    }
    for($i=0;$i<count($chunkarray);$i++) {
        if($chunkarray[$i+1] != '')
        $finalarray[] = array_slice($postData, $chunkarray[$i], $chunkarray[$i+1]-$chunkarray[$i]);
    }
    $finalarray[] = array_slice($postData,end($chunkarray),count($postData));
    $selfieldArray[]     = array('field_name' => 'id','field_type' => 'string','is_required' => 'TRUE','default_sortable' => 'FALSE','is_facet' => 'FALSE','display_as_range' => 'FALSE','parent' => '','default_search' => 'FALSE','is_multivalued' => 'TRUE','delimiter' => '','display_name' => 'id','is_highlighted' => 'FALSE',"is_unique"=>"TRUE","moreLikeThis"=>"FALSE",'facet_limit' => '','range_count' => '','is_indexed' => 'TRUE','delimiter_suffix' => '','displayable_column'=>'TRUE','is_stop_words'=>'FALSE','file_stopword'=>'','store'=>'TRUE','disp_as_title_suggestion'=>'TRUE','disp_as_url_suggestion'=>'TRUE');

    foreach($finalarray as $fields) {
        $facetflag          = 'FALSE';
        $sortableflag       = 'FALSE';
        $rangeflag          = 'FALSE';
        $multiselectflag    = 'FALSE';
        $defaultsearchflag  = 'FALSE';
        $facetLimit         = '';
        $rangeLimit         = '';
        foreach($fields as $key=>$value) {
            if(substr_compare($key, "field", 0, 5)==0){
               $typeName = explode("##",$value);
               $type = $typeName[0];
               $name = $typeName[1];
            }else if(substr_compare($key, "facet", 0, 5)==0){
                $facetflag = 'TRUE';
            }else if(substr_compare($key, "range", 0, 5)==0){
                $rangeflag = 'TRUE';
            }else if(substr_compare($key, "sortable", 0, 8)==0){
                $sortableflag = 'TRUE';
            }else if(substr_compare($key, "multiselect", 0, 11)==0){
                $multiselectflag = 'TRUE';
            }else if(substr_compare($key, "defaultsearch", 0, 13)==0){
                $defaultsearchflag = 'TRUE';
            }
            if($facetflag == 'TRUE'){
                $facetLimit = 5;
            } if($facetflag == 'TRUE'){
                $rangeLimit = 5;
            }

        }
$selfieldArray[] = array('field_name' => $name,'field_type' => $type,'default_sortable' => $sortableflag,'is_facet' => $facetflag,'display_as_range' => $rangeflag,'default_search' => $defaultsearchflag ,'display_name' => $name,'is_multivalued' => $multiselectflag,'facet_limit' => $facetLimit,'range_count' => $rangeLimit,'parent' => '','is_required' => 'FALSE','delimiter' => '','is_highlighted' => 'FALSE',"is_unique"=>"FALSE" ,"moreLikeThis"=>"FALSE",'is_indexed' => 'TRUE','file_stopword'=>'','is_stop_words'=>'FALSE','store'=>'TRUE','disp_as_title_suggestion'=>'TRUE','disp_as_url_suggestion'=>'TRUE','delimiter_suffix' => '','displayable_column'=>'TRUE');


    }

$tmp_fields         = json_encode($selfieldArray);
//$tmp_fields         = json_decode_nice($tmp_fields);
echo $tmp_fields;

die;
    /*$arrFields = array('field_name' => 'id','field_type' => 'string','is_required' => 'TRUE','default_sortable' => 'FALSE','is_facet' => 'FALSE','display_as_range' => 'FALSE','parent' => '','default_search' => 'FALSE','is_multivalued' => 'TRUE','delimiter' => '','display_name' => 'id','is_highlighted' => 'FALSE',"is_unique"=>"TRUE","moreLikeThis"=>"FALSE",'facet_limit' => '','range_count' => '','is_indexed' => 'TRUE','delimiter_suffix' => '','displayable_column'=>'TRUE','is_stop_words'=>'FALSE','file_stopword'=>'','store'=>'TRUE','disp_as_title_suggestion'=>'TRUE','disp_as_url_suggestion'=>'TRUE');
    //foreach($finalarrayas);
    $selfieldArray = array('field_name' => 'id','field_type' => 'string','default_sortable' => 'FALSE','is_facet' => 'FALSE','display_as_range' => 'FALSE','default_search' => 'FALSE','display_name' => 'id','is_multivalued' => 'TRUE','facet_limit' => '','range_count' => '');

    $defaultfieldArray = array('parent' => '','is_required' => 'FALSE','delimiter' => '','is_highlighted' => 'FALSE',"is_unique"=>"FALSE" ,"moreLikeThis"=>"FALSE",'is_indexed' => 'TRUE','file_stopword'=>'','is_stop_words'=>'FALSE','store'=>'TRUE','disp_as_title_suggestion'=>'TRUE','disp_as_url_suggestion'=>'TRUE','delimiter_suffix' => '','displayable_column'=>'TRUE');*/
}else {
require_once 'app/Mage.php';
Mage::app('default');

$proxy = new SoapClient('http://test.magento.com/api/soap/?wsdl');

$sessionId = $proxy->login('vishal', '123456789');




//$set = current($attributeSets);

/******************************************* Fetch all the product attribute related to any attribute set ******************************/

   function fetchProductAttributesTypes() {
       global $sessionId,$proxy;
       $attributeSets = $proxy->call($sessionId, 'product_attribute_set.list');
        $tmpAttributeCode =  array();
        $finalAttribute=array();
        for($i=0;$i<count($attributeSets);$i++){
            $attributes = $proxy->call($sessionId, 'product_attribute.list', $attributeSets[$i]['set_id']);

            foreach($attributes as $value){
                if(!in_array($value['code'],$tmpAttributeCode)){
                    $tmpAttributeCode[] = $value['code'];
                    if($value['type'] != '')
                    $finalAttribute[] = array('code'=>$value['code'],'type'=>$value['type']);
                }
            }
        }
        return $finalAttribute;
    }
    //print_r($finalAttribute);
    //$finalAttribute contains all the attribute and it types
/***************************************************************       END        ********************************************************/


Function fetchProductData(){
    global $sessionId,$proxy;
    $productList = $proxy->call($sessionId, 'catalog_product.list');
    $model = Mage::getModel('catalog/product');
    $fieldArray = Array ( 'id'=> 'id','name' =>'name','description' => 'description', 'short_description' => 'short_description', 'sku' => 'sku' , 'price' => 'price', 'cost' => 'cost' , 'meta_title' => 'meta_title');
    $fp = fopen('test.csv', "w");
    $blankLine[] = 'important';
    fputcsv($fp,$blankLine);
    ksort($fieldArray);
    fputcsv($fp,$fieldArray);
$i=0;
    foreach($productList as $v){
        $_product = $model->load($v['product_id']);
        //$attributes = $_product->getAttributes();
        $class_methods = get_class_methods($_product);
         $p_Data['id'] = ++$i;
        foreach($_product->getData() as $field => $data){
            if(in_array($field,$fieldArray)) {
                $p_Data[$field] = $data;
            }

        }
        //print_r($p_Data);die;
        ksort($p_Data);
     fputcsv($fp,$p_Data);
    }

}
if($_GET['fetchdata'] == 1) {
    $productdata = fetchProductData();
}else{
    $productAttribute = fetchProductAttributesTypes();
}

          //echo count($productAttribute);
 //$productAttribute = fetchProductData();
 if (count($productAttribute) >0){

 ?>
 <html>
    <head>
    <script>
    function fieldSelect(i , id ) {
        if(document.getElementById('field'+i).checked == false && document.getElementById(id).checked == true){
            document.getElementById('field'+i).checked = true;
        }
    }

    function checkAll(i){
        //alert(document.getElementById('field'+i).checked)
        if(document.getElementById('field'+i).checked == false){
            if(document.getElementById('range'+i).checked == true || document.getElementById('sortable'+i).checked == true || document.getElementById('facet'+i).checked == true ||document.getElementById('multiselect'+i).checked == true || document.getElementById('defaultsearch'+i).checked == true ){
                document.getElementById('field'+i).checked = true;
            }
        }
    }
    </script>

    </head>

    <body>
    <h2> Select the Attribute from list to used in Search</h2>
    <form method="post" action="">
        <table border ="1">
        <th> </th>
        <th>Name</th>
        <th>Is muliselect</th>
        <th>Is facet</th>
        <th>Is sortable</th>
        <th>Display as Range</th>
        <th>Include in default Search</th>
        <?php
        $i =0;

        foreach($productAttribute as  $k => $v) {
        ?>
        <tr>

            <td><input type="checkbox" id= "field<?php echo $i;?>" name="field<?php echo $i;?>" onclick="checkAll(<?php echo $i;?>)" value="<?php echo $v['type']."##".$v['code'];?>"></td>
            <td><?php echo  $v['code'];?></td>
            <?php if($v['type'] == 'textarea') {?>
            <td><input type="checkbox" id= "multiselect<?php echo $i;?>" name="multiselect<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" value="<?php echo $v['type']."##".$v['code'];?>"></td>
            <td><input type="checkbox" id= "facet<?php echo $i;?>" name="facet<?php echo $i;?>"  DISABLED value="1"></td>
            <td><input type="checkbox" id= "sortable<?php echo $i;?>" name="sortable<?php echo $i;?>" DISABLED value="1"></td>
            <td><input type="checkbox" id= "range<?php echo $i;?>" name="range<?php echo $i;?>" DISABLED value="1"></td>
            <td><input type="checkbox" id= "defaultsearch<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="defaultsearch<?php echo $i;?>"  value="1"></td>
            <?php } ?>
            <?php if($v['type'] == 'text') {?>
            <td><input type="checkbox" id= "multiselect<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="multiselect<?php echo $i;?>" value="1"></td>
            <td><input type="checkbox" id= "facet<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="facet<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "sortable<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="sortable<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "range<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="range<?php echo $i;?>" DISABLED value="1"></td>
            <td><input type="checkbox" id= "defaultsearch<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="defaultsearch<?php echo $i;?>"  value="1"></td>
            <?php } ?>
            <?php if($v['type'] == 'price') {?>
            <td><input type="checkbox" id= "multiselect<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="multiselect<?php echo $i;?>" value="1"></td>
            <td><input type="checkbox" id= "facet<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="facet<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "sortable<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="sortable<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "range<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="range<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "defaultsearch<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="defaultsearch<?php echo $i;?>"  value="1"></td>
            <?php } ?>
            <?php if($v['type'] == 'select') {?>
            <td><input type="checkbox" id= "multiselect<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)"  name="multiselect<?php echo $i;?>" value="1"></td>
            <td><input type="checkbox" id= "facet<?php echo $i;?>" DISABLED name="facet<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "sortable<?php echo $i;?>" DISABLED name="sortable<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "range<?php echo $i;?>" DISABLED name="range<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "defaultsearch<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)"  name="defaultsearch<?php echo $i;?>"  value="1"></td>
            <?php } ?>
            <?php if($v['type'] == 'date') {?>
            <td><input type="checkbox" id= "multiselect<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="multiselect<?php echo $i;?>" value="1"></td>
            <td><input type="checkbox" id= "facet<?php echo $i;?>"  onclick="fieldSelect(<?php echo $i?>, this.id)" name="facet<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "sortable<?php echo $i;?>" DISABLED name="sortable<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "range<?php echo $i;?>"  onclick="fieldSelect(<?php echo $i?>, this.id)" name="range<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "defaultsearch<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="defaultsearch<?php echo $i;?>"  value="1"></td>
            <?php } ?>
            <?php if($v['type'] == 'boolean') {?>
            <td><input type="checkbox" id= "multiselect<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="multiselect<?php echo $i;?>" value="1"></td>
            <td><input type="checkbox" id= "facet<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)"  name="facet<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "sortable<?php echo $i;?>" DISABLED name="sortable<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "range<?php echo $i;?>"  onclick="fieldSelect(<?php echo $i?>, this.id)" name="range<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "defaultsearch<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="defaultsearch<?php echo $i;?>"  value="1"></td>
            <?php } ?>
            <?php if($v['type'] == 'multiselect') {?>
            <td><input type="checkbox" id= "multiselect<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="multiselect<?php echo $i;?>" value="1"></td>
            <td><input type="checkbox" id= "facet<?php echo $i;?>"  DISABLED name="facet<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "sortable<?php echo $i;?>" DISABLED name="sortable<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "range<?php echo $i;?>" DISABLED name="range<?php echo $i;?>"  value="1"></td>
            <td><input type="checkbox" id= "defaultsearch<?php echo $i;?>" onclick="fieldSelect(<?php echo $i?>, this.id)" name="defaultsearch<?php echo $i;?>"  value="1"></td>
            <?php } ?>
        </tr>
        <?php

        $i++;
        }
        ?>
        </table>
        <input type ="submit" value="submit">
        </form>
    </body>
    <?php
}
}?>
