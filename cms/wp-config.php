<?php
/**
 * The base configurations of the WordPress.
 *
 * このファイルは、MySQL、テーブル接頭辞、秘密鍵、言語、ABSPATH の設定を含みます。
 * より詳しい情報は {@link http://wpdocs.sourceforge.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86
 * wp-config.php の編集} を参照してください。MySQL の設定情報はホスティング先より入手できます。
 *
 * このファイルはインストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さず、このファイルを "wp-config.php" という名前でコピーして直接編集し値を
 * 入力してもかまいません。
 *
 * @package WordPress
 */

// 注意:
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.sourceforge.jp/Codex:%E8%AB%87%E8%A9%B1%E5%AE%A4 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - こちらの情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', 'wordpress');

/** MySQL データベースのユーザー名 */
define('DB_USER', 'root');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'bmgcz9mw');

/** MySQL のホスト名 */
define('DB_HOST', 'localhost');

/** データベースのテーブルを作成する際のデータベースのキャラクターセット */
define('DB_CHARSET', 'utf8');

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'J,/US.K `|JZpKjLLBAk(`@HT$_MKSh~Mj>Owi),--jXa:+B^7&/NVo`>fN+1jJt');
define('SECURE_AUTH_KEY',  '_$93xzHYmU[??^3O,gtBrP@0:=itP xH_LH|!kO0Y?zM~)m$d)c&w[AzF3?r6kc8');
define('LOGGED_IN_KEY',    'eRgdtuyeQ]H}=C~ba5O|O+<qq2XqaSc;0U7d?2_WC+k]l)cw0]>_X%|~-S/O;PD?');
define('NONCE_KEY',        'x|=S!BMo0X:pCq>UDs1@WJg0Z1A,?i_!g7XA2P$KFj66#axJ}OEYu6c1GYd+t?(^');
define('AUTH_SALT',        'Xa~3UdV7yb<+Ms|&)fHk!Uc~8+Y4_g=77ev9$Y6Ht{pjWRXR&EroZ?=z[0v=y-9=');
define('SECURE_AUTH_SALT', '96~.|YIa`j#.)pPWJaahZj~NaYiZ$4rpv[->[+J;X-^77*sZxD}h(p2n+Q,x]hfs');
define('LOGGED_IN_SALT',   '_nv[t @cg+e*^`11`Jj5vD*E2Y/R;P#T&Xi3mXnA&4wxx=e0e%++L6NtJ,Fau0T$');
define('NONCE_SALT',       '7gW{p-NA}p2RVY_[[Im_O_+?<@?Q4<a|#m(`/2H%o [w?vZMNJ0JQY*BIg/LI=d&');


/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'wp_';

/**
 * ローカル言語 - このパッケージでは初期値として 'ja' (日本語 UTF-8) が設定されています。
 *
 * WordPress のローカル言語を設定します。設定した言語に対応する MO ファイルが
 * wp-content/languages にインストールされている必要があります。例えば de_DE.mo を
 * wp-content/languages にインストールし WPLANG を 'de_DE' に設定することでドイツ語がサポートされます。
 */
define('WPLANG', 'en');

/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 */
define('WP_DEBUG', false);


define('WP_CACHE', false);


/*=== Other Settings
==============================================================================================*/
/*	Limit the Maximum Number of Revisions
-----------------------------------------------*/
define( 'WP_POST_REVISIONS', 5 );

/*	Change the Autosave Interval
-----------------------------------------------*/
define( 'AUTOSAVE_INTERVAL', 10 * 60 );


/*=== Path / Directory
==============================================================================================*/
//define( 'WP_CONTENT_DIR', $_SERVER[ 'DOCUMENT_ROOT' ] . '/blog/wp-content' );
//define( 'WP_CONTENT_URL', 'http://example/blog/wp-content');
//define('WP_LANG_DIR', $_SERVER['DOCUMENT_ROOT'].'wordpress/languages');

/*
WP_CONTENT_URL
($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/'

http://www.creasty.dev/cms/wp-content
WP_LANG_DIR
WWWROOT. '/cms/wp-content/languages'

*/
/*=== FTP
==============================================================================================*/
/*
define('FS_METHOD', 'ftpext');
define('FTP_BASE', '/path/to/wordpress/');
define('FTP_CONTENT_DIR', '/path/to/wordpress/wp-content/');
define('FTP_PLUGIN_DIR ', '/path/to/wordpress/wp-content/plugins/');
define('FTP_PUBKEY', '/home/username/.ssh/id_rsa.pub');
define('FTP_PRIKEY', '/home/username/.ssh/id_rsa');
define('FTP_USER', 'username');
define('FTP_PASS', 'password');
define('FTP_HOST', 'ftp.example.org');
define('FTP_SSL', false);
*/


/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');


/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

?>