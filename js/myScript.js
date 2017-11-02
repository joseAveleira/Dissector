$(document).ready(function () {
    $('#checkBtn').click(function() {
      checked = $("input[type=checkbox]:checked").length;

      if(!checked) {
        Materialize.toast('You must check at least one checkbox.', 4000);
        return false;
      }

    });
});
