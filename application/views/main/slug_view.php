<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=768px, minimum-scale=1.0" />
		<link rel="stylesheet" href="assets/css/html5_reset.css">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="assets/css/jquery_ui_theme/jquery-ui-1.8.16.custom.css" />
		<link rel="stylesheet" href="assets/css/global.css"> 
		<!-- matt change this -->
		<!-- <link rel="stylesheet" href="http://localhost/assets/css/global.css"> -->
		
		<!-- Load the Google AJAX API Loader -->
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>

        <!-- Load the Google Friend Connect javascript library. -->
        <script type="text/javascript">
        google.load('friendconnect', '0.8');
        </script>

        <!-- Initialize the Google Friend Connect OpenSocial API. -->
        <script type="text/javascript">
        google.friendconnect.container.setParentUrl('/' /* location of rpc_relay.html and canvas.html */);
        google.friendconnect.container.initOpenSocialApi({
          site: '<?php echo $this->config->item('gfc_site_id_dev') ?>',
          onload:function(securityToken){ initAllData();}

        });

        function initAllData() {
          var req = opensocial.newDataRequest();
          req.add(req.newFetchPersonRequest("VIEWER"), "viewer_data");
          var idspec = new opensocial.IdSpec({
              'userId' : 'OWNER',
              'groupId' : 'FRIENDS'

          });
          req.send(onData);
        };

    // callback handler for datarequest.send() from above 
    function onData(data) {

          var viewer_info = document.getElementById("viewer-info");
          if (data.get("viewer_data").hadError()) {
              
            google.friendconnect.renderSignInButton({ 'id': 'gfc-button' });
            document.getElementById('gfc-button').style.display = 'block';
            viewer_info.innerHTML = '<span style="font-size: 75%" id="explanation">To keep this URL forever:</span>';
            
            global_gfc_id = '';
          
          } else {
              
            document.getElementById('gfc-button').style.display = 'none';
            var viewer = data.get("viewer_data").getData();
            viewer_info.innerHTML = "Hello, " + viewer.getDisplayName() + " " +
                "<a href='<?=base_url()?>'>Create New Slug</a> | " +
                "<a href='' onclick='showPreviousSlugs(); return false;'>Your Slugs</a> | " +
                "<a href='' onclick='showHistory(); return false;'>History</a> | " +
                "<a href='#' onclick='google.friendconnect.requestSettings()'>Settings</a> | " +
                "<a href='#' onclick='google.friendconnect.requestInvite()'>Invite</a> | " +
                "<a href='#' onclick='google.friendconnect.requestSignOut()'>Sign out</a>";
            var conference_id = $('#conference_id').val();
            
            global_gfc_id = viewer.getId();
            
            $('#main_current_callers').show();

          }
    };
        
    function runLoopTimeout()
    {
        loadCurrentCallers();
        setTimeout("runLoopTimeout()",5000);           
    }
            
    function loadCurrentCallers()
    {
        if(typeof global_gfc_id == 'undefined')
        {
            $('#main_current_callers').html('');
        }
        else if(global_gfc_id == '')
        {
            $('#main_current_callers').html('');
        }
        else
        {
            $.post('/ajax/current_callers/<?=$this->uri->segment(1)?>',{"gfc_id":global_gfc_id}, function(e){
                $('#main_current_callers').html(e);    
            });   
        }
    }
       
    function removeCaller(call_id, number)
    {            
        $.getJSON('/ajax/remove_caller/',{"call_id":call_id,"number":number},function(e){       
            if(e.status.ok == true)
            {       
                flashMessage('Caller: '+ number +' has been removed');           
            }
        });           
    }
    
    function showPreviousSlugs()
    {
        if(typeof global_gfc_id == 'undefined')
        {
            $('#previous_slugs').show().html('<p>No slugs</p>');
        }
        else if(global_gfc_id == '')
        {
            $('#previous_slugs').show().html('<p>No slugs</p>');
        }
        else
        {
            $.post('/ajax/show_previous_slugs/',{"gfc_id":global_gfc_id, "current_slug":"<?=$this->uri->segment(1)?>"},function(e){
                $('#previous_slugs').show().html(e);
            });
        }
    }
    
    function showHistory()
    {
        $.post('/ajax/show_history/',{"gfc_id":global_gfc_id, "current_slug":"<?=$this->uri->segment(1)?>"},function(e){
                $('#slug_history').show().html(e);
        });
    }
    
    function hidePreviousSlugs()
    {
        $('#previous_slugs').slideUp();
    }

    function flashMessage(message)
    {
        $('#flash_message').slideDown().html(message);              
        setTimeout("$('#flash_message').slideUp()",2000);
    }
            
    function flashCallingNow()
    {
        $('#main_calling').slideDown();              
        setTimeout("$('#main_calling').slideUp()",5000);
    }
    
    function deleteSlug(slug)
    {
        $.getJSON('/ajax/delete_slug/',{"slug":slug, "gfc_id":global_gfc_id},function(e){
            showPreviousSlugs();
            return false;
        });
    }
    

        </script>
        <script type="text/javascript" src="assets/js/global.js"></script>

		<title>Speek</title>
	</head>
	<body>
	    <div id="flash_message"></div>
		<div id="wrap">
			<div id="container">
				<div id="top-brand"></div>
				<div id="top-nav">
					<p id="viewer-info"></p>
					<div id="gfc-button"></div>
				</div>  
  
				<div id="main">
					<div id="slug-view-main-top" class="main-cta-top-message">
						<span class="bold">This is a dedicated Speek line for</span> speek.com/<span class="bold"><?=$this->uri->segment(1)?></span><br>
						Enter your info to join the call. There are already X people there.
					</div>
					<div id="">
					  <a href="#" id="send_feedback">Send Feedback</a>
					</div>
					<div id="main_form">
						<form id="form_call_now" onSubmit="return false;">
							<ul>
							    <li>
							        <label>Full Name (Optional)</label><br/>
							        <input type="text" id="form_name" name="form_name" />
							    </li>
							    <li>
							        <label>Email (Optional)</label><br/>
							        <input type="text" id="form_email" name="form_email" />
							    </li>
								<li>
									<select name="form_call_method" id="form_call_method" class="form_select">
										<option value="form_call_phone" selected="selected">Phone</option>
										<option value="form_call_skype">Skype</option>
										<option value="form_call_google">Google</option>
									</select>
								</li>
								<li>
									<input type="text" name="form_username" id="form_username" class="form_fields form_username"/>
	
									<span class="form_phone">(</span><input type="text" name="form_phone_01" value="" id="form_phone_01" class="form_fields form_phone" size="1" maxlength="3"/><span class="form_phone">)</span>
									<input type="text" name="form_phone_02" value="" id="form_phone_02" class="form_fields form_phone" size="1" maxlength="3"/><span class="form_phone"> -</span>
									<input type="text" name="form_phone_03" value="" id="form_phone_03" class="form_fields form_phone" size="2" maxlength="4"/><span id="call_method_message"></span>
	
									<input type="hidden" id="slug" name="slug" value="<?=$this->uri->segment(1)?>" />
								</li>
								<li>
									<input type="submit" value="Call Now">
								</li>
							</ul>
						</form>
					</div>
				</div>
				<div id="main_calling">
					<h1>Calling You Now !<br />Pick Up !</h1>
				</div>
			
				<div id="previous_slugs"></div>
				<div id="slug_history"></div>
			
				<div id="dialog_loading" class="dialog_box">
				  <h2>Loading...</h2><br/>
				  <img src="assets/img/loading-icon.gif" border="0" /><br/>
				  <p>Please Wait</p>
				</div>
				
				<div id="feedback_form_div" title="Send Feedback">
				    <div id="feedback_message_error"></div><br/>
                     <form id="feedback_form" onsubmit="return false;">
                         <label for="feedback_full_name">Full Name:</label><br/>
                         <input type="text" name="feedback_full_name" id="feedback_full_name" />
                         <span id="feedback_email_address_error"></span><br/>
                         <label for="feedback_email_address">Email:</label><br/>
                         <input type="text" name="feedback_email_address" id="feedback_email_address" />
                         <span id="feedback_email_address_error"></span><br/>
                         <label for="feedback_message">Feedback Message:</label>
                         <textarea name="feedback_message" id="feedback_message"></textarea><br/>
                         <input type="hidden" name="slug" value="<?=$this->uri->segment(1)?>" />
                         <input type="submit" value="Send Feedback" />
                     </form>
                </div>
				<div id="main_current_callers"></div>
			
			</div>
		</div>
	</body>
</html>