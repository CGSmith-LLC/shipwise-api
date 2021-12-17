<?php

use common\models\shipping\Carrier;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>

    <form>
        <div class="form-group row">
            <label for="colFormLabel" class="col-sm-2 col-form-label">Carrier</label>
            <div class="col-sm-10">
                <?= Html::dropDownList('carrier_id', null,
                    Carrier::getList(),
                    [
                        'prompt' => ' -- Unknown --',
                        'id' => 'carrier_id',
                        'class' => 'form-control',
                    ]) ?>
            </div>
        </div>
        <div class="form-group row">
            <label for="colFormLabel" class="col-sm-2 col-form-label">Service</label>
            <div class="col-sm-10">
                <?= Html::dropDownList('service_id', null,
                    [],
                    [
                        'prompt' => ' -- Unknown --',
                        'id' => 'service_id',
                        'class' => 'form-control',
                    ]) ?>
            </div>
        </div>
    </form>

<?php

$url = Url::to(['carrier-services']);

$JS = <<<JS
            $('#carrier_id').off('change').on('change', function () {
                    var carrier_id = this.value || 0;
                   $.ajax({
                        url: '{$url}?carrierId=' + carrier_id,
                        type: 'get',
                        dataType: 'json'
                    })
                    .done(function (response) {
                        console.log(response);
                        var service_dd = $('#service_id').empty().append($('<option/>', { value : '', text : ' -- Unknown --' }));
                        $.each(response, function(i, value) {
                           service_dd.append($('<option>').text(value).attr('value', i));
                        });
                    })
                    .fail(function (jqXHR, textStatus, error) {
                        popup.find('.modal-body').html(error).append(jqXHR.responseText || '');
                    }); 
            });

JS;
$this->registerJs($JS, View::POS_END, 'change-orders-carrier-pos-end');



