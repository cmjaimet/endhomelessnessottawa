<?php
/**
* Don't load this file directly
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $datafest_letter_writing;
$datafest_letter_writing = new DatafestLetterWriting();

/** DEFINE THE CLASS THAT HANDLES THE BACK END **/
class DatafestLetterWriting {

	private $post_type = 'letter';
	private $letter_id = 0;
	private $letter = null;
	private $fsa = '';
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
		// add shortcode datafest-letter-writing list_letters()
		add_shortcode( 'datafest-letter-writing', array( $this, 'web_write_letter' ) );
		$this->admin_email = get_option( 'letter_writing_email' );
		$this->get_mode();
	}
	
	function get_mode() {
		$this->letter_id = $this->get_param_from_ui( 'ltr', 'int', 0 );
		$this->fsa = $this->get_param_from_ui( 'fsa', 'str', '' );
		$_mode = $this->get_param_from_ui( 'df_form_submit', 'str', '' );
		if ( 0 < $this->letter_id ) {
			if ( 3 <= strlen( $this->fsa ) ) {
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

	function web_write_letter() {
		$_output = '';
		$this->fsa = substr( $this->fsa, 0, 3 );
		$_mode = $this->get_param_from_ui( 'df_form_submit', 'str', '' );
		$this->get_letter();
		switch ( $this->mode ) {
			case 1:
				// second page: enter postal code / fsa
				$_output .= $this->form_fsa();
				break;
			case 2:
				// third page: write letter
				$_output .= $this->set_cookie( 'fsa', $this->fsa );
				$_output .= $this->form_letter();
				break;
			case 3:
				// last page: send email and show next steps
				$this->get_user_form();
				$this->email_letter();
				$_output .= $this->page_done();
				break;
			case 0:
			default:
				// first page: list letters
				$_output .= $this->list_letters();
				break;
		}
		return $_output;
	}

	function get_user_form() {
		$this->user_name = $this->get_param_from_ui( 'df_letter_name', 'str', '' );
		$this->user_email = $this->get_param_from_ui( 'df_letter_email', 'str', '' );
		$this->user_comments = $this->get_param_from_ui( 'df_letter_notes', 'str', '' );
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
		wp_mail( 'cmjaimet@gmail.com', $_subject, $_content );
	}

	function page_done() {
		$_output = '';
		//echo $this->user_name . '<br />';
		//echo $this->user_email . '<br />';
		//echo $this->user_comments . '<br />';
		//$_output .= '<h2>All Done</h2>';
		return $_output;
	}

	function form_letter() {
		$_output = '';
		if ( ! is_null( $this->letter ) ) {
			$_output .= $this->show_recipients();
			$_output .= '<h3>' . $this->letter->post_title . '</h3>';
			$_output .= apply_filters( 'the_content', $this->letter->post_content );
			$_output .= '<form method="POST" action="/write-letter">' . "\n";
			$_output .= '<input type="hidden" name="ltr" value="' . intval( $this->letter_id ) . '" />' . "\n";
			$_output .= '<input type="hidden" name="fsa" value="' . esc_attr( $this->fsa ) . '" />' . "\n";
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
		$_output .= '<span class="df_recip_label">Recipients: </span>';
		$_output .= '<ul class="df_recip_list">';
		$_recipients = $this->get_recipients();
		if ( ! is_null( $_recipients ) ) {
			foreach ( $_recipients as $_idx => $_recip ) {
				if ( '' !== trim( $_recip->contact_email ) ) {
					$_output .= '<li>';
					$_output .= esc_html( $_recip->contact_name );
					$_output .= ' &#060;' . esc_html( $_recip->contact_email ) . '&#062;';
					$_output .= '</li>';
				}
			}
		}
		$_output .= '</ul>';
		return $_output;
	}

	function validate_sql_text( $_txt ) {
		$_txt = $_txt;
		return $_txt;
	}

	// get recipients from db based on $this->letter_id, $this->fsa
	function get_recipients() {
		global $wpdb;
		$_fsa = $this->validate_sql_text( $this->fsa );
		$_sql = "SELECT R.recip_id, R.contact_email, R.contact_name, G.recip_group_name
			FROM df_fsa_recipient AS F
			INNER JOIN df_recip AS R ON F.recip_id = R.recip_id
			INNER JOIN df_recip_group AS G ON G.recip_group_id = R.recip_group_id
			WHERE F.fsa LIKE '" . $this->fsa . "' 
			ORDER BY G.recip_group_id ASC ";
		$_results = $wpdb->get_results( $_sql, OBJECT );
		/*
				echo $_sql;
				print_r($_results);
		*/
		return $_results;
	}

	function get_letter() {
		if ( 0 < intval( $this->letter_id ) ) {
			$this->letter = get_post( $this->letter_id );
		}
	}

	function form_fsa() {
		$_output = '';
		$_output .= '<div class="post-content" style="overflow=auto">' . "\n";
		$_output .= '<div id="primary" class="content-area">' . "\n";
		$_output .= '<main id="main" class="site-main" role="main">' . "\n";
		$_output .= '<h1 data-fontsize="38" data-lineheight="48">Enter Postal Code:</h1>' . "\n";
		$_output .= '<form method="POST" action="/write-letter">' . "\n";
		$_output .= '<input type="hidden" name="ltr" value="' . intval( $this->letter_id ) . '" />' . "\n";
		$_output .= '<input type="text" name="fsa" value="" maxlength="7" style="max-width:150px;" border=""/>' . "\n";
		$_output .= '<br><br>' . "\n";
		$_output .= '<div class="fusion-button-wrapper">';
		//$_output .= '<style type="text/css" scoped>.fusion-button.button-1 .fusion-button-text, .fusion-button.button-1 i {color:#fff;}.fusion-button.button-1 {border-width:1px;border-color:#fff;}.fusion-button.button-1 .fusion-button-icon-divider{border-color:#fff;}.fusion-button.button-1:hover .fusion-button-text, .fusion-button.button-1:hover i,.fusion-button.button-1:focus .fusion-button-text, .fusion-button.button-1:focus i,.fusion-button.button-1:active .fusion-button-text, .fusion-button.button-1:active{color:#fff;}.fusion-button.button-1:hover, .fusion-button.button-1:focus, .fusion-button.button-1:active{border-width:1px;border-color:#fff;}.fusion-button.button-1:hover .fusion-button-icon-divider, .fusion-button.button-1:hover .fusion-button-icon-divider, .fusion-button.button-1:active .fusion-button-icon-divider{border-color:#fff;}.fusion-button.button-1{background: #cd5856;}.fusion-button.button-1:hover,.button-1:focus,.fusion-button.button-1:active{background: #f54444;}.fusion-button.button-1{width:auto;}</style>';
		//<a class="fusion-button button-flat button-round button-large button-default button-1" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id= 5C9C8WQHZPDZL" target="_self">' . "\n";
		$_output .= '<input type="submit" name="submit" value="GO!" class="df_letter_button" />' . "\n";
		$_output .= '<div class="fusion-sep-clear"></div>' . "\n";
		$_output .= '</form>' . "\n";
		$_output .= '</div>' . "\n";
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
		$_output .= '<h2>Choose a Letter</h2>';
		$_output .= '<ul class="df_letter_list">';
		foreach ( $_letters as $_idx => $_letter ) {
			$_output .= '<li><a href="?ltr=' . intval( $_letter->ID ) . '">';
			$_output .= esc_html( $_letter->post_title );
			$_output .= '</a></li>';
		}
		$_output .= '</ul>';
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
		echo '<span class="df_fsa_label">Email: </span>' . "\n";
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

}
