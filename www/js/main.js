/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Kristián Heřman, xherma33
*/

// Bootstrap Tooltips
// Zdroj: https://getbootstrap.com/docs/4.0/components/tooltips/
$(document).ready(function() {
$('[data-toggle="tooltip"]').tooltip();
});

// Modal window
// Zdroj: https://codepen.io/adambui/pen/KmJwdw
$("div[id^='myModal']").each(function(){
var currentModal = $(this);
currentModal.find('.btn-next').click(function(){
    currentModal.modal('hide');
    currentModal.closest("div[id^='myModal']").nextAll("div[id^='myModal']").first().modal('show'); 
});
currentModal.find('.btn-prev').click(function(){
    currentModal.modal('hide');
    currentModal.closest("div[id^='myModal']").prevAll("div[id^='myModal']").first().modal('show'); 
});
});

// Real-time filtering in table
// Zdroj: https://www.w3schools.com/bootstrap/bootstrap_filters.asp
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#data tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
