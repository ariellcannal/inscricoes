/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2015 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */
$.components.register("maskmoney", {
  mode: "init",
  init: function(context) {
    if (!$.fn.maskMoney) return;

    $('input[data-plugin="currency"]', context).each(function(){
	config = {
		affixesStay:true,
		allowZero: true,
		selectAllOnFocus: true
	}
	$(this).maskMoney(config);
    });
  }
});


