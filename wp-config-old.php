<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache




/** Enable W3 Total Cache Edge Mode */
define('W3TC_EDGE_MODE', true); // Added by W3 Total Cache

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'y90health2015');

/** MySQL database username */
define('DB_USER', 'young90health');

/** MySQL database password */
define('DB_PASSWORD', 'young0291');

/** MySQL hostname */
define('DB_HOST', 'young90health.chxw3sngrvwf.us-east-1.rds.amazonaws.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '1i{`&_iL-tB2dIRnE7+PX!,XPiHuX*A]/ThdeUF$B%4tyuR{Jgr!6^hXC,?N 17^');
define('SECURE_AUTH_KEY',  '-0x,R{(+f()vo=V5LOPTOHX!sUCh_R!|u`m:GZ_#iR/=gLvy?H}n.U=E V4Qvztq');
define('LOGGED_IN_KEY',    '4,-R+l6<[#bDlSmwRxf49nQSa<p=c?g>Z[LX*iZeD(V+`JZw.ozAK/=LT~+ $5xY');
define('NONCE_KEY',        '.{#A%[ht<r(F5Tn=U8[=@M?B!H@<.I >nrQAYvg:>JSXA!?eBZC~)Q9C#vRfFM');
define('AUTH_SALT',        '9Q&+I;Vh$]}}*8P,CQM;RAxq*w6H4)m3E%|U8?z3y(=Nx2CHSX2/R]1`x;=ZIFfc');
define('SECURE_AUTH_SALT', '6VRHn1tX *_rS9Trs1#%Qd+m}xpsjIf8(j0XU{/MJJqe/ev5te,-s7f%?}ehwsyR');
define('LOGGED_IN_SALT',   'k)DTM4plHI-$%yee@OvK*Ft3Yzs*8?gB3AY@nq|6O-}sGMkz#c88H%zQuIccC]!4');
define('NONCE_SALT',       'p~<r*kx8kYXlmQwa&8/C^z}@g&^X<4jt0W-3MpG|,:,&17s.(!%1N+X[,*RsKKb3');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'nfx_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** LEADGENIX CHANGE CONTENT FOLDERNAME **/	
define('WP_CONTENT_FOLDERNAME', 'assets');
define ('WP_CONTENT_DIR', ABSPATH . WP_CONTENT_FOLDERNAME) ;
//define('WP_SITEURL', 'https://' . $_SERVER['HTTP_HOST'] . '/');
define('WP_SITEURL', 'https://www.young90health.com/');
define('WP_CONTENT_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/' . WP_CONTENT_FOLDERNAME);

#define('WP_HTTP_BLOCK_EXTERNAL', false);//Disable File Edits
#define('DISALLOW_FILE_EDIT', false);
define( 'WP_MEMORY_LIMIT', '128M' );


/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');