<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=768px, minimum-scale=1.0" />
		<link rel="stylesheet" href="assets/css/html5_reset.css">
		<link rel="stylesheet" href="assets/css/global.css">
		<link rel="stylesheet" href="assets/css/conference_create.css">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="assets/css/jquery_ui_theme/jquery-ui-1.8.16.custom.css" />
		<script type="text/javascript" charset="utf-8" src="assets/js/zclip.js"></script>

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
		</script>
		<script type="text/javascript">
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
		  } else {
		    document.getElementById('gfc-button').style.display = 'none';
		    var viewer = data.get("viewer_data").getData();
		    viewer_info.innerHTML = "Hello, " + viewer.getDisplayName() + " " +
		        "<a href='#' onclick='showPreviousSlugs(); return false;'>Your Slugs</a> | " +
		        "<a href='#' onclick='google.friendconnect.requestSettings()'>Settings</a> | " +
		        "<a href='#' onclick='google.friendconnect.requestInvite()'>Invite</a> | " +
		        "<a href='#' onclick='google.friendconnect.requestSignOut()'>Sign out</a>";
		    var conference_id = $('#conference_id').val();
		    
		        global_gfc_id = viewer.getId();
		   
			if (conference_id != '')
			{
				

				var param_val = {
                    "gfc_id"        : viewer.getId(),
                    "conference_id" : conference_id
                    };
                $.getJSON('/ajax/set_conference_user_id', param_val, function(e){
                     if (e.success == true)
                    {
                        $('#explanation').hide();
                        $('#thanks_for_logging_in').show();
                    } 
                });

				
				
			}
		  }
		};

		</script>
		
		<script type="text/javascript" charset="utf-8">

			$(document).ready(function(e){

					/**
					 * Create Slug
					 */		
					$('#form_create_url').submit(function(ee) {
						
						if($('#slug').val() == ''){
							
							$('#slug').next().text('Enter a Slug');
							return false;
							
						}else{
						    
						    // if logged in include google friend connect id
						    if(typeof global_gfc_id == 'undefined'){
						        var form_create_url_data = $('#form_create_url').serialize(); 
						    }else{
						        var form_create_url_data = $('#form_create_url').serialize();
                                var form_create_url_data = form_create_url_data+'&gfc_id='+global_gfc_id;  
						    }
						   
							$.getJSON('/ajax/create_slug', form_create_url_data, function(create_slug) {
								//matt dont forget to remove index.php   
				
								if(create_slug.message == true) {
								    
								    //global var
									full_url = '<?php echo base_url(); ?>' + create_slug.slug;
									
									$('#main').hide();
									$('#main_success').show();
									$('#url_link').html('<a href="<?=base_url()?>' + create_slug.slug + '"><?=base_url()?>' + create_slug.slug + '</a>');
									$('#conference_id').val(create_slug.conference_id);
									initAllData();
									$('a#copy_clipboard').zclip({
							        	path:'assets/flash/ZeroClipboard.swf',
							        	copy:full_url,
							        	afterCopy:function(){
							        	 	
							        	 	flashMessage('Copied: <strong>'+full_url+'</strong> to the clipboard');
							        	 	
							        	 }
							    	});

								} else {
									if (create_slug.code == 4) // honeypot field was filled, so we're showing the user a captcha
									{
										$('#captcha').show();
										$('#captcha_expected').val('yes');
									}
									if (create_slug.code == 2) // slug taken error
									{
										//$('#slug_error').next().text('Oops! Slug Taken, Try another one.').css({"display":"block"});
										
										$('#slug_error').show();
										
									}
									if (create_slug.code == 3) // recaptcha error
									{
										$('#slug').next().text('Oops! Please reenter the captcha.').css({"display":"block"});
									}
								}
				
							});
						
						}
				});
			    
			    /**
			     * Open Email form dialog
			     */
				$('#email_to_friend').click(function(e){
				    $('#email_message').text('Click on the link to join the conference: '+full_url);
				    
				    $('#email_form_div').dialog({
                        autoOpen: true,
                        modal: true,
                        resizable: false,
                        draggable: false
                    });
				});
				
				/**
				 * Send Email Form
				 */
				$('#email_form').submit(function(e) {
				    
				    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
				    
				    if($('#email_address').val() == '')
				    {
				        $('#email_address_error').html('Please Enter an Email Address');
				        return false;
				    }
				    else if(!emailRegex.test($('#email_address').val()))
				    {
                        $('#email_address_error').html('Please Enter a valid Email');
                        return false;
                    }
                    else
                    {
                        $.getJSON('/ajax/send_email',$('#email_form').serialize(),function(e){
                            if(e.sent == true)
                            {
                                $('#email_form_div').dialog('close');
                                flashMessage('The email has been sent');
                            }
                            else
                            {
                                $('#email_form_div').dialog('close');
                                flashMessage(e.message);
                            }
                        });
                    }
                     
				});
				
			
						


			});
			
    function showPreviousSlugs()
    {
        if(typeof global_gfc_id == 'undefined')
        {
            $('#previous_slugs').html('<p>No slugs</p>');
        }
        else if(global_gfc_id == '')
        {
            $('#previous_slugs').html('<p>No slugs</p>');
        }
        else
        {
            $.post('/ajax/show_previous_slugs/',{"gfc_id":global_gfc_id},function(e){
                $('#previous_slugs').html(e);
            });
        }
    }
    
    /**
     * Flash data on top div
     */
    function flashMessage(message)
    {
        $('#flash_message').slideDown().html(message);			    
	    setTimeout("$('#flash_message').slideUp()",2000);
	}
	
	function deleteSlug(slug)
    {
        $.getJSON('/ajax/delete_slug/',{"slug":slug, "gfc_id":global_gfc_id},function(e){
            
            showPreviousSlugs();
            return false;
            
        });
    }
			
									
                        			
		</script>
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
					<div id="main-cta-area">
						<div class="main-cta-top">
							<span class="main-cta-2nd main-cta-active">
								<div id="main-cta-2nd-image-left"></div>
								<p>1. Make your link</p>
							</span>
							<span class="main-cta-2nd">
								<div id="main-cta-2nd-image-right"></div>
								<p>2. Give it to friends</p>
							</span>
						</div>
						<div class="main-cta-top-message">Organizing a conference call is as easy as giving out a link</div>				
					</div>
					<form id="form_create_url" onSubmit="return false;">
						<ul>
							<li><label for="slug" id="slug-title"><!-- <?=base_url()?> -->speek.com/</label><input id="slug" name="slug" type="text" size="40"><div id="slug_error" style="display:none;">Oops! Slug Taken, Try another one.'</div><input type="submit" value="Save It"></li>
						
							<li><div id="captcha" class="hidden"><br>Please fill out this captcha to verify that you are human: <br /><?php echo $recaptcha_html ?></div></li>
						
							<li>
								<input id="email" name="email" type="text" size="10">
								<input id="captcha_expected" name="captcha_expected" value="no" class="hidden">
								<input id="conference_id" name="conference_id" value"" class="hidden">
								
							</li>
						</ul>
					</form>
				</div>
				<div id="main_success">
					
					
					<div id="main">	
						<div id="main-cta-area">
							<div class="main-cta-top">
								<span class="main-cta-2nd">
									<div id="main-cta-2nd-image-left"></div>
									<p>1. Make your link</p>
								</span>
								<span class="main-cta-2nd main-cta-active">
									<div id="main-cta-2nd-image-right"></div>
									<p>2. Give it to friends</p>
								</span>
							</div>
							<div class="main-cta-top-message">Now give that link to your friends and tell them when to call</div>
							<div id="url-link-outter">
								<span id="url_link"></span>
								<span id="url-link-inner">
									
									<a href="#" id="copy_clipboard" class="button_style_grey">Copy It</a>
									<a href="#" id="email_to_friend" class="button_style_grey">Email It</a>
								</span>
							</div>
						</div>
					</div>	


				</div>
				<div id="email_form_div" title="Send Email">
				     <form id="email_form" onsubmit="return false;">
				         <label>Email:</label><br/>
				         <input type="text" name="email_address" id="email_address" />
				         <span id="email_address_error"></span><br/><br/>
				         <textarea name="email_message" id="email_message"></textarea><br/>
				         <input type="submit" value="Send Email" />
				     </form>
				</div>
			
				<div id="previous_slugs"></div>
			
			</div>

		</div>
		<div id="footer">
			<a href="#">About</a>
			<a href="http://developer.speek.com">Speek API Documentation</a>
		</div>
	</body>
</html>
