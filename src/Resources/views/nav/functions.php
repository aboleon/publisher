<?php

function recursiveNavRow($item, $pool, $level = 0, $row = '')
{

    if ($pool->has($item->id)) {
        foreach ($pool[$item->id] as $subitem) {
            $level = $level + 1;
            $row = printRow($subitem, $level, $row);
            $row .= recursiveNavRow($subitem, $pool, $level, '');
        }
    }
    return $row;
}

function printRow($item, $level, string $row)
{
    if ($item->customLinks->isNotEmpty()) {
        $is_custom_link = true;
        $title = $item->customLinks->first()->title;
        $link = $item->customLinks->first()->url;
    } else {
        $is_custom_link = true;
        $title = (($item->meta->meta->nav_title == 'Sans titre') or empty($item->meta->meta->nav_title)) ? $item->meta->meta->title : $item->meta->meta->nav_title;
        $link = $item->meta->meta->url;
    }

    $row .= '<tr>
                <td style="width:100px" class="text-center sort_order">' . ($item->position + 1) . '</td>
                   <td class="subitem' . $level . '">' . ($level ? '-- ' : '') . $title . '</td>';
    if ($is_custom_link) {
        $row .= '<td class="bg-' . ($item->published ? 'success' : 'danger') . '">' . ($item->published ? 'En ligne' : 'Hors ligne') . '</td>';
    } else {
        $row .= '<td class="bg-success">En ligne</td>';
    }
    $row .= '<td>
                    <input class="order" type="hidden" name="position[' . $item->id . ']" value="' . $item->position . '">
                    <a title="Éditer la page" target="_blank" class="btn btn-sm btn-warning" href="' . url('panel/Publisher/pages/edit/' . $item->pages_id) . '">
                        <i class="fa fa-pen"></i>
                    </a>
                    <a target="_blank" class="btn btn-sm btn-info" href="' . url($link) . '">
                        <i class="fa fa-link"></i>
                    </a>
                    <a class="btn btn-sm btn-success" href="' . url('panel/Publisher/nav/add/' . $item->id) . '">
                        <i class="fa fa-plus"></i>
                    </a>
                    <a class="btn btn-sm btn-danger" href="#" data-bs-toggle="modal" data-bs-target="#myModal' . $item->id . '">
                        <i class="fa fa-trash"></i>
                    </a>' .
        AboleonPublisherHelpers::printModal($item->id, 'panel/Publisher/nav/remove', 'Confirmez-vous la suppression de <b>' . $title . '</b> de la navigation ?') .
        '</td>
            </tr>';


    return $row;
}
