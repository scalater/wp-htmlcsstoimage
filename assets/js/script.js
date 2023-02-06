var wpHtmlCssToImageInstance = {
	btnGenerateImageCallBack: function(container, btnElement) {
		let dataTarget = jQuery( container ).attr( 'data-target' ) || false;
		let haveContent = jQuery( container ).attr( 'data-have-content' ) || false;
		let dataHeight = jQuery( container ).attr( 'data-height' ) || false;
		let dataWidth = jQuery( container ).attr( 'data-width' ) || false;
		let entry = jQuery( container ).attr( 'data-entry-id' ) || false;
		let formData = jQuery( container ).find( 'input[type="hidden"]' ) || false;
		if (formData && formData.val()) {
			formData = JSON.parse( formData.val() );
		}
		let html, target;
		let css = '';
		let cssElement = jQuery( container ).find( 'style' );
		if (cssElement && cssElement.length > 0) {
			css = cssElement.html();
			cssElement.remove();
		}
		if ( ! haveContent && dataTarget) {
			target = jQuery( '#' + dataTarget );
			if (target && target.length > 0) {
				html = target[0].outerHTML;
				if ( ! dataHeight) {
					dataHeight = wpHtmlCssToImageInstance.getHeight( target );
				}
				if ( ! dataWidth) {
					dataWidth = wpHtmlCssToImageInstance.getWidth( target );
				}
			}
		} else {
			target = jQuery( container ).find( '#htmlcsstoimage-content' );
			if (target && target.length > 0) {
				html = target.html();
				if ( ! dataHeight) {
					dataHeight = wpHtmlCssToImageInstance.getHeight( target );
				}
				if ( ! dataWidth) {
					dataWidth = wpHtmlCssToImageInstance.getWidth( target );
				}
			}
		}
		target.append( '<style>' + css + '</style>' );
		if (html) {
			html = html.replaceAll( 'castocitycom.local', 'castocity.com' );
			wpHtmlCssToImageInstance.postImageCall( html, css, btnElement, dataHeight, dataWidth, entry, formData );
		} else {
			alert( 'Invalid HTML, contact administrator!' );
		}
	},
	getWidth: function(container) {
		if (container) {
			return Math.round( jQuery( container ).width() );
		}
	},
	getHeight: function(container) {
		if (container) {
			return Math.round( jQuery( container ).height() );
		}
	},
	postImageCall: function(html, css, btnElement, dataHeight, dataWidth, entry, formData) {
		let originalHtml = jQuery( btnElement ).html();
		jQuery( btnElement ).html( '<i class="fas fa-spinner fa-spin"></i>' ).attr( 'disabled', 'disabled' );
		let subject, type, podcast_id, orientation;
		if (formData) {
			subject = formData['subject'] || false;
			type = formData['type'] || false;
			podcast_id = formData['podcast_id'] || false;
			orientation = formData['orientation'] || false;
		}
		jQuery.ajax(
			{
				type: 'POST',
				dataType: 'json',
				url: wpHtmlCssToImageObj.admin_url,
				data: {
					'action': 'post_htmlcsstoimage',
					'nonce': wpHtmlCssToImageObj.nonce,
					'html': html,
					'css': css,
					'dataHeight': dataHeight,
					'dataWidth': dataWidth,
					'entry': entry,
					'subject': subject,
					'type': type,
					'podcast_id': podcast_id,
					'orientation': orientation,
				},
				success: function(response) {
					console.log( response );
					if (response && response.data.code === 200 && response.data.url) {
						let extraPath = '.png';
						if (dataHeight || dataWidth) {
							extraPath += '?';
							if (dataHeight && dataWidth) {
								extraPath += 'height=' + dataHeight + '&width=' + dataWidth;
							} else if (dataHeight && ! dataWidth) {
								extraPath += 'height=' + dataHeight;
							} else if ( ! dataHeight && dataWidth) {
								extraPath += 'width=' + dataWidth;
							}
						}
						window.open( response.data.url + extraPath + '&dl=1', '_blank' );
					}
				},
				error: function(request, status, error) {
					alert( request.responseText );
				},
				complete: function() {
					jQuery( btnElement ).html( originalHtml ).removeAttr( 'disabled' );
				},
			}
		);
	},
	init: function() {
		let containers = jQuery( '.htmlcsstoimage-container' );
		if (containers && containers.length > 0) {
			jQuery.each(
				containers,
				function(i, e) {
					let container = jQuery( e );
					let btnGenerateImage;
					let triggerId = container.attr( 'data-trigger-id' );
					if (triggerId) {
						let triggerElement = jQuery( '#' + triggerId );
						if (triggerElement && triggerElement.length > 0) {
							btnGenerateImage = triggerElement;
						}
					}
					if ( ! btnGenerateImage) {
						btnGenerateImage = container.find( 'a.create-image' );
						btnGenerateImage.show();
					}
					if (btnGenerateImage && btnGenerateImage.length > 0) {
						btnGenerateImage.on(
							'click',
							function(e) {
								e.preventDefault();
								let isDisabled = btnGenerateImage.attr( 'disabled' ) || false;
								if (isDisabled) {
									return false;
								}
								wpHtmlCssToImageInstance.btnGenerateImageCallBack( container, btnGenerateImage );
							}
						);
					}
				}
			);
		}
	},
};

jQuery( document ).ready(
	function() {
		wpHtmlCssToImageInstance.init();
	}
);
