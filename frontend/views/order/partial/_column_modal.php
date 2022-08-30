<?php
/**
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $customColumns
 */

$userColumn = array();
foreach(json_decode($customColumns->column_data) as $column) {
    if ($column->status == 1) {
        $userColumn[] = $column->attribute;
    }
}
?>
<div class="modal fade" id="columnModal" tabindex="-1" role="dialog" aria-labelledby="columnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="columnModalLabel"><?= Yii::t('app', 'Manage columns') ?></h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                $columns = $dataProvider->sort->attributes;
                foreach($columns as $key => $value) {
                    $checked = '';
                    $inputValue = '';
                    if (isset($value['label'])) {
                        if (in_array($key, $userColumn)) {
                            $checked = 'checked';
                            $inputValue = 1;
                        }
                        echo '<div class="form-check col-md-4">
                            <input class="form-check-input" type="checkbox" value="' . $inputValue . '" name="' . key($value['asc']) . '" id="default_' . key($value['asc']) . '" ' . $checked . '>
                            <label class="form-check-label" for="default_' . key($value['asc']) . '">
                                ' . $value['label'] . '
                            </label>
                        </div>';
                    }
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('app', 'Close') ?></button>
                <button id="submit-change-columnModal" type="button" class="btn btn-primary"><?= Yii::t('app', 'Save changes') ?></button>
            </div>
        </div>
    </div>
</div>
<?php $this->registerJs( <<<JS
    $(document).ready(function() {
        $('#submit-change-columnModal').on('click', function (e) {
            var data = {};
            $('#columnModal input[type=checkbox]').each(function() {
                data[$(this).attr('name')] = $(this).is(':checked') ? 1 : 0;
            });
            $.ajax({
                url: '/column-manage/update',
                type: 'POST',
                data: data,
                success: function(data) {
                    location.reload();
                }
            });
        });
    });
JS
); ?>