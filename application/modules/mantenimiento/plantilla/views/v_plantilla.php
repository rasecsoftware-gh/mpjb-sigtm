<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	plantilla = {};
	plantilla.title = 'Plantilla';
	// general stores
	plantilla.tipo_doc_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'plantilla/getTipoDocList',
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

	plantilla.yes_no_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'S', desc: 'Si'},
			{id: 'N', desc: 'No'}
		]
	});

	plantilla.index_store = Ext.create('Ext.data.Store', {
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

	plantilla.estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'A', desc: 'Activo'},
			{id: 'I', desc: 'Inactivo'}
		]
	});

	plantilla.form_editing = false;
	plantilla.plantilla_id_selected = 0;
	
	plantilla.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'plantilla/getList',
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
				if (plantilla.plantilla_id_selected > 0) {
					Ext.getCmp('plantilla_main_grid').getSelectionModel().select(
						plantilla.main_store.getAt(
							plantilla.main_store.find('plantilla_id', plantilla.plantilla_id_selected)
						)
					);
				} else {
					Ext.getCmp('plantilla_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_plantilla_list'); ?>
<?php echo $this->load->view('v_plantilla_new'); ?>
<?php echo $this->load->view('v_plantilla_edit'); ?>
<?php echo $this->load->view('v_plantilla_delete'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-plantilla');
	tab.add(plantilla.panel);
</script>