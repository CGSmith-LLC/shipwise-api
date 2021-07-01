<?php

use frontend\assets\DatePickerAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>

<div class="body-content">
    <h1>Dashboard</h1>
    <table id="table" style="width: 100%" border=5
    >
        <tr>
            <th>CUSTOMER</th>
            <th>OPEN</th>
            <th>PRIME</th>
            <th>PENDING FULFILLMENT</th>
            <th>SHIPPED</th>
            <th>COMPLETED</th>
            <th>ERROR</th>
        </tr>
        <!--        <tr>-->
        <!--            <td>-->
        <!--                <p id="customer"</p>-->
        <!--            </td>-->
        <!--            <td>-->
        <!--                <p id="open"</p>-->
        <!--            </td>-->
        <!--            <td>-->
        <!--                <p id="prime"</p>-->
        <!--            </td>-->
        <!--            <td>-->
        <!--                <p id="pending"</p>-->
        <!--            </td>-->
        <!--            <td>-->
        <!--                <p id="shipped"</p>-->
        <!--            </td>-->
        <!--            <td>-->
        <!--                <p id="completed"</p>-->
        <!--            </td>-->
        <!--            <td>-->
        <!--                <p id="error"</p>-->
        <!--            </td>-->
        <!--        </tr>-->

    </table>
</div>
<script>
    fetch("/site/json")
        .then((res) => res.json())
        .then((data) => {
            console.log(data)
            // document.getElementById("customer").innerHTML = data[1].name;
            // document.getElementById("shipped").innerHTML = data[1].statuses[1].orders;
            // document.getElementById("prime").innerHTML = data[1].statuses[2].orders;
            // document.getElementById("pending").innerHTML = data[1].statuses[8].orders;
            // document.getElementById("open").innerHTML = data[1].statuses[9].orders;
            // document.getElementById("completed").innerHTML = data[1].statuses[11].orders;
            // document.getElementById("error").innerHTML = data[1].statuses[10].orders;
            // for(var k = 1;  ){
            for (var {name, open, prime, pending, shipped, complete, error} of data) {
                document.write(`
              <tr>
                    <td>${name}</td><td>${open}</td><td>${prime}</td><td>${pending}</td><td>${shipped}</td><td>${complete}</td><td>${error}</td>
              </tr>
            `)
            }
            // for(var b = 1; b < data.length; b++){
            //   document.write('<tr><td>' + data[b]. + '<tr><td>')
            // }
            // for(var c = 1; c < data.length; c++){
            //   document.write('<tr><td>' + data[c].name + '<tr><td>')
            // }
            // for(var d = d; d < data.length; d++){
            //   document.write('<tr><td>' + data[d].name + '<tr><td>')
            // }
            // for(var e = 1; e < data.length; e++){
            //   document.write('<tr><td>' + data[e].name + '<tr><td>')
            // }
            // for(var f = 1; f < data.length; f++){
            //   document.write('<tr><td>' + data[f].name + '<tr><td>')
            // }
            // for(var g = 1; g < data.length; g++){
            //   document.write('<tr><td>' + data[g].name + '<tr><td>')
            // }
            // for(var h = 1; h < data.length; h++){
            //   document.write('<tr><td>' + data[h].name + '<tr><td>')
            // }
        })

</script>