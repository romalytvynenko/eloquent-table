<?php
/**
 * @var $table \Romalytvynenko\EloquentTable\Engine\EloquentTable
 * @var $data array
 * @var $configs array
 * @var $link
 */

/*
 * Icon names for order
 */
$icons = [
    'asc' => 'icon-caret-up',
    'desc' => 'icon-caret-down'
];
$oppositeDirection = ($table->getOrderDirection() == 'asc')? 'desc' : 'asc';
?>

<div class="pull-left">
    <form action="<?=$table->getActionLink();?>">
    <label>Show
        <select size="1" onchange="this.form.submit()" name="itemsPerPage">
            <?php foreach($table->sizes as $size) : ?>
                <option value="<?=$size;?>" <?=($table->getItemsPerPage()==$size)?'selected':'';?>><?=$size;?></option>
            <?php endforeach; ?>
        </select> entries
    </label>
    </form>
</div>

<div class="pull-right">
    <form action="<?=$table->getActionLink();?>">
        <label>Search: <input type="text" name="search" value="<?=$table->getSearchKeyword();?>"/></label>
        <input type="submit" style="position: absolute; left: -9999px"/>
    </form>
</div>

<table class="table">
    <thead>
    <tr>
        <?php foreach($configs['columns'] as $key => $name) : ?>
        <th>
            <?php if(in_array($key, $table->getConfig('sortable'))) : ?>
                <a href="<?=$table->getActionLink(['column' => $key, 'order' => $oppositeDirection]);?>">
                    <?=$name;?>
                    <?php if($table->getOrderBy() == $key) : ?>
                        <i class="<?=$icons[$table->getOrderDirection()];?>"></i>
                    <?php endif; ?>
                </a>
            <?php else : ?>
                <?=$name;?>
            <?php endif; ?>
        </th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>

    <?php foreach($data as $item) : ?>
        <tr>
            <?php foreach($configs['columns'] as $key => $name) : ?>
                <td><?=$table->outputValue($item, $key);?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="pull-left" style="margin-top: 10px">
    Page: <?=$table->getPage() + 1;?>/<?=ceil($table->getConfig('allItemsCount')/$table->getItemsPerPage());?>
     (<?=$table->getConfig('allItemsCount');?> items found)
</div>

<div class="paging_two_button btn-group datatable-pagination">
    <?php if($table->getPage() !== 0) : ?>
    <a class="paginate_enabled_previous" href="<?=$table->getPrevLink();?>">
        <span>Previous</span>
        <i class="icon-chevron-left shaded"></i>
    </a>
    <?php endif; ?>
    <?php if($table->showNextLink()) : ?>
    <a class="paginate_enabled_next" href="<?=$table->getNextLink();?>">
        <span>Next</span>
        <i class="icon-chevron-right shaded"></i>
    </a>
    <?php endif; ?>
</div>

<div class="clearfix"></div>