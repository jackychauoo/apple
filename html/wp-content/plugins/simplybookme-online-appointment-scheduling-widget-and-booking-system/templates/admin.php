<?php

$result = "

    <script type='application/javascript'>
		function setElemVal(id, valueToSelect){    
		    if(valueToSelect){
		    	var element = document.getElementById(id);
		    	element.value = valueToSelect;
		    }
		}
	</script>
	
	<div class='' id='simplybook-page-container'>
	
		<div class=\"error\"></div>
		
		<div class='card'>
		<h2>".sbGetText("Plugin settings")."</h2>
	
		    <form method='POST' action='?page={$simplybookDomain}' >
		    
		        <table class=\"form-table\">
		            <tr>
		                <th scope=\"row\"><label>".sbGetText("Company login")."</label></th>
		                <td>
		                    <input name=\"login\" value=\"{$simplybookCfg['login']}\" class=\"regular-text\" />
		                    <p class=\"description\">".sbGetText("SimplyBook company login")." </p>
		                </td>
		            </tr>
		
		            <tr class='api-data hidden'>
		                <th scope=\"row\"><label>".sbGetText("Plugin themes:")."</label></th>
		                <td>

		                    <div id='themes-container'></div>
		                   <!-- <p class=\"description\">".sbGetText("Plugin themes from 'wordpress_dir/wp-content/plugins/simplybook.me/themes' ")." </p> -->
		                </td>
		            </tr>
		           
		            <!--
		            <input name=\"server\" type='hidden' value=\"{$simplybookCfg['server']}\" class=\"regular-text\" />
		         	-->
		   
		            <tr class='api-data hidden'>
		                <th scope=\"row\"><label>".sbGetText("Domain:")."</label></th>
		                <td>
		                    <input name=\"server\" value=\"{$simplybookCfg['server']}\" class=\"regular-text\" />
		                </td>
		            </tr>   
		            
		            <tr class='api-data hidden'>
		                <th scope=\"row\"><label>".sbGetText("Select timeline type:")."</label></th>
		                <td>
		                    <select class=\"form-control regular-text\" id=\"timeline_type\" name=\"timeline_type\">
                                <option value=\"flexible\">".sbGetText("Flexible")."</option>
		                        <option value=\"modern\">".sbGetText("Modern")."</option>
		                        <option value=\"flexible_week\">".sbGetText("Flexible weekly")."</option>
		                        <option value=\"modern_week\">".sbGetText("Slots weekly")."</option>
		                        <option value=\"classes\">".sbGetText("Modern Provider")."</option>
		                        <option value=\"flexible_provider\">".sbGetText("Flexible Provider")."</option>
		                        <option value=\"classes_plugin\">".sbGetText("Classes")."</option>
		                    </select>
		                    <script>setElemVal('timeline_type', " . json_encode($simplybookCfg['timeline_type']) . ");</script>
		                    <p class=\"description\">
		                    	<a href=\"https://help.simplybook.me/index.php/How_to_change_the_way_time_slots_are_shown\" target=\"_blank\">".sbGetText("What is timeline type?")."</a>
							</p>
		                </td>
		            </tr>  
		            
		            <tr class='api-data hidden'>
		                <th scope=\"row\"><label>".sbGetText("Select datepicker type:")."</label></th>
		                <td>
		                    <select class=\"form-control\" id=\"datepicker_type\" name=\"datepicker_type\">
    		    		    	<option value=\"top_calendar\">".sbGetText("Top calendar")."</option>
    		    				<option value=\"inline_datepicker\">".sbGetText("Inline datepicker")."</option>
    		    			</select>
    		    			
		                    <script>setElemVal('datepicker_type', " . json_encode($simplybookCfg['datepicker_type']) . ");</script>
		                </td>
		            </tr>  	
		            	            
		            <tr class='api-data hidden'>
		                <th scope=\"row\"><label>".sbGetText("RTL:")."</label></th>
		                <td>
		                    <select class=\"form-control\" id=\"is_rtl\" name=\"is_rtl\">
    		    		    	<option value=\"0\">".sbGetText("Disable")."</option>
    		    				<option value=\"1\">".sbGetText("Enable")."</option>
    		    			</select>
    		    			
		                    <script>setElemVal('is_rtl', " . json_encode($simplybookCfg['is_rtl']) . ");</script>
		                    
		                    <p class=\"description\">
		                    	<a href=\"https://en.wikipedia.org/wiki/Right-to-left\" target=\"_blank\">".sbGetText("What is RTL?")."</a>
							</p>
		                </td>
		            </tr>      
		            
		            <tr class='theme-data hidden'>
		            	<th scope=\"row\" colspan='2'>
		            		<h2>".sbGetText("Theme settings")."</h2>
		            	</th>
		            </tr>
		            
		            <tr class='theme-data hidden template'>
		                <th scope=\"row\"><label class='title'></label></th>
		                <td class='data-input'></td>
		            </tr>
		            
		            		            
		        </table>
		        
		        <p class=\"submit\">
		            <input type=\"submit\" name=\"submit\" id=\"submit\" class=\"button button-primary\" value=\"".sbGetText("Save changes")."\">
		        </p>
		        
		    </form>
	    
	    </div>
    </div>
    
    
    <script type='application/javascript'>
    	var isSbAdminLoaded = false;
    	
		var sbAdminLoad = function(){
		    isSbAdminLoaded = true;
		    var trArr = typeof simplybook_translation !== 'undefined'? simplybook_translation : [];
		    window.Locale = new sbLocale(trArr);
		    
		    var a = new SimplybookAdminInterface({
		    	login : " . json_encode($simplybookCfg['login']). ",
		    	timeline_type : " . json_encode($simplybookCfg['timeline_type']). ",
		    	template : " . json_encode($simplybookCfg['template']) . ",
		    	themeparams : " . json_encode($simplybookCfg['themeparams']) . ",
		    });
		};
		
		window.onload = function (ev) { 
		    if(!isSbAdminLoaded){
		    	sbAdminLoad();
		    }
		};
		
		var sbAdminInterval = setInterval(function() {
		  if(!isSbAdminLoaded && window.SimplybookAdminInterface && window.jQuery){
		      clearInterval(sbAdminInterval);
		      sbAdminLoad();
		  }
		}, 2000);
	</script>
";

echo $result;