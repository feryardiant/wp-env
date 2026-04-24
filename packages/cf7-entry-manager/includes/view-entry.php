<?php
/**
 * View entry template.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Variables for the view.
 *
 * @var Item $item
 * @var Page_Element $elm
 */

$elm->div(
	array(
		'id'    => 'cf7em-submission-entry-viewer',
		'class' => 'wrap',
	),
	static fn ( $elm ) => $elm
	->call(
		static function () use ( $item ) {
			do_action(
				'wpcf7_admin_warnings',
				$item->id ? 'wpcf7-new' : 'wpcf7',
				wpcf7_current_action(),
				$item
			);

			do_action(
				'wpcf7_admin_notices',
				$item->id ? 'wpcf7-new' : 'wpcf7',
				wpcf7_current_action(),
				$item
			);
		},
		$item
	)

	->h1(
		array( 'class' => 'wp-heading-inline' ),
		esc_html__( 'View Form Submission', 'cf7-entry-manager' )
	)

	->hr( array( 'class' => 'wp-header-end' ) )

	->form(
		array(
			'method'   => 'post',
			'action'   => $item->url(),
			'id'       => 'wpcf7-admin-form-element',
			'disabled' => ! $item->current_user_can( 'wpcf7_edit_contact_form' ),
		),
		static fn ( $elm ) => $elm
		->call_when(
			$item->current_user_can( 'wpcf7_edit_contact_form' ),
			static function () use ( $item ) {
				wp_nonce_field( 'wpcf7-save-submission-entry_' . $item->id );
			}
		)

		->input(
			array(
				'type'  => 'hidden',
				'id'    => 'post_ID',
				'name'  => 'post_ID',
				'value' => $item->id,
			)
		)

		->input(
			array(
				'type'  => 'hidden',
				'id'    => 'hiddenaction',
				'name'  => 'action',
				'value' => 'save',
			)
		)

		->div(
			array( 'id' => 'poststuff' ),
			static fn ( $elm ) => $elm
			->div(
				array(
					'id'    => 'post-body',
					'class' => 'metabox-holder columns-2 wp-clearfix',
				),
				static fn ( $elm ) => $elm

				->div(
					array(
						'id'    => 'postbox-container-1',
						'class' => 'postbox-container',
					),
					static fn ( $elm ) => $elm
					->section(
						array(
							'id'    => 'cf7em-info',
							'class' => 'cf7em-box postbox',
						),
						static fn ( $elm ) => $elm
						->header(
							array( 'class' => 'postbox-header' ),
							static fn ( $elm ) => $elm
							->h2( child: \__( 'Info', 'cf7-entry-manager' ) )
							->div(
								array( 'class' => 'handle-actions hide-if-no-js' ),
								static fn ( $elm ) => $elm
								// Nothing for now.
							), // .handle-actions
						) // .postbox-header

						->div(
							array( 'class' => 'inside' ),
							static fn ( $elm ) => $elm
							->div(
								array( 'class' => 'cf7em-row cf7em-info' ),
								static fn ( $elm ) => $elm
								->div(
									array( 'class' => 'cf7em-col cf7em-info-field' ),
									static fn ( $elm ) => $elm
									->p( child: \__( 'Submitted', 'cf7-entry-manager' ) )
								)
								->div(
									array( 'class' => 'cf7em-col cf7em-info-value' ),
									static fn ( $elm ) => $elm
									->p( child: esc_html( $item->datetime?->format( 'Y-m-d H:i:s' ) ) )
								)
							) // .cf7em-row

							->div(
								array( 'class' => 'cf7em-row cf7em-info' ),
								static fn ( $elm ) => $elm
								->div(
									array( 'class' => 'cf7em-col cf7em-info-field' ),
									static fn ( $elm ) => $elm
									->p( child: \__( 'Form', 'cf7-entry-manager' ) )
								)
								->div(
									array( 'class' => 'cf7em-col cf7em-info-value ' . ( $item->form_id ? '' : 'cf7em-no-value' ) ),
									static fn ( $elm ) => $elm
									->p(
										child: ( $form = $item->form() ) ? esc_html( $form->title() ) : sprintf(
											'<span aria-hidden="true">—</span><span class="screen-reader-text">(%s)</span>',
											\__( 'no form', 'cf7-entry-manager' )
										)
									)
								)
							) // .cf7em-row

							->div(
								array( 'class' => 'cf7em-row cf7em-info' ),
								static fn ( $elm ) => $elm
								->div(
									array( 'class' => 'cf7em-col cf7em-info-field' ),
									static fn ( $elm ) => $elm
									->img(
										array(
											'class'   => 'avatar photo',
											'src'     => get_avatar_url( $item->author_id ),
											'loading' => 'lazy',
										)
									)
								)
								->div(
									array( 'class' => 'cf7em-col cf7em-info-value ' . ( $item->author_id ? '' : 'cf7em-no-value' ) ),
									static fn ( $elm ) => $elm
									->p(
										array( 'class' => $item->author_name ? '' : 'cf7em-no-value' ),
										$item->author_name ? esc_html( $item->author_name ) : sprintf(
											'<span aria-hidden="true">%s</span><span class="screen-reader-text">(%s)</span>',
											\__( 'Anonymous', 'cf7-entry-manager' ),
											\__( 'no author info', 'cf7-entry-manager' )
										)
									)
									->p(
										array( 'class' => $item->author_email ? '' : 'cf7em-no-value' ),
										$item->author_email ? esc_html( $item->author_email ) : sprintf(
											'<span aria-hidden="true">—</span><span class="screen-reader-text">(%s)</span>',
											\__( 'no email info', 'cf7-entry-manager' )
										)
									)
									->p(
										array( 'class' => $item->author_phone ? '' : 'cf7em-no-value' ),
										$item->author_phone ? esc_html( $item->author_phone ) : sprintf(
											'<span aria-hidden="true">—</span><span class="screen-reader-text">(%s)</span>',
											\__( 'no phone info', 'cf7-entry-manager' )
										)
									)
								)
							) // .cf7em-row
						), // .inside
					) // #cf7em-info
				) // #postbox-container-1

				->div(
					array(
						'id'    => 'postbox-container-2',
						'class' => 'postbox-container',
					),
					static fn ( $elm ) => $elm
					->section(
						array(
							'id'    => 'cf7em-entry',
							'class' => 'cf7em-box postbox',
						),
						static fn ( $elm ) => $elm
						->header(
							array( 'class' => 'postbox-header' ),
							static fn ( $elm ) => $elm
							->h2( child: \__( 'Submission Entry', 'cf7-entry-manager' ) )
							->div(
								array( 'class' => 'handle-actions hide-if-no-js' ),
								static fn ( $elm ) => $elm
									// Nothing for now.
							), // .handle-actions
						) // .postbox-header
						->div(
							array( 'class' => 'inside' ),
							static function ( $elm ) use ( $item ) {
								/**
								 * Form tag object.
								 *
								 * @var \WPCF7_FormTag $tag
								 */
								foreach ( $item->form()->scan_form_tags() as $tag ) {
									if ( 'submit' === $tag->basetype ) {
										continue;
									}

									$value     = $item->submission[ $tag->name ] ?? '';
									$has_value = '' !== $value && null !== $value;

									$elm->div(
										array(
											'class' => 'cf7em-row cf7em-submission ' . ( $has_value ? 'field-answered' : 'field-no-answer' ),
										),
										static fn ( $elm ) => $elm
										->div(
											array( 'class' => 'cf7em-col cf7em-submission-field' ),
											static fn ( $elm ) => $elm->p( child: esc_html( $tag->name ) )
										)
										->div(
											array( 'class' => "cf7em-col cf7em-submission-value cf7em-type-{$tag->basetype}" ),
											static fn ( $elm ) => match ( $tag->basetype ) {
												'tel' => $elm->p(
													child: static fn ( $elm ) => $elm
													->a( array( 'href' => 'tel:' . esc_attr( $value ) ), esc_html( $value ) )
												),

												'email' => $elm->p(
													child: static fn ( $elm ) => $elm
													->a( array( 'href' => 'mailto:' . esc_attr( $value ) ), esc_html( $value ) )
												),

												'select', 'checkbox', 'radio' => $elm->ol(
													child: static function ( $elm ) use ( $tag, $value ) {
														foreach ( $tag->values as $i => $option ) {
															$elm->li(
																array( 'class' => ( $value === $option ) ? 'selected' : '' ),
																esc_html( $option )
															);
														}
													}
												),

												'file' => $elm->p( child: esc_html( $has_value ? $value : \__( 'No file uploaded', 'cf7-entry-manager' ) ) ),

												'acceptance' => $elm->p(
													child: boolval( $value )
													? \__( 'Accepted', 'cf7-entry-manager' )
													: \__( 'Not accepted', 'cf7-entry-manager' )
												),

												default => $elm->p( child: $has_value ? $value : \__( 'No answer', 'cf7-entry-manager' ) ),
											}
										)
										->div(
											array( 'class' => 'cf7em-col cf7em-submission-info' ),
											static fn ( $elm ) => $elm
											->when(
												! empty( $tag->options ),
												static fn ( $elm ) => $elm
												->span(
													array( 'class' => 'cf7em-submission-option' ),
													static function ( $elm ) use ( $tag ) {
														$options = array_reduce(
															$tag->options,
															static function ( $carry, $option ) {
															if ( ! str_contains( $option, ':' ) ) {
																if ( 'optional' !== $option ) {
																	$carry[] = $option;
																}

																return $carry;
															}

															list( $key, $value ) = explode( ':', $option );

															$carry[] = sprintf( '%s: %s', $key, $value );

															return $carry;
															},
															array()
														);

														if ( ! str_contains( $tag->type, '*' ) ) {
															array_unshift( $options, 'optional' );
														}

														$elm->p(
															child: sprintf(
															/* translators: %s: comma-separated list of form tag options */
																esc_html__( 'Options: %s', 'cf7-entry-manager' ),
																implode( ', ', $options )
															)
														);
													}
												)
											)
											->when(
												! empty( $tag->content ),
												static fn ( $elm ) => $elm
												->p( array( 'class' => 'cf7em-submission-content' ), $tag->content )
											)
											->when(
												'quiz' === $tag->basetype,
												static fn ( $elm ) => $elm
												->p( child: \__( 'Questions', 'cf7-entry-manager' ) )
												->ol(
													child: static function ( $elm ) use ( $tag ) {
														foreach ( $tag->raw_values as $i => $option ) {
															list( $question, $answer ) = array_map( 'trim', explode( '|', $option ) );

															$elm->li(
																child: static fn ( $elm ) => $elm
																->span( child: sprintf( '%s %s', $question, $answer ) )
															);
														}
													}
												)
											)
										)
									);
								}
							}
						) // #cf7em-entry
					) // #cf7em-viewer
				) // #postbox-container-2
			) // #post-body

			->clear()
		) // #poststuff
	) // #wpcf7-admin-form-element
); // #cf7em-submission-entry-viewer.wrap

$elm->render();
