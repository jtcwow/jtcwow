<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://central.tech
 * @since      1.0.0
 *
 * @package    Jtcwow
 * @subpackage Jtcwow/public/partials/wmc/front
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php
    $autoJoin=get_option('wmc_auto_register','no');

    $readonly='';

    if(isset($data['referral_code'])&& $data['referral_code']!=''){

        if(isset($_POST['wrong_referral_code']) && $_POST['wrong_referral_code']=='yes'){

              $readonly=''; 

        }else{

            $readonly='readonly="readonly"'; 

        }

    }

    if($autoJoin=='yes'){

?>

        <p class="referral_code_panel form-row form-row-wide">

            <input type="hidden" name="join_referral_program" value="1">

            <input type="hidden" name="termsandconditions" value="1">

            <label for="referral_code"><?php echo apply_filters('wmc_reg_field_referral_field_name_change',__( 'Referral Code', 'wmc' ));?></label>

            <input type="text" <?php echo $readonly;?> placeholder="<?php echo __( 'Add referral code if you have', 'wmc' );?>"  class="input-text" name="referral_code" id="referral_code" value="<?php echo isset($data['referral_code'])?$data['referral_code']:''; ?>" />           

        </p>

        <p class="referral_terms_conditions form-row form-row-wide">

        <small><i><?php echo __('By registering here, you agree to the','wmc');?><a href="<?php echo esc_url( get_permalink(get_option('wmc_terms_and_conditions',0)) ); ?>" target="_blank"><?php echo __(' Terms and conditions.','wmc')?></a> </i></small>

        </p>

<?php       

    }else{

?>      <div style="display: none;">
        <p class="form-row form-row-wide"><?php if (isset($data['referral_code']) && ! empty( $data['referral_code'] )) { $data['join_referral_program'] = '1'; } else { $data['join_referral_program'] = '3'; }?>

	        <label for="option_1"><input type="radio" id="option_1" name="join_referral_program"  <?php echo isset($data['join_referral_program']) && ($data['join_referral_program'] == 0 || $data['join_referral_program'] == "1" ) ? 'checked' : ''; ?> value="1" /> <?php echo __( 'I have the referral code and want to join referral program.', 'wmc' ); ?></label>

	        <!-- <label for="option_2"><input type="radio" id="option_2" name="join_referral_program" <?php echo isset($data['join_referral_program']) && $data['join_referral_program'] == "2" ? 'checked' : ''; ?> value="2" /> <?php echo __( 'I don\'t have referral code but i wish to join referral program.', 'wmc' ); ?></label> -->

			<label for="option_3"><input type="radio" id="option_3" name="join_referral_program" <?php echo isset($data['join_referral_program']) && $data['join_referral_program'] == "3" ? 'checked' : ''; ?> value="3"  /> <?php echo __('No, I don\'t want to be a part of referral program at this time.', 'wmc' ); ?></label>	

        </p>

        <p class="referral_terms_conditions form-row form-row-wide">

            <label for="termsandconditions"><input type="checkbox" <?php echo isset($data['termsandconditions']) && $data['termsandconditions'] ? 'checked' : 'checked'; ?> name="termsandconditions" id="termsandconditions" value="1" /> <?php _e( 'I\'ve read and agree to the referral program', 'wmc' ) ?> <a href="<?php echo esc_url( get_permalink(get_option('wmc_terms_and_conditions',0)) ); ?>" target="_blank">

            <?php _e( 'terms & conditions', 'wmc' ); ?></a></label>

        </p>

        <p class="referral_code_panel form-row form-row-wide">

	        <label for="referral_code"><?php echo apply_filters('wmc_reg_field_referral_field_name_change',__( 'Referral Code', 'wmc' ));?> <span class="required">*</span></label>

	        <input type="text" <?php echo $readonly;?>  class="input-text" name="referral_code" id="referral_code" value="<?php echo isset($data['referral_code'])?$data['referral_code']:''; ?>" />

	        <small><?php echo __( '&nbsp;', 'wmc' );?></small>

        </p></div>

<?php

    }

?>
