$('#speedeemanifestform-customer').change(function () {
    let rowHtml;
    $.get('/report/speedee-fetch?customerId=' + (this).value, function (data) {
        console.log(data);
        if (data.length === 0) {
            $('#ordersTable tbody').html('<tr><td><h1>No pending orders found for SpeeDee.</h1></td></tr>');
        } else {
            $.each(data, function (i, item) {
                rowHtml += `
                    <tr>
                        <td>${item.reference_1}</td>
                        <td>${item.ship_to_name}</td>
                    </tr>
                    `
            })
            $('#ordersTable tbody').html(rowHtml);
        }
    });
});