<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package KMA_DEMO
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) : ?>
        <h3 class="comments-title">Comments</h3><!-- .comments-title -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'kmaidx' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'kmaidx' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'kmaidx' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; // Check for comment navigation. ?>

		<ol class="comment-list" style="list-style: none; margin:1rem 0;border-bottom:1px solid #ddd;">
			<?php
				wp_list_comments( array(
					'style'      => 'ol',
					'type'       => 'comment',
					'short_ping' => true,
					'status'     => 'approve' //Change this to the type of comments to be displayed
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h3 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'kmaidx' ); ?></h3>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'kmaidx' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'kmaidx' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php
		endif; // Check for comment navigation.

	endif; // Check for have_comments().


	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'kmaidx' ); ?></p>
	<?php endif; ?>

	<?php

	$commenter = wp_get_current_commenter();
	$user = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';
	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5';

    comment_form(
		array(
			'id_form'           => 'commentform',
			'class_form'        => 'form form-horizontal',
			'id_submit'         => 'submit',
			'class_submit'      => 'btn btn-primary',
			'name_submit'       => 'submit',
			'title_reply'       => __( 'Leave a Reply' ),
			'title_reply_to'    => __( 'Leave a Reply to %s' ),
			'cancel_reply_link' => __( 'Cancel Reply' ),
			'label_submit'      => __( 'Post Comment' ),
			'format'            => 'xhtml',

			'comment_field'     =>  '<div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="sr-only" for="comment">' . _x( 'Comment', 'noun' ) . '</label>
                                                <textarea id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true" style="height: 92px;">' . '</textarea>
                                            </div>
                                        </div>',

			'fields' => apply_filters( 'comment_form_default_fields', array(
					'author' => '<div class="col-md-4">
                                    <div class="form-group">
                                    <label class="sr-only" for="author">' . __( 'Name' ) . '<span class="required">*</span></label>
                                    <input placeholder="' . __( 'Name' ) . '" id="author" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245"' . $aria_req . $html_req . ' />
					            </div>',
					'email'  => '<div class="form-group">
                                    <label class="sr-only" for="email">' . __( 'Email' ) . ' <span class="required">*</span></label>
                                    <input placeholder="' . __( 'Email' ) . '" id="email" class="form-control" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . $html_req  . ' />
                                    </div>
                                </div>',
				)
			),



			'must_log_in' => '<p class="must-log-in">' .
			                 sprintf(
				                 __( 'You must be <a href="%s">logged in</a> to post a comment.' ),
				                 '/user-login/'
			                 ) . '</p>',

			'logged_in_as' => '<p class="logged-in-as">' .
			                  sprintf(
				                  __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ),
				                  '/beachy-bucket/',
				                  $user_identity,
				                  wp_logout_url( '/' )
			                  ) . '</p>',

			'comment_notes_before' => '<p class="comment-notes">' .
			                          __( 'Your email address will not be published. All fields are required.' ) .
			                          '</p>',


		)
    );
	?>

</div><!-- #comments -->
