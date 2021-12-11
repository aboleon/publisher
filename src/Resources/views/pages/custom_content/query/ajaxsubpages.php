<div class="ajax_subpages form" data-url="panel/Publisher/ajax">
    <?php if (array_key_exists('create',$item_field)) { ?>
    <strong>Ajouter un élément</strong>
    <input name="ajax_title" class="form-control" type="text" style="min-width:300px" placeholder="Titre">
    <?php if ($query_with_children) {
        $values = $item_field['query']['method'] == 'attached_subcontent'
        ? $select_values[$custom_content_key]['values']
        : $select_values[$custom_content_key];
        ?>
    <select class="form-control" name="ajax_parent">
        <?php foreach($values as $value) { ?>
        <option value="<?= $value->id;?>">
            <?= $value->title;?>
        </option>
        <?php } ?>
    </select>
    <input type="hidden" name="query_with_children" value="1">
    <input type="hidden" name="ajax_arguments" value="<?= http_build_query(['type'=>$item_field['query']['with_children']]) ?>">
    <?php } else { ?>
    <input type="hidden" name="ajax_parent" value="<?= $data->parent ?>">
    <input type="hidden" name="ajax_arguments" value="<?= http_build_query($item_field['query']['arguments']) ?>">
    <?php } ?>
    <?php } ?>
    <div class="flex spb">
        <?php if (array_key_exists('create',$item_field)) { ?>
        <button class="ajaxable badge badge-success">
            Ajouter un élément
        </button>

        <input type="hidden" name="ajax_object" value="PagesCreateContent">
        <input type="hidden" name="ajax_action" value="add<?= $item_field['query']['method'] ?>">
        <input type="hidden" name="ajax_callback_target" value="#custom_content_<?= $custom_content_key;?>">
        <?php } else { ?>
        <a class="badge badge-info" target="_blank" href="<?= url('panel/Publisher/pages/list/'. $item_field['query']['arguments']['type']) ?>">
            Voir tous les éléments
        </a>
        <?php } ?>
    </div>
</div>
