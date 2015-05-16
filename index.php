<?php
if(file_exists('vendor/autoload.php')){
	require 'vendor/autoload.php';
} else {
	echo "<h1>Please install via composer.json</h1>";
	echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
	echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
	exit;
}

if (!is_readable('app/core/config.php')) {
	die('No config.php found, configure and rename config.php to config.php in app/core.');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
	define('ENVIRONMENT', 'development');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if (defined('ENVIRONMENT')){

	switch (ENVIRONMENT){
		case 'development':
			error_reporting(E_ALL);
		break;

		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}

}

//initiate config
new \core\config();

//create alias for Router
use \core\router,
    \helpers\url;

//define routes
Router::any('', '\controllers\welcome@index');
Router::any('/subpage', '\controllers\welcome@subpage');

//管理员 routes
Router::post('/adminRegister', '\controllers\admin@register');
Router::post('/adminLogin', '\controllers\admin@login');
Router::any('/adminLogout', '\controllers\admin@logout');
Router::any('/adminIsLogin', '\controllers\admin@isLogin');

//用户审核 routes
Router::any('/getUserExamine','\controllers\userexamine@get');
Router::any('/userExamine','\controllers\userexamine@examine');

//用户管理 routes
Router::any('/getUser', '\controllers\useradmin@get');
Router::any('/addUser', '\controllers\useradmin@add');
Router::any('/deleteUser', '\controllers\useradmin@delete');
Router::any('/updateUser', '\controllers\useradmin@update');


//客户经理管理 routes
Router::any('/getManager', '\controllers\manageradmin@get');
Router::any('/addManager', '\controllers\manageradmin@add');
Router::any('/deleteManager', '\controllers\manageradmin@delete');
Router::any('/updateManager', '\controllers\manageradmin@update');


//产品类别 routes
Router::any('/getProductCategory', '\controllers\productcategory@get');
Router::any('/addProductCategory', '\controllers\productcategory@add');
Router::any('/deleteProductCategory', '\controllers\productcategory@delete');
Router::any('/updateProductCategory', '\controllers\productcategory@update');

//产品版本 routes
Router::any('/getProductVersion', '\controllers\productversion@get');
Router::any('/addProductVersion', '\controllers\productversion@add');
Router::any('/deleteProductVersion', '\controllers\productversion@delete');
Router::any('/updateProductVersion', '\controllers\productversion@update');

//消费类别 routes
Router::any('/getConsumptionType', '\controllers\consumptiontype@get');
Router::any('/addConsumptionType', '\controllers\consumptiontype@add');
Router::any('/deleteConsumptionType', '\controllers\consumptiontype@delete');
Router::any('/updateConsumptionType', '\controllers\consumptiontype@update');

//礼品 routes
Router::any('/getPresent', '\controllers\present@get');
Router::any('/addPresent', '\controllers\present@add');
Router::any('/deletePresent', '\controllers\present@delete');
Router::any('/updatePresent', '\controllers\present@update');

//兑换规则 routes
Router::any('/getRule', '\controllers\rule@get');
Router::any('/addRule', '\controllers\rule@add');
Router::any('/deleteRule', '\controllers\rule@delete');
Router::any('/updateRule', '\controllers\rule@update');

//积分兑换记录 routes
Router::any('getCreditExchange', '\controllers\creditexchange@get');
Router::any('deleteCreditExchange', '\controllers\creditexchange@delete');

//积分使用记录 routes
Router::any('/getCreditUsage', '\controllers\creditusage@get');

//发送短信 routes
Router::any('/sendSMS', '\controllers\sms@send');

//用户 routes
Router::any('/getUserStatus', '\controllers\user@getUserStatus');
Router::any('/userRegister', '\controllers\user@register');
Router::any('/userLogin', '\controllers\user@login');
Router::any('/creditExchange', '\controllers\user@creditExchange');

//if no route found
Router::error('\core\error@index');

//turn on old style routing
Router::$fallback = false;

//execute matched routes
Router::dispatch();
