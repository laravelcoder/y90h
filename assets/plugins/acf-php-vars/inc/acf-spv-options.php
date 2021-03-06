<?php
//Simple function to check if partial strings are present in bigger ones. We'll use this later.
function p_Strings($thi_s, $tha_t){
    $pos = strpos($thi_s,$tha_t);
    if($pos === false) {
     return false;
    }
    else {
      return true;
    }
}

add_action( 'admin_menu', 'add_acf_spv_setting_page' ); 





function add_acf_spv_setting_page() {
	//Show as it's own Menu
	add_menu_page('options-general.php', 'ACF PHP VARS', 'edit_theme_options', 'acf_spv_show_variables', 'acf_spv_show_variables', ACFSPVURL.'/css/icons/sm-acf-logo.png');

	if(is_plugin_active('advanced-custom-fields-pro/acf.php')):
	//Show under Custom Fields Menu
    add_submenu_page('edit.php?post_type=acf-field-group', 'ACF PHP VARS', 'ACF PHP VARS', 'manage_options', 'acf_spv_show_variables', 'acf_spv_show_variables');
	else:
    add_submenu_page('edit.php?post_type=acf', 'ACF PHP VARS', 'ACF PHP VARS', 'manage_options', 'acf_spv_show_variables', 'acf_spv_show_variables');
	endif;

}

function acf_spv_show_variables($fgvar){
	global $wpdb;
	global $post;

		if(is_plugin_active('advanced-custom-fields-pro/acf.php')):
			$ACF_PRO_active = true;	
		else:
			$ACF_PRO_active = false;
		endif; 


	//Grab ACF Field Group Object

	//Creates the table. The ID and Class make the sorting work. 

	  
	    if(!$ACF_PRO_active):
	    $acf_field_group_VARS = $wpdb->get_results('SELECT * FROM  wp_postmeta WHERE meta_key =  "rule"');
		else:
	    $acf_field_group_VARS = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE meta_key = '_edit_last'");
		endif;

		?>
		

	<h2 class="acfpv-title">ACF Show PHP Variables | Template Usage</h2>

	<p class="description">(Copy and Paste into your template file.)</p>

	<?php if($acf_field_group_VARS[0] != ''){ ?>
		<table id="acf-table" class="tablesorterx wp-list-table widefat users">
			<thead>
				<tr class="tr-tablehead"> 
					

				<th><span class="vartitle"><strong>Variables for ACF function:</strong></span>
					<?php 
					$getf = " get_field(&#39;variable_name&#39;);";
					$thef = " the_field(&#39;variable_name&#39;);";
					$getfinst = " - This function gets the string of a value and allows you to store in a variable.";
					$thefinst = " - This function immediately displays values and cannot be store as a string.";
					echo "<div class='vardesc'";
                    echo "<span style='padding:2px 6px;background:#eee;' class='code'>";
					echo "<span class='change-function' data-name1='" . $thef . "' data-name2='".$getf."'>" . $getf . "</span>"; 
					echo "</span>";
					echo "<span class='description'>";
					echo "<span class='change-function' data-name1='" . $thefinst . "' data-name2='".$getfinst."'>" . $getfinst . "</span>"; 
					echo "</span>";
					echo "</div>";
					?>
					<br><div class="toggle-container" style="margin-top:8px;padding:6px 0px; border: 1px solid #ddd;background:#eee;">

					<!-- Button to Copy Paste ALL -->
				    <button style="margin-left:8px;" class="btn ctc" data-clipboard-action="copy" data-clipboard-target="#acf-table tbody">Copy All to Clipboard</button>

					<label style="border-right:1px solid #ddd;padding:4px 16px 4px 8px;font-weight:bold;" class="code toggle">SHOW/TOGGLE:</label> 
					<label style="border-right:1px solid #ddd;padding:4px 16px 4px 8px;" class="code toggle-function-id"><input type="checkbox" name="vauChange" value="changefunction" id="toggle-function-id"> Function:the_field</label>
					<label style="border-right:1px solid #ddd;padding:4px 16px 4px 8px;" class="code toggle-key-id"><input type="checkbox" name="vauChange" value="changekeys" id="toggle-key-id"> Field Keys</label>
					<label style="border-right:1px solid #ddd;padding:4px 16px 4px 8px;" class="code toggle-type-id"><input type="checkbox" name="vauChange" value="showtype" id="toggle-type-id"> Field Type</label>
					<label style="border-right:1px solid #ddd;padding:4px 16px 4px 8px;" class="code toggle-instructions-id"><input type="checkbox" name="vauChange" value="showinstructions" id="toggle-instructions-id"> Instructions</label>
					<label style="border-right:1px solid #ddd;padding:4px 16px 4px 8px;" class="code toggle-example-id"><input type="checkbox" name="vauChange" value="showexample" id="toggle-example-id"> Example Code</label>

					</div>
					</th>

				
				</tr>
			</thead>
		    <?php		
			foreach ($acf_field_group_VARS as $acf_field_group_VAR) {

				
				//Grab the id the of ACF Field group
				$acf_field_group_var_ID = $acf_field_group_VAR->post_id;
				//Get more from the object by using the get_post() function. Ultimately, we're trying to get

				//the name of the field group
				$acf_field_group = get_post($acf_field_group_var_ID);
				$acf_field_group_TITLE = $acf_field_group->post_title;

			    if(!$ACF_PRO_active):
					//Gets the custom field keys from the groups ID
					$acf_field_group_KEYS = get_post_custom_keys($acf_field_group_var_ID);

					//Create an array that will hold our individual field keys				 
					$acf_field_group_keys_ARRAY = array();			
					//Check to see if field_ exists in the array item and if it does, push the field into the new array we just created
					foreach ($acf_field_group_KEYS as $acf_field_group_KEY) {
						if(p_Strings($acf_field_group_KEY, "field_")){
							array_push($acf_field_group_keys_ARRAY, $acf_field_group_KEY);
						}

					}

				else:
					//If ACF PRO
		            $acf_field_field_VARS = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_parent = '".$acf_field_group_var_ID."' ");
		        	$acf_field_group_keys_ARRAY = $acf_field_field_VARS;
				endif;
		

				//Show in Metabox on Field Group screen
				if($fgvar):
					if ($fgvar === sanitize_title($acf_field_group_TITLE)):
						echo "<style type='text/css'>div#div-".$fgvar."{padding: 0 !important;}.acfpv-title{padding:0 !important;display:none;}tr.tr-".$fgvar."{background: white !important;}#acf-table td{padding:0 !important;}table#acf-table{padding: 0 !important;display: inline-block;width: 100%;overflow: auto;}</style>";

						$hide ="";
					else:
						$hide="hide";
	 				endif;
	 			
 				endif;

					echo "<tr class='tr-".sanitize_title($acf_field_group_TITLE)." ".$hide."'><td><div id='div-".sanitize_title($acf_field_group_TITLE)."' class='code' style='padding:25px;'>";
					if($count = 1): 
						$count++; 
					    echo '<div class="noselect" style="display:block;text-align:left;"><span style="opacity:0;">&lt;!---</span><br><a class="button btn noselect" data-clipboard-action="copy" data-clipboard-target="#div-'.sanitize_title($acf_field_group_TITLE).'"> Copy Code </a><br><span style="opacity:0;">--&gt;</span></div>';
					    echo "<pre>&lt;?php<br></pre>";
					    echo "<pre class='notebox'>/*";
					    echo "<br>*************************************<br>";
					    echo "ACF FIELD GROUP:<br>";
						echo '<a href="'. site_url('/wp-admin/post.php?post=' . $acf_field_group_var_ID . '&action=edit') .'" alt="Go to field group:'.$acf_field_group_TITLE.'">';
					    echo $acf_field_group_TITLE . "<br>";
					    echo "</a>";
					    echo "*************************************<br>";
					    echo "Made by ACF PHP VARS plugin<br>*************************************<br>*/</pre>"; 
					endif;
				

				foreach($acf_field_group_keys_ARRAY as $acf_field_group_keys_EACH){

					//var_dump($acf_field_field_VAR);
			        if(!$ACF_PRO_active):
			        	$acf_field_group_keys_EACH 	= get_field_object($acf_field_group_keys_EACH);
			        else:
			        	$acf_field_group_keys_EACH 	= get_field_object($acf_field_group_keys_EACH->post_name);
			        endif;

					$acf_field_LABEL 			= $acf_field_group_keys_EACH['label'];
					$acf_field_NAME 			= $acf_field_group_keys_EACH['name'];
					$acf_field_KEY 				= $acf_field_group_keys_EACH['key'];
					$acf_field_TYPE 			= $acf_field_group_keys_EACH['type'];
					$acf_field_INSTRUCTIONS 	= $acf_field_group_keys_EACH['instructions'];
					?>
					<?php //Put our data in the table ?>

						<?php if (($acf_field_TYPE == 'tab') || ($acf_field_TYPE == 'message')): //Dont show field types : TAB , Message in results ?> 
					    <?php else: ?>
						    <div>
						    	<?php 
						    	$acf_field_INSTRUCTIONS = preg_replace('!\s+!', ' ', $acf_field_INSTRUCTIONS);
						    	echo '<pre>';

						    	//echo '<a href="'. site_url('/wp-admin/post.php?post=' . $acf_field_group_var_ID . '&action=edit') .'" alt="Go to field group:'.$acf_field_group_TITLE.'">';
						    	echo '<strong>';
						    	echo '<span class="change-function" data-name1=" $' . $acf_field_NAME . ' = " data-name2=" "> $' . $acf_field_NAME . ' = </span>';
						    	echo '</strong>';
						    	//echo '</a>';
						    	echo '<span class="change-function" data-name1="get_field" data-name2="the_field">get_field</span>(&#39;<span class="change-keys" data-name1="'.$acf_field_NAME.'" data-name2="'.$acf_field_KEY.'">'.$acf_field_NAME.'</span>&#39;);';
						    	echo '<span class="hide toggle-type"> // # ' . $acf_field_TYPE . '</span>';
						    	echo '<span class="hide toggle-instructions" style="font-size:small;"> // #'. htmlspecialchars($acf_field_INSTRUCTIONS) .'</span></pre>';


						
						    	?>
						    </div>
						<?php endif; ?>

					<?php //print_r($acf_field_group_keys_EACH); echo "<br><br><hr/>";//die(); ?>


				<?php 
				}//end foreach

					if($count = 1): 
						$count++; 
					    echo '<span class="hide toggle-example">';
					    echo "<pre><br><br>/";
					    echo "*************************************";
					    echo "<br>ACF EXAMPLE CODE: <br>";
					    echo "*************************************";					  
					    echo "/</pre>"; 
					    echo '</span>';
					endif;

				foreach($acf_field_group_keys_ARRAY as $acf_field_group_keys_EACH){

					//var_dump($acf_field_field_VAR);
			        if(!$ACF_PRO_active):
			        	$acf_field_group_keys_EACH 	= get_field_object($acf_field_group_keys_EACH);
			        else:
			        	$acf_field_group_keys_EACH 	= get_field_object($acf_field_group_keys_EACH->post_name);
			        endif;

					$acf_field_LABEL 			= $acf_field_group_keys_EACH['label'];
					$acf_field_NAME 			= $acf_field_group_keys_EACH['name'];
					$acf_field_KEY 			 	= $acf_field_group_keys_EACH['key'];
					$acf_field_TYPE 			= $acf_field_group_keys_EACH['type'];
					$acf_field_INSTRUCTIONS 	= $acf_field_group_keys_EACH['instructions'];


					echo '<span class="hide toggle-example">';

					require('acf-examples.php');

					echo '</span>';

				}//end foreach




				    if($count = 1): 
				    $count++; 
				    echo "<pre>?&gt;</pre>"; 

      			    endif;
					echo "</div></tr></td>";
			}
		?>
		</table>

					    <!-- 3. Instantiate clipboard -->
					    <script>
					    var clipboard = new Clipboard('.btn');
					    clipboard.on('success', function(e) {
					    	alert('Copied to Clipboard!')
					        //console.log(e);
					    });
					    clipboard.on('error', function(e) {
					        console.log(e);
					    });
					    </script>


	<?php } else { ?>

		<?php if(!$ACF_PRO_active): ?>
				<h2>You don't have any fields created yet.</h2>
				<h3><a href="<?php echo site_url('/wp-admin/post-new.php?post_type=acf'); ?>">Get Started!</a></h3>
		<?php else: ?>
				<h2>You don't have any fields created yet for ACF PRO.</h2>
				<h3><a href="<?php echo site_url('/wp-admin/post-new.php?post_type=acf-field-group'); ?>">Get Started!</a></h3>
		<?php endif; ?>
	
	<?php } // End if ?>
<?php } //acf_spv_show_variables() ?>

<?php
	function add_link_to_acf(){
		if(is_plugin_active('advanced-custom-fields-pro/acf.php')):
		add_meta_box('ACF-PHP-VARS-ID', 'ACF PHP VARS <span class="description">(Quick View)</span>', 'show_link_to_acfphpvars', 'acf-field-group', 'side', 'low');
        else:
		add_meta_box('ACF-PHP-VARS-ID', 'ACF PHP VARS <span class="description">(Quick View)</span>', 'show_link_to_acfphpvars', 'acf', 'side', 'low');
		endif;
	}
	add_action('add_meta_boxes', 'add_link_to_acf'); 

	function show_link_to_acfphpvars(){

		?>
		<style type="text/css">
/*			.tr-tablehead{display: none;}
*/
			table#acf-table{border:none;}
			pre.notebox{display: none;}
			.toggle-type-id{display: block;}
			.toggle-instructions-id{display: none;}
			.toggle-example-id{display: none;}
			.ctc{display: none;}
			.vartitle{display: none;}
			.vardesc{display: none;}
			.toggle-key-id{display: block;}
			.toggle-function-id{display: block;}
			.toggle{display: block;}
			.toggle-container{display: block; margin-top: -18px !important;width: 230px;}
			/* div#side-sortables { position: fixed;} */
		</style>
		<?php

		$postid = get_the_ID();
		$fgvaru = get_the_title($postid);    
		$fgvar = sanitize_title(get_the_title($postid));

		if ($fgvar != 'auto-draft'):
			//echo '<div style="font-size: 14px;margin: 0;line-height: 1.4;" class="acfpv-title-qs">'.$fgvaru.' VARS | Quick Show</div>';
			acf_spv_show_variables($fgvar);
			echo '<div style="display:block;text-align:right;border-top:1px solid #ddd;padding: 8px 0 2px 0;" ><a href="admin.php?page=acf_spv_show_variables" class="acf-buttonx largex" id="show-vars-id"/>See All Field Group Vars</a></div>';
		else:
			echo 'Please save your field group to see fields';
		endif;
	}
    
?>
