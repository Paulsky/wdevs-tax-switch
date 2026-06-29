import { Component } from '@wordpress/element';
import { subscribe } from '@wordpress/data';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import { getIsSwitched } from '../../shared/store';

class LabelComponent extends Component {
	constructor( props ) {
		super( props );

		const { readOnly, isSwitched } = this.getInitialState( props );

		this.state = {
			readOnly,
			isSwitched,
		};

		this.unsubscribe = subscribe( () => {
			const newIsSwitched = getIsSwitched();
			if ( this.state.isSwitched !== newIsSwitched ) {
				this.setState( { isSwitched: newIsSwitched } );
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

	getCurrentLabel() {
		const { labelTextIncl, labelTextExcl } = this.props;
		if ( this.displayIncludingTax() ) {
			return labelTextIncl || '';
		}

		return labelTextExcl || '';
	}

	render() {
		const {
			labelTextColor,
			labelTextColorChecked,
			labelTextIncl,
			labelTextExcl,
		} = this.props;

		const showLabel = labelTextIncl || labelTextExcl;

		if ( ! showLabel ) {
			return '';
		}

		const isChecked = this.displayIncludingTax();

		return (
			<span
				className={ `wdevs-tax-switch-labels ${
					isChecked ? 'wts-price-incl' : 'wts-price-excl'
				}` }
				style={ {
					'--wts-text-color': labelTextColor,
					'--wts-text-color-checked': labelTextColorChecked,
				} }
			>
				<span className="wdevs-tax-switch-label-text">
					{ this.getCurrentLabel() }
				</span>
			</span>
		);
	}
}

export default LabelComponent;
