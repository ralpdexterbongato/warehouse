
  $(document).ready(function() {
    $('.addnon-existing').click(function(event) {
      $('.add-new-modal').addClass('active');
      $('.Account-modal').removeClass('active');
    });
    $('#cancel-btn').click(function(event) {
      $('.add-new-modal').removeClass('active');
    });
    $('.notice-title i').click(function(event) {
      $('.modal-for-all-notice').removeClass('active');
    });
    $('.modal-find-button button').click(function(event) {
      $('.modal-search-item').addClass('active');
    });
    $('.middle-modal-search > h5').click(function(event) {
      $('.modal-search-item').removeClass('active');
    });

    $('.pick-from-items > button').click(function(event) {
      $('.mrt-items-modal').addClass('active');
    });

    $('.mrt-items > h1').click(function(event) {
        $('.mrt-items-modal').removeClass('active');
    });

   $('.burger-button').click(function(event) {
     $('.Account-modal').addClass('active');
   });
   $('.middle-account-modal > ul >li >i').click(function(event) {
     $('.Account-modal').removeClass('active');
   });

   $('#mct-modal-btn').click(function(event) {
    $('.MCT-modal').addClass('active');
   });

   $('#cancel-mct').click(function(event) {
      $('.MCT-modal').removeClass('active');
   });

   $('.searchRRitem').click(function(event) {
     $('.search-itemRR-Container').addClass('active');
   });

   $('.search-RR-center h1').click(function(event) {
     $('.search-itemRR-Container').removeClass('active');
   });

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

   $('#signaturePreview').hide();

    $("#inputSignature").change(function(){
        readURL(this);
        $('#signaturePreview').show();
    });

    $('.add-item-RV > button').click(function(event) {
      $('.add-RV-item-modal').addClass('active');
    });

    $('#closemodalRV').click(function(event) {
        $('.add-RV-item-modal').removeClass('active');
    });

    
  });
