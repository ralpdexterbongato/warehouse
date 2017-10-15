
  $(document).ready(function() {
    $('#cancel-btn').click(function(event) {
      $('.add-new-modal').removeClass('active');
    });
    $('.notice-title i').click(function(event) {
      $('.modal-for-all-notice').removeClass('active');
    });
   $('#mct-modal-btn').click(function(event) {
    $('.MCT-modal').addClass('active');
   });

   $('#cancel-mct').click(function(event) {
      $('.MCT-modal').removeClass('active');
   });
   $('.burger-button').click(function(event)
  {
    $('.Account-modal').addClass('active');
  });
  $('.Account-modal').click(function(event)
  {
    $('.Account-modal').removeClass('active');
  })
  });
 //  $(document).bind("contextmenu",function(e) {
 // e.preventDefault();
//});
