It’s upgrades for SuiteCRM 7.11.10. There are some changes. The most interesting are:
-	Repair “collection” field type;
-	Make “collection_file” field type;
-       Work dropdown menu or buttons in Edit and Detail form;
-	Show system message;
-	Change design for search form;
-	and others.

1. 
include/Dashlets/DashletGeneric.php
        It's to use "displayParams" in fields of Relate and Parent.
2. 
include/Dashlets/SubpanelQuickCreate.php
        It’s to use only button in form QuickCreate and ignore dropdown menu. 
3. 
include/MVC/Controller/action_file_map.php
        It’s to add new field type which is made in field type “collection”.
4. 
include/SugarFields/Fields/Parent/EditView.tpl
include/SugarFields/Fields/Relate/EditView.tpl
        a) It’s to add to form “metadata” to use in script.
        b) It’s to clean all fill in fields with “field_to_name_array”.
5.
include/SugarFields/Fields/File/EditView.tpl
        It's addition check before delete file.
6.
include/SugarFields/Fields/Collection/CollectionDetailViewRow.tpl
include/SugarFields/Fields/Collection/CollectionEditViewRow.tpl
include/SugarFields/Fields/Collection/DetailView.tpl
include/SugarFields/Fields/Collection/EditView.tpl
include/SugarFields/Fields/Collection/SugarFieldCollection.php
include/SugarFields/Fields/Collection/ViewSugarFieldCollection.php
jssource/src_files/include/SugarFields/Fields/Collection/SugarFieldCollection.js

It’s repaired field type. This field type was still in the previous versions, include SugarCRM ver.6…, but it was broken. This field type can be used to include in one object a lot of records from another object. It’s to use only standard algorithm which SuiteCRM has. 
Example to use:

Main module: “BFD_Incontracts” 
Parts of files of this module:

modules/BFD_Incontracts/vardefs.php
This module should have 2 special fields. One - it’s a type “link many-to-many” and another field type is “collection”. Note, field of type “collection” should name as the field type “link” with the prefix “collection_”. 

“…

        'bfd_c_budget_items' => array (
            'name' => 'bfd_c_budget_items',
            'type' => 'link',
            'relationship' => 'bfd_incontracts_bfd_c_budget_items_collection',
            'module'=>'BFD_C_budget_items',
            'bean_name'=>'BFD_C_budget_items',
            'source'=>'non-db',
            'vname'=>'LBL_BFD_C_BUDGET_ITEMS_COLLECTION',
        ),
        'collection_bfd_c_budget_items' => array (
            'name' => 'collection_bfd_c_budget_items',
            'type' => 'collection',
            'source'=>'non-db',
        ),
        
…”

modules/BFD_Incontracts/metadata/editviewdefs.php
and
modules/BFD_Incontracts/metadata/quickcreatedefs.php
and
modules/BFD_Incontracts/metadata/detailviewdefs.php
You can use to all standard parameters ('displayParams' and 'customCode') in files editviewdefs.php and quickcreatedefs.php for secondary module, which named “BFD_C_budget_items”. The fields in array 'collection_field_list' should be announced in the secondary module.

 “…
 
    'panels' => array (
        'lbl_panel_advanced1' => array (
               9 => array (
                    0 => array (
                        'name' => 'bfd_c_budget_items',
                        'type' => 'collection',
                        'displayParams' => array (
                            'collection_field_list' => array(
                                array(
                                    'name' => 'bfd_budget_items_name',
                                    'displayParams' => array (
                                        'size'=>'40%',
                                        'field_to_name_array'=> array(
                                            'name' => 'bfd_budget_items_name',
                                            'id' => 'bfd_budget_items_id',
                                            'responsibility_center' => 'responsibility_center',
                                            'employees_name' => 'employees_name',
                                        ),
                                    ),
                                ),
                                array(
                                    'name' => 'amount',
                                    'displayParams' => array (
                                        'size'=>'20%',
                                    ),
                                ),
                                array(
                                    'name' => 'responsibility_center',
                                    'displayParams' => array (
                                        'size'=>'20%',
                                    ),
                                ),
                                array(
                                    'name' => 'employees_name',
                                    'displayParams' => array (
                                        'size'=>'20%',
                                    ),
                                ),
                                array(
                                    'name' => 'number_full',
                                    'displayParams' => array (
                                        'hidden'=> true,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
        ),
    ),

…”

Secondary module: “BFD_C_budget_items” 
Parts of files of this module:
modules/BFD_C_budget_items/vardefs.php

“…

        'number_full' => array (
            'required' => false,
            'name' => 'number_full',
            'vname' => 'LBL_NUMBER_FULL',
            'type' => 'varchar',
            'massupdate' => 0,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => true,
            'reportable' => true,
            'unified_search' => true,
            'merge_filter' => 'disabled',
            'len' => '255',
            'size' => '20',
        ),
        'amount' =>array(
            'required' => false,
            'name' => 'amount',
            'vname' => 'LBL_AMOUNT',
            'type' => 'float',
            'len' => '15',
            'size' => '20',
            'precision' => '2',
            'massupdate' => 0,
            'default' => '',
            'no_default' => false,
            'unified_search' => false,
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'merge_filter' => 'disabled',
            ),
        'responsibility_center' => array (
            'required' => false,
            'name' => 'responsibility_center',
            'vname' => 'LBL_RESPONSIBILITY_CENTER',
            'type' => 'varchar',
            'massupdate' => 0,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => true,
            'reportable' => true,
            'unified_search' => true,
            'merge_filter' => 'disabled',
            'len' => '255',
            'size' => '20',
        ),
        'employees_name' => array (
            'required' => false,
            'name' => 'employees_name',
            'vname' => 'LBL_EMPLOYEES_NAME',
            'type' => 'varchar',
            'massupdate' => 0,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => true,
            'reportable' => true,
            'unified_search' => true,
            'merge_filter' => 'disabled',
            'len' => '255',
            'size' => '20',
        ),
        'bfd_incontracts_collection' => array (
            'name' => 'bfd_incontracts_collection',
            'type' => 'link',
            'relationship' => 'bfd_incontracts_bfd_c_budget_items_collection',
            'module'=> 'BFD_Incontracts',
            'bean_name'=>'BFD_Incontracts',
            'source'=>'non-db',
            'vname'=>'LBL_BFD_INCONTRACTS_COLLECTION',
        ),
        
…”

Look at “screen 1.png”


And don’t forget about file with link many-to-many.
custom/metadata/bfd_incontracts_bfd_c_budget_items_collectionMetaData.php

“…

    $dictionary["bfd_incontracts_bfd_c_budget_items_collection"] = array (
        'bfd_incontracts_bfd_c_budget_items_collection' => 'many-to-many',
        'relationships' => array (
            'bfd_incontracts_bfd_c_budget_items_collection' => array (
                'rhs_module' => 'BFD_C_budget_items',
                'rhs_table' => 'bfd_c_budget_items',
                'lhs_key' => 'id',
                'lhs_module' => 'BFD_Incontracts',
                'lhs_table' => 'bfd_incontracts',
                'rhs_key' => 'id',
                'relationship_type' => 'many-to-many',
                'join_table' => 'bfd_incontracts_bfd_c_budget_items_collection',
                'join_key_lhs' => 'bfd_incontracts_bfd_c_budget_items_collection_ida',
                'join_key_rhs' => 'bfd_incontracts_bfd_c_budget_items_collection_idb',
            ),
        ),
       'table' => 'bfd_incontracts_bfd_c_budget_items_collection',
        'fields' => array (
            0 => array (
                'name' => 'id',
                'type' => 'varchar',
                'len' => 36,
            ),
            1 => array (
                'name' => 'date_modified',
                'type' => 'datetime',
           ),
            2 => array (
                'name' => 'deleted',
                'type' => 'bool',
                'len' => '1',
                'default' => '0',
                'required' => true,
            ),
            3 => array (
                'name' => 'bfd_incontracts_bfd_c_budget_items_collection_ida',
                'type' => 'varchar',
                'len' => 36,
            ),
            4 => array (
                'name' => 'bfd_incontracts_bfd_c_budget_items_collection_idb',
                'type' => 'varchar',
                'len' => 36,
            ),
        ),
        'indices' => array (
            0 => array (
                'name' => 'bfd_incontracts_bfd_c_budget_items_collectionspk',
                'type' => 'primary',
                'fields' => array (
                    0 => 'id',
                ),
            ),
            1 => array (
                'name' => 'bfd_incontracts_bfd_c_budget_items_collection_alt',
                'type' => 'alternate_key',
                'fields' => array (
                    0 => 'bfd_incontracts_bfd_c_budget_items_collection_ida',
                    1 => 'bfd_incontracts_bfd_c_budget_items_collection_idb',
                ),
            ),
        ),
    );

…”


7.
include/SugarFields/Fields/Collection_files/Collection_files DetailViewRow.tpl
include/SugarFields/Fields/Collection_files/Collection_files EditViewRow.tpl
include/SugarFields/Fields/Collection_files/DetailView.tpl
include/SugarFields/Fields/Collection_files/EditView.tpl
include/SugarFields/Fields/Collection_files/SugarFieldCollection_files.php
include/SugarFields/Fields/Collection_files/ViewSugarFieldCollection_files.php
include/SugarFields/Fields/Collection_files/view.sugarfieldcollection_files.php
jssource/src_files/include/SugarFields/Fields/Collection_files/SugarFieldCollection_files.js


It’s addition field type for multi load files. It works as a field type collection, but with one difference in a secondary object. You should make 3 required fields in addition to link. Note, there is a new field type – 'link_file' (see information below).
modules/<secondary_object>/vardefs.php

“…

        'filename' => array (
            'required' => false,
            'name' => 'filename',
            'vname' => 'LBL_FILENAME',
            'type' => 'varchar',
            'massupdate' => 0,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => true,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'len' => '255',
            'size' => '20',
        ),
        'mime_type' => array (
            'required' => false,
            'name' => 'mime_type',
            'vname' => 'LBL_MIME_TYPE',
            'type' => 'varchar',
            'massupdate' => 0,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => true,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'len' => '255',
            'size' => '20',
        ),
        'link_file' => array (
            'name' => 'link_file',
            'vname' => 'LBL_LINK_FILE',
            'type' => 'Link_file',
            'source'=>'non-db',
        ),
        
…”

Look at “screen 2.png”


8.
include/SugarFields/Fields/Link_file/DetailView.tpl
include/SugarFields/Fields/Link_file/EditView.tpl
include/SugarFields/Fields/Link_file/linkfiledownload.php

This is special field type to use with type “Collection_files”.

9.
include/utils/activity_utils.php
If you want to make custom module like the “Meeting” or “Call” modules.

10.
include/export_utils.php
It adds fields names of module to header of export form. 

11.
jssource/src_files/include/javascript/sugar_3.js
It adds special message about required field if you use tabs.

12.
jssource/src_files/include/javascript/alerts.js
It doesn’t check alert before you login.

13.
jssource/src_files/include/javascript/yui3/build
All files of this directory make correct use of Russian language, because SuiteCRM use format: “ru_RU” 

14.
jssource/JSGroupings.php
It adds field type “Collection_files” to grouping javascript files.

15.
modules/Calendar/fullcalendar/lang-all.js
It adds support different languages which description includes in this file.

16.
modules/Calls/CallFormBase.php
modules/Meetings/MeetingFormBase.php
It fixes mistake of person choice (Contact, User, Lead). If you added person who had been deleted before, then you didn’t find him.

17.
modules/FP_Event_Locations/vardefs.php
It fixes mistake with parameters 'optimistic_locking' and 'unified_search'. They were included into array 'relationships'.

18.
modules/Import/views/view.confirm.php
It fixes mistake of different languages use.

19.
modules/ModuleBuilder/parsers/relationships/ActivitiesRelationship.php
It fixes mistake of use of “Activities” in customer modules.

20.
modules/SugarFeed/SugarFeed.php
It fixes mistake of date in Feed List.

21.
modules/Users/views/view.detail.php
modules/Users/UserViewHelper.php
modules/Users/tpls/EditViewFooter.tpl
modules/Users/tpls/DetailView.tpl
It fixes mistake of 'Decimal Symbol' and '1000s separator'. Now this module uses system parameters, but should use personal parameters.

22.
themes/SuiteP/include/DetailView
themes/SuiteP/include/EditView
themes/SuiteP/js/style.js
themes/SuiteP/css/suitep-base/tabs.scss
It makes correct display of buttons and dropdown menu. 

23.
themes/SuiteP/css
There are some changes in forms and fields design.
Look at “screen 3.png”


You can install this upgrade to use “Admin->Upgrade Wizard”. I recommend to make: 
“Admin->Repair->Rebuild JS Compressed Files” , “Admin->Repair->Rebuild JS Grouping Files” , “Admin->Repair->Rebuild Minified JS Files” after the upgrade.

If you want to use this change in another version (from SugarCRM 6.x.x CE) you can make it manually. You should find comment in every file (start comment – ‘/* BizForce */’, end comment ‘/* */). Files themes/SuiteP/include/EditView/EditView.tpl and themes/SuiteP/include/DetailView/DetailView.tpl don’t have all comments because there are a lot of changes.

Petr Konetskiy
p.konetskiy@bizforce.ru
