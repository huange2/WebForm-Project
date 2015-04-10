/*
 * Name: Erica Huang
 * Date: 1-28-15
 * File: form.js
 * Purpose: Provide a easy way for user to navigate
 *          through the webform.Also provide validation.
 *
 */

var fields = document.getElementsByTagName("FIELDSET");
var index = fields.length;
var steps = [];
for(var i = 0; i < index-1; i++) {
     steps[i] = "#step" + (i+1);
}
steps[index-1] = "#lastStep";
var count = 0;

$(document).on('keyup keypress', 'form input[type="text"]', function(e) {
  if(e.which == 13) {
    e.preventDefault();
    return false;
  }
});
function init() {
    for(i=1; i < steps.length; i++){
        $(steps[i]).hide();
        console.log(steps[i]);
    }
    $("#submitB").hide();
    $("#prevB").hide();
    $("#errorBox").hide();
    $("#nameError").hide();
}
function next() {
    var phoneTest = /^\d{10}$|^(\(\d{3}\)\s*)?\d{3}[\s-]?\d{4}$/;
    if(count == steps.length-2) {
        $(steps[count]).hide();
        count = count + 1;
        $(steps[count]).show();
        $("#nextB").hide();
        $("#submitB").show();
    }
    else if(count == 0) {
        if($("#studentF").val() != "" && $("#studentL").val() != "" &&
           $("#supervisorF").val() != "" && $("#supervisorL").val() != "" && 
           $("#company").val() != "") {
            if($("#phone").val().match(phoneTest)) {
                $("#nameError").hide()
                $("#errorBox").hide();
                $(steps[count]).hide();
                count = count + 1;
                $(steps[count]).show();
                $("#prevB").show();
            }
            else {
                $("#errorBox").show();
                $("#nameError").hide()
            }
        }
        else {
            $("#nameError").show();
        }
    }
    else if(count < steps.length - 2) {
        $(steps[count]).hide();
        count = count + 1;
        $(steps[count]).show();
    }
}
function prev() {
    if(count == steps.length-1) {
        $("#submitB").hide();
        $("#nextB").show();
        count= count - 1;
        $(steps[count]).show();
        $(steps[count+1]).hide();
        console.log(count)
    }
    else if(count != 0){
        count= count - 1;
        $(steps[count]).show();
        $(steps[count+1]).hide();
        console.log(count);
    }
}
