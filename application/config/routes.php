<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "auth";
$route['404_override'] = '';
$route['c2/(:any)/(:any)'] = "/tracking/redirect/$1/$2"; // direct campaign click
$route['c3/(:any)/(:any)'] = "/tracking/redirect_with_macros/$1/$2"; // direct campaign click
$route['bidder'] = "v2/bidder/request"; // direct campaign click

$route['content/(:any)'] = "/content/index/$1";
$route['r/(:any)'] = "/campclick/random/$1"; // redirect (normal creative)
$route['c/(:any)/(:any)'] = "/campclick/redirect/$1/$2"; // direct campaign click
$route['s/(:any)'] = "/campclick/shape/$1"; // traffic shaping
$route['i/(:any)'] = "/campclick/impression/$1"; //impression retargeting


/* End of file routes.php */
/* Location: ./application/config/routes.php */