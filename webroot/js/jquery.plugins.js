$.fn.selectDepend = function(options) {
  
  var settings = $.extend({
    url: null,
    loader_text: 'Loading...',
    error_text: 'Error!',
    empty: false
  }, options);
  
  if(settings.url == null){
    console.error('The url option can not be empty');
    return false;
  }
  
  $(this).change(function(){
    _selects_depend($(this), settings.url);
  });
  
  var _selects_depend = function($current, url){
    var $child = $($current.data('child'));
    var $target = $($current.data('target'));
    $.ajax({
      url : url + '/' + $current.data('child') + '/' + $current.val(),
      dataType : 'json',
      success: function(json) {
        $target.empty(); // remove old options
        if(settings.empty !== false){
          $target.append($("<option></option>").attr("value", "").text(settings.empty));
        }
        
        $.each(json, function(value,key) {
          $target.append($("<option></option>")
          .attr("value", value).text(key));
        });
        if (typeof $child.data('child') !== "undefined") {
          _selects_depend($child, url);
        }
        $target.prop('disabled', false);
      },
      beforeSend: function() {
        block($target, settings.loader_text);
      },
      error: function(xhr, status) {
        block($target, settings.error_text);
        console.log(status);
      },
      //complete : function(xhr, status) {
      //  $child.prop('disabled', false);
      //}
    });
  }
  
  var block = function($select, message){
    $select.prop('disabled', true);
    $select.empty(); // remove old options
    $select.append($("<option></option>").attr("value", "").text(message));
  }
  
};