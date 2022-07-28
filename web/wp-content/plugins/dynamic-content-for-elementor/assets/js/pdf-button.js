"use strict";

function initializePdfButton($scope) {
	let button = $scope.find('a').first();
	let element_id = button.data('element-id');
	let post_id = button.data('post-id');
	let queried_id = button.data('queried-id');
	let converter = button.data('converter');
	let ajax_url = button.data('ajax-url');
	let ajax_action = button.data('ajax-action');
	let title = button.data('title');
	if (converter !== 'html') {
		return;
	}
	const fetchPdf = async function (ajaxUrl) {
		// // Backend might need current get parameters for setting the item value:
		let data = new FormData();
		data.set('queried_id', queried_id);
		data.set('post_id', post_id);
		data.set('element_id', element_id);
		data.set('action', ajax_action);
		let response;
		try {
			response = await fetch(ajaxUrl, {
				method: 'POST',
				body: new URLSearchParams(data)
			});
		} catch (e) {
			console.error('PDF Button: ' + e.message);
			return;
		}
		if (response.headers.get('Content-Type') !== 'application/pdf') {
			const json = await response.json();
			console.error('PDF Button: ' + json.data.message)
			return;
		}
		let blob = await response.blob()
		let link = document.createElement('a');
		link.href = window.URL.createObjectURL(blob);
		link.download = title;
		link.click();
	}
	button.on('click', () => {
		fetchPdf(ajax_url);
	});
}

jQuery(window).on('elementor/frontend/init', function() {
	if(elementorFrontend.isEditMode()) {
		return;
	}
	elementorFrontend.hooks.addAction('frontend/element_ready/dce_pdf_button.default', initializePdfButton);
});
