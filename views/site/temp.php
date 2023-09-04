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

        .dropbox {
            height: 500px;
            width: 373px;
            margin-left: 30px;
            margin-right: 30px;
            border: 5px solid black;
        }
        
        .dropbox2 {
            height: 80px;
            width: 373px;
            margin-left: 30px;
            margin-right: 30px;
            border: 5px solid black;
        }
        
        .dropbox22 {
            height: 380px;
            width: 373px;
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
            border: 5px solid black;
        }
        
        .orga {
            height: 50px;
            width: 280px;
            border-radius: 20px;
            border: 2px solid red;
            background: red;
            position: absolute;
        }
        
        .orgaGay {
            height: 50px;
            width: 280px;
            border-radius: 20px;
            border: 2px solid yellow;
            background: yellow;
            position: absolute;
        }
        
        .orgaGreen {
            height: 50px;
            width: 280px;
            border-radius: 20px;
            border: 2px solid green;
            background: green;
            position: absolute;
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
        <div class="dropbox" id="dropbox1"></div>
        <div>
            <div class="dropbox2" id="dropbox2"></div>
            <div class="dropbox22" id="dropbox2"></div>
        </div>
        <div class="dropbox" id="dropbox3"></div>
    </div>
    
    <div id="pidor1" class="orga">
        <p style="color: black">ГБОУ "АТЛ"</p>
    </div>
</body>
<script>
    window.onload = function () {
    //select the thing we wanna drag
    var mustachio = document.getElementById('pidor1');
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
        let elem = document.getElementById("pidor1");
        if (x > 430)
        {
            elem.classList.remove('orga');
            elem.classList.remove('orgaGay');
            elem.classList.remove('orgaGreen');
            elem.classList.add('orgaGay');
        }
        if (x > 800)
        {
            elem.classList.remove('orga');
            elem.classList.remove('orgaGay');
            elem.classList.remove('orgaGreen');
            elem.classList.add('orgaGreen');
        }
        if (x < 430)
        {
            elem.classList.remove('orga');
            elem.classList.remove('orgaGay');
            elem.classList.remove('orgaGreen');
            elem.classList.add('orga');
        }
        //check to see if that position meets our constraints
    })
}
</script>
</html>