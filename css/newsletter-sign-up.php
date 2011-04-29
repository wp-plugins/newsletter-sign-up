<?php header('Content-type: text/css'); ?>

<?php if(isset($_GET['checkbox_reset']) && $_GET['checkbox_reset'] == 1) { ?>
	p#ns-checkbox{
		clear:both; display:block;
	}

	#ns-checkbox input{
		margin:0 5px 0 0; display:inline-block; width:13px; height:13px; 
	}

	#ns-checkbox label{
		display:inline-block;
	}
<?php } ?>

<?php if(isset($_GET['widget_styles']) && $_GET['widget_styles'] == 1) { ?>
	.nsu-widget-form {
		margin:5px 0;
	}
	
	.nsu-widget-form p{
		clear:both;
		display:block;
		margin:5px 0;
	}
	.nsu-widget-form label{
		display:block;
		font-weight:bold;
	}
	.nsu-widget-form input{
		padding:2px;
		display:block;
	}
<?php } ?>