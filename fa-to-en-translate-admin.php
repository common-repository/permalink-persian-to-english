<?php
function wp_novinweb_translate_admin(){
	add_options_page('تنظیمات مترجم پیوند یکتا', 'مترجم پیوند یکتا','manage_options', __FILE__, 'wp_novinweb_translate_page');
	add_action('admin_init','wp_novinweb_translate_register');
}
function wp_novinweb_translate_register(){
	register_setting('fatoensettings','wp_novinweb_translate_clientid');
	register_setting('fatoensettings','wp_novinweb_translate_clientsecret');
}
function wp_novinweb_translate_page(){
	function wp_novinweb_translate_reset(){
		update_option('wp_novinweb_translate_clientid','wp-fa-to-en');
		update_option('wp_novinweb_translate_clientsecret','fSKPFSZNqZnruLNfkm1oJpaBcQwx9hzCIE5o42gVqDs=');
	}
	
?>
<style>
input[type=text]
{
    direction:ltr;
    border:solid 1px #BFBDBD;
    color: #979797;
    height: 30px;
    padding:5px;
    width: 191px;
    box-shadow: 2px 2px 0 #828181 inset;
}
</style>
<div class="wrap">
	
<?php screen_icon(); ?>
<h2>ترجمه ی اتوماتیک پیوند یکتا از فارسی به انگلیسی</h2>
<p>
این افزونه به صورت اتوماتیک پیوند پس از انتشار پیوند یکتای شما را از فارسی به انگلیسی ترجمه می کنید . برای ترجمه از مترجم مایکروسافت استفاده می شود.
</p>
<form action="options.php" method="post" enctype="multipart/form-data" name="wp_novinweb_translate_form">
<?php settings_fields('fatoensettings'); ?>

<table class="form-table">
	<tr valign="top">
		<th scope="row">
			Clientid
		</th>
		<td>
			<label>
<input type="text" name="wp_novinweb_translate_clientid" value="<?php echo get_option('wp_novinweb_translate_clientid'); ?>" style="width:300px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			Clientsecret
		</th>
		<td>
			<label>
<input type="text" name="wp_novinweb_translate_clientsecret" value="<?php echo get_option('wp_novinweb_translate_clientsecret'); ?>" style="width:300px;height:24px;" />
			</label>
		</td>
	</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes'); ?>" />
</p>

</form>

<h2>این دو گزینه چیست ؟</h2>
<p>
این افزونه از api مترجم مایکروسافت ویندوز استفاده می کند . برای استفاده از این مترجم آنلاین می بایست عضو مایکروسافت بوده و کلید های انتقال داده که Clientid و Clientsecret می باشد را داشته باشیم.
<br />
اکنون شما از کلید های ما استفاده می کنید . اگر مایلید که خودتان کلید جدیدی داشته باشید لطفا از <a href="https://datamarket.azure.com/developer/applications/register" target="_blank">این آدرس</a> وارد حسابتان در مایکروسافت ویندوز شوید و نصبت به ایجاد Clientid و Clientsecret اقدام کرده و آنها را جایگزین کلید های فعلی نمایید.
<br />
هر حساب توانایی ترجمه ی 2 میلیون حرف در ماه را دارد . پس بجنبید تا حساب ما غیر فعال نشد ! <br />
<a href="http://forum.shoptalk.ir/topic/26-%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D8%AA%D8%B1%D8%AC%D9%85%D9%87-%D9%BE%DB%8C%D9%88%D9%86%D8%AF-%DB%8C%DA%A9%D8%AA%D8%A7-%D8%A7%D8%B2-%D9%81%D8%A7%D8%B1%D8%B3%DB%8C-%D8%A8%D9%87-%D8%A7%D9%86%DA%AF%D9%84%DB%8C%D8%B3%DB%8C/" target="_blank">راهنما و انجمن پشتیبانی</a>
</p>

</div>
<?php 
}
add_action('admin_menu', 'wp_novinweb_translate_admin');
?>