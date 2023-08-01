<style>
    .main-div{
        padding: 10px;
        margin-top: 30px;
        background-color: #ffffff;
        border: 1px solid red;
        border-radius: 7px;
    }

    .nomination-div{
        border: 1px solid green;
        border-radius: 7px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .nomination-list-div{
        border: 1px solid purple;
        border-radius: 7px;
        padding: 10px;
        overflow-y: scroll;
        width: 50%;
    }

    .nomination-add-div{
        border: 1px solid blue;
        border-radius: 7px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .nomination-add-input-div{
        border: 1px solid #0d6efd;
        border-radius: 7px;
        display: inline-block;

        vertical-align: top;
        height: 100%;
        width: 40%;
    }

    .nomination-add-button-div{
        border: 1px solid #1abc9c;
        border-radius: 7px;
        display: inline-block;

        vertical-align: top;
        height: 100%;
    }

    .nomination-add-button{
        display: block;
        margin: 7px 10px;
        padding: 5px 5px;
        word-break: keep-all;
    }

    .nomination-add-input{
        display: block;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .nomination-label-input{
        padding-left: 10px;
        margin-bottom: 0;
        width: 100%;
    }

    .nomination-list-item{
        display: inline-block;
    }

    .nomination-list-row{
        display: block;
    }

    .nomination-list-item-delete{
        display: inline-block;
        margin-right: 5px;
    }


    .nomination-add-input {
        display: block;
        width: 97%;
        height: 30px;
        padding: 0.375rem 0.75rem;
        margin-top: 5px;
        margin-bottom: 5px;
        margin-right: 10px;
        margin-left: 0;
        font-family: inherit;
        font-size: 16px;
        font-weight: 400;
        line-height: 2;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #9f9f9f;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .nomination-add-input::placeholder {
        color: #212529;
        opacity: 0.4;
    }


    .delete-nomination-button{
        background-color: #b24848;
        font-weight: 400;
        color: white;
        border: 1px solid #962c2c;
        border-radius: 5px;
    }
</style>




<div id="prev-nom" style="display: none">
    <?php
    if ($nominations !== null && count($nominations))
        foreach ($nominations as $nomination)
            echo $nomination.'%boobs%';
    ?>
</div>

<div class="main-div">
    <div class="nomination-div" style="height: 500px;">
        <div class="nomination-add-div" style="height: 100px;">
            <div class="nomination-add-input-div">
                <label class="nomination-label-input">Номинация
                <input class="nomination-add-input" id="nom-name" placeholder="Введите номинацию" type="text"/>
                </label>
            </div>
            <div class="nomination-add-button-div">
                <button onclick="AddNom()" class="nomination-add-button btn btn-success">Добавить<br>номинацию</button>
            </div>
        </div>

        <div id="list" class="nomination-list-div" style="height: 300px;">
            <?php

            $flag = count($nominations) > 0;
            $strDisplay = $flag ? 'block' : 'none';

            ?>
            <div class="nomination-list-row" style="display: none">
                <div class="nomination-list-item-delete">
                    <button onclick="DelNom(this)" class="delete-nomination-button">X</button>
                </div>
                <div class="nomination-list-item">
                    <p>DEFAULT_ITEM</p>
                </div>
            </div>

            <?php

            if ($flag)
                foreach ($nominations as $nomination)
                    echo '<div class="nomination-list-row" style="display: block">
                                <div class="nomination-list-item-delete">
                                    <button onclick="DelNom(this)" class="delete-nomination-button">X</button>
                                </div>
                                <div class="nomination-list-item"><p>'.$nomination.'</p></div>
                            </div>';?>
        </div>
    </div>

    <select id="ddList">

    </select>
</div>


<script>
    let listId = 'ddList'; //айди выпадающего списка, в который будут добавлены номинации

    let nominations = [];

    window.onload = function(){
        let noms = document.getElementById("prev-nom").innerHTML;
        if (noms.length > 5)
        {
            nominations = noms.split("%boobs%");

            nominations.pop();
            //--Костыль, почему-то в первую строку приходит перенос строки и несколько пробелов--
            nominations[0] = nominations[0].substring(5);
            //-----------------------------------------------------------------------------------
            FinishNom();
        }

        console.log(nominations);
        console.log(noms);
    }

    function AddNom()
    {
        let elem = document.getElementById('nom-name');
        nominations.push(elem.value);

        let item = document.getElementsByClassName('nomination-list-row')[0];
        let itemCopy = item.cloneNode(true)
        itemCopy.getElementsByClassName('nomination-list-item')[0].innerHTML = '<p>' + elem.value + '</p>'
        itemCopy.style.display = 'block';

        let list = document.getElementById('list');
        list.append(itemCopy);
        FinishNom()
    }

    function DelNom(elem)
    {
        let orig = elem.parentNode.parentNode;
        console.log(elem.parentNode.parentNode.getElementsByClassName('nomination-list-item')[0].childNodes);

        let name = elem.parentNode.parentNode.getElementsByClassName('nomination-list-item')[0].childNodes[0].textContent;
        nominations.splice(nominations.indexOf(name), 1);
        elem.parentNode.parentNode.parentNode.removeChild(orig);

        console.log(name);

        console.log(nominations);
        FinishNom()
    }

    function FinishNom()
    {
        let elem = document.getElementById(listId);

        while (elem.options.length) {
            elem.options[0] = null;
        }


        for (let i = 0; i < nominations.length; i++)
        {
            var option = document.createElement('option');
            option.value = nominations[i];
            option.innerHTML = nominations[i];
            elem.appendChild(option);
        }

    }
</script>

