# Unobtrusive Admin Bar

_The Unobtrusive Admin Bar plugin for WordPress allows you to hide the admin bar for signed in users, but retain access to the features. Simply move the cursor to the top of your website and the admin bar will appear. Now you can keep the accessibility of the WordPress admin bar without compromising the appearance of your website._

### Installation

Upload the plugin to your WordPress plugins directory, or use the dashboard to 'Add New' plugins and activate it. That's it!

#### Filters

There are a couple of filters available to make adjustments for your site. These may be added to an options panel in a future release.

**uab_responsive_break_point**
* This passes a numeric value. The admin-bar is shown automatically on smaller screens because an auto-hide effect on hover would not work with touch devices. The default value is `783` which is the current WordPress break point for showing the mobile styling for the admin bar.

Sample use:

	function my_adminbar_breakpoint( $break )
		$break = 960;
		return $break;
	}
	add_filter('uab_responsive_break_point', 'my_adminbar_breakpoint');

**uab_add_top_margin**
* Optionally adds back the margin to `<html>` and `<body>` for the admin bar. The result is the page content pushes down during the display of the admin bar. Due to the positioning of headers and menus with fixed and top docked styles this often has no effect. The allowed values are (boolean) true, false and (string) 'top'. The default setting is `false`. The 'top' value will apply the margin only when the user has not scrolled.

Sample use:

	function my_adminbar_margin( $option )
		$option = true; // false (default), true or 'top'
		return $option;
	}
	add_filter('uab_add_top_margin', 'my_adminbar_margin');

### Changelog

**1.0.0**
*Release Date - 10 July 2015*
* Initial Release
