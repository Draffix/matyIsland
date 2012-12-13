/* Volání AJAXu u všech odkazů s třídou ajax */
$("a.ajax").live("click", function (event) {
    event.preventDefault();
    $.get(this.href);
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

$("a.ajax, .paginator a").live("click", function (event) {
    event.preventDefault();
    $.get(this.href);
});
