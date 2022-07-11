var totalCount = 5;



function ChangeIt()
{
    var num = Math.ceil( Math.random() * totalCount );
    document.body.background = chemin+'/img/backgrounds/'+num+'.svg';
    document.body.style.backgroundRepeat = "no-repeat";
    document.body.style.backgroundSize = "cover";
    // document.body.style.backgroundColor = "black";
    // document.body.style.backgroundOpacity = " -webkit-mask-image:-webkit-gradient(linear, left top, left bottom, from(rgba(0,0,0,1)), to(rgba(0,0,0,0)));\n" +
    //     "      mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));";
}

function ChangeSortiePicture()
{
    var num = Math.ceil( Math.random() * totalCount );
    document.getElementById('img_default').src = chemin+'/img/sorties/default/'+num+'.jpg';

}



function textToInput(id){
    var spans = document.getElementsByTagName("span"),
        index,
        span;

    for (index = 0; index < spans.length; ++index) {
        span = spans[index];
        if (span.contentEditable) {
            span.onblur = function() {
                var text = this.innerHTML;
                text = text.replace(/&/g, "&amp").replace(/</g, "&lt;");
                console.log("Content committed, span " +
                    (this.id || "anonymous") +
                    ": '" +
                    text + "'");
            };
        }
    }
}
