/*
 * Form validation
 *
 */

var FormValidation = function () {

    // Master Validation
    var quizValidation = function() {

            var form = $('#form_quiz');
            var errorAlert = $('.alert-danger', form);
            var successAlert = $('.alert-success', form);
             
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    title: {
                        minlength: 2,
                        required: true,
                    },
                    description:{
                        minlength: 2,
                        required: true,
                    },
                    target:{
                        required: true,
                    },

                },
                messages:{

                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    successAlert.hide();
                    errorAlert.show();
                    App.scrollTo(errorAlert, -200);
                },

                errorPlacement: function (error, element) { // render error placement for each input type

                    if (element.parent(".input-icon").size() > 0) {

                        // For icon group
                        var icon = element.parent('.input-icon').children('i');
                        icon.removeClass('fa-check').addClass("fa-warning");  
                        icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});

                    } else if (element.parent(".input-group").size() > 0){

                        // For select option
                        if(element.parent('.input-group').children('.input-group-addon')){

                            var span = element.parent('.input-group').children('.input-group-addon');
                            span.removeClass('display-hide');

                            var spanIcon = $(span).children('i');
                            spanIcon.removeClass('fa-check').addClass("fa-warning");
                            spanIcon.removeClass('font-green').addClass("font-red");
                            spanIcon.attr("data-original-title", error.text()).tooltip({'container': 'body'});

                        }else{

                            error.insertAfter(element.parent(".input-group"));

                        }

                    } else if (element.attr("data-error-container")) { 

                        error.appendTo(element.attr("data-error-container"));

                    } else if (element.parents('.mt-radio-list') || element.parents('.mt-checkbox-list')) {                        
                        if (element.parents('.mt-radio-list')[0]) {
                            error.appendTo(element.parents('.mt-radio-list')[0]);
                        }
                        if (element.parents('.mt-checkbox-list')[0]) {
                            error.appendTo(element.parents('.mt-checkbox-list')[0]);
                        }
                    } else if (element.parents('.mt-radio-inline') || element.parents('.mt-checkbox-inline')) {

                        if (element.parents('.mt-radio-inline')[0]) {
                            error.appendTo(element.parents('.mt-radio-inline')[0]);
                        }
                        if (element.parents('.mt-checkbox-inline')[0]) {
                            error.appendTo(element.parents('.mt-checkbox-inline')[0]);
                        }

                    } else {

                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }


                    // Check if all requirement invalid and show error text
                    if(successAlert.is(":visible")){
                        var errors = 0;
                        form.each(function(){
                            if($(this).find('.form-group').hasClass('has-error')){
                                errors += 1;
                            } 
                        });

                        if(errors > 0){ 
                            successAlert.hide();
                            errorAlert.show();
                        }
                    }

                },

                highlight: function (element) { // hightlight error inputs
                    // set error class to the control group   
                    $(element).closest('.form-group').removeClass("has-success").addClass('has-error');                   
                },

                unhighlight: function (element) { // revert the change done by hightlight
                
                },

                success: function (label, element) {

                    // set success class to the control group
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');

                    // For icon group
                    if ($(element).parent(".input-icon").size() > 0) {
                        var icon = $(element).parent('.input-icon').children('i');
                        icon.removeClass("fa-warning").addClass("fa-check");
                    }

                    // For select option
                    if ($(element).parent(".input-group").size() > 0){

                        if($(element).parent('.input-group').children('.input-group-addon')){
                            var span = $(element).parent('.input-group').children('.input-group-addon');
                            span.removeClass('display-hide');

                            var spanIcon = $(span).children('i');
                            spanIcon.removeClass('fa-warning').addClass("fa-check");
                            spanIcon.removeClass('font-red').addClass("font-green");                            
                        }
                    }

                    // Check if all requirement valid and show success text
                    if(errorAlert.is(":visible")){
                        var errors = 0;
                        form.each(function(){
                            if($(this).find('.form-group').hasClass('has-error')){
                                errors += 1;
                            } 
                        });

                        if(errors == 0){ 
                            successAlert.show();
                            errorAlert.hide();
                        }
                    }

                },

                submitHandler: function (form) {

                    // Using FormData to append file type to form input
                    var formData = new FormData($(form)[0]);

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        // data: $(form).serialize(),
                        data: formData,
                        dataType: 'json',                        
                        processData: false,
                        contentType: false,
                        success: function (data) {

                            // console.log(data);
                            // return;
                            
                            var titleMsg;
                            var textMsg;

                            if(data.method == "PATCH"){
                                titleMsg = "Update!";
                                textMsg = 'Data has been updated!';
                            }else{
                                titleMsg = "Insert!";
                                textMsg = 'Data has been created!';
                            }

                            swal({
                                    title: titleMsg,
                                    text: textMsg,
                                    type: 'success'
                                },
                                function(){
                                    window.location.href = data.url;
                                    // console.log(data);
                                }
                            )
                            // console.log(data.method);

                        },
                        error: function(response) {
                            console.log('Error:', response);
                            swal("Error!", "Failed to perform the task!", "error");
                        }
                    });

                }
            });


    }

    var quizTargetValidation = function() {

            var form = $('#form_quiz_target');
            var errorAlert = $('.alert-danger', form);
            var successAlert = $('.alert-success', form);
             
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    role_id: {
                        required: true,
                    },
                    grading_id:{
                        required: true,
                    },
                },
                messages:{

                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    successAlert.hide();
                    errorAlert.show();
                    App.scrollTo(errorAlert, -200);
                },

                errorPlacement: function (error, element) { // render error placement for each input type

                    if (element.parent(".input-icon").size() > 0) {

                        // For icon group
                        var icon = element.parent('.input-icon').children('i');
                        icon.removeClass('fa-check').addClass("fa-warning");  
                        icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});

                    } else if (element.parent(".input-group").size() > 0){

                        // For select option
                        if(element.parent('.input-group').children('.input-group-addon')){

                            var span = element.parent('.input-group').children('.input-group-addon');
                            span.removeClass('display-hide');

                            var spanIcon = $(span).children('i');
                            spanIcon.removeClass('fa-check').addClass("fa-warning");
                            spanIcon.removeClass('font-green').addClass("font-red");
                            spanIcon.attr("data-original-title", error.text()).tooltip({'container': 'body'});

                        }else{

                            error.insertAfter(element.parent(".input-group"));

                        }

                    } else if (element.attr("data-error-container")) { 

                        error.appendTo(element.attr("data-error-container"));

                    } else if (element.parents('.mt-radio-list') || element.parents('.mt-checkbox-list')) {                        
                        if (element.parents('.mt-radio-list')[0]) {
                            error.appendTo(element.parents('.mt-radio-list')[0]);
                        }
                        if (element.parents('.mt-checkbox-list')[0]) {
                            error.appendTo(element.parents('.mt-checkbox-list')[0]);
                        }
                    } else if (element.parents('.mt-radio-inline') || element.parents('.mt-checkbox-inline')) {

                        if (element.parents('.mt-radio-inline')[0]) {
                            error.appendTo(element.parents('.mt-radio-inline')[0]);
                        }
                        if (element.parents('.mt-checkbox-inline')[0]) {
                            error.appendTo(element.parents('.mt-checkbox-inline')[0]);
                        }

                    } else {

                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }


                    // Check if all requirement invalid and show error text
                    if(successAlert.is(":visible")){
                        var errors = 0;
                        form.each(function(){
                            if($(this).find('.form-group').hasClass('has-error')){
                                errors += 1;
                            } 
                        });

                        if(errors > 0){ 
                            successAlert.hide();
                            errorAlert.show();
                        }
                    }

                },

                highlight: function (element) { // hightlight error inputs
                    // set error class to the control group   
                    $(element).closest('.form-group').removeClass("has-success").addClass('has-error');                   
                },

                unhighlight: function (element) { // revert the change done by hightlight
                
                },

                success: function (label, element) {

                    // set success class to the control group
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');

                    // For icon group
                    if ($(element).parent(".input-icon").size() > 0) {
                        var icon = $(element).parent('.input-icon').children('i');
                        icon.removeClass("fa-warning").addClass("fa-check");
                    }

                    // For select option
                    if ($(element).parent(".input-group").size() > 0){

                        if($(element).parent('.input-group').children('.input-group-addon')){
                            var span = $(element).parent('.input-group').children('.input-group-addon');
                            span.removeClass('display-hide');

                            var spanIcon = $(span).children('i');
                            spanIcon.removeClass('fa-warning').addClass("fa-check");
                            spanIcon.removeClass('font-red').addClass("font-green");                            
                        }
                    }

                    // Check if all requirement valid and show success text
                    if(errorAlert.is(":visible")){
                        var errors = 0;
                        form.each(function(){
                            if($(this).find('.form-group').hasClass('has-error')){
                                errors += 1;
                            } 
                        });

                        if(errors == 0){ 
                            successAlert.show();
                            errorAlert.hide();
                        }
                    }

                },

                submitHandler: function (form) {

                    // Using FormData to append file type to form input
                    var formData = new FormData($(form)[0]);

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        // data: $(form).serialize(),
                        data: formData,
                        dataType: 'json',                        
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            
                            var titleMsg;
                            var textMsg;

                            if(data.method == "PATCH"){
                                titleMsg = "Update!";
                                textMsg = 'Data has been updated!';
                            }else{
                                titleMsg = "Insert!";
                                textMsg = 'Data has been created!';
                            }

                            swal({
                                    title: titleMsg,
                                    text: textMsg,
                                    type: 'success'
                                },
                                function(){
                                    // window.location.href = data.url;
                                    // console.log(data);
                                    $('#quizTarget').modal('hide');  
                                }
                            )
                            // console.log(data.method);

                        },
                        error: function(response) {
                            console.log('Error:', response);
                            swal("Error!", "Failed to perform the task!", "error");
                        }
                    });

                }
            });


    }

    return {
        //main function to initiate the module
        init: function () {

            quizValidation();
            quizTargetValidation();

        }

    };

}();

/*
 * Image upload handler
 *
 */

 var ImageHandler = function () {

    //File input change (to check upload just image [jpg, jpeg, png, gif, svg] & Max size 2048)
    $("input:file").change(function (e){                
        // error.appendTo();
        // $(this).attr()
        // alert($(this).parent('.input-group').children('.error_message'));
        // $(this).parent('.input-group').children('.error_message')[0].innerHTML += "tes";
        // alert($(this).parent('.input-group').children('.error_message')[0].innerHTML);

        var form = $('#form_quiz');
        var errorAlert = $('.alert-danger', form);
        var successAlert = $('.alert-success', form);
        var filename = $(this).val();          
        var extension = filename.replace(/^.*\./, '');
        var error_container = $(this).parent('.input-group').children('.file_error_message');
        var error_message = '';

        if (extension == filename) {
            extension = '';
        } else {                 
            extension = extension.toLowerCase();
        }

        switch (extension) {
            case '':
                $(this).closest('.form-group').removeClass("has-error");
                $(this).closest('.form-group').removeClass("has-success");
                break;
            case 'jpg': case 'jpeg': case 'png': case 'gif': case 'svg':

                if(typeof $(this)[0].files[0] !== 'undefined'){
                    if(($(this)[0].files[0].size/1024) > 2048){
                        $(this).closest('.form-group').removeClass("has-success").addClass("has-error");
                        error_message = "Max file size reached!";
                        break;
                    }
                }

                $(this).closest('.form-group').removeClass("has-error").addClass("has-success");                        
                break;

            default:
                $(this).closest('.form-group').removeClass("has-success").addClass("has-error");
                error_message = "Please select an image type file like above!";
                break;
        }

        if(error_message != ''){
            error_container.removeAttr('style');
            error_container[0].setAttribute("style","color: #e73d4a;");
            error_container[0].innerHTML = "";
            error_container[0].innerHTML = error_message;
        }else{
            error_container[0].setAttribute("style","display: none;");
        }

        // Check if all requirement valid and show success text
        if(errorAlert.is(":visible") || successAlert.is(":visible")){
            var errors = 0;
            form.each(function(){
                if($(this).find('.form-group').hasClass('has-error')){
                    errors += 1;
                } 
            });

            if(errors == 0){ 
                successAlert.show();
                errorAlert.hide();
            }else{
                successAlert.hide();
                errorAlert.show();
            }
        }

        // $(this).closest('.form-group').addClass("has-success");
    });


 };


/*
 * Set up module
 *
 */ 

jQuery(document).ready(function() {
    FormValidation.init();
    ImageHandler();
});

/*
 * Select2 validation
 *
 */ 

$(document.body).on("change",".select2select",function(){

    select2Change($(this), $('#form_quiz'));
    
});

/*
 * Add custom validation
 *
 */

jQuery.validator.addMethod("validate_filename",function(value, element) {

    if(/^([a-zA-Z0-9_\.\-])+$/.test( value )){
     return true;
    }
    else{
     return false;
    }
},"Please enter a valid filename.");

// Reset Validation
function resetValidation(){
    $('#form_quiz_target').each(function(){
        $(this).find('.form-group').removeClass('has-error').removeClass('has-success');            
        $(this).find('.fa').removeClass('fa-check').removeClass('fa-warning');
    });

    $('.alert-danger', $('#form_quiz_target')).hide();
    $('.alert-success', $('#form_quiz_target')).hide();
}