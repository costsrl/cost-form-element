/**
 * @author Renato salvatori
 */

var  CostDoctrineComboAutocompleteTableGateway = {
		init: function(selector) {
			$(selector)
			.find("select[data-Costtablegatwayacinit='Cost-tablegatway-combo-autocomplete']")
			.each(function() {
				var _class = $(this).data('Costtablegatwayacclass');
				var _select_warning_message = $(this).data('Costtablegatwayacselectwarningmessage');
				var _tag 	= $(this).data('zf2tagclass');
				var _id 	= $(this).attr('id');
				var _property = $(this).data('Costtablegatwayacproperty');
				var _allow_persist = $(this).data('Costtablegatwayacallowpersist');
				if (_class == null)
					return;
				/*
				 * remove initializers to prevent initialize again
				 */
				$(this).data('zf2tablegatwayacinit', null);
				$(this).data('zf2tablegatwayacclass', null);
				$(this).data('zf2tablegatwayacproperty', null);
				/**
				 * Wrap
				 */
				//$(this).wrap('<div class="wrap-Cost-tablegatway-autocomplete"></div>');
				var _clone = $(this).clone(true);
				$(this)
				.attr('name', $(this).attr('name'))
				.attr('type', 'hidden')
				.addClass('Cost-tablegatway-autocomplete-id');
				_clone.attr('name', _clone.attr('name') + '[' + _property + ']');
				if ($(this).data('Costtablegatwayacid')) {
					$(this).val($(this).data('Costtablegatwayacid'));
					$(this).data('Costtablegatwayacid', null);
				}
				//$(this).parent().append(_clone);
				//$(this).parent().append('<p class="Cost-tablegatway-autocomplete-msg"></p>');
				/** 
				 * autocomplete
				 */
				var cache = {};



				$(this).select2({
					ajax: {
						url: '/Cost-tablegateway-select-autocomplete/'+ _class + '/' + _property + '/' + _tag,  
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								q: params.term // search term
								//page: params.page
							};
						},
						processResults: function (data, params) {
							// parse the results into the format expected by Select2
							// since we are using custom formatting functions we do not need to
							// alter the remote JSON data, except to indicate that infinite
							// scrolling can be used
							return {
								results: $.map(data, function (item) {
									return {
										text: item.text,
										id:   item.id
									}
								})
							};
						},
						cache: false
					},
					//escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
					//minimumInputLength: 1,
					//templateResult: formatRepo, // omitted for brevity, see the source of this page
					//templateSelection: formatRepoSelection // omitted for brevity, see the source of this page

				});
			});	
		}

}


$(document).ready(function() {
	if (!$.isFunction($.fn.autocomplete)) {
		console.log('Required jQuery UI Autocomplete');
		return false;
	}
	CostDoctrineComboAutocompleteTableGateway.init('body');
});