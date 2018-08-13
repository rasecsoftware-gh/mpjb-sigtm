<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	tdr = {};
	tdr.title = 'Requisito por Tipo de Documento';
	// general stores
	tdr.tipo_doc_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'tdr/getTipoDocList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true,
		listeners: {
			load: function () {
			}
		}
	});

	tdr.tipo_permiso_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'tdr/getTipoPermisoList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true,
		listeners: {
			load: function () {
			}
		}
	});

	tdr.keyname_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'tdr/getKeynameList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true,
		listeners: {
			load: function () {
			}
		}
	});

	tdr.yes_no_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'S', desc: 'Si'},
			{id: 'N', desc: 'No'}
		]
	});

	tdr.index_store = Ext.create('Ext.data.Store', {
		data : [
			{id: '1', desc: '1'},
			{id: '2', desc: '2'},
			{id: '3', desc: '3'},
			{id: '4', desc: '4'},
			{id: '5', desc: '5'},
			{id: '6', desc: '6'},
			{id: '7', desc: '7'},
			{id: '8', desc: '8'},
			{id: '9', desc: '9'},
			{id: '10', desc: '10'},
			{id: '11', desc: '11'},
			{id: '12', desc: '12'},
			{id: '13', desc: '13'},
			{id: '14', desc: '14'},
			{id: '15', desc: '15'}
		]
	});

	tdr.estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'A', desc: 'Activo'},
			{id: 'I', desc: 'Inactivo'}
		]
	});

	tdr.form_editing = false;
	tdr.tipo_doc_requisito_id_selected = 0;
	
	tdr.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'tdr/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		pageSize: 50,
		listeners: {
			load: function () {
				if (tdr.tipo_doc_requisito_id_selected > 0) {
					Ext.getCmp('tdr_main_grid').getSelectionModel().select(
						tdr.main_store.getAt(
							tdr.main_store.find('tipo_doc_requisito_id', tdr.tipo_doc_requisito_id_selected)
						)
					);
				} else {
					Ext.getCmp('tdr_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_tdr_list'); ?>
<?php echo $this->load->view('v_tdr_new'); ?>
<?php echo $this->load->view('v_tdr_edit'); ?>
<?php echo $this->load->view('v_tdr_delete'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-tdr');
	tab.add(tdr.panel);
</script>