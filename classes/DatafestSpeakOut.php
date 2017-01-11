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
	private $recipients = array();
	private $user_name = '';
	private $user_email = '';
	private $user_comments = '';
	private $settings = null;
	private $mode = 0;

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'custom_post_letter' ) );
	}

	function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 50, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_menu', array( $this, 'create_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_shortcode( 'speak-out', array( $this, 'web_interface' ) );
	}
	
	function wp_enqueue_scripts() {
		wp_enqueue_style( 'speakout_css', DF_LETTER_URI . 'css/styles.css' );
	}

	function get_data() {
		$this->get_settings();
		$this->letter_id = $this->get_param_from_ui( 'ltr', 'int', 0 );
		$this->get_postal();
		if ( 0 < intval( $this->letter_id ) ) {
			$this->letter = get_post( $this->letter_id );
		}
		$this->recipients = $this->get_recipients();
		//$_mode = $this->get_param_from_ui( 'df_form_submit', 'str', '' );
		if ( 0 < $this->letter_id ) {
			if ( 6 === strlen( $this->postal ) ) {
				$_mode = isset( $_POST['mode'] ) ? intval( $_POST['mode'] ) : 0;
				if ( 3 === $_mode ) {
					if ( $this->check_recaptcha() ) {
						$this->mode = 3;
					} else {
						die('captcha');
						$this->mode = 2;
					}
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

	function web_interface() {
		$this->get_data();
		$_output = '';
		$_output .= '<div class="speakout_wrapper" >' . "\n";
		$_output .= '<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">'; // better to enqueue
		$_output .= '<h1>' . esc_html( $this->settings->page_title ) . '</h1>' . "\n"; // move to settings
		$_output .= '<div class="container" >' . "\n";
		$_output .= '<div class="leftColumn">' . "\n";
		$_output .= '<img src="' . DF_LETTER_URI . 'images/speakout.png" alt="Take Action" />' . "\n";
		$_output .= '</div>' . "\n";
		$_output .= '<div class="rightColumn">' . "\n";
		$_output .= '<h3>' . esc_html( $this->settings->page_tagline ) . '</h3>' . "\n"; // move to settings
		$_output .= '<h4>' . esc_html( $this->settings->step[ $this->mode ]->summary ) . '</h4>' . "\n";
		$_output .= '</div>' . "\n";
		$_output .= '</div>' . "\n";
    $_output .= '<hr>' . "\n";
		$_output .= '<h2>' . esc_html( $this->settings->step[ $this->mode ]->title ) . '</h2>' . "\n";
		//$_mode = $this->get_param_from_ui( 'df_form_submit', 'str', '' );
		switch ( $this->mode ) {
			case 1:
				// second page: enter postal code
				$_output .= $this->display_form_postal();
				$_output .= $this->display_letter();
				break;
			case 2:
				// third page: write letter
				$_output .= $this->set_cookie( 'postal', $this->postal );
				$_output .= $this->display_form_letter();
				$_output .= $this->display_letter();
				break;
			case 3:
				// last page: send email and show next steps
				$this->get_user_form();
				//$this->save_user_data();
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
		$_output .= '</div>';
		return $_output;
	}
	
	function get_user_form() {
		$this->user_name = $this->get_param_from_ui( 'df_letter_name', 'str', '' );
		$this->user_email = $this->get_param_from_ui( 'df_letter_email', 'str', '' );
		$this->user_comments = $this->get_param_from_ui( 'df_letter_notes', 'str', '' );
	}

	function email_letter() {
		if ( ! empty( $this->recipients ) ) {
			$_headers = array();
			$_headers[] = 'From: Alliance to End Homelessness Ottawa <' . esc_attr( $this->settings->email ) . '>';
			if ( '' !== trim( $this->user_email ) ) {
				$_headers[] = 'Cc: ' . esc_attr( $this->user_name ) . ' <' . esc_attr( $this->user_email ) . '>';
			}
			$_subject = $this->letter->post_title;
			$_content = 'Dear [representative]';
			$_content .= "\n\n";
			$_content = $this->letter->post_content;
			$_content .= "\n\n";
			$_content .= $this->user_comments;
			$_content .= "\n\n";
			$_content .= $this->user_name . "\n";
			$_content .= $this->user_email . "\n";
			// send email to admin
			foreach ( $this->recipients as $_email => $_recip ) {
				$_message = str_replace( '[representative]', $_recip->name, $_content );
	/* 			wp_mail( $_email, $_subject, $_message ); */
			}
				$_message = str_replace( '[representative]', 'TESTER', $_content );
			wp_mail( $this->settings->email, $_subject, $_content );
		}
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
		$_output = '';
		if ( ! is_null( $this->letter ) ) {
			$_output .= $this->display_recipients();
			$_output .= '<div class="letter_text">';
			$_output .= '<h3>' . $this->letter->post_title . '</h3>';
			$_output .= apply_filters( 'the_content', $this->letter->post_content );
			$_output .= '</div>';
		}
		return $_output;
	}

	function display_form_postal() {
		$_output = '';
		$_output .= '<form method="POST" action="">' . "\n";
		$_output .= wp_nonce_field( DF_LETTER_URI, 'speakout_noncename', true, false );
		$_output .= '<input type="hidden" name="ltr" value="' . intval( $this->letter_id ) . '" />' . "\n";
		$_output .= '<input type="text" name="postal" value="" maxlength="7" style="max-width:150px;" border=""/>' . "\n";
		$_output .= '<input type="submit" name="submit" value="GO!" class="button-rev" />' . "\n";
		$_output .= '</form>' . "\n";
		return $_output;
	}

	function display_form_letter() {
		$_output = '';
		if ( ! is_null( $this->letter ) ) {
			$_output .= '<script src="https://www.google.com/recaptcha/api.js"></script>' . "\n";
			$_output .= '<form method="POST" action="">' . "\n";
			$_output .= wp_nonce_field( DF_LETTER_URI, 'speakout_noncename', true, false );
			$_output .= '<input type="hidden" name="ltr" value="' . intval( $this->letter_id ) . '" />' . "\n";
			$_output .= '<input type="hidden" name="postal" value="' . esc_attr( $this->postal ) . '" />' . "\n";
			$_output .= '<input type="hidden" name="mode" value="3" />' . "\n";
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
			$_output .= '<div class="g-recaptcha" data-sitekey="' . $this->settings->recaptcha_public . '"></div>';
			$_output .= '<div class="df_letter_submit">';
			$_output .= '<input type="submit" name="df_form_submit" class="df_letter_button" value="Send" />';
			$_output .= '</div>';
			//$_output .= '<div class="df_letter_print">';
			//$_output .= '<input type="button" name="df_letter_print" class="df_letter_button_inverse" value="Print" />';
			//$_output .= '</div>';
			$_output .= '</form>';
		}
		return $_output;
	}

	function display_recipients() {
		$_output = '';
		if ( ! empty( $this->recipients ) ) {
			$_output .= '<h3>Recipients: </h3>';
			$_output .= '<ul class="df_recip_list">';
			foreach ( $this->recipients as $_email => $_recip ) {
				//$this->letter->recip_groups filter by this
					$_output .= '<li>';
					$_output .= esc_html( $_recip->name );
					$_output .= ', ';
					$_output .= esc_html( $_recip->office );
					$_output .= ( '' !== trim( $_recip->party ) ) ? esc_html( ', ' . $_recip->party ) : '';
					$_output .= ' (' . esc_html( $_email ) . ')';
					$_output .= '</li>';
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
			if ( ( 200 <= $_code ) && ( 400 > $_code ) ) {
				$_json = file_get_contents( esc_url( $_url ) );
				$_data = json_decode( $_json );
				if ( isset( $_data->representatives_concordance ) ) {
					foreach ( $_data->representatives_concordance as $_key=>$_obj ) {
						$_recipients[ $_obj->email ] = $this->get_representative( $_obj );
					}
				}
				if ( isset( $_data->representatives_concordance ) ) {
					foreach ( $_data->representatives_centroid as $_key=>$_obj ) {
						$_recipients[ $_obj->email ] = $this->get_representative( $_obj );
					}
				}
			}
		}
		return $_recipients;
	}
	
	function get_representative( $_obj ) {
		$_output = new stdClass();
		$_output->name = $_obj->name;
		//$_output->photo = $_obj->photo_url;
		$_output->office = $_obj->elected_office;
		$_output->party = $_obj->party_name;
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

	function create_menu() {
		add_options_page( 'SpeakOut', 'SpeakOut', 'manage_options', 'speakout', array( $this, 'settings_page' ), '' );		//create new top-level menu
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

	function check_recaptcha() {
		// When your users submit the form where you integrated reCAPTCHA, you'll get as part of the payload a string with the name "g-recaptcha-response". In order to check whether Google has verified that user, send a POST request with these parameters:
		$_url = 'https://www.google.com/recaptcha/api/siteverify';
		$fields = array(
			'secret' => urlencode( $this->settings->recaptcha_secret ),
			'response' => urlencode( $_POST["g-recaptcha-response"] ),
			'remoteip' => '',
		);
		foreach( $fields as $key=>$value ) {
			$fields_string .= $key.'='.$value.'&';
		}
		rtrim( $fields_string, '&' );
		$_curl = curl_init();
		curl_setopt( $_curl, CURLOPT_URL, $_url );
		curl_setopt( $_curl, CURLOPT_POST, count( $fields ) );
		curl_setopt( $_curl, CURLOPT_POSTFIELDS, $fields_string );
		curl_setopt( $_curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $_curl, CURLOPT_HEADER, false );
		$_out = curl_exec( $_curl );
		curl_close( $_curl );
		if ( ! empty( $_out ) ) {
			$_result = json_decode( $_out );
			if ( isset( $_result->success ) ) {
				if ( ( bool ) $_result->success ) {
					return true;
				}
			}
		}
		return false;
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

	function get_settings() {
		$this->settings = get_option( 'speakout_settings' );
	}
	
	function settings_page() {
		$this->get_settings();
		//print_r($this->settings);
		echo '<div class="speakout_wrapper" >' . "\n";
		echo '<h2>SpeakOut Settings</h2>' . "\n";
		echo '<form method="POST" action="">' . "\n";
		wp_nonce_field( DF_LETTER_URI, 'speakout_noncename' );
		settings_fields( 'letter-writing-settings-group' );
		echo '<table class="speakout_settings">' . "\n";
		echo '<tr>' . "\n";
		echo '<td>Site Email: </td>' . "\n";
		echo '<td><input type="text" name="email" value="' . esc_attr( $this->settings->email ) . '" /></td>';
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		echo '<td>reCaptcha Public Key: </td>' . "\n";
		echo '<td><input type="text" name="recaptcha_public" value="' . esc_attr( $this->settings->recaptcha_public ) . '" /></td>';
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		echo '<td>reCaptcha Secret Key: </td>' . "\n";
		echo '<td><input type="text" name="recaptcha_secret" value="' . esc_attr( $this->settings->recaptcha_secret ) . '" /></td>';
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		echo '<td>Page Title: </td>' . "\n";
		echo '<td><input type="text" name="page_title" value="' . esc_attr( $this->settings->page_title ) . '" /></td>';
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		echo '<td>Page Tagline: </td>' . "\n";
		echo '<td><input type="text" name="page_tagline" value="' . esc_attr( $this->settings->page_tagline ) . '" /></td>';
		echo '</tr>' . "\n";
		for ( $x = 0; $x <= 3; $x ++ ) {
			echo '<tr>' . "\n";
			echo '<td>Step #' . intval( $x + 1 ) . ' Title: </td>' . "\n";
			echo '<td><input type="text" name="step' . intval( $x ) . '_title" value="' . esc_attr( $this->settings->step[ $x ]->title ) . '" /></td>';
			echo '</tr>' . "\n";
			echo '<tr>' . "\n";
			echo '<td>Step #' . intval( $x + 1 ) . ' Summary: </td>' . "\n";
			echo '<td><input type="text" name="step' . intval( $x ) . '_summary" value="' . esc_attr( $this->settings->step[ $x ]->summary ) . '" /></td>';
			echo '</tr>' . "\n";
		}
		echo '</table>' . "\n";
		submit_button();
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}

	function register_settings() {
		//register our settings but skip the API because we want them all together in one option for efficiency
	  if ( ! isset( $_POST['speakout_noncename'] ) )	{
	  	return false;
	  }
	  if ( ! wp_verify_nonce( $_POST['speakout_noncename'], DF_LETTER_URI ) ) {
	  	return false;
	  }
		$_settings = new stdClass();
		$_settings->email = $this->sanitize_settings( $_POST['email'] );
		$_settings->recaptcha_public = $this->sanitize_settings( $_POST['recaptcha_public'] );
		$_settings->recaptcha_secret = $this->sanitize_settings( $_POST['recaptcha_secret'] );
		$_settings->page_title = $this->sanitize_settings( $_POST['page_title'] );
		$_settings->page_tagline = $this->sanitize_settings( $_POST['page_tagline'] );
		$_settings->step = array();
		for ( $x = 0; $x <= 3; $x ++ ) {
			$_settings->step[ $x ] = new stdClass();
			$_settings->step[ $x ]->title = $this->sanitize_settings( $_POST[ 'step' . intval( $x ) . '_title' ] );
			$_settings->step[ $x ]->summary = $this->sanitize_settings( $_POST[ 'step' . intval( $x ) . '_summary' ] );
		}
		update_option( 'speakout_settings', $_settings, false );
	}

	function sanitize_settings( $_txt ) {
		$_txt = wp_unslash( $_txt );
		$_txt = sanitize_text_field( $_txt );
		return $_txt;
	}

	/**
		* Some phase 2 stuff maybe
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
	function save_user_data() {
		//$this->user_name = $this->get_param_from_ui( 'df_letter_name', 'str', '' );
		//$this->user_email = $this->get_param_from_ui( 'df_letter_email', 'str', '' );
		//$this->user_comments = $this->get_param_from_ui( 'df_letter_notes', 'str', '' );
		//$this->letter->recip_groups
	}
	*/
}
