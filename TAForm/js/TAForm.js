/*
 * Name: Erica Huang
 * Date: 1-28-15
 * File: TAForm.js
 * Purpose: Provide a easy way for user to navigate
 *          through the webform.Also provide validation.
 *
 */

var steps = ["#part1", "#step1", "#part3", "#part4"];
var count = 0;

$(document).on('keyup keypress', 'form input[type="text"]', function(e) {
  if(e.which == 13) {
    e.preventDefault();
    return false;
  }
});

//Hide all error elements on load of html file.
function init() {
    for(i=1; i < steps.length; i++){
        $(steps[i]).hide();
    }
    $("#submitB").hide();
    $("#prevB").hide();
    $("#errorBox").hide();
    $("#nameError").hide();
    $("#agreeError").hide();
    $("#idError").hide();
    $("#emailError").hide();
}

//Move to next page if end of form become a submit button
//Also validate information as the user move page to page
function next() {
    var phoneTest = /^\d{10}$|^(\(\d{3}\)\s*)?\d{3}[\s-]?\d{4}$/;
    var idTest = /^[W]\d{8}$/;
    var emailTest = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if(count == steps.length-2) {
        $(steps[count]).hide();
        count = count + 1;
        $(steps[count]).show();
        $("#nextB").hide();
        $("#submitB").show();
    }
    else if(count == 0) {
        //Validate if agreement page has been signed
        if( $("#agreement1").is(":checked")) {
            $("#agreeError").hide();
            $(steps[count]).hide();
            count = count + 1;
            $(steps[count]).show();
            $("#prevB").show();
        }
        else {
            $("#agreeError").show();
        }
    }
    else if(count == 1) {
        // Validate all name fields, student Id, phone and email
        if($("#studentF").val() != "" && $("#studentL").val() != "" &&
           $("#studentN").val() != "" && $("#email").val() != "" && 
           $("#sponsor").val() != "") {
            if($("#phone").val().match(phoneTest)) {
                if($("#studentN").val().match(idTest)){
                    if($("#email").val().match(emailTest)){
                        $("agreeError").hide();
                        $("#nameError").hide()
                        $("#errorBox").hide();
                        $("#emailError").hide();
                        $(steps[count]).hide();
                        count = count + 1;
                        $(steps[count]).show();
                    }
                    else {
                        $("#emailError").show();
                        $("#idError").hide();
                        $("#errorBox").hide();
                        $("#nameError").hide();
                    }
                }
                else {
                    $("#idError").show();
                    $("#errorBox").hide();
                    $("#nameError").hide();
                }
            }
            else {
                $("#errorBox").show();
                $("#nameError").hide();
            }
        }
        else {
            $("#nameError").show();
        }
    }
    else if(count < steps.length-2) {
        $(steps[count]).hide();
        count = count + 1;
        $(steps[count]).show();
    }
}

//Move to previous page
function prev() {
    if(count == steps.length-1) {
        $("#submitB").hide();
        $("#nextB").show();
        count= count - 1;
        $(steps[count]).show();
        $(steps[count+1]).hide();
    }
    else if(count != 0){
        count= count - 1;
        $(steps[count]).show();
        $(steps[count+1]).hide();
    }
}
