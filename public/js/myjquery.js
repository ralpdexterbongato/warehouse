
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

    $('#none-existing-itemRV').click(function(event) {
      $('.add-RV-item-modal').addClass('active');
    });

    $('#closemodalRV').click(function(event) {
        $('.add-RV-item-modal').removeClass('active');
    });
    $('#forstock-ItemRV').click(function(event) {
      $('.for-stock-Modal').addClass('active');
    });

    $('.middle-forStock-div h1 i').click(function(event) {
      $('.for-stock-Modal').removeClass('active');
    });
    $('.searchRRitem').click(function(event) {
      $('.search-itemRR-Container').addClass('active');
    });
    $('.search-RR-center > h1').click(function(event) {
      $('.search-itemRR-Container').removeClass('active');
    });
    $('.add-toRRlist-btn').click(function(event) {
      $('.search-itemRR-Container').removeClass('active');
    });
    $('.edit-budget-opener').click(function(event)
    {
      $('.budget-number').addClass('disable');
      $('.edit-budget-opener').addClass('disable');
      $('.editbudget').addClass('active');
    });
    $('.cancel-edit').click(function(event) {
      $('.budget-number').removeClass('disable');
      $('.edit-budget-opener').removeClass('disable');
      $('.editbudget').removeClass('active');
    });
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
