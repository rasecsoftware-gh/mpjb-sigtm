<script>
	lc.lc_update_window = function() {
		var operation = Ext.getCmp('lc_form_operation_field').getValue();
		var frm = Ext.getCmp('lc_form');
		frm.mask('guardando');
		frm.submit({
			success: function(form, action) {
				frm.unmask();
				if (action.result.success) {
					lc.form_editing = false;
					if ( operation == 'new' ) {
						lc.reload_list(action.result.rowid);
					} else {
						lc.reload_list(frm.getRecord().get('lc_id'));
					}
				} else {
					Ext.Msg.alert('Error', action.result.msg);
				}
			},
			failure: function(form, action) {
				frm.unmask();
				Ext.Msg.alert('Guardar', action.result.msg, function () {
					sys_focus(action.result.target_id);
				});
			}
		});
	};
</script>