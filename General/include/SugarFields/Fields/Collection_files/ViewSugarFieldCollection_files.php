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

require_once('include/SugarFields/Fields/Collection_files/SugarFieldCollection_files.php');


class ViewSugarFieldCollection_files{
    var $ss; // Sugar Smarty Object
    var $bean;
    var $bean_id;
    var $name;
    var $value_name;
    var $displayParams; // DisplayParams for the collection field (defined in the metadata)
    var $vardef; // vardef of the collection field.
    var $related_module; // module name of the related module
    var $module_dir; // name of the module where the collection field is.
    var $numFields;
    var $json;
    var $tpl_path;
    var $extra_var;
    var $skipModuleQuickSearch = false;
    var $field_to_name_array; //mapping of fields for the return of the select popup
    var $showSelectButton = true;
    var $hideShowHideButton = false;
    var $action_type;
    var $form_name;

    function __construct($fill_data = true){
    	$this->json = getJSONobj();
    	if($fill_data){
/* BizForce */
                $this->displayParams = json_decode(html_entity_decode($_REQUEST['displayParams']), JSON_HEX_APOS);
/*                $this->displayParams = $this->json->decode(html_entity_decode($_REQUEST['displayParams'])); */
/*  */
	        $this->vardef = $this->json->decode(html_entity_decode($_REQUEST['vardef']));
	        $this->module_dir = $_REQUEST['module_dir'];
	        $this->action_type = $_REQUEST['action_type'];
	        $this->name = $this->vardef['name'];
/* BizForce */
	        $this->value_name = array();
/*	        $this->value_name = $this->name . '_values'; */
/* */
	        $this->numFields = 1;
	        $this->ss = new Sugar_Smarty();
/* BizForce */
/*	        $this->edit_tpl_path = $this->findTemplate('Collection_filesEditView');
	        $this->detail_tpl_path = $this->findTemplate('Collection_filesDetailView'); */
/* */
	        $this->extra_var = array();
	        $this->field_to_name_array = array();
    	}
    }
    /**
     * @deprecated deprecated since version 7.6, PHP4 Style Constructors are deprecated and will be remove in 7.8, please update your code, use __construct instead
     */
    function ViewSugarFieldCollection_files($fill_data = true){
        $deprecatedMessage = 'PHP4 Style Constructors are deprecated and will be remove in 7.8, please update your code';
        if(isset($GLOBALS['log'])) {
            $GLOBALS['log']->deprecated($deprecatedMessage);
        }
        else {
            trigger_error($deprecatedMessage, E_USER_DEPRECATED);
        }
        self::__construct($fill_data);
    }
    /*
     * Retrieve the related module and load the bean and the relationship
     * call retrieve values()
     */
    function setup(){
        if(!class_exists('Relationship')){

        }

        $rel = new Relationship();
        if(!empty($this->vardef['relationship'])){
        	$rel->retrieve_by_name($this->vardef['relationship']);
        }
        if($rel->relationship_type == 'many-to-many'){
            if($rel->lhs_module == $this->module_dir){
                $this->related_module = $rel->rhs_module;
                $module_dir = $rel->lhs_module;
            }else {
                if($rel->rhs_module == $this->module_dir){
                    $this->related_module = $rel->lhs_module;
                    $module_dir = $rel->rhs_module;
                }else{
                    die("this field has no relationships mapped with this module");
                }
            }
            if($module_dir != $this->module_dir){
                die('These modules do not match : '. $this->module_dir . ' and ' . $module_dir);
            }
            if(isset($GLOBALS['beanList'][$this->module_dir])){
                $class = $GLOBALS['beanList'][$this->module_dir];
                if(file_exists($GLOBALS['beanFiles'][$class])){
                    $this->bean = loadBean($this->module_dir);
                    $this->bean->retrieve($_REQUEST['bean_id']);
                    if($this->bean->load_relationship($this->vardef['name'])){
/* BizForce */
                        $this->check_id();
/* */
                        $this->retrieve_values();
                    }else{
                        die('failed to load the relationship');
                    }
                }else{
                    die('class file do not exist');
                }
            }else{
                die($this->module_dir . ' is not in the beanList.');
            }
        }
        else{
            die("the relationship is not a many-to-many");
        }
    }
/* BizForce */
    function check_id(){
        $check_id = false;
        foreach ($this->displayParams['collection_field_list'] as $key_field=>$value_field) {
            if ($this->displayParams['collection_field_list'][$key_field]['name'] == 'id')
                $check_id = true;
        }
        if (!$check_id) {
            $this->displayParams['collection_field_list'][$key_field+1]['name'] = 'id';
            $this->displayParams['collection_field_list'][$key_field+1]['displayParams']['hidden'] = true;
        }
    }
/* */
    /*
     * Retrieve the values from the DB using the get method of the link class
     * Organize and save the value into the bean
     */
    function retrieve_values(){
/* BizForce */
/* BizForce */
        if(!empty($this->vardef) && isset($this->bean->{$this->name})){
/*        if(empty($this->bean->{$this->value_name}) && isset($this->bean->{$this->name})){ */
/* */
            $values = array();
            $values = $this->bean->{$this->name}->get(true);
/* BizForce */
/*            $role_field = $this->bean->{$this->name}->_get_link_table_role_field($this->bean->{$this->name}->_relationship_name); */
            $new_bean = new $this->vardef['bean_name']();
            $fields_values = Array();
            $k = 0;
            if (!empty($values)) {
                foreach ($values as $key=>$value) {
                    $new_bean->retrieve($value);
                    foreach ($this->displayParams['collection_field_list'] as $key_field=>$value_field) {
                         $this->value_name[$k][$value_field['name']] = $new_bean->{$value_field['name']};
                         if($field_defs[$value_field['name']]['type'] == 'relate' && !empty($field_defs[$value_field['name']]['id_name'])){
                             $id_name = $field_defs[$value_field['name']]['id_name'];
                             $this->displayParams['collection_field_list'][$key_field]['displayParams']['id'] = $new_bean->$id_name;
                         }
                    }
                    if (!isset($this->value_name[$k]['id'])){
                        $this->value_name[$k]['id'] = $value;
                    }
                    $this->value_name[$k]['link_file'] = $this->value_name[$k]['id'].'_filename';
                    $k++;
                }
            } else {
                foreach ($this->displayParams['collection_field_list'] as $key_field=>$value_field) {
                     $this->value_name[0][$value_field['name']] = '';
                }
                if (!isset($this->value_name[0]['id'])){
                    $this->value_name[0]['id'] = '';
                }
            }
/* */
/* Sugar metod */
/* BizForce */
/*            foreach($values as $v){
                $role = '';
                foreach($v as $kk=>$vv){
                    if($kk == $role_field){
                        $role=$vv;
                    }
                }
                if($role == 'primary'){
                    $primary_id = $v['id'];
                }else{
                    $secondary_ids[] = array('id'=>$v['id'], 'role'=>$role);
                }
            }
            $this->bean->{$this->value_name} = array('role_field'=>$role_field);
            if(isset($primary_id) || isset($secondary_ids)){
                if(!isset($primary_id)){
                    $primary_id = $secondary_ids[0]['id'];
                    unset($secondary_ids[0]);
                }
                if(isset($GLOBALS['beanList'][ $this->related_module])){
                    $class = $GLOBALS['beanList'][$this->related_module];
                    if(file_exists($GLOBALS['beanFiles'][$class])){
                        $mod = loadBean($this->module_dir);
                        $mod->relDepth = $this->bean->relDepth + 1;
                        $mod->retrieve($primary_id);
                        if (isset($mod->name)) {
                            $this->bean->{$this->value_name}=array_merge($this->bean->{$this->value_name}, array('primary'=>array('id'=>$primary_id, 'name'=>$mod->name)));
                        }
                        $secondaries = array();
                        if(isset($secondary_ids)){
                            foreach($secondary_ids as $v){
                                if($mod->retrieve($v['id'])){
                                    if (isset($mod->name)){
                                        $secondaries['secondaries'][]=array('id'=>$v['id'], 'name'=>$mod->name);
                                    }
                                }
                            }
                        }
                        $this->bean->{$this->value_name}=array_merge($this->bean->{$this->value_name}, $secondaries);
                        if(isset($field['additionalFields'])){
                            foreach($field['additionalFields'] as $field=>$to){
                                if(isset($mod->$field)){
                                    $this->bean->$to = $mod->$field;
                                }
                            }
                        }
                    }
                }
            } */
/* */
        }
    }
    /*
     * redirect to the good process method.
     */
    function process(){
        if($this->action_type == 'editview'){
/* BizForce */
            $this->viewtype = 'EditView';
            $this->process_form();
/*            $this->process_editview(); */
/* */
        }else {
            if($this->action_type == 'detailview'){
/* BizForce */
                $this->viewtype = 'DetailView';
/* */
                $this->process_detailview();
            }
        }
    }
    function process_detailview(){
/* BizForce */
            $this->process_form();
/* */
    }
/* BizForce */
    function process_form(){
        if(isset($this->displayParams['collection_field_list'])){
            $relatedObject = BeanFactory::getObjectName($this->related_module);
            vardefmanager::loadVardef($this->related_module, $relatedObject);
            if (class_exists('BFL_HookCodes') && $this->viewtype == 'EditView') {
                if (isset($this->bean->bfl_readonly_fields_list) && !empty($this->bean->bfl_readonly_fields_list)){
                    $readonly_fields_list = explode(";", $this->bean->bfl_readonly_fields_list);
                    if (in_array($this->name, $readonly_fields_list)){
                        $this->action_type = 'detailview';
                        $this->viewtype = 'DetailView';
                    }
                }
            }
//$GLOBALS['log']->fatal("ViewSugarFieldCollection process_form this:\n ".print_r($this,true));
//$GLOBALS['log']->fatal("ViewSugarFieldCollection retrieve_values this->displayParams->collection_field_list:\n ".print_r($this->displayParams['collection_field_list'],true));
            foreach($this->value_name as $key_value=>$field_value){
                $this->count_values[$key_value] = $key_value;
                foreach($this->displayParams['collection_field_list'] as $k=>$v){
                    $collection_field_vardef = $GLOBALS['dictionary'][$relatedObject]['fields'][$v['name']];
                    foreach($v as $k_override=>$v_override){
                        if($k_override != 'displayParams'){
                            $collection_field_vardef[$k_override] = $v_override;
                        }
                    }
                    $realy_field_name = $collection_field_vardef['name'];
                    $collection_field_vardef['value'] = $field_value[$collection_field_vardef['name']];
                    $collection_field_vardef['name'] .= "_" . $this->vardef['name'] . "_collection_" . $key_value;
                    if(isset($collection_field_vardef['id_name'])){
                        $collection_field_vardef['id_name'] .= "_" . $this->vardef['name'] . "_collection_" . $key_value;
                    }
                    $name = $collection_field_vardef['name'];
                    $this->displayParams['to_display'][$key_value][$name]['vardefName'] = $this->displayParams['collection_field_list'][$k]['name'];
                    $this->displayParams['to_display'][$key_value][$name]['name'] = $name;
                    if($collection_field_vardef['type'] == 'relate'){
                        $this->displayParams['to_display'][$key_value][$name]['id_name'] = $collection_field_vardef['id_name'];
                        $this->displayParams['to_display'][$key_value][$name]['module'] = $collection_field_vardef['module'];
                    }
                    require_once('include/SugarFields/SugarFieldHandler.php');
                    if(!isset($sfh)) $sfh = new SugarFieldHandler();
                    if ($this->displayParams['collection_field_list'][$k]['displayParams']['hidden']) {
                        $this->displayParams['to_display'][$key_value][$name]['hidden'] = 'hidden';
                        $this->displayParams['to_display'][$key_value][$name]['field'] = '<input type="text" hidden="hidden" name="'.$collection_field_vardef['name'].'" id="'.$collection_field_vardef['name'].'" value="'.$collection_field_vardef['value'].'">';
                    } else {
                        if (isset($this->displayParams['collection_field_list'][$k]['customCode']) && !empty($this->displayParams['collection_field_list'][$k]['customCode']) && $this->viewtype != 'DetailView' && $collection_field_vardef['type'] != 'Link_file') {
                            $customCode = str_replace('value=""', 'value="'.$collection_field_vardef['value'].'"', $this->displayParams['collection_field_list'][$k]['customCode']);
                            $customCode = str_replace($realy_field_name, $realy_field_name.'_'.$this->name.'_collection_'.$key_value.'', $customCode);
                            $this->displayParams['to_display'][$key_value][$name]['field'] = $customCode;
                        } else {
                            if (isset($collection_field_vardef['options']) && !empty($collection_field_vardef['options'])) {
                                $this->displayParams['to_display'][$key_value][$name]['options'] = $GLOBALS['app_list_strings'][$collection_field_vardef['options']];
                            }
                            if ($collection_field_vardef['type'] == 'relate') {
                                if ($this->displayParams['formName'] != 'EditView'){
                                    $v['displayParams']['formName'] = $this->displayParams['formName'];
                                    $this->displayParams['to_display'][$key_value][$this->displayParams['to_display'][$key_value][$name]['id_name']]['name'] = $this->displayParams['to_display'][$key_value][$name]['id_name'];
                                    $this->displayParams['to_display'][$key_value][$this->displayParams['to_display'][$key_value][$name]['id_name']]['hidden'] = 'hidden';
                                    $this->displayParams['to_display'][$key_value][$this->displayParams['to_display'][$key_value][$name]['id_name']]['value'] = $this->displayParams['collection_field_list'][$k]['displayParams']['id'];
                                }
                                if (!empty ($v['displayParams']['field_to_name_array'])) {
                                    foreach ($v['displayParams']['field_to_name_array'] as $key_field_to_name_array => $value_field_to_name_array) {
                                        $v['displayParams']['field_to_name_array'][$key_field_to_name_array] = $value_field_to_name_array.'_'.$this->name.'_collection_'.$key_value;
                                    }
                                }
                            }
                            $this->displayParams['to_display'][$key_value][$name]['value'] = $collection_field_vardef['value'];
                            if ($collection_field_vardef['type'] == 'Link_file') {
                                $key_file_name = str_replace($this->displayParams['to_display'][$key_value][$name]['vardefName'].'_', '', $name);
                                $this->displayParams['to_display'][$key_value][$name]['realyname'] = $this->displayParams['to_display'][$key_value]['filename_'.$key_file_name]['value'];
                            }
                            $this->displayParams['to_display'][$key_value][$name]['field'] = $sfh->displaySmarty('displayParams.to_display.'.$key_value, $collection_field_vardef, $this->viewtype, $v['displayParams'], 1);
                        }
                    }
                if ($this->viewtype == 'EditView')
                    $this->displayParams['to_display'][$key_value][$name]['field'] .= 
                        '{literal}
                            <script language="javascript">'."collection" . $this->vardef['name'] . ".add_change_control('{$collection_field_vardef['name']}');
                                if(document.getElementById('{$collection_field_vardef['name']}').getAttribute('readonly') == 'readonly'){
                                    document.getElementById('{$collection_field_vardef['name']}').setAttribute('style', 'background: none;');}".'</script>
                        {/literal}';                
                }
            }
            foreach($this->displayParams['collection_field_list'] as $k=>$v){
                if (!isset($this->displayParams['collection_field_list'][$k]['displayParams']['hidden'])) {
                    if (isset($this->displayParams['collection_field_list'][$k]['label']) && !empty($this->displayParams['collection_field_list'][$k]['label'])) {
                        $this->displayParams['collection_field_list'][$k]['label'] = "{sugar_translate label='{$this->displayParams['collection_field_list'][$k]['label']}' module='{$this->related_module}'}";
                    } else {
                        $this->displayParams['collection_field_list'][$k]['label'] = "{sugar_translate label='{$GLOBALS['dictionary'][$relatedObject]['fields'][$v['name']]['vname']}' module='{$this->related_module}'}";
                        }
                }
            }
// $GLOBALS['log']->fatal("ViewSugarFieldCollection retrieve_values this->displayParams:\n ".print_r($this->displayParams,true));
        }
    }
/*  */
    /*
     * Build the DisplayParams array
     */
    function process_editview(){
        if(isset($this->bean->{$this->value_name}['secondaries'])){
            $this->numFields=count($this->bean->{$this->value_name}['secondaries'])+1;
        }
        if(!isset($this->displayParams['readOnly'])) {
           $this->displayParams['readOnly'] = '';
        } else {
           $this->displayParams['readOnly'] = $this->displayParams['readOnly'] == false ? '' : 'READONLY';
        }
        // If there is extra field to show.
        if(isset($this->displayParams['collection_field_list'])){
            $relatedObject = BeanFactory::getObjectName($this->related_module);
            vardefmanager::loadVardef($this->related_module, $relatedObject);
            foreach($this->displayParams['collection_field_list'] as $k=>$v){
                $javascript='';
                $collection_field_vardef = $GLOBALS['dictionary'][$relatedObject]['fields'][$v['name']];

                // For each extra field the params which are not displayParams will be consider as params to override the vardefs values.
                foreach($v as $k_override=>$v_override){
                    if($k_override != 'displayParams'){
                        $collection_field_vardef[$k_override] = $v_override;
                    }
                }

                // If relate field : enable quick search by creating the sqs_object array.
                if($collection_field_vardef['type'] == 'relate'){
                    require_once('include/TemplateHandler/TemplateHandler.php');
                    $tph = new TemplateHandler();
                    $javascript = $tph->createQuickSearchCode(array($collection_field_vardef['name']=>$collection_field_vardef), array($v), $this->form_name);
                    $javascript = str_replace('<script language="javascript">'."if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}sqs_objects['{$collection_field_vardef['name']}']=","",$javascript);
                    $javascript = substr($javascript, 0, -10);//remove ";</script>"
                    $javascriptPHP = $this->json->decode($javascript);
                    foreach($javascriptPHP['populate_list'] as $kk=>$vv){
/* BizForce */                        
                        $javascriptPHP['populate_list'][$kk] .= "_" . $this->vardef['name'] . "_collection_0";
/*                        $javascriptPHP['populate_list'][$kk] .= "_" . $this->vardef['name'] . "_collection_extra_0"; */
/*  */
                    }
                    foreach($javascriptPHP['required_list'] as $kk=>$vv){
/* BizForce */
                        $javascriptPHP['required_list'][$kk] .= "_" . $this->vardef['name'] . "_collection_0";
/*                        $javascriptPHP['required_list'][$kk] .= "_" . $this->vardef['name'] . "_collection_extra_0"; */
/*  */
                    }
                    foreach($javascriptPHP['field_list'] as $kk=>$vv){
                        if($vv == 'id'){
                            $javascriptPHP['populate_list'][$kk];
                        }
                    }
                    $javascript = $this->json->encode($javascriptPHP);
/* BizForce */
                    $javascript = "<script language='javascript'>if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}sqs_objects['{$collection_field_vardef['name']}_" . $this->vardef['name'] . "_collection_0']=".$javascript.';</script>';
/*                    $javascript = "<script language='javascript'>if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}sqs_objects['{$collection_field_vardef['name']}_" . $this->vardef['name'] . "_collection_extra_0']=".$javascript.';</script>'; */
/*  */
                }

/* BizForce */
                $collection_field_vardef['name'] .= "_" . $this->vardef['name'] . "_collection_0";
/*                $collection_field_vardef['name'] .= "_" . $this->vardef['name'] . "_collection_extra_0"; */
/*  */
                if(isset($collection_field_vardef['id_name'])){
/* BizForce */
                    $collection_field_vardef['id_name'] .= "_" . $this->vardef['name'] . "_collection_0";
/*                    $collection_field_vardef['id_name'] .= "_" . $this->vardef['name'] . "_collection_extra_0"; */
/*  */
                }
                if(isset($this->displayParams['allow_update']) && ($this->displayParams['allow_update'] === false || $this->displayParams['allow_update'] === 'false')){
                    $this->displayParams['allow_update']='false';
                    $v['displayParams']['field']['disabled']='';
                }else{
                    $this->displayParams['allow_update']='true';
                    if(!isset($v['displayParams'])){
                        $v['displayParams']=array();
                    }
                }
                    $viewtype='EditView';
                $name = $collection_field_vardef['name'];
                // Rearranging the array with name as key instaead of number. This is required for displaySmarty() to assign the good variable.
                $this->displayParams['collection_field_list'][$name]['vardefName'] = $this->displayParams['collection_field_list'][$k]['name'];
                $this->displayParams['collection_field_list'][$name]['name'] = $name;
                if($collection_field_vardef['type'] == 'relate'){
                    $this->displayParams['collection_field_list'][$name]['id_name'] = $collection_field_vardef['id_name'];
                    $this->displayParams['collection_field_list'][$name]['module'] = $collection_field_vardef['module'];
                }
/* BizForce */ if (isset($this->displayParams['collection_field_list'][$k]['label']) && !empty($this->displayParams['collection_field_list'][$k]['label'])) {
                    $this->displayParams['collection_field_list'][$name]['label'] = "{sugar_translate label='{$this->displayParams['collection_field_list'][$k]['label']}' module='{$this->related_module}'}";
                } else {
/* */
                $this->displayParams['collection_field_list'][$name]['label'] = "{sugar_translate label='{$collection_field_vardef['vname']}' module='{$this->related_module}'}";//translate($collection_field_vardef['vname'], $this->related_module);
/* BizForce */
                }
                require_once('include/SugarFields/SugarFieldHandler.php');
                if(!isset($sfh)) $sfh = new SugarFieldHandler();
                if ($this->displayParams['collection_field_list'][$k]['displayParams']['hidden']) {
                    $this->displayParams['collection_field_list'][$name]['hidden'] = 'hidden';
                    $this->displayParams['collection_field_list'][$name]['field'] = '<input type="text" hidden="hidden" name="'.$this->displayParams['collection_field_list'][$name]['name'].'" id="'.$this->displayParams['collection_field_list'][$name]['name'].'" value="">';
                } else {
                    if (isset($this->displayParams['collection_field_list'][$k]['customCode']) && !empty($this->displayParams['collection_field_list'][$k]['customCode'])) {
                        $this->displayParams['collection_field_list'][$name]['field'] = $this->displayParams['collection_field_list'][$k]['customCode'];
                    } else {
                        if (isset($collection_field_vardef['options']) && !empty($collection_field_vardef['options'])) {
                            $this->displayParams['collection_field_list'][$name]['options'] = $GLOBALS['app_list_strings'][$collection_field_vardef['options']];
                        }
/* */
                        $this->displayParams['collection_field_list'][$name]['field'] = $sfh->displaySmarty('displayParams.collection_field_list', $collection_field_vardef, $viewtype, $v['displayParams'], 1);
/* BizForce */
                        $this->displayParams['collection_field_list'][$name]['field'] .= '{literal}'.$javascript;
                    // Handle update_field array ONCHANGE
                        $this->displayParams['collection_field_list'][$name]['field'] .= <<<FRA
                        <script language='javascript'>
                            var oldonchange = '';
                            if(typeof(document.getElementById('{$collection_field_vardef['name']}').attributes.onchange) != 'undefined')
                            {
                                oldonchange=document.getElementById('{$collection_field_vardef['name']}').attributes.onchange.value;
                            }
FRA;
                        $this->displayParams['collection_field_list'][$name]['field'] .= "eval(\"document.getElementById('{$collection_field_vardef['name']}').onchange = function onchange(event){collection['{$this->vardef['name']}'].update_fields.{$collection_field_vardef['name']}=true;";
                        if($collection_field_vardef['type'] == 'relate'){
                            // If relate add the ID field to the array
                            $this->displayParams['collection_field_list'][$name]['field'] .= "collection['{$this->vardef['name']}'].update_fields.{$collection_field_vardef['id_name']}=true;";
                        }
                        $this->displayParams['collection_field_list'][$name]['field'] .= "document.getElementById('update_fields_{$this->vardef['name']}_collection').value = YAHOO.lang.JSON.stringify(collection['{$this->vardef['name']}'].update_fields);\" + oldonchange + \"};\");</script>{/literal}";

                    }
                }
/*  */
                //we need to get rid of the old value;
                unset($this->displayParams['collection_field_list'][$k]);
            }
        }
        if(!isset($this->displayParams['class'])) $this->displayParams['class']='';
        if(isset($this->displayParams['allow_new']) && ($this->displayParams['allow_new'] === false || $this->displayParams['allow_new'] === 'false')){
            $this->displayParams['allow_new']='false';
            $this->displayParams['class']=str_replace('sqsNoAutofill','',$this->displayParams['class']);
        }else{
            $this->displayParams['allow_new']='true';
            $this->displayParams['class'].=' sqsNoAutofill';
        }
        if(isset($this->displayParams['new_on_update']) && ($this->displayParams['new_on_update'] !== false || $this->displayParams['new_on_update'] !== 'false' || $this->displayParams['new_on_update'] !== 'FALSE' || $this->displayParams['new_on_update'] !== '0')){
            $this->displayParams['new_on_update']='true';
        }else{
            $this->displayParams['new_on_update']='false';
        }
    }

    /*
     * Init the template with the variables
     */
    function init_tpl(){
        foreach($this->extra_var as $k=>$v){
            $this->ss->assign($k,$v);
        }
        if($this->action_type == 'editview'){
/* BizForce */
//            $this->ss->assign('quickSearchCode',$this->createQuickSearchCode());
//            $this->createPopupCode();// this code populate $this->displayParams with popupdata.
/* */
            $this->tpl_path = $this->edit_tpl_path;
        }else {
            if($this->action_type == 'detailview'){
                $this->tpl_path = $this->detail_tpl_path;
            }
        }
/* BizForce */
        $this->ss->assign('count_values',$this->count_values);
        $this->ss->assign('count',count($this->count_values)-1);
/* */
        $this->ss->assign('displayParams',$this->displayParams);
        $this->ss->assign('vardef',$this->vardef);
        $this->ss->assign('module',$this->related_module);
        $this->ss->assign('values',$this->bean->{$this->value_name});
        $this->ss->assign('showSelectButton',$this->showSelectButton);
        $this->ss->assign('hideShowHideButton',$this->hideShowHideButton);
        $this->ss->assign('APP',$GLOBALS['app_strings']);
    }
    /*
     * Display the collection field after retrieving the cached row.
     */
    function display(){
        $cacheRowFile = sugar_cached('modules/') . $this->module_dir .  '/collections_files/'.$this->viewtype. $this->name .'_'. count($this->count_values) .  '.tpl';
        if(!$this->checkTemplate($cacheRowFile)){
            $dir = dirname($cacheRowFile);
            if(!file_exists($dir)) {

               mkdir_recursive($dir, null, true);
            }
            $cacheRow = $this->ss->fetch($this->findTemplate('Collection_files'.$this->viewtype.'Row'));

            file_put_contents($cacheRowFile, $cacheRow);
        }
        $this->ss->assign('cacheRowFile', $cacheRowFile);   
/* BizForce */
/* $GLOBALS['log']->fatal("ViewSugarFieldCollection display fetch cacheRowFile:\n ".print_r($this->ss->fetch($cacheRowFile),true)); */
        return $this->ss->fetch($cacheRowFile);
/*        return $this->ss->fetch($this->tpl_path); */
/* */
    }
    /*
     * Check if the template is cached
     * return a bool
     */
    function checkTemplate($cacheRowFile){
        if(inDeveloperMode() || !empty($_SESSION['developerMode'])){
            return false;
        }
        return file_exists($cacheRowFile);
    }


    /*
     * Create the quickSearch code for the collection field.
     * return the javascript code which define sqs_objects.
     */
    function createQuickSearchCode($returnAsJavascript = true){
        $sqs_objects = array();
        require_once('include/QuickSearchDefaults.php');
        $qsd = QuickSearchDefaults::getQuickSearchDefaults();
        $qsd->setFormName($this->form_name);
        for($i=0; $i<$this->numFields; $i++){
            $name1 = "{$this->form_name}_{$this->name}_collection_{$i}";
            if(!$this->skipModuleQuickSearch && preg_match('/(Campaigns|Teams|Users|Accounts)/si', $this->related_module, $matches)) {
                if($matches[0] == 'Users'){
                    $sqs_objects[$name1] = $qsd->getQSUser();
                } else {
                    if($matches[0] == 'Campaigns') {
                        $sqs_objects[$name1] = $qsd->getQSCampaigns();
                    } else {
                        if($matches[0] == 'Users'){
                            $sqs_objects[$name1] = $qsd->getQSUser();
                        } else {
                            if($matches[0] == 'Accounts') {
                                $nameKey = "{$this->name}_collection_{$i}";
                                $idKey = "id_{$this->name}_collection_{$i}";

                             //There are billingKey, shippingKey and additionalFields entries you can define in editviewdefs.php
                             //entry to allow quick search to autocomplete fields with a suffix value of the
                             //billing/shippingKey value (i.e. 'billingKey' => 'primary' in Contacts will populate
                             //primary_XXX fields with the Account's billing address values).
                             //addtionalFields are key/value pair of fields to fill from Accounts(key) to Contacts(value)
                                $billingKey = isset($this->displayParams['billingKey']) ? $this->displayParams['billingKey'] : null;
                                $shippingKey = isset($this->displayParams['shippingKey']) ? $this->displayParams['shippingKey'] : null;
                                $additionalFields = isset($this->displayParams['additionalFields']) ? $this->displayParams['additionalFields'] : null;
                                $sqs_objects[$name1] = $qsd->getQSAccount($nameKey, $idKey, $billingKey, $shippingKey, $additionalFields);
                            } else {
                                if($matches[0] == 'Contacts'){
                                    $sqs_objects[$name1] = $qsd->getQSContact($name1, "id_".$name1);
                                }
                            }
                        }
                    }
                }


                $temp_array = array('field_list'=>array(),'populate_list'=>array());
                foreach($sqs_objects[$name1]['field_list'] as $k=>$v){
                    if(!in_array($v, array('name','id'))){
                        $sqs_objects[$name1]['primary_field_list'][]=$v;
                        $sqs_objects[$name1]['primary_populate_list'][]=$sqs_objects[$name1]['populate_list'][$k];
                    }else{
                        $temp_array['field_list'][]=$v;
                        $temp_array['populate_list'][]=$sqs_objects[$name1]['populate_list'][$k];
                    }
                }
                $sqs_objects[$name1]['field_list'] = $temp_array['field_list'];
                $sqs_objects[$name1]['populate_list'] = $temp_array['populate_list'];
                if(isset($this->displayParams['collection_field_list'])){
                    foreach($this->displayParams['collection_field_list'] as $v){
/* BizForce */
                        $sqs_objects[$name1]['populate_list'][]=  $v['vardefName']."_".$this->name."_collection_".$i;
/*                        $sqs_objects[$name1]['populate_list'][]=  $v['vardefName']."_".$this->name."_collection_extra_".$i; */
/*  */
                        $sqs_objects[$name1]['field_list'][] = $v['vardefName'];
                    }
                }
            }else {
                $sqs_objects[$name1] = $qsd->getQSParent($this->related_module);
                $sqs_objects[$name1]['populate_list'] = array("{$this->vardef['name']}_collection_{$i}", "id_{$this->vardef['name']}_collection_{$i}");
                $sqs_objects[$name1]['field_list'] = array('name', 'id');
                if(isset($this->displayParams['collection_field_list'])){
                    foreach($this->displayParams['collection_field_list'] as $v){
/* BizForce */
                        $sqs_objects[$name1]['populate_list'][] = $v['vardefName']."_".$this->name."_collection_".$i;
/*                        $sqs_objects[$name1]['populate_list'][] = $v['vardefName']."_".$this->name."_collection_extra_".$i; */
/*  */
                        $sqs_objects[$name1]['field_list'][] = $v['vardefName'];
                    }
                }
                if(isset($this->displayParams['field_to_name_array'])){
                    foreach($this->displayParams['field_to_name_array'] as $k=>$v){
                        /*
                         * "primary_populate_list" and "primary_field_list" are used when the field is selected as a primary.
                         * At this time the JS function changePrimary() will copy "primary_populate_list" and "primary_field_list"
                         * into "populate_list" and "field_list" and remove the values from all the others which are secondaries.
                         * "primary_populate_list" and "primary_field_list" contain the fields which has to be populated outside of
                         * the collection field. For example the "Address Information" are populated with the "billing address" of the
                         * selected account in a contact editview.
                         */
                        $sqs_objects[$name1]['primary_populate_list'][] = $v;
                        $sqs_objects[$name1]['primary_field_list'][] = $k;
                    }
                }else {
                    if(isset($field['field_list']) && isset($field['populate_list'])){
                        $sqs_objects[$name1]['primary_populate_list'] = array_merge($sqs_objects[$name1]['populate_list'], $field['field_list']);
                        $sqs_objects[$name1]['primary_field_list'] = array_merge($sqs_objects[$name1]['field_list'], $field['populate_list']);
                    }else{
                        $sqs_objects[$name1]['primary_populate_list'] = array();
                        $sqs_objects[$name1]['primary_field_list'] = array();
                    }
                }
            }
        }

        $id = "{$this->form_name}_{$this->name}_collection_0";

        if(!empty($sqs_objects) && count($sqs_objects) > 0) {
            foreach($sqs_objects[$id]['field_list'] as $k=>$v){
                $this->field_to_name_array[$v] = $sqs_objects[$id]['populate_list'][$k];
            }
            if($returnAsJavascript){
	            $quicksearch_js = '<script language="javascript">';
	            $quicksearch_js.= "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}";

	            foreach($sqs_objects as $sqsfield=>$sqsfieldArray){
	               $quicksearch_js .= "sqs_objects['$sqsfield']={$this->json->encode($sqsfieldArray)};";
	            }

	            return $quicksearch_js .= '</script>';
            }else{
            	return $sqs_objects;
            }
       }
       return '';
    }
    /*
     * Always call createQuickSearchCode() before createPopupCode() to define field_to_name_array
     */
    function createPopupCode(){
        // TODO the 'select' button is not fully working. We should use the sqs_objects in open_popup instead of the parameter.
        if(isset($this->field_to_name_array) && !empty($this->field_to_name_array)){
            $call_back_function = 'set_return';

            if(isset($this->displayParams['formName'])) {
                $form = $this->displayParams['formName'];
            } else {
                if($this->action_type == 'editview'){
                    $form = 'EditView';
                } else {
                    if($this->action_type == 'quickcreate'){
                        $form = "QuickCreate_{$this->module_dir}";
                    }
                }
            }

            if(isset($this->displayParams['call_back_function'])) {
                $call_back_function = $this->displayParams['call_back_function'];
            }

            $popup_request_data= array(
                'call_back_function' => $call_back_function,
                'form_name' => $form,
                'field_to_name_array' => $this->field_to_name_array,
            );

            //Make sure to replace {{ and }} with spacing in between because Smarty template parsing will treat {{ or }} specially
            $this->displayParams['popupData'] = '{literal}'. str_replace(array('{{', '}}'), array('{ {', '} }'), $this->json->encode($popup_request_data)) . '{/literal}';
        }
    }



    function findTemplate($view){
        static $tplCache = array();

        if ( isset($tplCache[$this->type][$view]) ) {
            return $tplCache[$this->type][$view];
        }

        $lastClass = get_class($this);
        $classList = array($this->type,str_replace('ViewSugarField','',$lastClass));
        while ( $lastClass = get_parent_class($lastClass) ) {
            $classList[] = str_replace('ViewSugarField','',$lastClass);
        }

        $tplName = '';
        foreach ( $classList as $className ) {
            global $current_language;
            if(isset($current_language)) {
                $tplName = 'include/SugarFields/Fields/'. $className .'/'. $current_language . '.' . $view .'.tpl';
                if ( file_exists('custom/'.$tplName) ) {
                    $tplName = 'custom/'.$tplName;
                    break;
                }
                if ( file_exists($tplName) ) {
                    break;
                }
            }
            $tplName = 'include/SugarFields/Fields/'. $className .'/'. $view .'.tpl';
            if ( file_exists('custom/'.$tplName) ) {
                $tplName = 'custom/'.$tplName;
                break;
            }
            if ( file_exists($tplName) ) {
                break;
            }
        }

        $tplCache[$this->type][$view] = $tplName;

        return $tplName;
    }
}
