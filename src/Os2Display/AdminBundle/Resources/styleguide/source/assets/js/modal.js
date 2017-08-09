/**
 *
 * Toggle modal
 *
 * This is used in Pattern Lab for presentation purposes, don't used in production.
 *
 */

(function($) {
  // Function for toggle burger navigation.
  function toggle_modal() {
    var body = $('body');

    $('.js-toggle-modal').bind('touchstart click', function(e) {
      var modal = $("." + $(this).attr('data-modal'));
      var modalDialog = $('.js-modal-dialog');

      // If nav is open we close it.
      if (modal.hasClass('is-visible')) {

        // Hide menu.
        modal.addClass('is-hidden');
        modal.removeClass('is-visible');
        modalDialog.addClass('is-hidden');
        modalDialog.removeClass('is-visible');
        body.removeClass('is-locked');
      }

      // If nav is closed we open it.
      else {
        // show menu.
        modal.removeClass('is-hidden');
        modal.addClass('is-visible');
        modalDialog.removeClass('is-hidden');
        modalDialog.addClass('is-visible');
        body.addClass('is-locked');
      }
    });
  }

  // Start the show
  $(document).ready(function () {
    toggle_modal();
  });

})(jQuery);
