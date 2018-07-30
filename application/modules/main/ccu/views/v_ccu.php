<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	ccu = {};
	// general stores
	ccu.permiso_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ccu/getPermisoList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: false,
		pageSize: 50,
		listeners: {
			load: function () {
			}
		}
	});

	ccu.usuario_permiso_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ccu/getUsuarioPermisoList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
			}
		}
	});

	ccu.usuario_estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'A', desc: 'ACTIVO'},
			{id: 'I', desc: 'INACTIVO'}
		]
	});

	ccu.yesno_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'S', desc: 'Si'},
			{id: 'N', desc: 'No'}
		]
	});

	ccu.usuario_id_selected = 0;
	
	ccu.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ccu/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: false,
		pageSize: 100,
		listeners: {
			load: function () {
				if (ccu.usuario_id_selected > 0) {
					Ext.getCmp('ccu_main_grid').getSelectionModel().select(
						ccu.main_store.getAt(
							ccu.main_store.find('ccu_id', ccu.usuario_id_selected)
						)
					);
				} else {
					Ext.getCmp('ccu_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_ccu_list'); ?>
<?php echo $this->load->view('v_ccu_usuario_new'); ?>
<?php echo $this->load->view('v_ccu_usuario_delete'); ?>
<?php echo $this->load->view('v_ccu_usuario_permiso_add'); ?>
<?php echo $this->load->view('v_ccu_usuario_permiso_delete'); ?>
<?php echo $this->load->view('v_ccu_permiso_list'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-ccu');
	tab.add(ccu.panel);
</script>