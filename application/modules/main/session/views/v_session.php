<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="UTF-8">
	<title>Sistema de Informacion y Gestion de Transporte Municipal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="shortcut icon" href="sigtm.ico"/>
	<link rel="stylesheet" type="text/css" href="../ext-6/build/classic/theme-neptune/resources/theme-neptune-all.css">
	<script type="text/javascript" src="../ext-6/build/ext-all.js"></script>
	<script type="text/javascript" src="../ext-6/build/classic/theme-neptune/theme-neptune.js"></script>
	<!--<script type="text/javascript" src="<?php echo base_url('resource/jquery-2.1.4.js')?>"></script>-->
	<script type ="text/javascript">
		Ext.application({
			name:'Guia de Entrega de Bienes',
			appFolder: 'app',
    		launch: function() {
        		Ext.create({
            		xtype: 'window',
            		autoShow: true,
            		closable: false,
            		bodyPadding: 10,
					title: 'Inicio de sesion',
            		items: [{
						xtype: 'textfield',
						name: 'username',
						fieldLabel: 'Usuario',
						allowBlank: false,
						id: 'login.username'
				    },{
						xtype: 'textfield',
						name: 'password',
						inputType: 'password',
						fieldLabel: 'Contraseña',
						allowBlank: false,
						id: 'login.password'
				    },{
				    	xtype: 'label',
				    	id: 'login.message',
				    	text: '',
				    	style:{
				    		color:'red'
				    	}
				    }],
				    buttons: [{
				        text: 'Acceder',
				        formBind: true,
				        listeners: {
				    	    click: function() {
				    	    	Ext.Ajax.request({
				    	    		url:'session/Login',
				    	    		params:{
				    	    			u:Ext.getCmp('login.username').getValue(),
				    	    			p:Ext.getCmp('login.password').getValue()
				    	    		},
				    	    		success:function(resp, opts){
				    	    			obj = Ext.decode(resp.responseText);
				    	    			if(obj.msg == 'true'){
				    	    				window.location.replace("app");
				    	    			}else{
				    	    				lbl = Ext.getCmp('login.message');
				    	    				lbl.setText(obj.msg);
				    	    			}
				    	    		},
				    	    		failure:function(resp, opts){
				    	    			Ext.Msg.alert('Error', 'Error en la conexion en la Base de Datos');
				    	    		}
				    	    	});
				    	    }
				    	}
					}]
        		});
    		}
		});
	</script>
</head>
<body>
	<div style="padding: 20px 0 0 30px; color: gray; font-size: 11pt; ">Municipalidad Provincial Jorge Basadre</div>
	<div style="padding: 10px 0 0 30px; color: silver; font-size: 17pt; ">Sistema de Informaci&oacute;n y Gesti&oacute;n de Transporte Municipal</div>
</body>
</html>