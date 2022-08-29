<?php
/**
 * @var $dataProvider yii\data\ActiveDataProvider
 */
?>
<div class="modal fade" id="columnModal" tabindex="-1" role="dialog" aria-labelledby="columnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnModalLabel">Manage columns</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                $columns = $dataProvider->sort->attributes;
                $customColumns = ['id', 'customer_id', 'status_id', 'carrier_id', 'service_id', 'tracking_number', 'created_date'];
                $i = 0;
                foreach($columns as $key => $value) {
                    $checked = '';
                    $i++;

                    if (isset($value['label'])) {
                        if (in_array($key, $customColumns)) {
                            $checked = 'checked';
                        }
                        echo '<div class="form-check col-md-4">
                            <input class="form-check-input" type="checkbox" value="" name="' . key($value['asc']) . '" id="default_' . key($value['asc']) . '" ' . $checked . '>
                            <label class="form-check-label" for="default_' . key($value['asc']) . '">
                                ' . $value['label'] . '
                            </label>
                        </div>';
                    }
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>