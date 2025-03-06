/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
export default function save({ attributes }) {
	const { nameLabel, emailLabel, messageLabel, submitButtonText } = attributes;
	
	return (
		<div { ...useBlockProps.save() }>
			<div className="contact-form-container">
				<form className="contact-form" data-contact-form>
					<div className="form-field">
						<label htmlFor="contact-name">{nameLabel}</label>
						<input 
							type="text" 
							id="contact-name" 
							name="contact-name" 
							required 
						/>
					</div>
					<div className="form-field">
						<label htmlFor="contact-email">{emailLabel}</label>
						<input 
							type="email" 
							id="contact-email" 
							name="contact-email" 
							required 
						/>
					</div>
					<div className="form-field">
						<label htmlFor="contact-message">{messageLabel}</label>
						<textarea 
							id="contact-message" 
							name="contact-message" 
							required
						></textarea>
					</div>
					<div className="form-field">
						<button type="submit" className="submit-button">{submitButtonText}</button>
					</div>
					<div className="form-message" data-form-message></div>
				</form>
			</div>
		</div>
	);
}
