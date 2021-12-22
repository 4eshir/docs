<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            width: 100%;
            display: flex;
            align-items: center;
            flex-direction: column;
            margin-top: 100px;
        }

        #dropbox {
            height: 250px;
            width: 373px;
            padding-left: 30px;
            padding-right: 30px;
            border: 5px solid black;
        }

        img {
            position: absolute;
            height: 150px;
            width: 150px;
        }
    </style>
    <title>Document</title>
</head>
<body>
    <div style="display: flex">
        <div id="dropbox"></div>
        <div id="dropbox"></div>
        <div id="dropbox"></div>
    </div>
    
    <img id="gif" src="https://media.giphy.com/media/3ohzdNF74rFtPMZzck/giphy.gif">
</body>
<script>
    window.onload = function () {
    //select the thing we wanna drag
    var mustachio = document.getElementById('gif');
    //listen to the touchmove event, every time it fires, grab the location of the touch
    //then assign it to mustachio
    mustachio.addEventListener('touchmove', function (ev) {
        //grab the location of the touch
        var touchLocation = ev.targetTouches[0];
        //assign mustachio new coordinates based on the touch
        mustachio.style.left = touchLocation.pageX + 'px';
        mustachio.style.top = touchLocation.pageY + 'px';
    })
    mustachio.addEventListener('touchend', function (ev) {
        //current mustachio position when dropped
        var x = parseInt(mustachio.style.left);
        var y = parseInt(mustachio.style.top);
        let elem = document.getElementById("dropbox");
        elem.innerHTML = "<p>" + x + " " + y + "</p>";
        //check to see if that position meets our constraints
    })
}
</script>
</html>