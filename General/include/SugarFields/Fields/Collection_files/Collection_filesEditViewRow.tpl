{*
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

*}
{* BizForce *}
<script type="text/javascript">
    var collection{$vardef.name} = (typeof collection{$vardef.name} == 'undefined') ? new Array() : collection{$vardef.name};
    collection{$vardef.name} = new SUGAR.collection('{$displayParams.formName}', '{$vardef.name}', '{$module}', '');
    collection{$vardef.name}.fields_count = '{$count}';
    collection{$vardef.name}.field_row_change('{$count}','open');
{* Files *}
    var collectionfiles{$vardef.name} = new SUGAR.collectionfiles('{$displayParams.formName}', '{$vardef.name}', '{$module}', '');
    document.getElementById('{$displayParams.formName}').enctype="multipart/form-data";
{* *}
</script>
<div id="{$displayParams.formName}_{$vardef.name}" name="{$displayParams.formName}_{$vardef.name}">
{* Files *}
        <div id="drop_zone{$vardef.name}" style="float: left; display: inline-block;min-height: 72px;width: 50%; border: solid 1px #999999;text-align: center;border-radius: 6px 0px 0px 6px;">
            <input type="file" id="files{$vardef.name}" name="files{$vardef.name}[]" multiple onchange="collectionfiles{$vardef.name}.add_new_files(this.id)" style="display: inline-block;line-height: 58px;width: 100%;text-align: center;"/>
        </div>
        <div id="list_files{$vardef.name}" name="list_files{$vardef.name}" style="padding: 5px;display: inline-block;width: 50%; min-height: 72px; border: solid 1px #999999;border-radius: 0px 6px 6px 0px;"></div>
    <span class="id-ff">
        <button class="button" type="button" name="remove_{$vardef.name}_collection_0" tabindex="{$tabindex}" class="utilsLink" onclick="collection{$vardef.name}.selected_remove();">
            <img id="removeButton_collection_0" name="removeButton_collection_0" src="{sugar_getimagepath file="id-ff-remove-nobg.png"}"/>
            <p value={$APP.LBL_DELETE_BUTTON}></p>
        </button>
    </span>
{* *}
<input hidden="hidden" id="collection_{$vardef.name}" name="collection_{$vardef.name}" value="{$vardef.name}">
<input hidden="hidden" id="collection_{$vardef.name}_remove" name="collection_{$vardef.name}_remove" value="">
<input hidden="hidden" id="collection_{$vardef.name}_change" name="collection_{$vardef.name}_change" value="">
<table id="table_collection_{$vardef.name}" style="display:block;border-collapse:separate;border:solid 1px #999999; margin-top: 5px !important;border-radius: 6px;">
    <tr id="lineLabel_{$displayParams.formName}_{$vardef.name}" name="lineLabel_{$displayParams.formName}_{$vardef.name}" style="font-weight: bold;">
        <td style="min-width: 30px; padding: 5px 5px 10px 5px !important; text-align: center;border-right: solid 1px #999999;">
            <span>{$APP.LBL_LINK_SELECT}</span>
        </td>
        {foreach item=extra_field from=$displayParams.collection_field_list key=key_extra}
            {if $extra_field.label != ''}
                <td style="padding: 5px 5px 10px 5px !important; text-align: center;{if $extra_field.displayParams.size != ''}width:{$extra_field.displayParams.size}{/if}">
                    {$extra_field.label}
                </td>
            {else}
                <td></td>
            {/if}
        {/foreach}
   </tr>
    {foreach item=extra_value from=$count_values key=key_extra_value}
        <tr id="lineFields_{$displayParams.formName}_{$vardef.name}_{$extra_value}">
            <td style="min-width: 30px; padding: 5px 5px 10px 5px !important; text-align: center;border-right: solid 1px #999999;">
                <input onclick="this.value=='0' ? this.value='1' : this.value='0';" type="checkbox" id="check_{$vardef.name}_collection_{$extra_value}" name="check_{$vardef.name}_collection_{$extra_value}" value='0'>
            </td>
            {foreach item=extra_field from=$displayParams.to_display.$extra_value key=key_extra}
                {if $extra_field.hidden != 'hidden'}
                    <td nowrap style="padding: 5px 5px 10px 5px !important; vertical-align: middle;">
                        {$extra_field.field}
                    </td>
                {else}
                    <td>{$extra_field.field}</td>
                {/if}
            {/foreach}
       </tr>
   {/foreach}
</table>
</div>
<script type="text/javascript">
    var tableelement = document.getElementById('table_collection_{$vardef.name}');
    collection{$vardef.name}.correctnewpage(tableelement);
    var checkid = document.getElementById('id_{$vardef.name}_collection_0').value;
    if (checkid === null || checkid ==='')
        document.getElementById('lineFields_{$displayParams.formName}_{$vardef.name}_0').remove();
</script>
{*  *}