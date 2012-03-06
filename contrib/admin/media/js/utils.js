/** work as **/
function initAccoutSwitcher(searchUrl){
  $switcher = $("#oaNavigationExtra .accountSwitcher");
  //$("#oaNavigation").append("<div class='accountSwitcherOverlay'>&nbsp;</div>");

  $(".switchTrigger").hover(function() {
      $(".triggerContainer").addClass("hover");
    }, function() {
      $(".triggerContainer").removeClass("hover");
  });

  $(".switchTrigger, .triggerContainer > a", $switcher).click(function() {
    $switcher.toggleClass("expanded");
    $switcher.accountswitch({action: 'show'});
    $(".accountSwitcherOverlay").toggle();
    return false;
  });

  $(".accountsPanel li").hover(function() {
      $this = $(this);
      if (!$this.is(".opt,.more")) {
        $this.addClass("hover");
      }
    }, function() {
      $(this).removeClass("hover");
  });

  $(document).click(function(event) {
    if ($(event.target).parents(".expanded").length == 0) {
      $switcher.removeClass("expanded");
      $(".accountSwitcherOverlay").hide();
    }
  });

  $(document).keydown(function(event) {
    if ($(".expanded").length > 0 && event.keyCode == 27) {
      $switcher.removeClass("expanded");
      $(".accountSwitcherOverlay").hide();
    }
    return true;
  });
}



function fillSelect(element, options){
	var defaults = {
		selected: false,
		title: 'Lütfen Seçiniz.'
	};
	var settings = $.extend({}, defaults, options);
	
	$.getJSON(settings.url,{ajax: 'true'}, function(resp){
		
		var options = '<option value="">'+settings.title+'</option>';
		for (var i = 0; i < resp.data.length; i++) {
			options += '<option value="' + resp.data[i].id + '" ';
	        if(resp.data[i].id == settings.selected){
	        	options += ' selected="selected"';
	        }
	        options += '>' + resp.data[i].name + '</option>';              
		}
		$(element).html(options);
	}); 
}
