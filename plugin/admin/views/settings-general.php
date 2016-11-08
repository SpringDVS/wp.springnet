<?php defined( 'ABSPATH' ) or die( 'Error' ); ?>

<form method="post" action="options.php">
<?php
settings_fields( 'springnet-general-options' );
do_settings_sections('springnet-general-options');
?>
<table class="form-table">

	
	<tr valign="top">
	<th>Hide <em>Network News</em></th>
	<td><input type="checkbox" name="springnet_news_display" value="hidden" 
		<?php if('hidden' == get_option('springnet_news_display')) echo 'checked'; ?>>
		<input type="hidden" name="updater" value="ok"> 
	</td>
	</tr>

</table>
<input type="submit" value="Save Changes" class="button button-primary">
</form>
