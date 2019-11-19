<?php
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.

 * SuiteCRM is an extension to SugarCRM Community Edition developed by Salesagility Ltd.
 * Copyright (C) 2011 - 2014 Salesagility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for  technical reasons, the Appropriate Legal Notices must
 * display the words  "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 ********************************************************************************/

require_once('include/SugarFields/Fields/Base/SugarFieldBase.php');
class SugarFieldCollection_files extends SugarFieldBase {
	var $tpl_path;
	
	function getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
		$nolink = array('Users');
		if(in_array($vardef['module'], $nolink)){
			$displayParams['nolink']=true;
		}else{
			$displayParams['nolink']=false;
		}
		$json = getJSONobj();
        $displayParamsJSON = $json->encode($displayParams);
        $vardefJSON = $json->encode($vardef);
        $this->ss->assign('displayParamsJSON', '{literal}'.$displayParamsJSON.'{/literal}');
        $this->ss->assign('vardefJSON', '{literal}'.$vardefJSON.'{/literal}');
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        if(empty($this->tpl_path)){
        	$this->tpl_path = $this->findTemplate('DetailView');
        }
        return $this->fetch($this->tpl_path);
    }

    function getEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex, $searchView = false) {
        if($searchView){
        	$form_name = 'search_form';
        }else{
    		$form_name = 'EditView';
        }
        $json = getJSONobj();
/* BizForce */
        $displayParamsJSON = json_encode($displayParams, JSON_HEX_APOS);
/*        $displayParamsJSON = $json->encode($displayParams); */
/* */
        $vardefJSON = $json->encode($vardef);
        $this->ss->assign('required', !empty($vardef['required']));
        $this->ss->assign('displayParamsJSON', '{literal}'.$displayParamsJSON.'{/literal}');
        $this->ss->assign('vardefJSON', '{literal}'.$vardefJSON.'{/literal}');

        $keys = $this->getAccessKey($vardef,'COLLECTION',$vardef['module']);
        $displayParams['accessKeySelect'] = $keys['accessKeySelect'];
        $displayParams['accessKeySelectLabel'] = $keys['accessKeySelectLabel'];
        $displayParams['accessKeySelectTitle'] = $keys['accessKeySelectTitle'];
        $displayParams['accessKeyClear'] = $keys['accessKeyClear'];
        $displayParams['accessKeyClearLabel'] = $keys['accessKeyClearLabel'];
        $displayParams['accessKeyClearTitle'] = $keys['accessKeyClearTitle'];

        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
	    if(!$searchView) {
	    	if(empty($this->tpl_path)){
        		$this->tpl_path = $this->findTemplate('EditView');
        	}
	    	return $this->fetch($this->tpl_path);
	    }
    }

	function getSearchViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
		$this->getEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex, true);
    }
     /**
     * This should be called when the bean is saved. The bean itself will be passed by reference
     * @param SugarBean bean - the bean performing the save
     * @param array params - an array of paramester relevant to the save, most likely will be $_REQUEST
     */
	public function save(&$bean, $params, $field, $properties, $prefix = ''){
//$GLOBALS['log']->fatal("SugarFieldCollection_files save _FILES:\n ".print_r($_FILES,true));
//$GLOBALS['log']->fatal("SugarFieldCollection_files save params:\n ".print_r($params,true));
/* BizForce */
        $link_field = $params[$field];
        if(!empty($link_field) && ($bean->field_defs[$link_field]['type'] == 'link')){
            require_once('data/Link2.php');
            $bean->load_relationship($link_field);
            $actual_field_list = Array();
            foreach($params as $name=>$value){
                $explode_string = '_'.$link_field.'_collection_';
                $new_array = explode($explode_string, $name);
                if (count($new_array) == 2)
                    $actual_field_list[$new_array[1]][$new_array[0]] = $value;
            }
            $bean_name = $bean->field_defs[$link_field]['bean_name'];
            $filefilds = $_FILES['files'.$link_field];
//    $GLOBALS['log']->fatal("SugarFieldCollection_files save filefilds:\n ".print_r($filefilds,true));
            $countfiles = count($filefilds['error']);
            include_once 'include/upload_file.php';
            $destination = "upload://";
            if(UploadStream::writable()) {
                for ($i = 0; $i < $countfiles; $i++){
                    if ($filefilds['error'][$i] == 0 && $filefilds['size'][$i] != 0)
                        if(is_uploaded_file($filefilds['tmp_name'][$i])) {
                            $bean_collection = new $bean_name();
                            $bean_collection->filename = $filefilds['name'][$i];
                            $bean_collection->mime_type = $filefilds['type'][$i];
                            $bean_collection->assigned_user_id = $bean->assigned_user_id;
                            $bean_collection->save();
                            if (empty($bean->id))
                                $bean->save();
                            $bean->{$link_field}->add($bean_collection->id);
                            if(!UploadStream::move_uploaded_file($filefilds['tmp_name'][$i], $destination.$bean_collection->id."_filename")) {
                                $GLOBALS['log']->fatal("ERROR: SugarFieldCollection_files can't move_uploaded_file to $destination. You should try making the directory writable by the webserver");
                            }
//                            $GLOBALS['log']->fatal("SugarFieldCollection_files save filefilds[error][$i]:\n ".print_r($filefilds['name'][$i],true));   
                        }
                }
            }

            $change_list = Array();
            $change_list = explode(';', $params['collection_'.$link_field.'_change']);
            foreach($actual_field_list as $key => $value_list){
                if(in_array($key, $change_list)) {
                    $bean_collection = new $bean_name();
                    if (!empty($value_list['id'])) {
                        $bean_collection->retrieve($value_list['id']);
                    }
                    foreach($value_list as $name=>$value){
                        if ($name != 'id' && $name != 'filename') {
                            $bean_collection->$name = $value;
                        } else {
                            if ($name == 'filename' && !empty($value)) {
                                $bean_collection->$name = $value;
                            }
                        }
                    }
                    $bean_collection->assigned_user_id = $bean->assigned_user_id;
                    $bean_collection->save();
                    if (empty($bean->id))
                        $bean->save();
                    $bean->{$link_field}->add($bean_collection->id);
                }
            }
            
            $delete_id_list = Array();
            $delete_id_list = explode(';', $params['collection_'.$link_field.'_remove']);
            if (!empty($delete_id_list))
                foreach ($delete_id_list as $delete_id) {
                    $bean_collection = new $bean_name();
                    $bean_collection->retrieve($delete_id);
                    $bean_collection->mark_deleted($delete_id);
                    if (is_file($destination.$delete_id.'_filename'))
                        unlink($destination.$delete_id.'_filename');
//                    $bean->$link_field->delete($delete_id);
                }
        }
/* */
    }

}
?>