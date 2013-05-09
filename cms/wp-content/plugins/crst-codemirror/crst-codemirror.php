<?php
/**
 * Plugin Name: Creasty CodeMirror for WordPress
 * Plugin URI:
 * Description: Syntax highlighting in WordPress post, theme, and plugin editor
 * Author: Yuuki Iwanaga
 * Author URI: http://yuuki.creasty.com/
 * Version: 1.0
 * Requires at least: 3.3
 * Tested up to: 3.3
 * Stable tag: 1.0
 **/

if( !defined( 'ABSPATH' ) )
	die( 'You are not allowed to call this page directly.' );

// Bug
// http://wordpress.org/support/topic/trouble-using-get_current_user_id
require_once( ABSPATH . 'wp-includes/pluggable.php' );

define( 'CODEMIRROR_LIBS', plugins_url( '/lib/', __FILE__ ) );

class wp_codemirror{
	private $rich_editing;

	public function __construct(){
		$this->rich_editing = get_user_option( 'rich_editing', get_current_user_id() ) == 'true';

		if($this->rich_editing)
			return;

		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_head', array( &$this, 'admin_head' ) );
		add_action( 'admin_footer', array( &$this, 'admin_footer' ) );
	}
	private function is_editor(){
		global $pagenow;

		if(
			$pagenow == 'post.php'
			|| $pagenow == 'post-new.php'
			|| $pagenow == 'theme-editor.php'
			|| $pagenow == 'plugin-editor.php'
		)
			return true;

		return false;
	}

	public function admin_init(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-resizable' );

		add_action( 'edit_form_advanced', array( &$this, 'add_input_field' ) );
		add_action( 'edit_page_form', array( &$this, 'add_input_field' ) );
		add_action( 'save_post', array( &$this, 'preserve_scrollpos' ) );

	}

	public static function add_input_field(){
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		$post_id = (int) $_GET['post'];
		$id = 'content';

		$data = safe_value( get_user_meta( $user_id, '_crst_scrollpos', true ), array(), 'array' );

		if( $data['post_id'] == $post_id )
			$pos = (int) $data[ $id ];
		else
			$pos = 0;

		printf( '<input type="hidden" id="crst_scrollpos_%s" name="crst_scrollpos[%1$s]" value="%d"/>', $id, $pos );
		echo '<input type="hidden" name="crst_scrollpos_nonce" value="', wp_create_nonce( 'preserve_scrollpos' ) ,'"/>';
	}
	public function preserve_scrollpos( $post_id ){
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if( !wp_verify_nonce( $_POST[ 'crst_scrollpos_nonce' ], 'preserve_scrollpos' ) )
			return $post_id;

		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		$data = safe_value( $_POST[ 'crst_scrollpos' ], array(), 'array' );
		$data['post_id'] = $post_id;

		update_user_meta( $user_id, '_crst_scrollpos', $data );
	}

	public function admin_head(){
		if( !$this->is_editor() )
			return;

		?>
			<link rel="stylesheet" href="<?php echo CODEMIRROR_LIBS; ?>codemirror.css" />
			<link rel="stylesheet" href="<?php echo CODEMIRROR_LIBS; ?>util/dialog.css" />
			<link rel="stylesheet" href="<?php echo CODEMIRROR_LIBS; ?>util/simple-hint.css" />
			<script src="<?php echo CODEMIRROR_LIBS; ?>codemirror.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>xml.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>javascript.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>css.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>htmlmixed.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>clike.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>php.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>util/dialog.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>util/javascript-hint.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>util/simple-hint.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>util/search.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>util/searchcursor.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>util/match-highlighter.js"></script>
			<script src="<?php echo CODEMIRROR_LIBS; ?>util/zen_codemirror.min.js"></script>
			<style>
				#wp-content-editor-container .CodeMirror,
				#wp-content-editor-container .CodeMirror-scroll{
					height: 600px;
				}
				#template{
				    margin-right: 190px;
				}
				#template div{
					margin-right: 0 !important;
				}
				#ed_toolbar{ display: none; }
				.CodeMirror-scroll{
					overflow: auto;
					margin-right: 0 !important;
				}

				.plugin-editor-php .CodeMirror,
				.plugin-editor-php .CodeMirror-scroll,
				.theme-editor-php .CodeMirror,
				.theme-editor-php .CodeMirror-scroll{
					height: 600px;
				}

				#content_ifr{
					height: 600px !important;
				}

				#crst_headcode .inside{
					padding: 0;
					margin: 0;
				}
			</style>

			<script>
				var cm_editors = {};

				function runEditorHighlighter( id, mode, onChange ){
					var $scroller, isComplete = false, $scrollto = jQuery( '#crst_scrollpos_' + id );

					var editor = cm_editors[ id ] = CodeMirror.fromTextArea(
						document.getElementById( id ),
						{
				        	mode           : mode || 'application/x-httpd-php',
							tabMode        : 'indent',
							matchBrackets  : true,
							indentUnit     : 4,
							indentWithTabs : true,
							tabMode        : 'shift',
							enterMode      : 'keep',
							lineNumbers    : true,
							lineWrapping   : true,
							fixedGutter    : true,
							onHighlightComplete: function( cm ){
								if( !isComplete ){
									$scroller = jQuery( cm.getScrollerElement() );
									$scroller.scrollTop( parseInt( $scrollto.val() ) );
									isComplete = true;
								}
							},
							onScroll: function( cm ){
								if( isComplete ){
									var pos = $scroller.scrollTop();
									$scrollto.val( pos );
								}
							},
							onCursorActivity: function( cm ){
								cm.setLineClass( hlLine, null, null );
								hlLine = cm.setLineClass( cm.getCursor().line, null, 'activeline' );
							},
							onChange: function( cm ){
								cm.save();

								onChange && onChange( cm );
							},
							onGutterClick: function( cm, line ){
								cm.setSelection( { line: line, ch: 0 }, { line: line + 1, ch: 0 } );
							},
							onKeyEvent: function( cm, e ){
								return zen_editor.handleKeyEvent.apply( zen_editor, arguments );
							},
							extraKeys: {
								'Ctrl-Enter': function( cm ){
									var cur = cm.getCursor();
									cur.ch += 6;
									cm.replaceSelection( '<br />' );
									cm.setCursor( cur );
								},
								'Cmd-S': function( cm ){
									cm.save();
									jQuery( '#post' ).submit();
								},
								'Tab': function( cm ){
									if( cm.somethingSelected() )
										CodeMirror.commands.indentMore( cm );
									else
										CodeMirror.commands.insertTab( cm );
								},
								'Shift-Tab': function( cm ){
									if( cm.somethingSelected() )
										CodeMirror.commands.indentLess( cm );
									else
										CodeMirror.commands.indentAuto( cm );
								},
								'Ctrl-O': 'newlineAndIndent',
								'Ctrl-J': 'newlineAndIndent'
							}
						}
					);
					var hlLine = editor.setLineClass( 0, 'activeline' );

					return editor;
				}


				var cm_lastPos = null, cm_lastQuery = null, cm_marked = [];

				function cm_util_unmark(){
					for( var i = 0; i < cm_marked.length; ++i )
						cm_marked[ i ].clear();

					cm_marked.length = 0;
				}

				function cm_util_search(){
					cm_util_unmark();

					var editor = cm_editors[ 0 ],
						text = jQuery( '#cm_util_query' ).val();

					if( !text )
						return;

					for( var cursor = editor.getSearchCursor( text ); cursor.findNext(); )
						cm_marked.push( editor.markText( cursor.from(), cursor.to(), "searched" ) );

					if( cm_lastQuery != text )
						cm_lastPos = null;

					var cursor = editor.getSearchCursor( text, cm_lastPos || editor.getCursor() );

					if( !cursor.findNext() ){
						cursor = editor.getSearchCursor( text );
						if( !cursor.findNext() )
							return;
					}

					editor.setSelection( cursor.from(), cursor.to() );
					cm_lastQuery = text;
					cm_lastPos = cursor.to();
				}

				function cm_util_replace(){
					cm_util_unmark();

					var editor = cm_editors[ 0 ],
						text = jQuery( '#cm_util_query' ).val(),
						replace = jQuery( '#cm_util_replace_str' ).val();

					if( !text )
						return;

					var cursor = editor.getSearchCursor( text );
					cursor.findNext();

					if( !cursor )
						return;

					editor.replaceRange( replace, cursor.from(), cursor.to() );
					editor.setSelection( cursor.from(), cursor.to() );
				}

				function cm_util_replace_all(){
					cm_util_unmark();

					var editor = cm_editors[ 0 ],
						text = jQuery( '#cm_util_query' ).val(),
						replace = jQuery( '#cm_util_replace_str' ).val();

					if( !text )
						return;

					for( var cursor = editor.getSearchCursor( text ); cursor.findNext(); )
						cursor.replace( replace );

				}

				function cm_util_clear_result(){
					cm_lastQuery = null;
					cm_lastPos = null;
					cm_util_unmark();
					jQuery( '#cm_util_query' ).val( '' );
					jQuery( '#cm_util_replace_str' ).val( '' );
				}

			</script>

		<?php

	}


	public function admin_footer(){
		if( !$this->is_editor() )
			return;

		global $pagenow;

		if( $pagenow == 'post.php' || $pagenow == 'post-new.php' ):

		?>

			<script>
				jQuery( window ).load(function(){
					var counter = jQuery( '#wp-word-count > span.word-count' );

					var editor = cm_editors[ 0 ] = runEditorHighlighter( 'content', null, function( cm ){
						counter.text( cm.getTextArea().value.length );
					});

					counter.text( editor.getTextArea().value.length );

					window.send_to_editor = function( html ){
						tb_remove();
						editor.replaceSelection( html );
					};

					jQuery( '#qt_content_fullscreen' ).click(function(){
						if( cm_editors[ 0 ] )
							cm_editors[ 'content' ].toTextArea();

						fullscreen.switchmode( 'html' );
						setTimeout( 'runEditorHighlighter( "wp_mce_fullscreen" )', 2000 );
					});
					jQuery( '#wp-fullscreen-close' ).click(function(){
						fullscreen.off();
						runEditorHighlighter( 'content' );
						return false;
					});

					if( jQuery( '#crst_headcode_txt' ).length == 1 )
						runEditorHighlighter( 'crst_headcode_txt' );
				});

			</script>

		<?php

			return;

		endif;

		if( isset( $_GET[ 'file' ] ) ){
			$extension = strtolower( pathinfo( esc_html( $_GET[ 'file' ] ), PATHINFO_EXTENSION ) );
		}elseif( $pagenow == 'theme-editor.php' ){
			$extension = 'css';
		}elseif( $pagenow == 'plugin-editor.php' ){
			$extension = 'php';
		}

		switch( $extension ){
			case 'css':
				$mode = 'text/css';
				break;
			case 'html':
			case 'htm':
				$mode = 'text/html';
				break;
			case 'js':
				$mode = 'text/javascript';
				break;
			case 'php':
				$mode = 'application/x-httpd-php';
				break;
			default:
				$mode = 'text/plain';
				break;
		}

		?>
			<script>

				jQuery( window ).load(function(){
					cm_editors[ 0 ] = runEditorHighlighter( 'newcontent', '<?php echo $mode; ?>' );

					var $ = jQuery;

					$( '#newcontent' ).after(
						$( '<input/>' ).attr({
							'type': 'text',
							'size': '12',
							'id': 'cm_util_query'
						}),

						$( '<button/>' )
							.addClass( 'button' )
							.attr( 'type', 'button' )
							.click( cm_util_search )
							.text( '検索' ),

						$( '<input/>' ).attr({
							'type': 'text',
							'size': '12',
							'id': 'cm_util_replace_str'
						}),

						$( '<button/>' )
							.addClass( 'button' )
							.attr( 'type', 'button' )
							.click( cm_util_replace )
							.text( '置換' ),

						$( '<button/>' )
							.addClass( 'button' )
							.attr( 'type', 'button' )
							.click( cm_util_replace_all )
							.text( 'すべて置換' ),

						$( '<button/>' )
							.addClass( 'button' )
							.attr( 'type', 'button' )
							.click( cm_util_clear_result )
							.text( 'クリア' )
					);
				});
			</script>

		<?php

	}

}

if( is_admin() )
	new wp_codemirror();


?>
