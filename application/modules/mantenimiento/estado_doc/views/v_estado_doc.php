<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	estado_doc = {};
	estado_doc.title = 'Estado por Tipo de Documento';
	// general stores
	estado_doc.tipo_doc_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'estado_doc/getTipoDocList',
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

	estado_doc.color_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'estado_doc/getColorList',
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

	estado_doc.yes_no_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'S', desc: 'Si'},
			{id: 'N', desc: 'No'}
		]
	});

	estado_doc.index_store = Ext.create('Ext.data.Store', {
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

	estado_doc.estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'A', desc: 'Activo'},
			{id: 'I', desc: 'Inactivo'}
		]
	});

	estado_doc.form_editing = false;
	estado_doc.estado_doc_id_selected = 0;
	
	estado_doc.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'estado_doc/getList',
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
				if (estado_doc.estado_doc_id_selected > 0) {
					Ext.getCmp('estado_doc_main_grid').getSelectionModel().select(
						estado_doc.main_store.getAt(
							estado_doc.main_store.find('estado_doc_id', estado_doc.estado_doc_id_selected)
						)
					);
				} else {
					Ext.getCmp('estado_doc_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_estado_doc_list'); ?>
<?php echo $this->load->view('v_estado_doc_new'); ?>
<?php echo $this->load->view('v_estado_doc_edit'); ?>
<?php echo $this->load->view('v_estado_doc_delete'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-estado_doc');
	tab.add(estado_doc.panel);
</script>