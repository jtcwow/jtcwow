<?php
/**
 * @version    1.0
 * @package    urna
 * @author     Thembay Team <support@thembay.com>
 * @copyright  Copyright (C) 2019 Thembay.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: https://thembay.com
 */


function urna_child_enqueue_styles() {
	$parent_style = 'urna-style';
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'urna-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'urna_child_enqueue_styles', 10000);

function urna_child_enqueue_scripts() {
}
add_action( 'wp_enqueue_scripts', 'urna_child_enqueue_scripts' );

function urna_child_load_js() {
    ?>
    <script>
        var unc_ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}
add_action( 'wp_footer', 'urna_child_load_js' );

// save mobile number
// function urna_child_on_woocommerce_save_account_details_required_fields( $fields ) {
//     $mobile_number = ! empty( $_POST['account_mobile_number'] ) ? $_POST['account_mobile_number'] : '';

//     if ( ! empty ( $mobile_number ) ) {
//         if ( ! preg_match('/^\++[0-9]+$/', $mobile_number) ) {
//             wc_add_notice( __( 'Please fill country code in mobile number field.', 'urna-child' ), 'error' );
//         } else if ( ! preg_match('/^\++[0-9]{10,15}+$/', $mobile_number) ) {
//             wc_add_notice( __( 'Please fill correctly mobile number format.', 'urna-child' ), 'error' );
//         }
//     }
//     return $fields;
// }
// add_action( 'woocommerce_save_account_details_required_fields', 'urna_child_on_woocommerce_save_account_details_required_fields' );

// function urna_child_woocommerce_save_account_details( $user_id ) {
//     if (isset($_POST['account_mobile_number'])) {
//         $new_mobile_number = $_POST['account_mobile_number'];
//         $updated = update_user_meta( $user_id, 'mobile_number', $new_mobile_number );
//         do_action( 'urna_child_update_account_mobile_number', $updated );
//     }
// }
// add_action( 'woocommerce_save_account_details', 'urna_child_woocommerce_save_account_details' );

class UNC_Ajax_Controller {
    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $actions = array(
            array( 'name' => 'unc_test', 'function' => 'unc_test', 'nopriv' => false ),
            array( 'name' => 'submit_otp_verification_code', 'function' => 'submit_otp_verification_code', 'nopriv' => false ),
            array( 'name' => 'delete_mobile_number', 'function' => 'delete_mobile_number', 'nopriv' => false )
        );

        foreach ($actions as $action) {
            $this->add_wp_ajax( $action['name'], $action['function'], $action['nopriv'] );
        }
    }

    private function add_wp_ajax( $action, $function, $nopriv = false )
    {
        add_action( "wp_ajax_$action", array ( $this, $function ) );

        if ($nopriv)
            add_action( "wp_ajax_nopriv_$action", array ( $this, $function ) );
    }

    public function unc_test()
    {
        if (! function_exists('twl_send_sms'))
            wp_send_json_error( 'send_sms_function_not_found', 500 );

        if (! is_user_logged_in())
            wp_send_json_error( 'please_login', 401 );

        $user_id            = get_current_user_id();
        $mobile_number      = $_POST['mobile_number'] ?? '';
        $error_message      = '';

        if ( ! empty ( $mobile_number ) ) {
            if ( ! preg_match('/^\++[0-9]+$/', $mobile_number) ) {
                $error_message = __( 'Please fill country code in mobile number field.', 'urna-child' );
            } else if ( ! preg_match('/^\++[0-9]{10,15}+$/', $mobile_number) ) {
                $error_message = __( 'Please fill correctly mobile number format.', 'urna-child' );
            }

            if (! empty($error_message))
                wp_send_json_error($error_message, 400);

            $otp            = unc_account_mobile_otp($user_id);
            $send_otp       = $otp->set_mobile_number($mobile_number)->generate_new_otp()->send_sms();

            if (is_wp_error($send_otp))
                wp_send_json_error('cannot_send_message', 500);
            else
                $otp->cache_set();
                wp_send_json( __( 'Please fill verification code.', 'urna-child' ), 200 );
        }

    }

    public function submit_otp_verification_code()
    {
        $user_id = get_current_user_id();
        $otp_code = $_POST['otp_code'] ?? false;
        $account_otp = unc_account_mobile_otp($user_id);
        $account_otp->set_numberic_otp($otp_code);
        $get_otp_cache = $account_otp->get();

        if (! $otp_code)
            wp_send_json_error(__( 'Verification code cannot empty', 'urna-child' ), 400);

        if (! empty($get_otp_cache)) {
            if ( isset($get_otp_cache['otp_code']) && isset($get_otp_cache['mobile_number']) ) {
                if ($otp_code == $get_otp_cache['otp_code']) {
                    // Update usermeta mobile_number
                    update_user_meta( $user_id, 'mobile_number', $get_otp_cache['mobile_number'] );
                    wp_send_json( 'Mobile number verification successfully.', 200 );
                } else {
                    wp_send_json_error('Invalid OTP Code', 400);
                }
            } else {
                wp_send_json_error('Invalid OTP Code', 400);
            }
        } else {
            wp_send_json_error('Invalid OTP Code', 400);
        }
    }

    public function delete_mobile_number()
    {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'mobile_number', '');
            wp_send_json( 'ok', 200 );
        } else {
            wp_send_json_error('please_login', 401);
        }
    }
}

function unc_ajax_controller() {
    return new UNC_Ajax_Controller();
}

unc_ajax_controller();

class UNC_Account_Mobile_OTP {
    public      $user_id            = 0;
    public      $mobile_number      = '';
    public      $cache_prefix       = 'otp_';
    public      $cache_group        = 'unc_account_mobile_otp';
    private     $numberic_otp       = '';
    public      $otp_expire         = 3;
    public      $otp_message        = 'Your OTP is %s please fill in %s';

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function generate_new_otp()
    {
        $generator = "1357902468";
        for ($i = 1; $i <= 6; $i++) {
            $this->numberic_otp .= substr($generator, (rand()%(strlen($generator))), 1);
        }

        return $this;
    }

    public function set_numberic_otp($numberic_otp)
    {
        $this->numberic_otp = $numberic_otp;
    }

    public function set_mobile_number($mobile_number)
    {
        $this->mobile_number = $mobile_number;
        return $this;
    }

    public function get_expire()
    {
        return MINUTE_IN_SECONDS * $this->otp_expire;
    }

    public function send_sms()
    {
        $human_readable_duration = human_readable_duration( gmdate( 'i:s', $this->get_expire() ) );
        $arr_human_readable_duration = explode( ',', $human_readable_duration, 2 );
        $sms_message = sprintf($this->otp_message, $this->numberic_otp, $arr_human_readable_duration[0]);

        if ( function_exists('twl_send_sms') ) {
            $args = array(
                'number_to' => $this->mobile_number,
                'message' => $sms_message
            );
            $result = twl_send_sms( $args );

            return $result;
        } else {
            return new WP_Error( 'function-error', __( 'send_sms_function_not_found', 'urna-child' ) );
        }

    }

    public function cache_set()
    {
        $value = array(
            'otp_code' => $this->numberic_otp,
            'mobile_number' => $this->mobile_number
        );
        return wp_cache_set( $this->get_cache_key(), $value, $this->cache_group, $this->get_expire() );
    }

    public function cache_get()
    {
        return wp_cache_get( $this->get_cache_key(), $this->cache_group );
    }

    public function get_cache_key()
    {
        return $this->cache_prefix.$this->user_id.$this->numberic_otp;
    }

    public function get()
    {
        $cache = $this->cache_get();
        return $cache;
    }
}

if ( function_exists('twl_send_sms') && ! function_exists('unc_account_mobile_otp') ) :
    function unc_account_mobile_otp($user_id) {
        return new UNC_Account_Mobile_OTP($user_id);
    }
endif;