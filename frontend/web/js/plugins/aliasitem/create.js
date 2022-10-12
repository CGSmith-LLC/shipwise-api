
$("#btn-add-item").off('click').on("click", function() {
    $("#items").append(
        '<div class="item">'
        + $('#new-item-block').html()
            .replace('Quantity', '')
            .replace('SKU', '')
            .replace('Name', '')
            .replace(/__id__/g, 'new' + ($("#items > div").length + 1))
        + '</div>'
    );

});

// remove item button
$(document).on('click', '.btn-remove-item', function () {
    $(this).closest('.row').remove();
});
