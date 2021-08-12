# Assets Version

### Description

Helps with versioning of CSS and JS files. Plugin [adds](https://github.com/innocode-digital/wp-flush-cache#documentation)
flush buttons to admin area for version upgrading.
**Requires** [Flush Cache Buttons](https://github.com/innocode-digital/wp-flush-cache) plugin.

### Install

- Preferable way is to use [Composer](https://getcomposer.org/):

    ````
    composer require innocode-digital/wp-assets-version
    ````

  By default, it will be installed as [Must Use Plugin](https://codex.wordpress.org/Must_Use_Plugins).
  It's possible to control with `extra.installer-paths` in `composer.json`.

- Alternate way is to clone this repo to `wp-content/mu-plugins/` or `wp-content/plugins/`:

    ````
    cd wp-content/plugins/
    git clone git@github.com:innocode-digital/wp-assets-version.git
    cd wp-assets-version/
    composer install
    ````

If plugin was installed as regular plugin then activate **Assets Version** from Plugins page
or [WP-CLI](https://make.wordpress.org/cli/handbook/): `wp plugin activate wp-assets-version`.

### Usage

There are known functions [wp_enqueue_script](https://developer.wordpress.org/reference/functions/wp_enqueue_script/) and
[wp_enqueue_style](https://developer.wordpress.org/reference/functions/wp_enqueue_style/) in WordPress core to register
and enqueue scripts and styles. In both of them [4th parameter](https://developer.wordpress.org/reference/functions/wp_enqueue_script/#parameters)
is for version number and in ideal case it should be version of build or theme or `null` with some version hash in filename ... 
but sometimes it's hard to implement or there are could be particular issues in caching, and it's where this plugin becomes
as a good **hotfix**. There are few ways to add version with this plugin:

- Retrieve version through function `innocode_assets_version()` and set as 4th parameter in enqueue functions, e.g.:

  ````
  $ver = function_exists( 'innocode_assets_version' ) ? innocode_assets_version() : false;
  
  wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
  ````
  
- Force using of version where `false` or `null` set as 4th parameter:

  ````
  add_filter( 'innocode_assets_version_allow_default', '__return_true' );
  ````
  
- Force using of version but with own logic per dependency (requires previous hook to be set), e.g.:

  ````
  /**
   * @param bool $allow
   * @param string $type - One of [ 'script', 'style' ].
   * @param _WP_Dependency $dependency
   */
  add_filter( 'innocode_assets_version_allow_dependency', function ( bool $allow, string $type, _WP_Dependency $dependency ) {
      // @TODO: implement logic

      return $allow;
  }, 20, 3 );
  ````
