import { createReduxStore, select, register, dispatch } from '@wordpress/data';

const STORAGE_KEY = 'wdevs_tax_switch_is_switched';
const STORE_NAME = 'wdevs-tax-switch/store';
const SWITCH_TYPE = 'SET_IS_SWITCHED';
const DISABLED_TYPE = 'SET_IS_DISABLED';

const getInitialState = () => {
	const storedValue = window.localStorage.getItem( STORAGE_KEY );
	return {
		isSwitched: storedValue ? JSON.parse( storedValue ) : false,
		isDisabled: false,
	};
};

const actions = {
	setIsSwitched( value ) {
		return {
			type: SWITCH_TYPE,
			value,
		};
	},
	saveIsSwitched( value ) {
		window.localStorage.setItem( STORAGE_KEY, JSON.stringify( value ) );
		return {
			type: SWITCH_TYPE,
			value,
		};
	},
	setIsDisabled( value ) {
		return {
			type: DISABLED_TYPE,
			value,
		};
	},
};

const reducer = ( state = getInitialState(), action ) => {
	switch ( action.type ) {
		case SWITCH_TYPE:
			return {
				...state,
				isSwitched: action.value,
			};
		case DISABLED_TYPE:
			return {
				...state,
				isDisabled: action.value,
			};
		default:
			return state;
	}
};

const selectors = {
	getIsSwitched( state ) {
		return state.isSwitched;
	},
	getIsDisabled( state ) {
		return state.isDisabled;
	},
};

let store = select( STORE_NAME );

if ( store === undefined ) {
	store = createReduxStore( STORE_NAME, {
		reducer,
		actions,
		selectors,
	} );

	register( store );
}

export function getIsSwitched() {
	return select( STORE_NAME ).getIsSwitched();
}

export function saveIsSwitched( value ) {
	return dispatch( STORE_NAME ).saveIsSwitched( value );
}

export function switchedIsSaved() {
	return window.localStorage.getItem( STORAGE_KEY ) !== null;
}

export function setIsSwitched( value ) {
	return dispatch( STORE_NAME ).setIsSwitched( value );
}

export function getIsDisabled() {
	return select( STORE_NAME ).getIsDisabled();
}

export function setIsDisabled( value ) {
	return dispatch( STORE_NAME ).setIsDisabled( value );
}
