<script type="text/javascript">
	req = {};
	// stores
	req.st_obra_preloading = false;
	req.st_obra = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'req/getObraList',
			params: {
				anio_eje: '2015',
			},
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function (sender, records, successful, eOpts) {
				if (successful && req.st_obra_preloading) {
					req.st_obra_preloading = false;
					var cb = Ext.getCmp('req_form_cb_obra');
					if (Ext.isDefined(cb)) {
						cb.bindStore(req.st_obra);
					}
				}
			}
		}
	});

	req.st_frente_preloading = false;
	req.st_frente = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'req/getFrenteList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function (sender, records, successful, eOpts) {
				if (successful && req.st_frente_preloading) {
					req.st_frente_preloading = false;
					var cb = Ext.getCmp('req_form_cb_frente');
					if (Ext.isDefined(cb)) {
						cb.bindStore(req.st_frente);
					}
				}
			}
		}
	});

	req.st_clapre_preloading = false;
	req.st_clapre = Ext.create("Ext.data.Store", {
		proxy: {
			type:'ajax',
			url:'req/getClapreList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function (sender, records, successful, eOpts) {
				if (successful && req.st_clapre_preloading) {
					req.st_clapre_preloading = false;
					var cb = Ext.getCmp('req_form_detail_cb_clapre');
					if (Ext.isDefined(cb)) {
						cb.bindStore(req.st_clapre);
					}
				}
			}
		}
	});
	
	req.st_estado_for_search = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'req/getEstadoForSearchList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	req.st_blank = Ext.create('Ext.data.Store', {data : []});

	req.st_grid_main = Ext.create("Ext.data.Store",{
		proxy: {
			type: 'ajax',
			url: 'req/getList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_req_list'); ?>
<?php echo $this->load->view('v_req_new'); ?>
<?php echo $this->load->view('v_req_edit'); ?>
<?php echo $this->load->view('v_req_detail_new'); ?>
<?php echo $this->load->view('v_req_detail_edit'); ?>
<?php echo $this->load->view('v_req_bs_search'); ?>

<script type="text/javascript">
	/*************************** main ************************/
	tab = Ext.getCmp('tab-appReq');
	tab.add(req.grid);
</script>