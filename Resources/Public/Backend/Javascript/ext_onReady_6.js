(function(){
  $$('div.t3-form-field .lef_responsive_grid').each(function(el){
    var cls = $(el).className;
    var p   = $(el).up('div.typo3-TCEforms-flexForm');
    if (p){
      $(p).addClassName(cls);
    };
    var rm = $(el).up('.t3-form-field-container-flex');
    if (rm){
      $(rm).remove();
    }
    
  });
}());