<script type="text/javascript">
	nea = {};
	nea.anio = '<?php echo $this->model->get_anio();?>';
	// stores
	nea.tipo_nea_store = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'nea/getTipoNeaList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	nea.procedencia_store = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'nea/getProcedenciaList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: false
	});
	
	nea.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'nea/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		pageSize: 30
	});

	
</script>

<?php echo $this->load->view('v_nea_list'); ?>
<?php echo $this->load->view('v_nea_new'); ?>
<?php echo $this->load->view('v_nea_edit'); ?>
<?php echo $this->load->view('v_nea_print'); ?>
<?php echo $this->load->view('v_nea_change_year'); ?>
<?php echo $this->load->view('v_nea_nemo_search'); ?>
<?php echo $this->load->view('v_nea_det_import_from_oc'); ?>
<?php echo $this->load->view('v_nea_det_import_from_cb'); ?>
<?php //echo $this->load->view('v_nea_det_edit'); ?>
<?php echo $this->load->view('v_nea_det_delete'); ?>
	
<script>
	/*************************** main ************************/
	tab = Ext.getCmp('tab-nea');
	tab.add(nea.grid);
</script>