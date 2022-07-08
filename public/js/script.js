var totalCount = 5;



function ChangeIt()
{
    var num = Math.ceil( Math.random() * totalCount );
    document.body.background = chemin+'/img/backgrounds/'+num+'.svg';
    document.body.style.backgroundRepeat = "no-repeat";
    document.body.style.backgroundSize = "cover";
}

function ChangeSortiePicture(){
    var num = Math.ceil( Math.random() * totalCount );
    document.getElementById('img_default').src = chemin+'/img/sorties/default/'+num+'.jpg';

}


console.log(nom);
console.log(codePostal);
