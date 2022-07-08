var totalCount = 5;



function ChangeIt()
{
    var num = Math.ceil( Math.random() * totalCount );
    document.body.background = chemin+'/img/backgrounds/'+num+'.svg';
    document.body.style.backgroundRepeat = "no-repeat";
    document.body.style.backgroundSize = "cover";
}

function textToInput(id){
    console.log(id);
    document.getElementById(id).innerHTML = "aze";
}
