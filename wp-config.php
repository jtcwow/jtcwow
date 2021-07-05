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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'jtcwow_dev' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'WP_AUTO_UPDATE_CORE', false );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '7~Fu;~f$OmWbP9m9R[~ 10~{bt[AHaHjnTA*Bh**s;; gZg3<#|J1: <xi 54i:L' );
define( 'SECURE_AUTH_KEY',   'nS~nBPKY:_dk+$*LR76^A#Pompk5h3>tUU,^nX`Xq7kdNf@T_3IQt6&H`xXiU2wT' );
define( 'LOGGED_IN_KEY',     'v}Au{gU-rCT]<%rdS+h5BCq|bA,_sKWv}pFTm$OW%[Gy:@[>Sx>Pm=o>sI_*~8mJ' );
define( 'NONCE_KEY',         'NtS<`}pxqm~`lwFJ~[$9+PqHU-m[9`n78[|T2IoXIe9D49EG<s+OAZ?]_{]O#H:J' );
define( 'AUTH_SALT',         ')0pg/k{69bG,?NWW^^,lr 3j61-k5G)vMmF%Fh63lS=ZO*r(A|~IfKYOhFZFJkRC' );
define( 'SECURE_AUTH_SALT',  '@1f9^|?~W,35<KCu-IHH|2fLl/c=p )kvJQvH2-tjZ!98y!Dh7GO,KH.20O!4P)Q' );
define( 'LOGGED_IN_SALT',    'EIB0m0P6C+e.Z#r&siqxV/5D4CY 6*Ci[6Jci,gizGfHb%vpq*LkPLXIB-,-DR)D' );
define( 'NONCE_SALT',        'Brtd_jR`!aQSTgYsK+:e;c=-i3)9_SR`Cis *Rc(~kBhZxII>KNn3&X7%V[Z,)Tq' );
define( 'WP_CACHE_KEY_SALT', 'FZavgthTBmHWgBXUTcAjMXhQSosJaupBIMmggfjlHwbFYBhuhILCSyfWUEkHPxvl' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
