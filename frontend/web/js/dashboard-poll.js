
function getMyData() {
    fetch("/site/json")
        .then((res) => res.json())
        .then((data) => {

            console.log(data)
            html = '\n' +
                '      <div class="row">\n' +
                '        <div class="col-md-2"></div>\n' +
                '        <div class="col-md-1 cole-head">OPEN</div>\n' +
                '        <div class="col-md-1 cole-head">PENDING</div>\n' +
                '        <div class="col-md-1 cole-head">SHIPPED</div>\n' +
                '        <div class="col-md-2 cole-head" style="margin-left: 5px; margin-right: 5px;">COMPLETED</div>\n' +
                '        <div class="col-md-1 cole-head">ERROR</div>\n' +
                '      </div>'; // init html array
            for (var key in data) {
                html += '<div class="row row-bottom">' +
                    '<div class="col-md-2 customer">' +
                    '<div class="avatar" style="background-color: ' + data[key].avatarcolor + ' ">' + data[key].avatar + '</div>' +
                    '<div class="real-customer">' + data[key].name + "</div>" +
                    "</div>";
                for (var key2 in data[key].statuses) {
                    html += '<div class="col-md-'+data[key].statuses[key2].colwidth+' number-box ' + data[key].statuses[key2].slug + '">' + data[key].statuses[key2].orders + '</div>';
                }
                html += "</div>";
            }
            $("#content").empty();
            $("#content").append(html);

        })
}

getMyData();
setInterval(function () {
    getMyData();
}, 120 * 1000);