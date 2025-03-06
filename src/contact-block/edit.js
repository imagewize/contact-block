/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const { 
		nameLabel, 
		emailLabel, 
		messageLabel, 
		submitButtonText, 
		successMessage,
		emailTo
	} = attributes;

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={__('Form Settings', 'contact-block')}>
					<TextControl
						label={__('Name Field Label', 'contact-block')}
						value={nameLabel}
						onChange={(value) => setAttributes({ nameLabel: value })}
					/>
					<TextControl
						label={__('Email Field Label', 'contact-block')}
						value={emailLabel}
						onChange={(value) => setAttributes({ emailLabel: value })}
					/>
					<TextControl
						label={__('Message Field Label', 'contact-block')}
						value={messageLabel}
						onChange={(value) => setAttributes({ messageLabel: value })}
					/>
					<TextControl
						label={__('Submit Button Text', 'contact-block')}
						value={submitButtonText}
						onChange={(value) => setAttributes({ submitButtonText: value })}
					/>
					<TextControl
						label={__('Success Message', 'contact-block')}
						value={successMessage}
						onChange={(value) => setAttributes({ successMessage: value })}
					/>
					<TextControl
						label={__('Email To (optional)', 'contact-block')}
						value={emailTo}
						onChange={(value) => setAttributes({ emailTo: value })}
						help={__('Leave empty to use admin email', 'contact-block')}
					/>
				</PanelBody>
			</InspectorControls>

			<div className="wp-block-create-block-contact-block-preview">
				<h4>{__('Contact Form Preview', 'contact-block')}</h4>
				<form className="contact-form-preview">
					<div className="form-field">
						<label>{nameLabel}</label>
						<input type="text" placeholder={__('John Doe', 'contact-block')} disabled />
					</div>
					<div className="form-field">
						<label>{emailLabel}</label>
						<input type="email" placeholder={__('johndoe@example.com', 'contact-block')} disabled />
					</div>
					<div className="form-field">
						<label>{messageLabel}</label>
						<textarea placeholder={__('Your message here...', 'contact-block')} disabled></textarea>
					</div>
					<div className="form-field">
						<button type="button" className="submit-button">{submitButtonText}</button>
					</div>
				</form>
			</div>
		</div>
	);
}
