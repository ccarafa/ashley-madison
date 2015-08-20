

var app = function($) {
  $("#checker").on('submit', function(){
    var email = $("#email").val();
    $(".alert").hide().text('');
    $.get('check.php', {email: email}, function(data){
      var result = parseInt(data.result);
      if (result) {
        $(".alert.alert-danger").show().text('The email ' + email + ' was found in the database :(');
      } else {
        $(".alert.alert-success").show().text('The email ' + email + ' was NOT found in the database :)');
      }
    });
    return false;
  });

  $(document).ready(function(){
    var options = {
      beforeSend: function() {
        $("#progress").show();
        //clear everything
        $(".progress-bar").width('0%').html("");
      },
      uploadProgress: function(event, position, total, percentComplete) {
        $(".progress-bar")
          .width(percentComplete+'%')
          .html(percentComplete+'%');
      },
      success: function() {
        $(".progress-bar").width('100%').html('100%');
      },
      complete: function(response) {
        $("#results").html("<font color='green'>"+response.responseText+"</font>");
      },
      error: function() {
        $("#results").html("<font color='red'> ERROR: unable to upload files</font>");
      }
    };

    $("#uploader").ajaxForm(options);

});

};

app(jQuery);