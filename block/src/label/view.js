import { createRoot, Suspense } from '@wordpress/element';
import LabelComponent from './components/LabelComponent';
import { onDomReady, shouldBeEnabled } from '../shared/utils/render';
import { initTaxSwitchApi } from '../shared/api';

const getViewConfig = () =>
	window.wtsViewObject || {
		originalTaxDisplay: 'incl',
	};

initTaxSwitchApi( getViewConfig() );

const renderLabelComponent = ( element, ajaxConfig ) => {
	const attributes = {
		...element.dataset,
		...ajaxConfig,
	};

	const root = createRoot( element );

	root.render(
		<Suspense fallback={ <div className="wp-block-placeholder" /> }>
			<LabelComponent { ...attributes } />
		</Suspense>
	);
};

onDomReady( () => {
	const viewConfig = getViewConfig();

	initTaxSwitchApi( viewConfig );

	if ( ! shouldBeEnabled() ) {
		return;
	}

	const elements = document.querySelectorAll(
		'.wp-block-wdevs-tax-switch-label'
	);

	if ( elements.length > 0 ) {
		elements.forEach( ( element ) => {
			if ( element ) {
				renderLabelComponent( element, viewConfig );
			}
		} );
	}
} );
