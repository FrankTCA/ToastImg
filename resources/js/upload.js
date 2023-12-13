$(document).ready(function() {
    let ip = $("#copy");
    ip.select();
    navigator.clipboard.writeText(ip.attr("value"));
    ip.hide();
    $("#clipboardNotice").text("The URL has been copied to your clipboard!");
});