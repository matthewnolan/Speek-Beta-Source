<?php
class Speek
{
	#function for Call Now
	function callNow($api_key,$format,$description,$numbers,$organizer=NULL,
					$music_on_hold=NULL,$greeting=NULL,$greeting_method=NULL,
					$greeting_text=NULL,$greeting_link=NULL,$recording=NULL,
					$sound_on_inout=NULL,$exit_on_leave=NULL,$call_name=NULL,$rsvp=NULL)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/callNow?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($description != '')
				$params['description'] = $description;	
			else
				$errArr[] = 'Call description not provided!';
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			if($organizer != "")
				$params['organizer'] = $organizer;
			if($music_on_hold != "")
				$params['music_on_hold'] = $music_on_hold;
			if($greeting != "")
				$params['greeting'] = $greeting;
			if($greeting_method != "")
			{
				$params['greeting_method'] = $greeting_method;
				if($greeting_method == 'text' && $greeting_text == '')
					$errArr[] = 'Greeting text is not provided!';
				else
					$params['greeting_text'] = $greeting_text;
				if($greeting_method == 'audio' && $greeting_link == '')
					$errArr[] = 'Greeting link is not provided!';
				else
					$params['greeting_link'] = $greeting_link;
			}
			if($recording != "")
				$params['recording'] = $recording;
			if($sound_on_inout != "")
				$params['sound_on_inout'] = $sound_on_inout;
			if($exit_on_leave != "")
				$params['exit_on_leave'] = $exit_on_leave;
			if($call_name != "")
				$params['call_name'] = $call_name;
			if($rsvp != "")
				$params['rsvp'] = $rsvp;
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function for duplicateCall
	function duplicateCall($api_key,$format,$call_id,$date,$time,$timezone=NULL)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/duplicateCall?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($date != '')
				$params['date'] = $date;	
			else
				$errArr[] = 'Date not provided!';
			if($time != '')
				$params['time'] = $time;	
			else
				$errArr[] = 'Time not provided!';
			if($timezone != '')
				$params['timezone'] = $timezone;	
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function for scheduleCall
	function scheduleCall($api_key,$format,$description,$numbers,$time,$date,$organizer=NULL,
					$music_on_hold=NULL,$greeting=NULL,$greeting_method=NULL,$timezone=NULL,$greeting=NULL,
					$greeting_method=NUL,$greeting_text=NULL,$greeting_link=NULL,$recording=NULL,
					$sound_on_inout=NULL,$exit_on_leave=NULL,$call_name=NULL,$rsvp=NULL)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/scheduleCall?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($description != '')
				$params['description'] = $description;	
			else
				$errArr[] = 'Call description not provided!';
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			if($organizer != "")
				$params['organizer'] = $organizer;
			if($music_on_hold != "")
				$params['music_on_hold'] = $music_on_hold;
			if($greeting != "")
				$params['greeting'] = $greeting;
			if($greeting_method != "")
			{
				$params['greeting_method'] = $greeting_method;
				if($greeting_method == 'text' && $greeting_text == '')
					$errArr[] = 'Greeting text is not provided!';
				else
					$params['greeting_text'] = $greeting_text;
				if($greeting_method == 'audio' && $greeting_link == '')
					$errArr[] = 'Greeting link is not provided!';
				else
					$params['greeting_link'] = $greeting_link;
			}
			if($recording != "")
				$params['recording'] = $recording;
			if($sound_on_inout != "")
				$params['sound_on_inout'] = $sound_on_inout;
			if($exit_on_leave != "")
				$params['exit_on_leave'] = $exit_on_leave;
			if($call_name != "")
				$params['call_name'] = $call_name;
			if($rsvp != "")
				$params['rsvp'] = $rsvp;
			if($date != '')
				$params['date'] = $date;
			else
				$errArr[] = 'Date not provided!';
			if($time != '')
				$params['time'] = $time;
			else
				$errArr[] = 'Time not provided!';
			if($timezone != '')
				$params['timezone'] = $timezone;
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function for dropCall
	function dropCall($api_key,$format,$call_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/dropCall?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function restartCall
	function restartCall($api_key,$format,$call_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/restartCall?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	
	#function addParticipant
	function addParticipant($api_key,$format,$numbers,$call_id,$retry=NULL,$hunt=NULL,$longitude=NULL,$latitude=NULL)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/addParticipant?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($retry != '')
				$params['retry'] = $retry;
			if($hunt != '')
				$params['hunt'] = $hunt;
			if($longitude != '')
				$params['longitude'] = $longitude;
			if($latitude != '')
				$params['latitude'] = $latitude;
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function removeParticipant
	function removeParticipant($api_key,$format,$call_id,$numbers)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/removeParticipant?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function setIVR
	  function setIVR($api_key,$format,$ivr_link)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/setIVR?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($ivr_link != '')
				$params['ivr_link'] = $ivr_link;	
			else
				$errArr[] = 'Call id not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function removeIVR
	  function removeIVR($api_key,$format,$ivr_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/removeIVR?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($ivr_id != '')
				$params['ivr_id'] = $ivr_id;	
			else
				$errArr[] = 'Call id not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function ivr
	  function ivr($api_key,$format,$ivr_id,$numbers)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/ivr?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($ivr_id != '')
				$params['ivr_id'] = $ivr_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function transfer
	  function transfer($api_key,$format,$call_id,$numbers,$paction)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/transfer?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			if($paction != '')
				$params['paction'] = $paction;
			else
				$errArr[] = 'Action not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function participantAction
	  function participantAction($api_key,$format,$call_id,$numbers,$paction)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/participantAction?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			if($paction != '')
				$params['paction'] = $paction;
			else
				$errArr[] = 'Action not provided!';

			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function muteParticipant
	function muteParticipant($api_key,$format,$call_id,$numbers)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/muteParticipant?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function unmuteParticipant
	function unmuteParticipant($api_key,$format,$call_id,$numbers)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/unmuteParticipant?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function muteAll
	function muteAll($api_key,$format,$call_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/muteAll?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function unmuteAll
	function unmuteAll($api_key,$format,$call_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/unmuteAll?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function playSound
	function playSound($api_key,$format,$call_id,$Sound_link)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/playSound?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($Sound_link != '')
				$params['sound_link'] = $Sound_link;	
			else
				$errArr[] = 'Sound link not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function playText
	function playText($api_key,$format,$call_id,$text,$members)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/playText?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			if($text != '')
				$params['text'] = $text;	
			else
				$errArr[] = 'Text not provided!';
			if($members != '')
				$params['members'] = $members;	
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function getRecording
	function getRecording($api_key,$format,$call_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/getRecording?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function getParticipantStatus
	function getParticipantStatus($api_key,$format,$call_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/getParticipantStatus?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function callAnalytics
	function callAnalytics($api_key,$format,$call_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/callAnalytics?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($call_id != '')
				$params['call_id'] = $call_id;	
			else
				$errArr[] = 'Call id not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function recording
	function recording($api_key,$format,$numbers)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/recording?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function translateBC
	function translateBC($api_key,$format,$numbers,$Broadcast_id=NULL,$text=NULL,$source=NULL,$target=NULL)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/translateBC?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			if($Broadcast_id != "")
				$params['broadcast_id'] = $Broadcast_id;
			if ($text != "")
			        $params['text'] = $text;
			else
			        $errArr[] = 'text message not specified!';
			if ($source != "")
			        $params['source'] = $source;
			else
			        $errArr[] = 'source language not specified!';
			if ($target != "")
			        $params['target'] = $target;
			else 
			        $errArr[] = 'target language not specified!';

			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function broadcast
	function broadcast($api_key,$format,$numbers,$Broadcast_id=NULL,
			   $Broadcast_method=NULL,$Broadcast_text=NULL,$Broadcast_link=NULL,$Broadcast_voice=NULL)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/broadcast?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($numbers != '')
				$params['numbers'] = $numbers;
			else
				$errArr[] = 'Phone number not provided!';
			if($Broadcast_id != "")
				$params['broadcast_id'] = $Broadcast_id;
			if($Broadcast_method != "")
			{
				$params['broadcast_method'] = $Broadcast_method;
				if($Broadcast_method == 'text' && $Broadcast_text == '')
					$errArr[] = 'Greeting text is not provided!';
				else {
					$params['broadcast_text'] = $Broadcast_text;
					if ($Broadcast_voice != "")
					  $params['broadcast_voice'] = $Broadcast_voice;
				}
				if($Broadcast_method == 'audio' && $Broadcast_link == '')
					$errArr[] = 'Greeting link is not provided!';
				else
					$params['broadcast_link'] = $Broadcast_link;
			}
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	#function broadcastAnalytics
	function broadcastAnalytics($api_key,$format,$broadcast_id)
	{
		$errArr = array();
		if($api_key != "")
		{
			$url = 'http://184.72.240.225/calls/broadcastAnalytics?';
			$params['api_key'] = $api_key;
			if($format != "")
				$params['format'] = $format;
			else
				$errArr[] = 'Server response format not specified!'	;
			if($broadcast_id != '')
				$params['broadcast_id'] = $broadcast_id;
			else
				$errArr[] = 'Broadcast id not provided!';
			#submit the url request to the speek api when ther is not any error
			if(count($errArr) == 0)
				return $this->submitRequest($url,$params);
			else
			{
				$err = @implode("<br />",$errArr);
				return $err;
			}		
		}
		else
		{
			return "API Key Not Provided!";
		}
	}
	# sendRequst
	# Sends a REST Request to the speek REST API
	# $url : the URL (URL with variables)
	function submitRequest($url,$params)
	{
	//	echo "<strong>Request Url : </strong><a href='".$url."' target='_blank'>".$url."</a><br /><br />";
	//	echo "<strong>Request Params : </strong><br />";
		$encoded = '';
		foreach($params AS $key=>$value)
		{
	//		echo "$key = ".$value."<br />";
			$encoded .= "$key=".urlencode($value)."&";
		}
		$param = substr($encoded, 0, -1);
		# ensure Curl is installed
		if(@function_exists(curl_init))
		{
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);			
			$head = curl_exec($ch); 
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			if(curl_error($ch))
				return 'Curl error: ' . curl_error($ch);
			else
				return $head;
			curl_close($ch); 
		}
		else
			return "Curl is not enabled on your server. Please enable curl.";
	}
}
?>
