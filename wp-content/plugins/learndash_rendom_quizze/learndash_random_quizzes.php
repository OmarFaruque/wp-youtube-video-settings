<?php
/*
Plugin Name: Learndash random quizzes
Plugin URI: https://larasoftbd.com/
Description: Learndash random quizzes is the most professional WordPress plugin, it's lightweight and high efficiency to help you build any layout design quickly.
Version: 2.0.1
Author: LaraSoft
Author URI: https://larasoftbd.com/
Text Domain: random_quizzes
*/

add_action( 'admin_enqueue_scripts', 'safely_add_stylesheet_to_admin' );
    function safely_add_stylesheet_to_admin() {
       
        wp_enqueue_style( 'select2-min-css', plugins_url('css/select2.min.css', __FILE__) );
        wp_enqueue_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );
        wp_enqueue_script( 'select2-js', plugins_url('js/select2.min.js', __FILE__) , array(), '1.0.1', true );
        wp_enqueue_script( 'prefix-style2', plugins_url('js/script.js', __FILE__) , array(), '1.0.2', true );
        wp_localize_script( 'prefix-style2', 'prefix', admin_url( 'admin-ajax.php') );
    }

add_action('admin_menu','main_menu_quizzes'); 
function main_menu_quizzes(){

	add_menu_page(  'Random quizze', 'Random quizze', 'manage_options','main_manu_s', 'menu_quizzes' ,'',100);
}


function updetvalue(){
    global $wpdb;

	if($_POST['dataType']== 'quiz')
	{

		$wp_quiz_master = $wpdb->prefix.'pro_quiz_master';
	   $all_que = $wpdb->get_results('SELECT * FROM `'.$wp_quiz_master.'`', OBJECT);
	}
    else
    {
    	$all_que = get_terms('category', array('hide_empty' =>true ));    
    }
	echo json_encode(array(
			'massage'=> 'succuess',
			'quizs' => $all_que
		));

	die();
}

add_action( 'wp_ajax_nopriv_updetvalue','updetvalue' );
add_action( 'wp_ajax_updetvalue','updetvalue' );
   
function menu_quizzes()
{
	global $wpdb;
 $massage = '';

	if(isset($_POST['submit']))
	{

		$quizz_table = $wpdb->prefix.'pro_quiz_master';

		if($_POST['post_title'] != '')
		{
		    $quize = $wpdb ->insert(
		    	$quizz_table,
		    	array(
		    	'name' => $_POST['post_title'],
		    	'text' => $_POST['quiz_content'] 
		    ),
		    array('%s','%s'));

			$quize_id = $wpdb->insert_id;


			if(isset($_POST['new_category']))
			{
				$term = $_POST['new_category'];

				$slug = strtolower($term);
				$slug = str_replace(' ', '-', $term);

			 $new_category_id = wp_insert_term( $term, 'category', array(
			 'slug' =>	$slug

			 ) );

				
			}
			$category_id = (!isset($_POST['new_category']))?$_POST['item_category']:array($new_category_id['term_id']);

            $my_post = array(
				  'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
				  'post_content'  => $_POST['quiz_content'],
				  'post_status'   => 'publish',
				  'post_author'   => get_current_user_id(),
				  'post_type'     => 'sfwd-quiz',
				  'post_category' => $category_id

				);
				 
				// Insert the post into the database
				$insert_quiz_post = wp_insert_post( $my_post );
                
                $quizz_meta_valu = array( 
                	'sfwd-quiz_quiz_materials' => '' ,
				    'sfwd-quiz_repeats' => '',
				    'sfwd-quiz_threshold' => 0.8,
				    'sfwd-quiz_passingpercentage' => 80,
				    'sfwd-quiz_course' => 0,
				    'sfwd-quiz_lesson' => 0,
				    'sfwd-quiz_certificate' => 0,
				    'sfwd-quiz_quiz_pro' => $quize_id );


				update_post_meta( $insert_quiz_post, 'quiz_pro_id', $quize_id );
				update_post_meta( $insert_quiz_post, 'quiz_pro_id_'.$quize_id, $quize_id );
				update_post_meta( $insert_quiz_post, '_sfwd-quiz', $quizz_meta_valu );

				if ($insert_quiz_post)
				{
					$massage .= __('This value is inserted successfully !','random_quizzes');
				} 
				else{
					$massage .= __('This value is not insert','random_quizzes');
				}

		}

		foreach ($_POST['js_quiz_id'] as $key => $sitem)
		{
			$item_number = $_POST['js_item_number'][$key];

			$tableName = $wpdb->prefix.'pro_quiz_question';


			if($_POST['quiz_type'] == 'quiz')
			{
				$all_que = $wpdb->get_results('SELECT * FROM `'.$tableName.'` WHERE quiz_id='.$sitem.' ORDER BY RAND()
					LIMIT '.$item_number.' ', OBJECT);
			}
			else{

					$args = array(
					'post_type' => 'sfwd-quiz', 
					'post_status'   => 'publish',
					'tax_query' => array(
						array(
							'taxonomy' => 'category',
							'field'    => 'term_id',
							'terms'    => $sitem
						),
					),
				);

				$category_query = new WP_Query( $args );
				$all_quizz = $category_query->get_posts();

				if (count($all_quizz) > 0) {
					$all_cat_qu = array();
					foreach ($all_quizz as $key => $value) 
					{

						$all_ques_id = get_post_meta( $value->ID , 'quiz_pro_id', true );
						if ($all_ques_id != '') {

						array_push($all_cat_qu, $all_ques_id);

						}

					}
					$ex_cat_id = implode(', ', $all_cat_qu);

					$all_que = $wpdb->get_results('SELECT * FROM `'.$tableName.'` WHERE quiz_id IN('.$ex_cat_id.') ORDER BY RAND()
						LIMIT '.$item_number.' ', OBJECT);

				}


			}//if($_POST['quiz_type'] == 'quiz')

			if ($all_que) {
			  foreach ($all_que as $key => $siq) {
			  	unset($siq->id);
			  	    $siq->quiz_id = $quize_id;
			  		$quize = $wpdb ->insert(
			    	$tableName,
			    	(array)$siq
			    );
			  }
			}
		}

		
		if ($massage != '') {
			
		
		?>
		<form method="post" action="" class="submit_from">
	      <div class="submit_massage">
		    	<h2><?php echo $massage; ?></h2>
		  </div>
		  <button class="button button-primary submit_button" type="submit"><span class="dashicons dashicons-arrow-left-alt2"></span><?php _e('Go to back','random_quizzes' ); ?>
		  </button>
		</form>

		<?php
		}

	}

	elseif(isset($_POST['quizze_type']) && $_POST['quizze_type'] != '' )
	{ 

		if($_POST['quizze_type']== 'quizzes')
		{
           $quiz_master = $wpdb->prefix.'pro_quiz_master';
		   $all_que = $wpdb->get_results('SELECT * FROM `'.$quiz_master.'`', OBJECT);
	    }
	    else{

	    	 $all_que = get_terms('category', array('hide_empty' =>true ));
	    } 


		?>
		<div class="post_quizz">
			<div class="form-group">



				<form method="post" action="" id="add_name" name="add_name">
					<div class="single_item">
	                    <div id="titlewrap">
	                    	<?php
                            if($_POST['quizze_type']== 'quizzes')
							{
								?>
		                  	<h1 class="wp-heading-inline" ><?php _e('Quiz Editor','random_quizzes' ); ?></h1>
		                  	<?php
		                  }
		                  else
		                  	{
		                  		?>
		                  		<h1 class="wp-heading-inline" ><?php _e('Category Quiz Editor','random_quizzes' ); ?></h1>
		                  	<?php
		                  }
		                  ?>
		                  	<div class="post_title">
								<input type="text" name="post_title" id="title" placeholder="Enter title here" required/>
							</div>
						</div>
					</div>
					<div class="single_item text_from">	
						<?php wp_editor(
						    '',
						    'quiz_content',
						    array(
						      'media_buttons' => false,
						      'textarea_rows' => 8,
						      'tabindex' => 4,
						      'tinymce' => array(
						        'theme_advanced_buttons1' => 'bold, italic, ul, pH, temp',
						      ),
						    )
						)   ?>
				    </div>
					<div class="left_side">
						<div id="dynamic_field">
							 <div class="single_item">
								<?php
	                            if($_POST['quizze_type']== 'quizzes')
								{
									?>
									<label for="item_select"><?php _e('select your quiz question and number :','random_quizzes' ); ?><br></label>
								<?php }else{ ?>
									<label for="item_select"><?php _e('select your category question and number :','random_quizzes' ); ?><br></label>
								<?php
							}

								?>

							    <select class="from_item" id="item_select" name="item_list">
							    	<option value="">
							    		<?php
							    		 if($_POST['quizze_type']== 'quizzes')
											{
												_e('select quiz','random_quizzes' );
											}
										else{
											_e('select category','random_quizzes' );
											}
										?>	
									</option>
							 		<?php 
			                        	foreach($all_que as $row){   
					   				
							 		?>
							 		<option  class="form-control name_list" value="<?php if($_POST['quizze_type'] == 'quizzes')
									{
		                                  echo $row->id;
									}
							 		echo $row->term_id;
							 		 ?>" ><?php echo $row->name; ?></option>
							        <?php } ?>

							    </select>
							    <input type="number" min='1'  name="item_number" class="item_number_1" required/>
								<button type="button" name="add" id="add" class="button btn btn-success">+</button>
							</div>
							<div id="single_item_1">
								<ul></ul>
							</div>
						</div>
						
						<input type="hidden" name="quiz_type" value="<?php echo ($_POST['quizze_type'] == 'quizzes')?'quiz':'cat';?>"/>

					</div>
					<div class="right_side">

						<label for="select_category"><?php _e('select a category :','random_quizzes' ); ?><br></label>
						<div class="select_category">
							<select class="from_item2" multiple="multiple" id="item_select2" name="item_category[]">
						 		<?php 
						 		$all_que = get_terms('category', array('hide_empty' =>false ));
		                        	foreach($all_que as $row){   
				   				
						 		?>
						 		<option  class="form-control name_list" value="<?php if($_POST['quizze_type'] == 'quizzes')
								{
	                                  echo $row->id;
								}
						 		echo $row->term_id;
						 		 ?>" ><?php echo $row->name; ?></option>
						        <?php } ?>
						    </select>
						</div>
						<div class="add_category">
						<a name="add1" id="add1" href="#" ><b>+ Add New Category</b></a>

                        </div>
				    </div>


					<input type="submit" name="submit" id="submit" class="button butt_sub single_item button-primary" value="<?php _e('Submit','random_quizzes' ); ?>" /> 
	
				</form>

			</div>
		</div>
        
	<?php           
	}
	else
	{
	 ?>
		<div class="form-group">
			<h1 class="wp-heading-inline" ><?php _e('Quizzes Customization','random_quizzes' ); ?></h1>
			<form method="post" action="" id="searchpro">
				<tr>
					<td>
						<div>
						 	<select required name="quizze_type" class="form-control form-control-lg from_item">
						 		<option value=""><?php _e('Select One','random_quizzes' ); ?></option>
						 		<option class="here" value="quizzes"><?php _e('Quizzes','random_quizzes' ); ?></option>
							 	<option value="category"><?php _e('Category','random_quizzes' ); ?></option>
						        <div class="valid-feedback"></div>
						    </select>
						    <button class="button button_1 button-primary" type="submit"><?php _e('Go','random_quizzes' ); ?></button>
						</div>
					</td>
				</tr>
			</form>
		</div>
	<?php

	}
}      
?>