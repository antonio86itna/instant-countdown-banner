(function ($) {
	'use strict';
	function initPickers() {
		$('.icb-color').wpColorPicker({
			change() {
				livePreview();
			},
			clear() {
				livePreview();
			},
		});
	}
	function livePreview() {
		const $form = $('#icb-settings-form');
		const data = {
			message_before: $form
				.find('[data-icb-field="message_before"]')
				.val(),
			message_after: $form.find('[data-icb-field="message_after"]').val(),
			position: $form.find('[data-icb-field="position"]').val(),
			sticky: $form.find('[data-icb-field="sticky"]').is(':checked'),
			body_offset: $form
				.find('[data-icb-field="body_offset"]')
				.is(':checked'),
			bg: $form.find('[name="icb_options[bg_color]"]').val(),
			text: $form.find('[name="icb_options[text_color]"]').val(),
			accent: $form.find('[name="icb_options[accent_color]"]').val(),
			cta_label: $form.find('[data-icb-field="cta_label"]').val(),
			cta_url: $form.find('[data-icb-field="cta_url"]').val(),
			dismissible: $form
				.find('[data-icb-field="dismissible"]')
				.is(':checked'),
		};
		const root = document.getElementById('icb-preview-root');
		root.innerHTML = '';
		const wrap = document.createElement('div');
		wrap.className =
			'icb-banner icb-' +
			data.position +
			(data.sticky ? ' icb-sticky' : '');
		wrap.style.background = data.bg;
		wrap.style.color = data.text;
		const inner = document.createElement('div');
		inner.className = 'icb-inner';
		const msg = document.createElement('div');
		msg.className = 'icb-message';
		const strong = document.createElement('strong');
		strong.style.color = data.accent;
		strong.textContent = data.message_before.replace('{time}', '01:23:45');
		msg.appendChild(strong);
		inner.appendChild(msg);
		if (data.cta_label) {
			const a = document.createElement('a');
			a.className = 'icb-cta';
			a.href = data.cta_url || '#';
			a.textContent = data.cta_label;
			a.style.background = data.accent;
			a.style.color = data.text;
			inner.appendChild(a);
		}
		if (data.dismissible) {
			const btn = document.createElement('button');
			btn.className = 'icb-close';
			btn.innerHTML = '&times;';
			wrap.appendChild(btn);
		}
		wrap.appendChild(inner);
		root.appendChild(wrap);
	}
	function copyShortcode() {
		const $btn = $('#icb-copy-shortcode');
		const shortcode = $btn.data('shortcode');
		navigator.clipboard
			.writeText(shortcode)
			.then(function () {
				$('#icb-copy-msg').text('Copied!');
				setTimeout(function () {
					$('#icb-copy-msg').text('');
				}, 1500);
			})
			.catch(function () {
				$('#icb-copy-msg').text(
					'Press Ctrl/Cmd+C to copy: ' + shortcode
				);
			});
	}
	$(document).on('input change', '.icb-field', livePreview);
	$(document).on('click', '#icb-copy-shortcode', copyShortcode);
	$(document).ready(function () {
		initPickers();
		livePreview();
	});
})(jQuery);
