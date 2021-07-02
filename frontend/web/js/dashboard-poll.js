
function getMyData() {
    fetch("/site/json")
        .then((res) => res.json())
        .then((data) => {

            $("#content").empty();
            console.log(data)
            html = ''; // init html array
            for (var key in data) {
                html += "<div class='row customer'>" +
                    "<label>" + data[key].name + "</label>";
                for (var key2 in data[key].statuses) {
                    html += "<button type='button' class='btn btn-" + data[key].statuses[key2].slug + "'>" + data[key].statuses[key2].name + "<br/>" +
                        "<span class='badge badge-light'>" + data[key].statuses[key2].orders + "</span></button>";
                }
                html += "</div>";
            }

            $("#content").append(html);

        })
}

getMyData();
setInterval(function () {
    getMyData();
}, 300 * 1000);