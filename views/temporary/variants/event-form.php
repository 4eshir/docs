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
        overflow: scroll;
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
    }

    .nomination-add-button-div{
        border: 1px solid #1abc9c;
        border-radius: 7px;
        display: inline-block;
    }

    .nomination-add-button{
        display: block;
        margin: 0;
        padding: 0
    }

    .nomination-add-input{
        display: block;
        margin: 0;
        padding: 0
    }

    .nomination-list-item{
        display: inline-block;
    }

    .nomination-list-row{
        display: block;
    }

    .nomination-list-item-delete{
        display: inline-block;
        margin-right: 10px;
    }
</style>


<div class="main-div">
    <div class="nomination-div" style="height: 500px;">
        <div class="nomination-add-div" style="height: 100px;">
            <div class="nomination-add-input-div" style="width: 400px; height: 100%">
                <input class="nomination-add-input" id="nom-name" type="text"/>
            </div>
            <div class="nomination-add-button-div" style="width: 100px; height: 100%">
                <button onclick="AddNom()" class="nomination-add-button">Добавить номинацию</button>
            </div>
            <div class="nomination-add-button-div" style="width: 100px; height: 100%">
                <button onclick="FinishNom()" class="nomination-add-button">Завершить добавление номинаций</button>
            </div>
        </div>

        <div id="list" class="nomination-list-div" style="height: 300px;">
            <div class="nomination-list-row" style="display: none">
                <div class="nomination-list-item-delete">
                    <button onclick="DelNom(this)">X</button>
                </div>
                <div class="nomination-list-item">
                    <p>DEFAULT_ITEM</p>
                </div>
            </div>
        </div>
    </div>

    <select id="ddList">

    </select>
</div>


<script>
    let listId = 'ddList'; //айди выпадающего списка, в который будут добавлены номинации

    let nominations = [];

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
    }

    function DelNom(elem)
    {
        let orig = elem.parentNode.parentNode;

        let name = elem.parentNode.parentNode.getElementsByClassName('nomination-list-item')[0].childNodes[0].innerHTML;
        nominations.splice(nominations.indexOf(name), 1);
        elem.parentNode.parentNode.parentNode.removeChild(orig);

        console.log(nominations);
    }

    function FinishNom()
    {
        let elem = document.getElementById(listId);

        while (elem.options.length) {
            elem.options[0] = null;
        }

        console.log(nominations);

        for (let i = 0; i < nominations.length; i++)
        {
            console.log(nominations[i]);
            var option = document.createElement('option');
            option.value = nominations[i];
            option.innerHTML = nominations[i];
            elem.appendChild(option);
        }

    }
</script>

