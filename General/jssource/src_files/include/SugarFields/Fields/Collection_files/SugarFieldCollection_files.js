
/* BizForce */
if(typeof(SUGAR.collectionfiles) == "undefined") {
    SUGAR.collectionfiles = function(form_name, field_name, module, totalfieldsin){
        this.form = form_name;
        this.field = field_name;
        this.module = module;
        this.filesid = new Array();
        this.totalfieldsin = totalfieldsin;
    };
    SUGAR.collectionfiles.prototype = {
        add_new_files: function (id) {
            document.getElementById('list_files'+this.field).innerHTML = '';
            var output = [];
            var list_files = document.getElementById(id).files;
            for (var i = 0, f; f = list_files[i]; i++) {
                output.push('<li><strong>', f.name, '</strong> (', f.type || '-', ')', '</li>');
            }
            document.getElementById('list_files'+this.field).innerHTML = '<ul style="list-style-type:disc;margin-left: 20px;">' + output.join('') + '</ul>';
        },
    };
}
/* */