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
import {
	InspectorControls,
	useBlockProps,
	PanelColorSettings,
} from '@wordpress/block-editor';

import { TextControl, PanelBody } from '@wordpress/components';

import LabelComponent from './components/LabelComponent';

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
export default function Edit( { attributes, setAttributes } ) {
	const {
		labelTextColor,
		labelTextColorChecked,
		labelTextIncl,
		labelTextExcl,
	} = attributes;

	const { originalTaxDisplay } = window.wtsEditorObject || {
		originalTaxDisplay: 'incl',
	};

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody
					title={ __( 'Labels', 'tax-switch-for-woocommerce' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __(
							'Including tax label',
							'tax-switch-for-woocommerce'
						) }
						value={ labelTextIncl }
						onChange={ ( value ) =>
							setAttributes( { labelTextIncl: value } )
						}
					/>
					<TextControl
						label={ __(
							'Excluding tax label',
							'tax-switch-for-woocommerce'
						) }
						value={ labelTextExcl }
						onChange={ ( value ) =>
							setAttributes( { labelTextExcl: value } )
						}
					/>
				</PanelBody>
				<PanelColorSettings
					title={ __( 'Label colors', 'tax-switch-for-woocommerce' ) }
					initialOpen={ true }
					colorSettings={ [
						{
							value: labelTextColor,
							onChange: ( color ) =>
								setAttributes( {
									labelTextColor: color,
								} ),
							label: __(
								'Label excluding tax color',
								'tax-switch-for-woocommerce'
							),
						},
						{
							value: labelTextColorChecked,
							onChange: ( color ) =>
								setAttributes( {
									labelTextColorChecked: color,
								} ),
							label: __(
								'Label including tax color',
								'tax-switch-for-woocommerce'
							),
						},
					] }
				/>
			</InspectorControls>
			<LabelComponent
				{ ...attributes }
				originalTaxDisplay={ originalTaxDisplay }
			/>
		</div>
	);
}
