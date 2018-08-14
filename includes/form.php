<?php 

/**
* Load the base class
*/
class Endo_Feedback_Form {

	/**
	 * Kick it off
	 * 
	 */
	public function run() {

		// create cpt
		// create front end display of form
		// store data on submission (including IP address)
		// store data use WP REST API
		// add a honeypot for bots
		// settings for
		//  - display feedkback form
		//  - show on: all pagaes, only certain pages
		// 	- show to logged in, logged out, or both
		// 	- what email to send submission to?
		// 	- form title
		// 	- use askismet to ward off spam
		// 	- color for buttons
		// 	after a user has submitted feedback, set a cookie (and uesr meta) so they don't see it again
		// 	- store time, user, page submitted
		// 	- fade out the form and change button text to "thanks for your feedback!", then fade out the button.
		// 	- move all cookie tests to the javascript to bypass caching

		add_action( 'init', array( $this, 'register_feedback_post_type' ) );
		add_filter('manage_edit-endo_feedback_columns' , array( $this, 'feedback_admin_columns') );
		add_action('manage_endo_feedback_posts_custom_column' , array( $this, 'feedback_admin_custom_columns' ), 10, 2 );



		add_action( 'wp_footer', array( $this, 'show_form' ) );

		// add_action( 'init', array( $this, 'process_form' ) );

		add_action( 'wp_ajax_nopriv_endo_feedback_process', array( $this, 'process_form' ) );
		add_action( 'wp_ajax_endo_feedback_process', array( $this, 'process_form' ) );
	}

	public function show_form() 
	{

		$show_form = $this->display_rules();

		if ( !$show_form ) {
			return;
		}
		?>

		<div id="endo-feedback">

			<transition name="slide-fade">
				<div class="endo-feedback-wrapper" v-if="show">
					
					<div class="endo-feedback-form-wrapper">
						<form id="endo-feedback-form" action="<?php echo get_permalink( get_the_ID() ); ?>" method="POST" v-on:submit.prevent="onSubmit">
							<div class="field">
							  <label class="label">{{ question }}</label>
							  <div class="control">
							    <textarea v-model="message" name="message" class="textarea" rows="5" required placeholder="Enter your message here..."></textarea>
							  </div>
							</div>	

							<div class="field">
							  <div class="control">
							  	<input type="hidden" name="question" :value="question">
							  	<?php wp_nonce_field( 'endo_feedback_submit', 'endo_feedback_nonce_field' ); ?>
							    <button class="button is-submit" v-bind:disabled="submitting" v-text="submitButton"></button>
							  </div>
							 
							</div>

						</form>

					</div>
					
				</div>
			</transition>

			<transition name="slide-fade">
				<div class="endo-feedback-message" v-if="success">
					<p>Thanks for your feedback!</p>
				</div>
			</transition>

			<transition name="slide-fade">
				<button class="endo-feedback-button" v-if="!success" @click="show = !show">{{ show ? 'X' : ctaButton }}</button>
			</transition>
		</div>

		<?php 
	}

	public function display_rules() 
	{
		
		$question = isset( $_COOKIE['endo_feedback_submitted'] ) ? $_COOKIE['endo_feedback_submitted'] : '';

		// if the user does not have a cookie, show form
		if ( !$question ) {
			return true;
		}

		$current_question = 'What do you think of the new Track and Field News site?';

		// if the user has a cookie, but the title of the current question is different, show form
		if ( $current_question !== $question ) {
			return true;
		}


		// if the user has a cookie and the form question matches, do not show form
		return false;

		
	}

	public function process_form( ) 
	{

		

		// if ( 
		//     ! isset( $_REQUEST['nonce'] ) 
		//     || ! wp_verify_nonce( $_REQUEST['nonce'], 'endo_feedback_submit' ) 
		// ) {
		//    return;
		// }

		// Create post object
		$feedback_data = array(
		  'post_title'    => wp_strip_all_tags( $_REQUEST['question'] ),
		  'post_content'  => sanitize_textarea_field( $_REQUEST['message'] ),
		  'post_status'   => 'publish',
		  'post_author'   => 1,
			'post_type'	=> 'endo_feedback'
		);
		 
		// Insert the post into the database
		$feedback_id = wp_insert_post( $feedback_data );

		if ( is_wp_error( $feedback_id ) ) {
			wp_send_json( array(
				'response'	=> 'fail',
			));
		}

		update_post_meta( $feedback_id, '_user_id', get_current_user_id() );
		update_post_meta( $feedback_id, '_url', sanitize_text_field( $_REQUEST['referrer'] ) );


		// add cookie to hide form, base it on the question so that if the question changes the feedback form with show again
		setcookie('endo_feedback_submitted', sanitize_text_field( $_REQUEST['question'] ), time() + (86400 * 7 * 31)); // 86400 = 1 day

		wp_send_json( array(
			'response'	=> 'success',
		));
	}

	public function register_feedback_post_type() 
	{
		$labels = array(
			'name'               => 'Feedback',
			'singular_name'      => 'Feedback',
			'menu_name'          => 'Feedback',
			'name_admin_bar'     => 'Feedback',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Item',
			'new_item'           => 'New Item',
			'edit_item'          => 'Edit Feedback',
			'view_item'          => 'View Feedback',
			'all_items'          => 'All Feedback',
			'search_items'       => 'Search Feedback',
			'parent_item_colon'  => 'Parent Feedback:',
			'not_found'          => 'No feedback found',
			'not_found_in_trash' => 'No feedback found in trash.'
		);

		$args = array(
			'labels'             => $labels,
	        'description'        => __( 'Description.', 'your-plugin-textdomain' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor' )
		);

		register_post_type( 'endo_feedback', $args );
	}

	public function feedback_admin_columns( $columns ) 
	{
		
		 $columns = array(
    		'cb' => '<input type="checkbox" />',
    		'question' => __( 'Question' ),
    		'message' => __( 'Message' ),
    		'user' => __( 'User' ),
    		'url' => __( 'URL' ),
    		'date' => __( 'Date' )
    	);

    	return $columns;

	}

	public function feedback_admin_custom_columns( $column, $post_id ) 
	{

		$feedback = get_post( $post_id );
		
		switch ( $column ) {
		
		   case 'question' :
		   		
		       echo $feedback->post_title;
		       break;

		   case 'message':
		   		echo $feedback->post_content;
		       break;

		   case 'user':
		   		$user_id = get_post_meta( $post_id, '_user_id', true );
		   		if ( $user_id ) {
		   			$user = get_user_by( 'id', $user_id );
		   			echo '<a href="' . admin_url() . '/user-edit.php?user_id=' . $user_id . '">' . $user->user_email . '</a>';
		   		} else {
		   			echo 'Not logged in.';
		   		}
		   		
		       break;

		   case 'url':
		   		echo get_post_meta( $post_id, '_url', true );
		       break;
		}

	}


}

(new Endo_Feedback_Form)->run();