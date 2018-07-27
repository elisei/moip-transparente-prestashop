/**
 * 2017-2018 Moip Wirecard Brasil
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author MOIP DEVS - <prestashop@moip.com.br>
 *  @copyright  2017-2018 Moip Wirecard Brasil
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Moip Wirecard Brasil
 */

(function($){
	ChangeType = function(){
		
		jQuery("#MOIPV2_TYPE_RECEIPT").change(function() {
		 jQuery("#MOIPV2_OAUTH_PROD").val("");
		 jQuery("#MOIPV2_PUBLICKEY_PROD").val("");
		});
	}
})(jQuery);
