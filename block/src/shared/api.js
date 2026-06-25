import TaxSwitchHelper from './TaxSwitchHelper';
import { getIsSwitched, saveIsSwitched, switchedIsSaved } from './store';

const DISPLAY_REQUEST_EVENT_NAME = 'wdevs-tax-switch-context-changed';
const VALID_DISPLAYS = [ 'incl', 'excl' ];
const VALID_DISPLAY_REQUESTS = [ ...VALID_DISPLAYS, 'toggle' ];

let originalTaxDisplay = 'incl';

const getGlobalApi = () => {
	if (
		typeof window.wdevsTaxSwitch !== 'object' ||
		window.wdevsTaxSwitch === null
	) {
		window.wdevsTaxSwitch = {};
	}

	return window.wdevsTaxSwitch;
};

const getContextEventState = () => {
	const api = getGlobalApi();
	api._contextEvent = api._contextEvent || {};

	return api._contextEvent;
};

const normalizeOriginalTaxDisplay = ( value ) => {
	if ( value === 'excl' ) {
		return 'excl';
	}

	return 'incl';
};

const getSwitchStateForDisplay = ( display ) => {
	const displayIncludingVat = display === 'incl';

	if ( originalTaxDisplay === 'incl' ) {
		return ! displayIncludingVat;
	}

	return displayIncludingVat;
};

const fireTaxSwitchChangedEvent = ( isSwitched ) => {
	const switchEvent = new CustomEvent( 'wdevs-tax-switch-changed', {
		detail: {
			isSwitched,
			displayIncludingVat: TaxSwitchHelper.displayIncludingVat(
				originalTaxDisplay,
				isSwitched
			),
		},
	} );

	document.dispatchEvent( switchEvent );
};

const shouldRespectSavedChoice = ( options = {} ) => {
	return options.respectExistingChoice === true && switchedIsSaved();
};

const applySwitchState = ( nextIsSwitched, options = {} ) => {
	if ( shouldRespectSavedChoice( options ) ) {
		return false;
	}

	const currentIsSwitched = getIsSwitched();
	const stateChanged = currentIsSwitched !== nextIsSwitched;
	const choiceIsSaved = switchedIsSaved();

	if ( ! stateChanged ) {
		if ( ! choiceIsSaved ) {
			saveIsSwitched( nextIsSwitched );
		}

		return false;
	}

	saveIsSwitched( nextIsSwitched );
	TaxSwitchHelper.togglePriceClasses( originalTaxDisplay, nextIsSwitched );
	fireTaxSwitchChangedEvent( nextIsSwitched );

	return true;
};

const getDisplay = () => {
	return TaxSwitchHelper.displayIncludingVat(
		originalTaxDisplay,
		getIsSwitched()
	)
		? 'incl'
		: 'excl';
};

const setDisplay = ( display, options = {} ) => {
	if ( ! VALID_DISPLAYS.includes( display ) ) {
		return false;
	}

	return applySwitchState( getSwitchStateForDisplay( display ), options );
};

const toggle = ( options = {} ) => {
	return applySwitchState( ! getIsSwitched(), options );
};

const handleContextChanged = ( event ) => {
	const detail = event.detail || {};
	const { display, respectExistingChoice } = detail;

	if ( ! VALID_DISPLAY_REQUESTS.includes( display ) ) {
		return false;
	}

	const options = {
		respectExistingChoice,
	};

	if ( display === 'toggle' ) {
		return toggle( options );
	}

	return setDisplay( display, options );
};

const exposeApi = () => {
	Object.assign( getGlobalApi(), {
		hasSavedChoice: switchedIsSaved,
		getDisplay,
		setDisplay,
		toggle,
	} );
};

const registerContextEvent = () => {
	const contextEvent = getContextEventState();

	if ( contextEvent.isRegistered ) {
		return;
	}

	document.addEventListener(
		DISPLAY_REQUEST_EVENT_NAME,
		handleContextChanged
	);
	contextEvent.isRegistered = true;
};

export function initTaxSwitchApi( config = {} ) {
	originalTaxDisplay = normalizeOriginalTaxDisplay(
		config.originalTaxDisplay || originalTaxDisplay
	);

	exposeApi();
	registerContextEvent();
}
