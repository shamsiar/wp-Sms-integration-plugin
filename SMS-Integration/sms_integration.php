<?php

/**
 *Plugin Name: SMS Integration for Woocommerce
 *Plugin URI: https://www.unitedwebsoft.in/pawan/
 *Description: integration plugin for Woocommerce registration with OTP and SMS send on new order place
 *Author: Shams
 *Version: 1.0
 *Author URI: https://www.unitedwebsoft.in/pawan/
 */

//ad js file
add_action( 'wp_enqueue_scripts', 'custom_add_js' );

function custom_add_js() {

    wp_enqueue_script( 'phone-verification-js', plugins_url( '/js/phone_verification.js', __FILE__ ), [ 'jquery' ] );

    wp_localize_script( 'phone-verification-js', 'my_ajax_object',
        [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );
}

//End ///////

register_activation_hook( __FILE__, 'create_phone_verification_table' );
register_deactivation_hook( __FILE__, 'delete_phone_verification_table' );

function create_phone_verification_table() {
    global $wpdb;

    $wpdb->query( "CREATE TABLE IF NOT EXISTS phone_verification  (
    id int(11) NOT NULL AUTO_INCREMENT,
    phone varchar(20) NOT NULL,
    otp varchar(6) NOT NULL,
    is_verified int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (id)
    )" );
}

function delete_phone_verification_table() {
    global $wpdb;

    $wpdb->query( "DROP TABLE phone_verification " );

}

function sms_send( $mobile, $msg, $debug = 0 ) {

    $url = get_option( 'sms_api_url' );
    $uname = get_option( 'sms_api_username' );
    $pass = get_option( 'sms_api_password' );
    $sender_id = get_option( 'sms_api_sender_id' );

    $message = urlencode( $msg );

    $api = $url . "?uname=" . $uname . "&pass=" . $pass . "&send=" . $sender_id . "&msg=" . $message . "&dest=" . $mobile;

    $response = file_get_contents( $api );

    if ( $debug == 1 ) {
        echo $response;
    }

}

//Create admin menu //////////////////////////////////
add_action( 'admin_menu', 'create_sample_menu' );

function create_sample_menu() {

    //page title, menu title, capability, slug , function name
    add_menu_page( 'SMS Integration', 'SMS Integration', 'manage_options', 'sms_integration_setting', 'sms_integration_setting' );

}

function sms_integration_setting() {
    global $wpdb;

    if ( isset( $_POST['submit'] ) ) {

        $admin_phone = $_POST['admin_phone'];

        $sms_api_url = $_POST['sms_api_url'];
        $sms_api_username = $_POST['sms_api_username'];
        $sms_api_password = $_POST['sms_api_password'];
        $sms_api_sender_id = $_POST['sms_api_sender_id'];

        update_option( 'admin_phone', $admin_phone, 'yes' );

        update_option( 'sms_api_url', $sms_api_url, 'yes' );
        update_option( 'sms_api_username', $sms_api_username, 'yes' );
        update_option( 'sms_api_password', $sms_api_password, 'yes' );
        update_option( 'sms_api_sender_id', $sms_api_sender_id, 'yes' );

        echo "Successfully Updated ! ";

    }

    $admin_phone_old = get_option( 'admin_phone' );

    $sms_api_url_old = get_option( 'sms_api_url' );
    $sms_api_username_old = get_option( 'sms_api_username' );
    $sms_api_password_old = get_option( 'sms_api_password' );
    $sms_api_sender_id_old = get_option( 'sms_api_sender_id' );

    ?>
 <h1>SMS Integration Setting</h1>

 <form action="" method="post" enctype="multipart/form-data">
 <table width="100%" border="0">
   <tr>
     <td width="25%">Admin Phone to receive order SMS</td>
     <td width="75%"> <input  type="text" name="admin_phone" value="<?php echo $admin_phone_old; ?>" /> </td>
   </tr>

    <tr>
     <td><h2>SMS API Details</h2></td>
     <td> </td>
   </tr>

    <tr>
     <td> URL</td>
     <td> <input  type="text" name="sms_api_url" value="<?php echo $sms_api_url_old; ?>" /> </td>
   </tr>

   <tr>
     <td>Username</td>
     <td> <input  type="text" name="sms_api_username" value="<?php echo $sms_api_username_old; ?>" /> </td>
   </tr>
   <tr>
     <td>Password</td>
     <td> <input  type="text" name="sms_api_password" value="<?php echo $sms_api_password_old; ?>" /> </td>
   </tr>
   <tr>
     <td>Sender ID</td>
     <td> <input  type="text" name="sms_api_sender_id" value="<?php echo $sms_api_sender_id_old; ?>" /> </td>
   </tr>
   <tr>
     <td>&nbsp;</td>
     <td><input type="submit" name="submit" value="Submit" /> </td>
   </tr>
 </table>
<br /><br />


    <h1>Test SMS </h1>

    <?php

    if ( isset( $_POST['sms_test_submit'] ) ) {

        $phone_test = $_POST['phone_test'];

        $sms_msg_test = $_POST['sms_msg_test'];

        sms_send( $phone_test, $sms_msg_test, 1 );

    }
    ?>

 <form action="" method="post" enctype="multipart/form-data">
    <table width="100%" border="0">
        <tr>
            <td width="25%">Phone</td>
            <td width="75%"> <input  type="text" name="phone_test"   /> </td>
        </tr>
            <tr>
            <td width="25%">Message</td>
            <td width="75%"> <textarea name="sms_msg_test" ></textarea> </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="sms_test_submit" value="Send SMS Now" /> </td>
        </tr>
    </table>
 </form>
<?php

}

function send_otp() {
    global $wpdb;
    $table = "phone_verification";

    $phone = $_GET['phone'];

    $otp = rand( 1000, 9999 );

    $status = 1;
    $send_otp = 1;

    $wpdb->delete( $table, ['phone' => $phone], ['%s'] );

    $wpdb->insert( $table, ['phone' => $phone, 'otp' => $otp], ['%s', '%d'] );

    $message = "OTP sent to your mobile no. ";

    //Send SMS//
    if ( $send_otp == 1 ) {
        $sms_msg = "Your OTP for Registration is " . $otp . ". Thanks for joining ZEUS NUTRITIONS.";

        sms_send( $phone, $sms_msg );
    }
    ////

    return wp_send_json( [ 'status' => $status, 'message' => $message, 'phone' => $phone ] );

    wp_die();
}

add_action( 'wp_ajax_send_otp', 'send_otp' );
add_action( 'wp_ajax_nopriv_send_otp', 'send_otp' );

function check_is_valid_otp() {
    global $wpdb;
    $table = "phone_verification";

    $phone = $_GET['phone'];

    $otp = $_GET['otp'];

    $count1 = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE phone='$phone'" );

    if ( $count1 > 0 ) {
        $PhoneVarification = $wpdb->get_row( "SELECT * FROM $table WHERE phone='$phone'" );
        $is_varified = $PhoneVarification->is_verified;

        if ( $is_varified == 1 ) {
            $status = 1;
            $message = "This phone no. is already varified";
        } else {

            $count = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE phone='$phone' AND otp='$otp' " );

            if ( $count > 0 ) {
                $status = 1;
                $message = "Phone no. successfully varified";

                $wpdb->update( $table, ['is_verified' => 1], ['phone' => $phone, 'otp' => $otp], ['%d'], ['%s'] );

            } else {
                $status = 0;
                $message = "Incorrect OTP";
            }

        }

    } else {
        $status = 0;
        $message = "No record for this phone no.";
    }

    return wp_send_json( [ 'status' => $status, 'message' => $message ] );

    wp_die();

}

add_action( 'wp_ajax_check_is_valid_otp', 'check_is_valid_otp' );
add_action( 'wp_ajax_nopriv_check_is_valid_otp', 'check_is_valid_otp' );

add_action( 'woocommerce_register_form', 'woocommerce_register_form_add_phone_otp', 10, 1 );

function woocommerce_register_form_add_phone_otp() {

    ?>

<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
<label for="reg_phone"><?php esc_html_e( 'Phone', 'woocommerce' );?>&nbsp;<span class="required">*</span></label>
<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="phone" id="phone" value="<?php echo ( !empty( $_POST['phone'] ) ) ? esc_attr( wp_unslash( $_POST['phone'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>


<a href="#" style="display:none" class="send_otp btn btn-info btn-sm">Send OTP</a>
<div style="display:none" class="otp_sent"  ></div>


</p>



<p style="display:none" class="enter_otp woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
<label for="reg_phone"><?php esc_html_e( 'OTP', 'woocommerce' );?>&nbsp;<span class="required">*</span></label>
<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="otp" id="otp" /><?php // @codingStandardsIgnoreLine ?>
</p>

<?php

}

add_action( 'user_register', 'add_phone_field' );
add_action( 'personal_options_update', 'add_phone_field' );
add_action( 'edit_user_profile_update', 'add_phone_field' );
function add_phone_field( $user_id ) {
    update_user_meta( $user_id, 'billing_phone', $_POST['phone'] );
}