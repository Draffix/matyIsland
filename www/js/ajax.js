/* Volání AJAXu u všech odkazů s třídou ajax */
$("a.ajax").live("click", function () {
    $.get(this.href);
    
    return false;
});
    
/* AJAXové odeslání formulářů */
$("form.ajax").live("submit", function () {
    $(this).ajaxSubmit();
    return false;
});

$("form.ajax :submit").live("click", function () {
    $(this).ajaxSubmit();
    return false;
});
