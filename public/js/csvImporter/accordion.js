var accordionInit = function () {
    $(".invalid-data .panel-default .panel-heading").on('click', 'label', function () {
        $(this).children('.data-listing').slideToggle();
    });
};

$('.check-btn').on('change', function () {
    $("input[type=checkbox]:not(:disabled)").prop('checked', $(this).prop('checked'));
});
