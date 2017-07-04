
  $(document).ready(function() {
    $('.add-new-item button').click(function(event) {
      $('.add-new-modal').addClass('active');
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
    $('#accepted').click(function(event)
    {
      $('.MCT-modal').addClass('active');
    });
    $('#cancel-mct').click(function(event)
    {
      $('.MCT-modal').removeClass('active');
    });

    $('.pick-from-items > button').click(function(event) {
      $('.mrt-items-modal').addClass('active');
    });

    $('.mrt-items > h1').click(function(event) {
        $('.mrt-items-modal').removeClass('active');
    });

    $(document).bind("contextmenu",function(e)
    {                             //disabled rightclick so user cannot edit the inspect element
      e.preventDefault();
    });
    $(document).keydown(function(e){  //disabled f12 which is a shortcut openning inspect element
    if(e.which === 123){
       return false;
    }
});
  });
