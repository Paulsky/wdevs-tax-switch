import { Component } from '@wordpress/element';
import { subscribe } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import {
	getIsSwitched,
	getIsDisabled,
	saveIsSwitched,
	setIsSwitched,
} from '../../shared/store';

class SwitchComponent extends Component {
	constructor( props ) {
		super( props );

		const { readOnly, isSwitched } = this.getInitialState( props );

		this.state = {
			readOnly,
			isSwitched,
			isDisabled: getIsDisabled(),
		};

		this.handleChange = this.handleChange.bind( this );

		this.unsubscribe = subscribe( () => {
			const newIsSwitched = getIsSwitched();
			const newIsDisabled = getIsDisabled();
			if (
				this.state.isSwitched !== newIsSwitched ||
				this.state.isDisabled !== newIsDisabled
			) {
				this.setState( {
					isSwitched: newIsSwitched,
					isDisabled: newIsDisabled,
				} );
			}
		} );
	}

	getInitialState( props ) {
		const readOnly = TaxSwitchHelper.parseBooleanValue( props.readOnly );
		const originalTaxDisplay = props.originalTaxDisplay || 'incl';
		let isSwitched;

		if ( readOnly ) {
			isSwitched = ! ( originalTaxDisplay === 'incl' );
		} else {
			isSwitched = getIsSwitched();
		}

		return { readOnly, isSwitched };
	}

	componentWillUnmount() {
		if ( this.unsubscribe ) {
			this.unsubscribe();
		}
	}

	handleChange() {
		const newIsSwitched = ! this.state.isSwitched;
		this.setState( { isSwitched: newIsSwitched }, () => {
			if ( ! this.state.readOnly ) {
				saveIsSwitched( newIsSwitched );

				this.fireSwitchChangeEvent( newIsSwitched );
			} else {
				setIsSwitched( newIsSwitched );
			}

			this.togglePriceClasses();
		} );
	}

	displayIncludingTax() {
		const { originalTaxDisplay = 'incl' } = this.props;
		const { isSwitched } = this.state;

		return TaxSwitchHelper.displayIncludingTax(
			originalTaxDisplay,
			isSwitched
		);
	}

	/** @deprecated Since 1.8.0. Use displayIncludingTax(). */
	displayIncludingVat() {
		return this.displayIncludingTax();
	}

	togglePriceClasses() {
		const { originalTaxDisplay = 'incl' } = this.props;
		const { isSwitched } = this.state;

		return TaxSwitchHelper.togglePriceClasses(
			originalTaxDisplay,
			isSwitched
		);
	}

	getToggleSwitchAriaLabel() {
		const { switchAriaLabel, switchLabelIncl, switchLabelExcl } =
			this.props;

		if ( switchAriaLabel ) {
			return switchAriaLabel;
		}

		if ( switchLabelIncl && switchLabelExcl ) {
			return switchLabelIncl + ' / ' + switchLabelExcl;
		}

		return __(
			'Switch between prices including and excluding tax',
			'tax-switch-for-woocommerce'
		);
	}

	getCurrentLabel() {
		const { switchLabelIncl, switchLabelExcl } = this.props;
		if ( this.displayIncludingTax() ) {
			return switchLabelIncl || '';
		}
		return switchLabelExcl || '';
	}

	fireSwitchChangeEvent( isSwitched ) {
		const vm = this;
		const displayIncludingTax = vm.displayIncludingTax();
		const switchEvent = new CustomEvent( 'wdevs-tax-switch-changed', {
			detail: {
				isSwitched: isSwitched,
				displayIncludingTax,
				displayIncludingVat: displayIncludingTax, // deprecated since 1.8.0
			},
		} );

		document.dispatchEvent( switchEvent );
	}

	renderButtons() {
		const {
			switchColor,
			switchColorChecked,
			switchBackgroundColor,
			switchBackgroundColorChecked,
			switchTextColor,
			switchLabelIncl,
			switchLabelExcl,
		} = this.props;

		const isIncl = this.displayIncludingTax();
		const { isDisabled } = this.state;

		const setInclusive = () => {
			if ( ! isIncl ) {
				this.handleChange();
			}
		};

		const setExclusive = () => {
			if ( isIncl ) {
				this.handleChange();
			}
		};

		return (
			<div
				className="wdevs-tax-switch wdevs-tax-buttons"
				style={ {
					'--wts-color': switchColor,
					'--wts-color-checked': switchColorChecked,
					'--wts-bg-color': switchBackgroundColor,
					'--wts-bg-color-checked': switchBackgroundColorChecked,
					'--wts-text-color': switchTextColor,
				} }
			>
				<button
					type="button"
					className={ `wdevs-tax-button ${
						isIncl ? 'wdevs-tax-button-active' : ''
					}` }
					disabled={ isDisabled }
					onClick={ setInclusive }
				>
					{ switchLabelIncl ||
						__( 'Incl. VAT', 'tax-switch-for-woocommerce' ) }
				</button>
				<button
					type="button"
					className={ `wdevs-tax-button ${
						! isIncl ? 'wdevs-tax-button-active' : ''
					}` }
					disabled={ isDisabled }
					onClick={ setExclusive }
				>
					{ switchLabelExcl ||
						__( 'Excl. VAT', 'tax-switch-for-woocommerce' ) }
				</button>
			</div>
		);
	}

	renderToggleSwitch() {
		const {
			switchColor,
			switchColorChecked,
			switchBackgroundColor,
			switchBackgroundColorChecked,
			switchTextColor,
			switchLabelIncl,
			switchLabelExcl,
		} = this.props;

		const isChecked = this.displayIncludingTax();
		const showLabel = switchLabelIncl || switchLabelExcl;
		const { isDisabled } = this.state;

		return (
			<div
				className="wdevs-tax-switch"
				style={ {
					'--wts-color': switchColor,
					'--wts-color-checked': switchColorChecked,
					'--wts-bg-color': switchBackgroundColor,
					'--wts-bg-color-checked': switchBackgroundColorChecked,
					'--wts-text-color': switchTextColor,
				} }
			>
				<label className="wdevs-tax-switch-label">
					<input
						type="checkbox"
						name="wdevs-tax-switch-checkbox"
						onChange={ this.handleChange }
						checked={ isChecked }
						disabled={ isDisabled }
						className="wdevs-tax-switch-checkbox"
						aria-label={ this.getToggleSwitchAriaLabel() }
					/>
					<span className="wdevs-tax-switch-slider"></span>
				</label>
				{ showLabel && (
					<span
						className="wdevs-tax-switch-label-text"
						onClick={ this.handleChange }
						role="button"
						tabIndex={ 0 }
						onKeyDown={ ( e ) => {
							if ( e.key === 'Enter' || e.key === ' ' ) {
								e.preventDefault();
								this.handleChange();
							}
						} }
					>
						{ this.getCurrentLabel() }
					</span>
				) }
			</div>
		);
	}

	render() {
		const { switchType = 'switch' } = this.props;

		if ( switchType === 'buttons' ) {
			return this.renderButtons();
		}

		return this.renderToggleSwitch();
	}
}

export default SwitchComponent;
