var totalCount = 5;



function ChangeIt()
{
    var num = Math.ceil( Math.random() * totalCount );
    document.body.background = chemin+'/img/backgrounds/'+num+'.svg';
    document.body.style.backgroundRepeat = "no-repeat";
    document.body.style.backgroundSize = "cover";
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
