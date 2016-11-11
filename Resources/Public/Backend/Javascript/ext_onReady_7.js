(function(){
  
  TYPO3.jQuery('div.lef_responsive_grid').each(function(i, el){
    console.log(el);
    var cls = TYPO3.jQuery(el).attr('class');
    console.log('cls %s', cls);
    var p   = TYPO3.jQuery(el).parents('div.tab-pane');
    if (p.length){
      TYPO3.jQuery(p[0]).addClass(cls);
    };
    var rm = TYPO3.jQuery(el).parents('.form-section');
    if (rm.length){
      TYPO3.jQuery(rm[0]).remove();
    }
    
  });
}());