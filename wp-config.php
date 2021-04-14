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
define( 'DB_NAME', 'graduate' );

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

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'DXaCg;{7XXsWk0d>/eC^B6__[YJR/LTer~A8-6y.w]Kv^[57uM9w}IXRs9mgDX<k' );
define( 'SECURE_AUTH_KEY',  '?Dh{hC`GPV,BjKzj9%I=;&2B+/IKBJdJ#P=R5C9!Ab+zSq{Y!K<Iww%6Z-D)@<HR' );
define( 'LOGGED_IN_KEY',    'nyde,qN^%0hG^oS4l+ReeGi10XD&9|bJ&B?K`I$-a=41o>Dnc[VfpFGOJ,7)is>s' );
define( 'NONCE_KEY',        'DzG&hiQ?QCMk| .Br=C. :[*?$~-XpoklkdBl/PE$M5TY|=Uz+D<8e6+:{J!`-2d' );
define( 'AUTH_SALT',        '3sOb>s`twIhF3%q/et>(;OvD~l)#n+Tz2e=m@E!t]<*tQ4VSRGMK=h:g_vup5 ;B' );
define( 'SECURE_AUTH_SALT', 'Ze+~M2<x#R$%A|Yk{we8v?O-4drp.$N@R(>,|8{8$,zSuil,jZ-}*)EOP#X1s^+|' );
define( 'LOGGED_IN_SALT',   'Qx4YYe#sDJmT8VLYQgg,Kb4%z&8R8j}xDJlg7>}  _3=`c1Ykvj*#V.zBtL^?D=>' );
define( 'NONCE_SALT',       ',]i>wSz3hsJk7K!WO($$Yb-vl(0L61>As?O}V!Xuf|;TZ;FC$[W Vpo`xQCVbh8-' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
