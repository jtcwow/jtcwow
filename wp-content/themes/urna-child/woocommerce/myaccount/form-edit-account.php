<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
    </p>

    <?php if ( is_plugin_active( 'wp-twilio-core/core.php' ) ) :
        $mobile_number = get_user_meta( $user->ID, 'mobile_number', true ) ?? '';
        $mobile_number_verified = get_user_meta( $user->ID, 'mobile_number', true ) ?? false; ?>
        <!-- <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_mobile_number"><?php esc_html_e( 'Mobile Number', 'woocommerce' ); ?></label>
            <input type="mobile_number" class="woocommerce-Input woocommerce-Input--mobile_number input-text" name="account_mobile_number" id="account_mobile_number" value="<?php echo $mobile_number;?>" placeholder="+66812345678" />
        </p> -->
        <div id="account-mobile-number">
            <template v-if="mobileNumberVerified">
                <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                    <label for="account_mobile_number"><?php esc_html_e( 'Mobile Number', 'woocommerce' ); ?></label>
                    <input type="mobile_number" class="woocommerce-Input woocommerce-Input--mobile_number input-text" name="account_mobile_number" id="account_mobile_number" v-model="mobileNumber" disabled/>
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                    <label for="">&nbsp;</label>
                    <button type="button" class="woocommerce-Button button" @click="deleteMobileNumber" :disabled="otpBtnDisabled">Delete</button>
                </p>
                <div class="clear"></div>
                <template v-if="otpSuccessMsg">
                    <span class="text-success">{{otpSuccessMsg}}</span>
                    <div class="clear"></div>
                </template>
            </template>
            <template v-else>
                <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                    <label for="account_mobile_number"><?php esc_html_e( 'Mobile Number', 'woocommerce' ); ?></label>
                    <input type="mobile_number" class="woocommerce-Input woocommerce-Input--mobile_number input-text" name="account_mobile_number" id="account_mobile_number" v-model="mobileNumber" placeholder="+66812345678" />
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                    <label for="">&nbsp;</label>
                    <button type="button" class="woocommerce-Button button" @click="getOtp" :disabled="otpBtnDisabled">{{otpBtnText}}</button>
                </p>
            </template>
            <div class="clear"></div>
            <template v-if="otpErrMsg">
                <span class="required">{{otpErrMsg}}</span>
                <div class="clear"></div>
            </template>
            <template v-if="showVerificationForm">
                <template v-if="otpSuccessMsg">
                    <span class="text-success">{{otpSuccessMsg}}</span>
                    <div class="clear"></div>
                </template>
                <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                    <label for="otp_verify_code"><?php esc_html_e( 'OTP Verification Code', 'woocommerce' ); ?></label>
                    <input type="otp_verify_code" class="woocommerce-Input woocommerce-Input--mobile_number input-text" name="otp_verify_code" id="otp_verify_code" v-model="verificationCode" />
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                    <label for="">&nbsp;</label>
                    <button type="button" class="woocommerce-Button button" @click="submitVerificationCode">Submit</button>
                </p>
                <div class="clear"></div>
            </template>
        </div>
        <script src="<?php echo get_stylesheet_directory_uri() . '/js/vue.js';?>"></script>
        <script src="<?php echo get_stylesheet_directory_uri() . '/js/axios.min.js';?>"></script>
        <script>
            var accountMobileNumber = new Vue({
                el: '#account-mobile-number',
                data: function(){
                    return {
                        mobileNumber: '<?php echo $mobile_number ;?>',
                        verificationCode: '',
                        mobileNumberVerified: '<?php echo $mobile_number_verified ;?>',
                        otpBtnDisabled: false,
                        otpGetBtnText: '<?php esc_html_e( 'GET OTP', 'woocommerce' ); ?>',
                        otpReSendBtnText: '<?php esc_html_e( 'RESEND OTP', 'woocommerce' ); ?>',
                        otpBtnText: '<?php esc_html_e( 'GET OTP', 'woocommerce' ); ?>',
                        otpErrMsg: '',
                        otpSuccessMsg: '',
                        showVerificationForm: false
                    }
                },
                created: function() {
                },
                methods: {
                    getOtp: async function(){
                        let form_data = new FormData;
                        form_data.append('action', 'unc_test');
                        form_data.append('mobile_number', this.mobileNumber);

                        try {
                            accountMobileNumber.otpBtnDelayer();
                            const response = await axios.post(unc_ajaxurl, form_data);
                            accountMobileNumber.showVerificationForm = true;
                            accountMobileNumber.otpSuccessMsg = response.data || '';
                        } catch(e) {
                            accountMobileNumber.otpErrMsg = e.response.data.data || '';
                        }
                    },
                    submitVerificationCode: async function(){
                        let form_data = new FormData;
                        form_data.append('action', 'submit_otp_verification_code');
                        form_data.append('otp_code', this.verificationCode);
                        try {
                            const response = await axios.post(unc_ajaxurl, form_data);
                            accountMobileNumber.mobileNumberVerified = true;
                            accountMobileNumber.showVerificationForm = false;
                            accountMobileNumber.otpSuccessMsg = response.data || '';
                        } catch(e) {
                            accountMobileNumber.otpErrMsg = e.response.data.data
                        }
                    },
                    deleteMobileNumber: async function(){
                        if(confirm('<?php echo __( 'Are you sure.', 'urna-child' );?>')){
                            let form_data = new FormData;
                            form_data.append('action', 'delete_mobile_number');
                            try {
                                const response = await axios.post(unc_ajaxurl, form_data);
                                accountMobileNumber.mobileNumberVerified = false;
                                accountMobileNumber.mobileNumber = '';
                            } catch(e) {
                            }
                        }

                    },
                    otpBtnDelayer: function(){
                        this.otpBtnDisabled = true;
                        setTimeout(() => {this.otpBtnDisabled = false; this.otpBtnText = this.otpReSendBtnText;}, 3000);
                    }
                },
                watch: {
                    mobileNumber: function(val){
                        this.otpErrMsg = '';
                    },
                    verificationCode: function(val){
                        this.otpErrMsg = '';
                        this.otpSuccessMsg = '';
                    }
                }
            })
        </script>
    <?php endif; ?>

	<fieldset>
		<legend><?php esc_html_e( 'Password change', 'woocommerce' ); ?></legend>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
		</p>
	</fieldset>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
