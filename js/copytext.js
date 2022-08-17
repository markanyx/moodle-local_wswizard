function copyTextFromElement(elem,token){
    //console.log(elem);
    copyText(token);
}
function copyText(text) {
    navigator.clipboard.writeText(text).then(function(x) {
        //alert("Link copied to clipboard: " + text);
    });
}
