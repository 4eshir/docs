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
        
        .orga {
            height: 50px;
            width: 280px;
            border-radius: 20px;
            border: 2px solid red;
            background: red;
        }
        
        .orgaGay {
            height: 50px;
            width: 280px;
            border-radius: 20px;
            border: 2px solid red;
            background: gray;
        }
        
        .orgaGreen {
            height: 50px;
            width: 280px;
            border-radius: 20px;
            border: 2px solid red;
            background: green;
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
        <div id="dropbox1"></div>
        <div id="dropbox2"></div>
        <div id="dropbox3"></div>
    </div>
    
    <div id="pidor" class="orga">
        <p style="color: black">ГБОУ "АТЛ"</p>
    </div>
</body>
<script>
    window.onload = function () {
    //select the thing we wanna drag
    var mustachio = document.getElementById('pidor');
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
        let elem = document.getElementById("dropbox1");
        elem.innerHTML = "<p>" + x + " " + y + "</p>";
        if (x > 430)
        {
            elem.classList.remove('orga');
            elem.classList.remove('orgaGray');
            elem.classList.remove('orgaGreen');
            elem.classList.add('orgaGray');
        }
        if (x > 800)
        {
            elem.classList.remove('orga');
            elem.classList.remove('orgaGray');
            elem.classList.remove('orgaGreen');
            elem.classList.add('orgaGreen');
        }
        if (x < 430)
        {
            elem.classList.remove('orga');
            elem.classList.remove('orgaGray');
            elem.classList.remove('orgaGreen');
            elem.classList.add('orga');
        }
        //check to see if that position meets our constraints
    })
}
</script>
</html>