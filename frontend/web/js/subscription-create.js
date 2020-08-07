/**
 * Add new item
 */
function addItem() {

    itemKey = $(".item").length;
    itemKey += 1;
    $("#items").append(
        '<div class="row item item-' + itemKey + '">'
        + $('#new-item-block').html().replace(/__id__/g, 'new' + itemKey)
        + '</div>'
    );
    // disable remove button on first item
    var row = $('.item-0').length ? $('.item-0') : $('.item-1');
    row.find('.btn-remove-item').addClass('hidden');
    if (itemKey !== 1) {
        // hide label titles
        $('.item-' + itemKey).find('label').not('.fake').html('');
        // enable remove btn
        $('.item-' + itemKey).find('.btn-remove-item').removeClass('hidden');
    }
}