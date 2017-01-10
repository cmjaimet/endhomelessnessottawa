<?php
/**
* Don't load this file directly
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $datafest_letter_writing;
$datafest_letter_writing = new DatafestSpeakOut();

/** DEFINE THE CLASS THAT HANDLES THE BACK END **/
class DatafestSpeakOut {

	private $post_type = 'letter';
	private $letter_id = 0;
	private $letter = null;
	private $postal = '';
	private $user_name = '';
	private $user_email = '';
	private $user_comments = '';
	private $admin_email = '';
	public $mode = 0;

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'custom_post_letter' ) );
	}

	function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_menu', array( $this, 'create_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_shortcode( 'speak-out', array( $this, 'web_interface' ) );
	}
	
	function get_data() {
		$this->admin_email = get_option( 'letter_writing_email' );
		$this->letter_id = $this->get_param_from_ui( 'ltr', 'int', 0 );
		$this->get_postal();
		if ( 0 < intval( $this->letter_id ) ) {
			$this->letter = get_post( $this->letter_id );
		}
		// get recipient group list 
		$_mode = $this->get_param_from_ui( 'df_form_submit', 'str', '' );
		if ( 0 < $this->letter_id ) {
			if ( 3 <= strlen( $this->postal ) ) {
				if ( '' !== trim( $_mode ) ) {
					$this->mode = 3;
				} else {
					$this->mode = 2;
				}
			} else {
					$this->mode = 1;
			}
		} else {
			$this->mode = 0;
		}
	}

	function wp_enqueue_scripts() {
		//wp_enqueue_style( 'df_letter_css', DF_LETTER_URI . 'css/styles.css' );
	}

	function create_menu() {
		add_options_page( 'Letter Writing', 'Letter Writing', 'manage_options', 'letter-writing', array( $this, 'settings_page' ), '' );		//create new top-level menu
	}

	function get_param_from_ui( $_name = '', $_type = 'int', $_default = 0 ) {
		// needs validation and nonce but good for now
		$_output = $_default;
		if ( isset( $_GET[ $_name ] ) ) {
			$_output = ( 'int' === $_type ) ? intval( $_GET[ $_name ] ) : trim( $_GET[ $_name ] );
		} elseif ( isset( $_POST[ $_name ] ) ) {
			$_output = ( 'int' === $_type ) ? intval( $_POST[ $_name ] ) : trim( $_POST[ $_name ] );
		}
		return $_output;
	}

	function web_interface() {
		$this->get_data();
		$df_letter_content = array(
			array(
				'title'   => 'Choose a Topic - Step 1:',
				'content' => 'Choose a cause, enter your postal code and quickly send a message to your local representatives.'
			),
			array(
				'title'   => 'Enter Postal Code - Step 2:',
				'content' => 'Enter your postal code so we can search for the representatives in your area.'
			),
			array(
				'title'   => 'Send Message - Step 3:',
				'content' => 'In addition to sharing this important message, you can include your own thoughts. Email or print and send by mail yourself.'
			),
			array(
				'title'   => 'Learn More - Step 4:',
				'content' => 'Thank you for taking the time to help this cause. Share the message on social media. Learn more about the issues and find out about opportunities to volunteer or donate.'
			),
		); // move to settings
		$_output = '';
		$_output .= '<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">'; // better to enqueue
		$_output .= '<link rel="stylesheet" href="' . DF_LETTER_URI . 'css/styles.css"></link>'; // better to enqueue
		$_output .= '<h1>Speak Out Ottawa!</h1>' . "\n"; // move to settings
		$_output .= '<div class="container" >' . "\n";
		$_output .= '<div class="leftColumn">' . "\n";
		$_output .= '<img src="' . DF_LETTER_URI . 'images/speakout.png" alt="Take Action" />' . "\n";
		$_output .= '</div>' . "\n";
		$_output .= '<div class="rightColumn">' . "\n";
		$_output .= '<h3>Take action. Write a letter. Learn more.</h3>' . "\n"; // move to settings
		$_output .= '<h4>' . esc_html( $df_letter_content[ $this->mode ]['content'] ) . '</h4>' . "\n";
		$_output .= '</div>' . "\n";
		$_output .= '</div>' . "\n";

		$_output .= '<h2>' . esc_html( $df_letter_content[ $this->mode ]['title'] ) . '</h2>' . "\n";
		$_mode = $this->get_param_from_ui( 'df_form_submit', 'str', '' );
		switch ( $this->mode ) {
			case 1:
				// second page: enter postal code
				$_output .= $this->form_postal();
				$_output .= $this->display_letter();
				break;
			case 2:
				// third page: write letter
				$_output .= $this->set_cookie( 'postal', $this->postal );
				$_output .= $this->display_letter();
				$_output .= $this->form_letter();
				break;
			case 3:
				// last page: send email and show next steps
				$this->get_user_form();
				$this->save_user_data();
				$this->email_letter();
				$_output .= $this->page_done();
				break;
			case 0:
			default:
				// first page: list letters
				$_output .= $this->list_letters();
				break;
		}
		$_output .= '</div>';
		return $_output;
	}

	function get_user_form() {
		$this->user_name = $this->get_param_from_ui( 'df_letter_name', 'str', '' );
		$this->user_email = $this->get_param_from_ui( 'df_letter_email', 'str', '' );
		$this->user_comments = $this->get_param_from_ui( 'df_letter_notes', 'str', '' );
	}

	/**
	* store user data and include letter chosen and notes added
	* render elsewhere (admin) as petition
	*/
	function save_user_data() {
		//$this->user_name = $this->get_param_from_ui( 'df_letter_name', 'str', '' );
		//$this->user_email = $this->get_param_from_ui( 'df_letter_email', 'str', '' );
		//$this->user_comments = $this->get_param_from_ui( 'df_letter_notes', 'str', '' );
		//$this->letter->recip_groups
	}

	function email_letter() {
		$_headers = array();
		$_headers[] = 'From: Alliance to End Homelessness Ottawa <eho@calmseamedia.com>';
		if ( '' !== trim( $this->user_email ) ) {
			$_headers[] = 'Cc: ' . esc_attr( $this->user_name ) . ' <' . esc_attr( $this->user_email ) . '>';
		}
		$_subject = $this->letter->post_title;
		$_content = $this->letter->post_content;
		$_content .= "\n\n";
		$_content .= $this->user_comments;
		$_content .= "\n\n";
		$_content .= $this->user_name . "\n";
		$_content .= $this->user_email . "\n";
		// send email to admin
		wp_mail( $this->admin_email, $_subject, $_content );
		// foreach recipient {
		// wp_mail( $this->recip[], $_subject, $_content );
		// }
	}

	function page_done() {
		$_output = '';
		//echo $this->user_name . '<br />';
		//echo $this->user_email . '<br />';
		//echo $this->user_comments . '<br />';
		//$_output .= '<h2>All Done</h2>';
		return $_output;
	}

	function display_letter() {
		if ( ! is_null( $this->letter ) ) {
			echo $this->show_recipients();
			echo '<h3>' . $this->letter->post_title . '</h3>';
			echo '<i>';
			echo apply_filters( 'the_content', $this->letter->post_content );
			echo '</i>';
		}
	}

	function form_letter() {
		$_output = '';
		if ( ! is_null( $this->letter ) ) {
			$_output .= '<form method="POST" action="/write-letter">' . "\n";
			$_output .= '<input type="hidden" name="ltr" value="' . intval( $this->letter_id ) . '" />' . "\n";
			$_output .= '<input type="hidden" name="postal" value="' . esc_attr( $this->postal ) . '" />' . "\n";
			$_output .= '<div class="df_letter_notes">';
			$_output .= '<h3>Additional Comments</h3>';
			$_output .= '<textarea name="df_letter_notes">';
			$_output .= '</textarea>';
			$_output .= '</div>';
			$_output .= '<div class="df_letter_email">';
			$_output .= '<h3>Name</h3>';
			$_output .= '<input type="text" name="df_letter_name" value="' . esc_attr( $this->user_name ) . '" placeholder="sign your letter" />';
			$_output .= '</div>';
			$_output .= '<div class="df_letter_email">';
			$_output .= '<h3>Email</h3>';
			$_output .= '<input type="text" name="df_letter_email" value="' . esc_attr( $this->user_email ) . '" placeholder="your email" />';
			$_output .= '</div>';
			$_output .= '<div class="df_letter_submit">';
			$_output .= '<input type="submit" name="df_form_submit" class="df_letter_button" value="Send" />';
			$_output .= '</div>';
			$_output .= '<div class="df_letter_print">';
			$_output .= '<input type="button" name="df_letter_print" class="df_letter_button_inverse" value="Print" />';
			$_output .= '</div>';
			$_output .= '</form>';
		}
		return $_output;
	}

	function show_recipients() {
		$_output = '';
		$_recipients = $this->get_recipients();
		if ( ! empty( $_recipients ) ) {
			$_output .= '<span class="df_recip_label">Recipients: </span>';
			$_output .= '<ul class="df_recip_list">';
			foreach ( $_recipients as $_idx => $_recip ) {
				//$this->letter->recip_groups filter by this
				if ( '' !== trim( $_recip->rep_email ) ) {
					$_output .= '<li>';
					$_output .= esc_html( $_recip->rep_name );
					$_output .= ' &#060;' . esc_html( $_recip->rep_email ) . '&#062;';
					$_output .= '</li>';
				}
			}
			$_output .= '</ul>';
		}
		return $_output;
	}

	// get recipients from db based on $this->letter_id, $this->postal
	function get_recipients() {
		$_results = array();
		if ( '' !== trim( $this->postal ) ) {
			$_url = 'https://represent.opennorth.ca/postcodes/' . $this->postal; // validate in $this->get_data and below with esc_url()
			$_code = $this->get_http_response_code( $_url );
			echo $_code;
			if ( ( 200 <= $_code ) && ( 400 > $_code ) ) {
			$_json = file_get_contents( esc_url( $_url ) );
			$_data = json_decode( $_json );
			echo '<textarea style="width:100%;height:600px;">';
			print_r($_data);
			exit;
			}
		}
		return $_results;
	}

	/**
	* Excellent API but imperfect error handling on invalid endpoint so trap before retrieving file
	* This may change from a 404 at some point to an empty/error object
	*/
	function get_http_response_code( $_url ) {
		$_headers = get_headers( $_url );
		$_code = 404; // default to failure
		if ( is_array( $_headers ) && ( 9 <= count( $_headers ) ) ) {
			$_code = intval( substr( $_headers[8], 9, 3 ) );
		}
		return $_code;
	}

	function form_postal() {
		$_output = '';
		$_output .= '<form method="POST" action="">' . "\n";
		$_output .= '<input type="hidden" name="ltr" value="' . intval( $this->letter_id ) . '" />' . "\n";
		$_output .= '<input type="text" name="postal" value="" maxlength="7" style="max-width:150px;" border=""/>' . "\n";
		$_output .= '<input type="submit" name="submit" value="GO!" class="button-rev" />' . "\n";
		$_output .= '</form>' . "\n";
		return $_output;
	}

	function list_letters() {
		$_output = '';
		$_args = array(
			'posts_per_page'   => 25,
			'offset'           => 0,
			'orderby'          => 'post_title',
			'order'            => 'DESC',
			'post_type'        => $this->post_type,
			'post_status'      => 'publish',
			'suppress_filters' => false
		);
		$_letters = get_posts( $_args );
		$_output .= '<ul>';
		foreach ( $_letters as $_idx => $_letter ) {
			$_output .= '<li><a href="?ltr=' . intval( $_letter->ID ) . '">';
			$_output .= esc_html( $_letter->post_title );
			$_output .= '</a></li>';
		}
		$_output .= '</ul>';
		//$_output .= '<div class="button">SUGGEST A LETTER</div>';
		return $_output;
	}

	function get_selected_groups( $_post_id ) {
		$_groups = get_post_meta( $_post_id, 'df_letter_recip', true );
		return $_groups;
	}

	function get_recipient_groups() {
		// extract from db - stub for now
		$_groups = array(
			1 => 'Municipal Politicians',
			2 => 'Provincial Politicians',
			3 => 'Federal Politicians',
		);
		return $_groups;
	}

	function custom_post_letter() {
		$labels = array(
			'name'               => _x( 'Letters', 'post type general name', 'datafest-letter-writing' ),
			'singular_name'      => _x( 'Letter', 'post type singular name', 'datafest-letter-writing' ),
			'menu_name'          => _x( 'Letters', 'admin menu', 'datafest-letter-writing' ),
			'name_admin_bar'     => _x( 'Letter', 'add new on admin bar', 'datafest-letter-writing' ),
			'add_new'            => _x( 'Add New', 'letter', 'datafest-letter-writing' ),
			'add_new_item'       => __( 'Add New Letter', 'datafest-letter-writing' ),
			'new_item'           => __( 'New Letter', 'datafest-letter-writing' ),
			'edit_item'          => __( 'Edit Letter', 'datafest-letter-writing' ),
			'view_item'          => __( 'View Letter', 'datafest-letter-writing' ),
			'all_items'          => __( 'All Letters', 'datafest-letter-writing' ),
			'search_items'       => __( 'Search Letters', 'datafest-letter-writing' ),
			'parent_item_colon'  => __( 'Parent Letters:', 'datafest-letter-writing' ),
			'not_found'          => __( 'No letters found.', 'datafest-letter-writing' ),
			'not_found_in_trash' => __( 'No letters found in Trash.', 'datafest-letter-writing' )
		);
		$_args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'datafest-letter-writing' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $this->post_type ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 18,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' )
		);		
		register_post_type( 'letter', $_args );
	}

	function admin_init() {
		add_meta_box( 'recipient_meta_box', 'Recipient Groups', array( $this, 'admin_recipient_meta_box' ), $this->post_type, 'side', 'core' );
		add_action( 'save_post', array( $this, 'admin_letter_save' ) );
	}
	
	function admin_recipient_meta_box() {
		global $post;
		$_output = '';
		$_groups = $this->get_recipient_groups();
		$_selected = $this->get_selected_groups( $post->ID );
		foreach( $_groups as $_idx => $_group_name ) {
			$_output .= '<input type="checkbox" value="' . intval( $_idx ) . '" name="df_letter_recip[]" ' . checked( true, in_array( $_idx, $_selected ), false ) . '>' . esc_html( $_group_name ) . "<br />\n";
		}
		return $_output;
	}

	function admin_letter_save( $_post_id ) {
		// validate nonce, is_array
		//global $post;
		$_groups = array();
		if ( isset( $_POST['df_letter_recip'] ) ) {
			if ( is_array( $_POST['df_letter_recip'] ) ) {
				$_groups = $_POST['df_letter_recip'];
			}
		}
		update_post_meta( $_post_id, 'df_letter_recip', $_groups );
	}
	
	function settings_page() {
		echo '<h2>Letter Writing Settings</h2>' . "\n";
		echo '<form method="POST" action="options.php">' . "\n";
		wp_nonce_field( $this->post_type, 'pm_layout_noncename' );
		settings_fields( 'letter-writing-settings-group' );
		echo '<span class="df_postal_label">Email: </span>' . "\n";
		echo '<input type="text" name="letter_writing_email" value="' . esc_attr( $this->admin_email ) . '" />';
		submit_button();
		echo '</form>' . "\n";
	}

	function register_settings() {
		//register our settings
		register_setting( 'letter-writing-settings-group', 'letter_writing_email', array( $this, 'sanitize_text_input' ) );
	}

	function sanitize_text_input( $_txt ) {
		$_txt = wp_unslash( $_txt );
		$_txt = sanitize_text_field( $_txt );
		return $_txt;
	}

	function set_cookie( $_name, $_value = '' ) {
		//setcookie( $_name, $_value, time() + ( 60 * 60 * 24 * 120 ), '/' ); // 120 days - headers issue
	}

	function get_postal() {
		$_postal = $this->get_param_from_ui( 'postal', 'str', '' ); // returns trimmed string
		if ( '' !== $_postal ) {
			$_postal = strtoupper( $_postal ); // API requires all caps postal
			$_postal = preg_replace( '/[^A-Z0-9]/', '', $_postal ); // strip out spaces and anything else that isn't alphanumeric
			if ( 6 !== strlen( $_postal ) ) {
				$_postal = ''; // wrong length for a postal code so reject
			} elseif ( ! preg_match( '/[A-Z][0-9][A-Z][0-9][A-Z][0-9]/', $_postal ) ) {
				$_postal = ''; // wrong format - not A1A1A1 so reject
			}
			//$_postal = substr( $this->postal, 0, 3 );
		}
		$this->postal = $_postal;
	}
}
