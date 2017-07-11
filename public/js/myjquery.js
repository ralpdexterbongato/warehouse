
  $(document).ready(function() {
    $('.add-noexist').click(function(event) {
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

  });
