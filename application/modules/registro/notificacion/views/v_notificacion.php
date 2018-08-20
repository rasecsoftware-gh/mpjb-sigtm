<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	notificacion = {};
	notificacion.title = 'Notificacion';
	// general stores
	notificacion.contribuyente_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'notificacion/getContribuyenteList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
			}
		}
	});

	notificacion.yesno_store = Ext.create("Ext.data.Store", {
		data : [
			{id: 'SI', desc: 'Si'},
			{id: 'NO', desc: 'No'}
		]
	});
	
	notificacion.form_editing = false;
	notificacion.notificacion_id_selected = 0;
	
	notificacion.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'notificacion/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		pageSize: 100,
		listeners: {
			load: function () {
				if (notificacion.notificacion_id_selected > 0) {
					Ext.getCmp('notificacion_main_grid').getSelectionModel().select(
						notificacion.main_store.getAt(
							notificacion.main_store.find('notificacion_id', notificacion.notificacion_id_selected)
						)
					);
				} else {
					Ext.getCmp('notificacion_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_notificacion_list'); ?>
<?php echo $this->load->view('v_notificacion_new'); ?>
<?php echo $this->load->view('v_notificacion_edit'); ?>
<?php echo $this->load->view('v_notificacion_delete'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-notificacion');
	tab.add(notificacion.panel);
</script>