$(document).ready(function () {

  $(".alert").click(function () {
    $(this).fadeOut('slow');
  });

  $('[data-toggle="tooltip"]').tooltip();
});