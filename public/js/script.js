var totalCount = 5;
function ChangeIt()
{
    var num = Math.ceil( Math.random() * totalCount );
    document.body.background = '../img/backgrounds/'+num+'.svg';
    document.body.style.backgroundRepeat = "no-repeat";
    document.body.style.backgroundSize = "cover";

}