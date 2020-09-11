<!-- admin/partial/ sendex-admin-sms.php. -->

<h2><?php esc_attr_e('Send message', 'WpAdminStyle'); ?></h2>

<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	    <!-- main content -->
	    <div id="post-body-content">
		<div class="meta-box-sortables ui-sortable">
		    <div class="postbox">
			<!--<h2 class="hndle"><span><?php esc_attr_e('SEND SMS', 'WpAdminStyle'); ?></span>-->
			</h2>
			<div class="inside">
			    <h3>Available sms Credit :<?php echo get_option( 'sms_credit' ); ?></h3>
			         <form method="post" name="cleanup_options" action="" >
				<!--<label for="sms_credit">SMS CREDIT:</label>-->
				<!--<input type="text" name="sms_credit" class="regular-text" value="<?php // echo get_option( 'sms_credit' ); ?>" readonly /><br><br>-->
				<!--<input type="text" name="sender" class="regular-text" placeholder="Sender ID" required/><br><br>-->
				
				<select id="" name="role">
				         <option value="">Select Role</option>
				         <?php wp_dropdown_roles(  ); ?>
				</select><br><br>
				
				<input type="text" name="numbers" class="regular-text" placeholder="Numbers exp(01712121212,01712312312)"/><br><br>
				<p id="charcterLength"></p>
				<textarea name="message" id="message" cols="60" rows="10" placeholder="Message Body"></textarea><br><br>
				
				<input type="hidden" name="box" id="box"/>
				
				
				<input class="button-primary" type="submit" value="SEND MESSAGE" name="send_sms_message"/>
			         </form>
			</div>
			<!-- .inside -->
			
		    </div>
		    <!-- .postbox -->
		</div>
		<!-- .meta-box-sortables .ui-sortable -->
	    </div>
	    <!-- post-body-content -->
	</div>
	<!-- #post-body .metabox-holder .columns-2 -->
	<br class="clear">
    </div>
    <!-- #poststuff -->
</div> <!-- .wrap -->