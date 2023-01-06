//wip, obvs
$('#speedeemanifestform-customer').change(function () {
    let rowHtml;
    $.get('/report/speedee-fetch', function (data) {
        const response = $.parseJSON(data);
        $.each(response, function (i, item) {
            rowHtml += `<tr>
                        <td>${item.ship_to_name}</td>
                    </tr>
                    `
        })
    });
});