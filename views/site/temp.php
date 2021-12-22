<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
</head>

<body>
    <div id="cont">
      <p style="font-size:50px;" >Какой то тескт</p>
    </div>
</body>
    
    <script>
    document.querySelector('#cont').addEventListener('touchstart', function(){
	document.querySelector('#cont').style.background = '#f00';
    });

      document.querySelector('#cont').addEventListener('touchend', function(){
        document.querySelector('#cont').style.background = '#fff';
    });
</script>
    
</html>
