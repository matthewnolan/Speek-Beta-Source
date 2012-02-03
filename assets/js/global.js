$(document).ready(function(e) {
    open_loading_dialog();

    /**
     * Display the right method onOnload
     */
    display_call_method(get_call_method());

    /**
     * Check if it is the first caller on load
     */
    $.getJSON('/ajax/check_first_caller', {
        "slug" : $('#slug').val()
    }, function(check_first_caller) {
        close_loading_dialog();

        if(check_first_caller.message == true) {

            $('#form_call_method').show();
            return false;

        }
    });
    /**
     * Executed when #form_call_now form is submited
     */
    $('#form_call_now').submit(function(data) {
        
        open_loading_dialog();
        submit_call_now();
        
    });
    // Get the right field onChange
    $('#form_call_method').change(function(e) {

        display_call_method(get_call_method());

    });
    
    //Runs loop to load the current callers
    runLoopTimeout();
    
    
    /**
    * Open Feedback form dialog
    */
    $('#send_feedback').click(function(e){
                    
        $('#feedback_form_div').dialog({
            autoOpen: true,
            modal: true,
            resizable: false,
            draggable: false
        });
        
    });
    
    $('#feedback_form').submit(function(e){
        $.getJSON('/ajax/send_feedback', $('#feedback_form').serialize(), function(e){
            if(e.sent == false)
            {
                $('#feedback_message_error').html(e.error);  
            }
            else
            {
                $('#feedback_form').hide();
                $('#feedback_message_error').html('Your feedback message has been sent.');
                return false;
            }
        });
    });
    
});
// end jQuery onLoad

/**
 * check status before submiting the form
 * an attempt to fix the race condition in the speek API
 */
function submit_call_now(e){
    $.getJSON('/ajax/get_call_status',{'slug':$('#slug').val()},function(e){
        if(e.call_status == 'active'){
            call_now();
        }else if(e.call_status == 'processing'){
            setTimeout("submit_call_now()", 1000);
        }else{
            alert('Sorry, Something failed, please refresh the page and try again.');
        }
    });
}

/**
 * Submit the call now form
 */
function call_now(){
    
        // checks if there is any caller in the conference
        $.getJSON('/ajax/check_first_caller', {
            "slug" : $('#slug').val()
        }, function(check_first_caller) {

            if(check_first_caller.message == false) {
                
                var form_call_method = get_call_method();
                var form_slug = $('#slug').val();
                if(form_call_method == 'form_call_phone') {
                    form_phone_number = '1' + $('#form_phone_01').val() + $('#form_phone_02').val() + $('#form_phone_03').val();
                } else {
                    form_phone_number = $('#form_username').val();
                }

                //if there is no caller create a call_id
                $.getJSON('/ajax/first_caller', {
                    "form_name"              : $('#form_name').val(),
                    "form_email"             : $('#form_email').val(),
                    "form_call_method"       : form_call_method,
                    "form_call_phone_number" : form_phone_number,
                    "slug"                   : form_slug
                }, function(first_caller) {

                    // Add call id to the db
                    $.getJSON('/ajax/add_caller_id', {
                        "slug" : $('#slug').val(),
                        "caller_id" : first_caller.results.call_id
                    }, function(add_caller_id) {
                        if(add_caller_id.message == true) {
                            $('#main').hide();
                            //$('#main_calling').show();
                            flashCallingNow();
                            close_loading_dialog();
                            return false;
                        } else {
                            alert('Could NOT insert the caller id');
                        }

                    });
                });
            } else {
                
                var form_call_method = get_call_method();
                var form_slug = $('#slug').val();
                if(form_call_method == 'form_call_phone') {
                    form_phone_number = '1' + $('#form_phone_01').val() + $('#form_phone_02').val() + $('#form_phone_03').val();
                } else {
                    form_phone_number = $('#form_username').val();
                }

                $.getJSON('/ajax/add_caller', {
                    "slug" : $('#slug').val(),
                    "form_name"              : $('#form_name').val(),
                    "form_email"             : $('#form_email').val(),
                    "form_call_id"           : check_first_caller.call_id,
                    "form_call_method"       : form_call_method,
                    "form_call_phone_number" : form_phone_number
                }, function(add_caller) {

                    $('#main').hide();
                    //$('#main_calling').show();
                    flashCallingNow();
                    close_loading_dialog();
                    return false;

                });
            }

        });
    
}



/**
 * Gets the call method
 */
function get_call_method() {
    var call_method = $("#form_call_method option:selected").val();
    return call_method;
}

/**
 * Displays call method
 */
function display_call_method(get_call_method) {
    switch(get_call_method) {
        case 'form_call_skype':
            $('.form_fields').hide();
            $('.form_phone').hide();
            $('.form_username').show();
            $('#call_method_message').html('@skype.com')
            break;
        case 'form_call_google':
            $('.form_fields').hide();
            $('.form_phone').hide();
            $('.form_username').show();
            $('#call_method_message').html('@gmail.com')
            break;
        default:
            $('.form_fields').hide();
            $('.form_phone').show();
            $('#call_method_message').html('Phone Number')
    }
}

function open_loading_dialog(){
    $('#dialog_loading').dialog({
        autoOpen: true,
        height: 140,
        modal: true,
        resizable: false,
        draggable: false
    });
}
function close_loading_dialog(){
    $('#dialog_loading').dialog( "destroy" );
}
