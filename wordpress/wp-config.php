<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('FS_METHOD', 'direct');


define('WP_SITEURL', 'http://192.168.30.179/wordpress');
define('WP_HOME', 'http://192.168.30.179/wordpress');
//define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST']);
//define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);

define('CONCATENATE_SCRIPTS', false);



/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '_V{r1@RGyE|kR;YJD1;5y-r03zV{wT|=@& #$n!aUiY.:mvn!7]ao$fE4^Cq@U7H' );
define( 'SECURE_AUTH_KEY',  'M5j}#c~l@_A&B(jzBx$Jz4)tObq;:Q~bUiUT.mVUZd=1:fm~ykwdcZAn{F!7uMt;' );
define( 'LOGGED_IN_KEY',    '0}!^I%2N5Hk]4+l+b1*7:}wRFD>-7|qrtD[_n=S(<)SNH>s VKOx20q&8SOT|T*]' );
define( 'NONCE_KEY',        'Q5fC$(PJp-0W`_5$J$l:<uj;H3R.W?4)u}6nOKRc6yuT.o=l[lUq;/0VdC=(a4|0' );
define( 'AUTH_SALT',        '(Icc[{[8L.~+~lB5Z|__O%D=S@7R3wj3,A:v[r}!C_fb!VJMjf:U##B]7SFjq&- ' );
define( 'SECURE_AUTH_SALT', '3x1eMS$eHLPmK_RK2]B?R(@xihEt)Xvvm>P)U[;2LOX~$RJ%i2y40AHdoP`S[I$q' );
define( 'LOGGED_IN_SALT',   '7MOFmJz;>FiA.-O#(!`]MArrHaiAVo=zQiJ.DA^9y:J=s|)iov<tI.;R-0MoFY0u' );
define( 'NONCE_SALT',       'u6<rnYX3=px[U%.t#x9r3nGz^CPd`S1M4dD<@J5zi:`v>%D;b{/yKVaqL{Aj+8xa' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'all_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );
//define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST']);
//define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
