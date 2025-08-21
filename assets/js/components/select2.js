/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2016 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */
$.components.register("select2", {
    mode : "init",
    defaults : {
	width : "100%",
	theme : "material"
    },
    init : function(context) {
	if (!$.fn.select2)
	    return;

	$.fn.select2.amd.require([ 'select2/utils', 'select2/dropdown', 'select2/dropdown/attachBody' ], function(Utils, Dropdown, AttachBody) {
	    function SelectAll() {
	    }

	    SelectAll.prototype.render = function(decorated) {
		var $rendered = decorated.call(this);
		var self = this;

		var $selectAll = $('<button class="btn btn-primary" type="button">Marcar Todos</button>');
		var $selectNone = $('<button class="btn btn-default" type="button">Desmarcar Todos</button>');

		$rendered.find('.select2-dropdown').prepend($selectNone);
		$rendered.find('.select2-dropdown').prepend($selectAll);

		$selectAll.on('click', function(e) {
		    var $results = self.$element.find('option');

		    vals = [];
		    // Get all results that aren't selected
		    $results.each(function() {
			var $result = $(this);
			if($result.attr('value') != ""){
			    vals.push($result.attr('value'));
			}
		    });
		    //	console.log(vals);
		    self.$element.val(vals);
		    self.$element.trigger('change');
		    self.trigger('close');
		});
		
		$selectNone.on('click', function(e) {
		    self.$element.val([]);
		    self.$element.trigger('change');
		    self.trigger('close');
		});

		return $rendered;
	    };

	    var defaults = $.components.getDefaults("select2");

	    $('select:not(.xcrud-columns-select):not(.xcrud-searchdata):not(.not_select2):not(.theme-bars-pill select):not(.bootstrap-select):not(select[data-plugin="barrating"])', context).each(function() {
		var options = $.extend({}, defaults, $(this).data());
		if ($(this).attr('data-selectAll') == "true") {
		    $.extend(options, {
			dropdownAdapter : Utils.Decorate(Utils.Decorate(Dropdown, AttachBody), SelectAll)
		    });
		}
		if ($(this).hasClass('select2-ajax')) {
		    var container = $(this).closest('.xcrud-ajax');
		    var depend_on = jQuery(this).data("depend");
		    var dados = Xcrud.list_controls_data(container);
		    dados.dependval = jQuery('.xcrud-input[name="' + depend_on + '"]').val();
		    dados.name = $(this).data('relationajax');
		    dados.task = 'relation_search';
		    $.extend(options, {
			ajax : {
			    url : "/ajax/xcrud",
			    dataType : 'json',
			    delay : 250,
			    type : 'POST',
			    beforeSend : function(jqXHR, settings) {
				// console.log(jqXHR);
			    },
			    data : function(params) {
				return {
				    q : params.term,
				    xcrud : dados
				};
			    },
			    processResults : function(data, page) {
				return {
				    results : data.items
				};
			    },
			    cache : false
			},
			minimumInputLength : 2
		    });
		}
		$(this).select2(options);
	    });

	});

    }
});