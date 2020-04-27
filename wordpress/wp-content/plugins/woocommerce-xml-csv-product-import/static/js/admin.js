/**
 * plugin admin area javascript
 */
(function($){$(function () {
	if ( ! $('body.wpallimport-plugin').length) return; // do not execute any code if we are not on plugin page

	$('.product_data_tabs').find('a').click(function(){
		var $parent = $(this).parents('.product_data_tabs').first();
		$parent.find('li').removeClass('active');
		$(this).parent('li').addClass('active');
		$(this).parents('.panel-wrap').first().find('.panel').hide();
		$('#' + $(this).attr('rel')).show();
	});

	var change_depencies = function (){

		var is_variable = ($('#product-type').val() == 'variable');
		var is_grouped = ($('#product-type').val() == 'grouped');
		var is_simple = ($('#product-type').val() == 'simple');
		var is_external = ($('#product-type').val() == 'external');		
		var is_simple_subscription = ($('#product-type').val() == 'subscription');
		var is_variable_subscription = ($('#product-type').val() == 'variable-subscription');
		var is_downloadable = !($('input[name=is_product_downloadable]:checked').val() == 'no');
		var is_variable_downloadable = !($('input[name=is_variable_product_downloadable]:checked').val() == 'no');		
		var is_virtual = ($('#_virtual').is(':checked'));			
		var is_multiple_product_type = ($('input[name=is_multiple_product_type]:checked').val() == 'yes');		

		if (!is_multiple_product_type) $('.product_data_tabs li, .options_group').show();

		if (!is_variable && !is_grouped && !is_external && !is_simple_subscription && !is_variable_subscription && is_multiple_product_type ) {
			is_simple = true;
		}		

		$('.product_data_tabs li, .options_group').each(function(){

			if (($(this).hasClass('hide_if_grouped') || $(this).hasClass('hide_if_external')) && is_multiple_product_type) {
	 			if ($(this).hasClass('hide_if_grouped') && is_grouped) {
	 				$(this).hide();
	 				return true;
	 			}
	 			else if ( $(this).hasClass('hide_if_grouped') && !is_grouped ) {
					$(this).show();
				}
	 			if ($(this).hasClass('hide_if_external') && is_external) {
	 				$(this).hide();
	 				return true;
	 			}
	 			else if ($(this).hasClass('hide_if_external') && !is_external) {
					$(this).show();
				}
	 		}

	 		if (($(this).hasClass('show_if_simple') || $(this).hasClass('show_if_variable') || $(this).hasClass('show_if_grouped') || $(this).hasClass('show_if_external') || $(this).hasClass('show_if_subscription') || $(this).hasClass('show_if_variable_subscription')) && is_multiple_product_type) {
	 			if ($(this).hasClass('show_if_simple') && is_simple) {
					$(this).show();
				} else if (!is_simple) {
	 				$(this).hide();
	 				if ($(this).hasClass('show_if_variable') && is_variable) {
						$(this).show();
					} else if (!is_variable) {
	 					$(this).hide();
	 					if ($(this).hasClass('show_if_grouped') && is_grouped) {
							$(this).show();
						} else if (!is_grouped) {
	 						$(this).hide();
	 						if ($(this).hasClass('show_if_external') && is_external) {
								$(this).show();
							} else if (!is_external) {
								$(this).hide();
								if ($(this).hasClass('show_if_subscription') && is_simple_subscription) {
									$(this).show();
								} else if (!is_simple_subscription) {
									$(this).hide();
									if ($(this).hasClass('show_if_variable_subscription') && is_variable_subscription) {
										$(this).show();
									} else if (!is_variable_subscription) {
										$(this).hide();
									}
								}
							}
	 					}
	 				}
	 			}
	 			else if (!$(this).hasClass('show_if_simple')) {
	 				if ($(this).hasClass('show_if_variable') && is_variable) {
						$(this).show();
					} else if (!is_variable) {
	 					$(this).hide();
	 					if ($(this).hasClass('show_if_grouped') && is_grouped) {
							$(this).show();
						} else if (!is_grouped) {
	 						$(this).hide();
	 						if ($(this).hasClass('show_if_external') && is_external) {
								$(this).show();
							} else if (!is_external) {
								$(this).hide();
								if ($(this).hasClass('show_if_subscription') && is_simple_subscription) {
									$(this).show();
								} else if (!is_simple_subscription) {
									$(this).hide();
									if ($(this).hasClass('show_if_variable_subscription') && is_variable_subscription) {
										$(this).show();
									} else if (!is_variable_subscription) {
										$(this).hide();
									}
								}
							}
	 					}
	 				}
	 				else if (!$(this).hasClass('show_if_variable')) {
	 					if ($(this).hasClass('show_if_grouped') && is_grouped) {
							$(this).show();
						} else if (!is_grouped) {
	 						$(this).hide();
	 						if ($(this).hasClass('show_if_external') && is_external) {
								$(this).show();
							} else if (!is_external) {
								$(this).hide();
								if ($(this).hasClass('show_if_subscription') && is_simple_subscription) {
									$(this).show();
								} else if (!is_simple_subscription) {
									$(this).hide();
									if ($(this).hasClass('show_if_variable_subscription') && is_variable_subscription) {
										$(this).show();
									} else if (!is_variable_subscription) {
										$(this).hide();
									}
								}
							}
	 					}
	 					else if (!$(this).hasClass('show_if_grouped')) {
	 						if ($(this).hasClass('show_if_external') && is_external) {
								$(this).show();
							} else if (!is_external) {
								$(this).hide();
								if ($(this).hasClass('show_if_subscription') && is_simple_subscription) {
									$(this).show();
								} else if (!is_simple_subscription) {
									$(this).hide();
									if ($(this).hasClass('show_if_variable_subscription') && is_variable_subscription) {
										$(this).show();
									} else if (!is_variable_subscription) {
										$(this).hide();
									}
								}
							}
							else if (!$(this).hasClass('show_if_external')) {
								if ($(this).hasClass('show_if_subscription') && is_simple_subscription) {
									$(this).show();
								} else if (!is_simple_subscription) {
									$(this).hide();
									if ($(this).hasClass('show_if_variable_subscription') && is_variable_subscription) {
										$(this).show();
									} else if (!is_variable_subscription) {
										$(this).hide();
									}
								}
								else if (!$(this).hasClass('show_if_subscription')) {
									if ($(this).hasClass('show_if_variable_subscription') && is_variable_subscription) {
										$(this).show();
									} else if (!is_variable_subscription) {
										$(this).hide();
									}
								}
							}
	 					}
	 				}
	 			}
	 		}

	 		if ($(this).hasClass('hide_if_virtual') || $(this).hasClass('show_if_virtual') || $(this).hasClass('show_if_downloadable') || $(this).hasClass('variable_downloadable')) {
	 			if ($(this).hasClass('hide_if_virtual') && is_virtual) {
					$(this).hide();
				} else if ($(this).hasClass('hide_if_virtual') && !is_virtual)	{
					$(this).show();
				}
	 			if ($(this).hasClass('show_if_virtual') && is_virtual) {
					$(this).show();
				} else if ($(this).hasClass('show_if_virtual') && !is_virtual) {
					$(this).hide();
				}
	 			if ($(this).hasClass('show_if_downloadable') && is_downloadable) {
					$(this).show();
				} else if ($(this).hasClass('show_if_downloadable') && !is_downloadable) {
					$(this).hide();
				}
	 			if ($(this).hasClass('variable_downloadable') && is_variable_downloadable) {
					$(this).show();
				} else if ($(this).hasClass('variable_downloadable') && !is_variable_downloadable) {
					$(this).hide();
				}
	 		}
		});

		if ($('input[name=is_product_manage_stock]:checked').val() == 'no') {
			$('.stock_fields').hide();
		} else {
			$('.stock_fields').show();
		}

		if ($('input[name=is_variable_product_manage_stock]:checked').val() == 'no') {
			$('.variable_stock_fields').hide();
		} else {
			$('.variable_stock_fields').fadeIn();
		}
		
		if ($('#link_all_variations').is(':checked')) {
			$('.variations_tab').hide();
		} else if (is_variable) {
			$('.variations_tab').show();
		}
		
		if ($('#xml_matching_parent').is(':checked') && is_variable) {
			$('#variations_tag').show();
		} else {
			$('#variations_tag').hide();
		}

		var matching_parent = $('input[name=matching_parent]:checked').val();

		if (matching_parent == "xml" || matching_parent == "first_is_parent_title" || matching_parent == "auto" || matching_parent == "existing") {
			$('#variations_tag').show();
			$('.variations_are_not_child_elements').hide();
		} 
		else {
			$('#variations_tag').hide();
			$('.variations_are_not_child_elements').show();
		}

		if ($('input[name=is_variation_product_manage_stock]:checked').val() == 'no' || matching_parent == "xml" || matching_parent == "first_is_parent_title" || matching_parent == "auto" || matching_parent == "existing"){
			$('.variation_stock_fields').hide();
		} else {
			$('.variation_stock_fields').fadeIn();
		}

		/****************/
		/* Free Edition */
		/****************/
		if (woo_addon_free_edition == 'free') {
			if ( ! is_simple ) {
				$('.woocommerce_options_panel').find('input, select').attr('disabled','disabled');
				$('.upgrade_template').show();
			}
			else {
				$('.woocommerce_options_panel').find('input, select').removeAttr('disabled');
				$('.upgrade_template').hide();
			}
		}
	};

	$('input[name=matching_parent]').click(function(){

		if ($(this).val() == "xml" || $(this).val() == "first_is_parent_title" || $(this).val() == "auto" || $(this).val() == "existing") {
			$('#variations_tag').show();
			$('.variations_are_not_child_elements').hide();
		} 
		else{ 
			$('#variations_tag').hide();
			$('.variations_are_not_child_elements').show();
			if ($('input[name=is_variation_product_manage_stock]:checked').val() == 'no') {
				$('.variation_stock_fields').hide();
			} else {
				$('.variation_stock_fields').fadeIn();
			}
		}

	});

	$('input[name=is_variation_product_manage_stock]').click(function(){
		if ($('input[name=is_variation_product_manage_stock]:checked').val() == 'no') {
			$('.variation_stock_fields').hide();
		} else {
			$('.variation_stock_fields').fadeIn();
		}
	}); 

	change_depencies();

	$('#product-type').change(function(){
		change_depencies();
		$('.wc-tabs').find('li:visible:first').find('a').click();
	});
	$('#_virtual, #_downloadable, input[name=is_product_manage_stock], input[name=is_variable_product_manage_stock], input[name=is_product_downloadable], input[name=is_variable_product_downloadable]').click(change_depencies);
	$('input[name=is_multiple_product_type]').click(function(){
		change_depencies();
		$('.wc-tabs').find('li:visible:first').find('a').click();
	});
	$('#link_all_variations').change(function(){
		if ($(this).is(':checked')) {
			$('.variations_tab').hide();
		}
		else {
			$('.variations_tab').show();
		}
	});
	$('#regular_price_shedule').click(function(){
		$('#sale_price_range').show();
		$('input[name=is_regular_price_shedule]').val('1');
		$(this).hide();
	});

	$('#cancel_regular_price_shedule').click(function(){
		$('#sale_price_range').hide();
		$('input[name=is_regular_price_shedule]').val('0');
		$('#regular_price_shedule').show();
	});

	$('#variable_sale_price_shedule').click(function(){
		$('#variable_sale_price_range').show();
		$('input[name=is_variable_sale_price_shedule]').val('1');		
		$(this).hide();
	});

	$('#cancel_variable_regular_price_shedule').click(function(){
		$('#variable_sale_price_range').hide();
		$('input[name=is_variable_sale_price_shedule]').val('0');		
		$('#variable_sale_price_shedule').show();
	});

	var variation_xpath = $('#variations_xpath').val();

	$('#variations_xpath').blur(function(){
		if (variation_xpath == ""){
			$(this).val($(this).val().replace(/(\[\d\]})$/, '[*]'));			
		}
		variation_xpath = $(this).val();
	});

	$('#variations_xpath').mousemove(function(){
		if (variation_xpath == ""){
			$(this).val($(this).val().replace(/(\[\d\]})$/, '[*]}'));			
		}
		variation_xpath = $(this).val();
	});

	$('#variations_xpath').each(function () {

		var $input = $('#variations_xpath');
		var $xml = $('#variations_xml');		
		var $next_element = $('#next_variation_element');
		var $prev_element = $('#prev_variation_element');		
		var $goto_element =  $('#goto_variation_element');
		var $variation_tagno = 0;
		
		var variationsXPathChanged = function () {
			
			if ($input.val() == $input.data('checkedValue')) return;					
			
			// request server to return elements which correspond to xpath entered
			$input.attr('readonly', true).unbind('change', variationsXPathChanged).data('checkedValue', $input.val());
			
			var parent_tagno = parseInt($('.tag').find('input[name="tagno"]').val());
			
			$.post('admin.php?page=pmxi-admin-import&action=evaluate_variations' +  ((typeof import_id != "undefined") ? '&id=' + import_id : '') , {xpath: $input.val(), tagno: $variation_tagno, parent_tagno: parent_tagno}, function (data) {
				$('#variations_console').html(data.html);
				$input.attr('readonly', false);			
				$xml.xml('dragable');
				$('#close_xml_tree').show();
			}, 'json');
		};

		$xml.find('.navigation a').live('click', function (e) {
			e.preventDefault();
			$variation_tagno += '#variation_prev' == $(this).attr('href') ? -1 : 1;
			$input.data('checkedValue', '');
			variationsXPathChanged();
		});

		$('#variations_xpath').change(function(){$variation_tagno = 0; variationsXPathChanged();});	
		
		$('#variations_xpath').blur(function(){$variation_tagno = 0; variationsXPathChanged();});

		$('#variations_xpath').keyup(function (e) {
			if (13 == e.keyCode) {$variation_tagno = 0;  $(this).change();}
		});

		if ($input.val() != "") {
            variationsXPathChanged();
		}

		$('#variations_xpath').mousemove(function(){
			variationsXPathChanged();				
		});
	});
    

	$('.variation_attributes').find('label').live({
        mouseenter: function () {
            if ("" == $(this).attr('for')) {
                var counter = $(this).parents('table:first').find('tr.form-field:visible').length;
                $(this).parents('span:first').find('input').attr('id', $(this).parents('span:first').find('input').attr('name').replace('[]', '') + '_' + counter);
                $(this).attr('for', $(this).parents('span:first').find('input').attr('id'));
                var $create_terms = $(this).parents('.wpallimport-radio-field:first').find('.is_create_taxonomy');
                if (!$create_terms.hasClass('switcher-target-is_taxonomy_' + counter)) $create_terms.addClass('switcher-target-is_taxonomy_' + counter);
            }
        },
        mouseleave: function () {}
    });

    $('.add-new-custom').click(function(){

    	var counter = $(this).parents('table:first').find('tr.form-field:visible').length - 1;

    	$('#woocommerce_attributes').find('.default_attribute_settings').find('label').each(function(){
    		if ( "" == $(this).attr('for') ) {
           		var $parent = $(this).parents('tr.form-field:first');
           		if ( ! $parent.hasClass('template')) {
					$(this).parents('span:first').find('input').attr('id', $(this).parents('span:first').find('input').attr('name').replace('[]','') + '_' + counter );
					$(this).attr('for', $(this).parents('span:first').find('input').attr('id'));
					var $create_terms = $(this).parents('.wpallimport-radio-field:first').find('.is_create_taxonomy');
					if ( ! $create_terms.hasClass('switcher-target-is_taxonomy_' + counter)) $create_terms.addClass('switcher-target-is_taxonomy_' + counter);
           		}				
			}
    	});

    	$('#woocommerce_attributes').find('.advanced_settings_template').each(function(){
    		var $tpl = $(this).parents('tr.form-field:first');
       		if ( ! $tpl.hasClass('template')) {
	    		$(this).find('label').each(function(){    		   			    			
		   			$(this).attr('for', $(this).attr('for').replace('00', counter));							
		    	});
		    	$(this).find('input').each(function(){    		   			    			
		   			if (typeof $(this).attr('id') != "undefined") $(this).attr('id', $(this).attr('id').replace('00', counter));							
		   			$(this).attr('name', $(this).attr('name').replace('00', counter));
		    	});
		    	$(this).find('div.set_with_xpath').each(function(){
		    		var $parent = $(this).parents('.wpallimport-radio-field:first');
		    		$(this).addClass('switcher-target-' + $parent.find('input.switcher').attr('id'));
		    		$parent.find('input.switcher').change();
		    	});
		    	$(this).removeClass('advanced_settings_template');
		    }
    	});    	    	

    });

	$('#variations_tag').draggable({ containment: "#wpwrap", zIndex: 100 }).hide();	

	$('#toggle_xml_tree').click(function(){
		$('#variations_tag').show();
	});	     

	$('#close_xml_tree').click(function(){
		$('#variations_tag').hide();
	});

	var $unique_key = $('input[name=unique_key]:first').val();
	
	$('.auto_generate_unique_key').click(function(){
		
		var attrs = new Array();
		$('#attributes_table').find('textarea[name^=attribute_value]').each(function(){
			if ("" != $(this).val() && $(this).val() != undefined) attrs.push($(this).val());
		});
		if (attrs.length) {
			$(this).parents('#product:first').find('input[name=unique_key]').val($unique_key + attrs.join('-'));
			alert('The unique key has been successfully generated');
		}
		else {
            alert('At first, you should add minimum one attribute on the "Attributes" tab.');
		}
	});

	$('.pmwi_adjust_type').change(function(){
		if ($(this).val() == '%') {
			$(this).parents('.form-field:first').find('.pmwi_reduce_prices_note').hide();
			$(this).parents('.form-field:first').find('.pmwi_percentage_prices_note').show();
		}
		else {
			$(this).parents('.form-field:first').find('.pmwi_reduce_prices_note').show();
			$(this).parents('.form-field:first').find('.pmwi_percentage_prices_note').hide();	
		}
	}).change();

	if ($('input[name=matching_parent]:checked').val() == 'first_is_parent_id' || $('input[name=matching_parent]:checked').val() == 'first_is_variation'){
		$('.set_parent_stock_option').slideDown();
	}
	else{
		$('.set_parent_stock_option').slideUp();
	}

	$('input[name=matching_parent]').click(function(){
		if ($(this).val() == 'first_is_parent_id' || $(this).val() == 'first_is_variation'){
			$('.set_parent_stock_option').slideDown();
		}
		else{
			$('.set_parent_stock_option').slideUp();
		}
	});

	$('.pmwi_trigger_adjust_prices').click(function(){
		if ($(this).find('span').html() == '-') {
            $(this).find('span').html('+');
		}
		else {
            $(this).find('span').html('-');
		}
		$('.pmwi_adjust_prices').slideToggle();
	});

	$('.advanced_attributes').live('click', function(){
		var $parent = $(this).parent('div.wpallimport-radio-field:first');

		if ($(this).find('span').html() == "+") {
			$parent.find('.default_attribute_settings').hide();
			$parent.find('.advanced_attribute_settings').fadeIn();
			$parent.find('input[name^=is_advanced]').val('1');
			$(this).find('span').html("-");			
		}
		else {
			$parent.find('.advanced_attribute_settings').hide();
			$parent.find('.default_attribute_settings').fadeIn();
			$parent.find('input[name^=is_advanced]').val('0');
			$(this).find('span').html("+");
		}
	});

	$('input[name^=is_advanced]').each(function(){
		if ($(this).val() == '1') {
			var $parent = $(this).parent('div.wpallimport-radio-field:first');
			$parent.find('.advanced_attributes').click();
		}
	});

	// [ WC Orders View ]
	// swither show/hide logic
	$('select.switcher').live('change', function (e) {	

		var $targets = $('.switcher-target-' + $(this).attr('id'));

		var is_show = $(this).val() == 'xpath'; if ($(this).is('.switcher-reversed')) is_show = ! is_show;
		if (is_show) {
			$targets.slideDown();
		} else {
			$targets.slideUp().find('.clear-on-switch').add($targets.filter('.clear-on-switch')).val('');
		}

	}).change();

	$('a.add-new-line').live('click', function(){
		var $parent = $(this).parents('table').first();
		var $template = $parent.children('tbody').children('tr.template');
		var $clone = $template.clone(true);
		var $number = parseInt($parent.find('tbody:first').children().not('.template').length) - 1;	
		
		var $cloneHtml = $clone.html().replace(/ROWNUMBER/g, $number).replace(/CELLNUMBER/g, 'ROWNUMBER').replace('date-picker', 'datepicker');

		$clone.html($cloneHtml);		

		$clone.insertBefore($template).css('display', 'none').removeClass('template').fadeIn();		

		// datepicker
		$parent.find('input.datepicker').removeClass('date-picker').addClass('datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			showOn: 'button',
			buttonText: '',
			constrainInput: false,
			showAnim: 'fadeIn',
			showOptions: 'fast'
		}).bind('change', function () {
			var selectedDate = $(this).val();
			var instance = $(this).data('datepicker');
			var date = null;
			if ('' != selectedDate) {
				try {
					date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				} catch (e) {
					date = null;
				}
			}
			if ($(this).hasClass('range-from')) {
				$(this).parent().find('.datepicker.range-to').datepicker("option", "minDate", date);
			}
			if ($(this).hasClass('range-to')) {
				$(this).parent().find('.datepicker.range-from').datepicker("option", "maxDate", date);
			}
		}).change();
		$('.ui-datepicker').hide(); // fix: make sure datepicker doesn't break wordpress wpallimport-layout upon initialization		

		return false;
	});	

	$('a.add-new-line').each(function(){
		var $parent = $(this).parents('table:first');		
		if ($(this).parents('table').length < 4 && $parent.children('tbody').children('tr').length == 2) {
			$(this).click();	
		} 
	});

	$('a.switcher').live('click', function (e) {	
		
		var $targets = $('.switcher-target-' + $(this).attr('id'));

		var is_show = $(this).find('span').html() == '+'; if ($(this).is('.switcher-reversed')) is_show = ! is_show;
		if (is_show) {
			$(this).find('span').html('-');
			
				if ($targets.find('a.add-new-line').length){
					var $parent = $targets.find('a.add-new-line').parents('table:first');
					if ($parent.children('tbody').children('tr').length == 2){
						var $add_new = $targets.find('a.add-new-line');
						var $taxes = $add_new.parents('table').first();
						var $template = $taxes.children('tbody').children('tr.template');
						var $clone = $template.clone(true);
						var $number = parseInt($taxes.find('tbody:first').children().not('.template').length) - 1;	
						
						var $cloneHtml = $clone.html().replace(/ROWNUMBER/g, $number).replace(/CELLNUMBER/g, 'ROWNUMBER').replace('date-picker', 'datepicker');

						$clone.html($cloneHtml);		

						$clone.insertBefore($template).css('display', 'none').removeClass('template').show();		
					}
				}
			$targets.slideDown('slow');
		} else {
			$(this).find('span').html('+');
			$targets.slideUp().find('.clear-on-switch').add($targets.filter('.clear-on-switch')).val('');
		}
	}).click();	

	$('.variable_repeater_mode').live('change', function(){
		// if variable mode
		if ($(this).is(':checked')) {
			var $parent = $(this).parents('.options_group:first');
			if ($(this).val() == 'xml' || $(this).val() == 'csv') {
				$parent.find('table.wpallimport_variable_table').find('tr.wpallimport-row-actions').hide();			
			}						
			else {
				$parent.find('table.wpallimport_variable_table').find('tr.wpallimport-row-actions').show();
			}
		}
	}).change();

	$('#billing_source_match_by').on('change', function(){
		$('.wpallimport-select-switcher-target').hide();
		$('.wpallimport-select-switcher-target[rel='+ $(this).val() +']').slideDown();
	}).change();

	$('#billing_is_guest_matching').on('change', function(){
		if ($(this).is(':checked')){
			$('.is_guest_matching_notice').hide();
		}
		else{
			$('.is_guest_matching_notice').slideDown();
		}
	}).change();

	$('input[name="is_multiple_product_subscription_period"]').live('click', function(){
		if ($(this).val() == 'no') {
			$('select[name="multiple_product_subscription_length"]').html($('.subscription_length-xpath').html());
		}
		else {
			var $period = $('select[name="multiple_product_subscription_period"]').val();
			$('select[name="multiple_product_subscription_length"]').html($('.subscription_length-' + $period).html());
	}
	});

	$('input[name="is_multiple_product_subscription_period"]:checked').click();

	$('select[name="multiple_product_subscription_period"]').live('change', function(){
		$('select[name="multiple_product_subscription_length"]').html($('.subscription_length-' + $(this).val()).html());
	});

	// Sortable product attributes.
	$('#attributes_table tbody, #variation_attributes_table tbody').sortable({
		items: "tr:not(.wpallimport-table-actions, .template)",
		handle: ".drag-attribute",
	}).disableSelection();

});})(jQuery);
