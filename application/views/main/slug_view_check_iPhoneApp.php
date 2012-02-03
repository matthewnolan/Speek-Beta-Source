<!doctype html> 

<head> 
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
</head> 

      <script type="text/javascript"> 
       //alert('trying to forward to speek app..');  
       window.setTimeout(function () {window.location='<?=$this->uri->segment(1) ?>'}, 25);  
       window.location = 'speek://<?=$this->uri->segment(1)?>'; 
     </script> 
<body> 
   Checking for iPhone app..
</body> 
