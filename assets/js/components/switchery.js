/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2016 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */
$.components.register("switchery", {
  mode: "init",
  defaults: {
    color: $.colors("primary", 600)
  },
  init: function(context) {
    if (typeof Switchery === "undefined") return;

    var defaults = $.components.getDefaults("switchery");

    $('[data-plugin="switchery"]', context).each(function() {
    	if(!$(this).hasClass('no-switchery')){
	      var options = $.extend({}, defaults, $(this).data());
	      $(this).parent().find('.switchery').detach();
	      new Switchery(this, options);
    	}
    });
  }
});
