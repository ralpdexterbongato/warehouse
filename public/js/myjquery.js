
  $(document).ready(function() {
    $('#cancel-btn').click(function(event) {
      $('.add-new-modal').removeClass('active');
    });
    $('.notice-title i').click(function(event) {
      $('.modal-for-all-notice').removeClass('active');
    });
    $('.pick-from-items > button').click(function(event) {
      $('.mrt-items-modal').addClass('active');
    });

    $('.mrt-items > h1').click(function(event) {
        $('.mrt-items-modal').removeClass('active');
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

   //previewing Signature image before uploading
  function readURL(input) {

     if (input.files && input.files[0]) {
         var reader = new FileReader();

         reader.onload = function (e) {
             $('#signaturePreview').attr('src', e.target.result);
         }
         reader.readAsDataURL(input.files[0]);
     }
   }

    $('.button-find-item-container button').click(function(event) {
      $('.mct-modal-ofItems').addClass('active');
    });
    $('.mct-modal-center h1 i').click(function(event) {
        $('.mct-modal-ofItems').removeClass('active');
    });

  });
 //  $(document).bind("contextmenu",function(e) {
 // e.preventDefault();
//});
