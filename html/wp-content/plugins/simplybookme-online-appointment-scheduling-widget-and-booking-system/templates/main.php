<?php
/**
*   Default template SimplyBook
**/



$result = "
<div class='sb-container' id='sb-plugin-container'>


    <script src='https://{$simplybookCfg['server']}/v2/widget/widget.js'></script>
    
    <script type='application/javascript'>
         var widget = new SimplybookWidget({  
            'widget_type':'iframe',
            'url':'https:\/\/" . $simplybookCfg['server'] . "',
            'theme' : " . json_encode($simplybookCfg['template']) . ",
            'theme_settings' : " . json_encode($simplybookCfg['themeparams']) . ",
			'timeline': " . json_encode($simplybookCfg['timeline_type']) . ",
			'datepicker': " . json_encode($simplybookCfg['datepicker_type']) . ",
			'is_rtl': " . json_encode($simplybookCfg['is_rtl']) . ",
		});
    </script>
                                                                        
</div> <!-- /container -->

";

echo  $result;
