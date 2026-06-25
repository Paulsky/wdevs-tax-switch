import { createRoot, Suspense } from '@wordpress/element';
import SwitchComponent from './components/SwitchComponent';
import TaxSwitchHelper from '../shared/TaxSwitchHelper';
import ThirdPartyCompatibility from './includes/ThirdPartyCompatibility';
import { onDomReady, shouldBeEnabled } from '../shared/utils/render';
import { initTaxSwitchApi } from '../shared/api';

const roots = new WeakMap();
const getViewConfig = () =>
	window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

initTaxSwitchApi( getViewConfig() );

const renderSwitchComponent = ( element, ajaxConfig ) => {
	const attributes = {
		...element.dataset,
		...ajaxConfig,
	};

	let root = roots.get( element );
	if ( ! root ) {
		root = createRoot( element );
		roots.set( element, root );
	}

	root.render(
		<Suspense fallback={ <div className="wp-block-placeholder" /> }>
			<SwitchComponent { ...attributes } />
		</Suspense>
	);
};

let isInitialized = false;

const initPage = ( viewConfig ) => {
	initTaxSwitchApi( viewConfig );
	TaxSwitchHelper.setPriceClasses( viewConfig.originalTaxDisplay );
	if ( ! isInitialized ) {
		ThirdPartyCompatibility.initialize( viewConfig.originalTaxDisplay );
		isInitialized = true;
	}
};

const renderElements = () => {
	const viewConfig = getViewConfig();

	initTaxSwitchApi( viewConfig );

	if ( ! shouldBeEnabled() ) {
		return;
	}

	const elements = document.querySelectorAll( '.wp-block-wdevs-tax-switch' );

	if ( elements.length > 0 ) {
		initPage( viewConfig );

		elements.forEach( ( element ) => {
			if ( element ) {
				renderSwitchComponent( element, viewConfig );
			}
		} );
	}
};

onDomReady( () => {
	renderElements();
} );

document.addEventListener( 'wdevs-tax-switch-appeared', () => {
	renderElements();
} );
