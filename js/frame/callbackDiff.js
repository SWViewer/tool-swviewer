window.onload = function() {
    var uniqueID = document.getElementById("uniq").innerHTML;
    window.parent.postMessage({ uniqueID: uniqueID }, window.origin);
}