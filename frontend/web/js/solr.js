
function searchSolr(searchTerm, csrf) {
    fetch("/order/solr?query=" + searchTerm)
        .then((res) => res.json())
        .then((data) => {
            console.log(data)
        })
}

$( "#quick-search" ).keyup(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    console.error(csrfToken);
    searchSolr($("#quick-search").val(), csrfToken);

});
