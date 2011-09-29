<?=CHtml::openTag($tagName,$htmlOptions);?>
<?php foreach($rates as $rate)  { ?>
    <div class="<?=$rate['name']?>">
        <span class="<?=$rate['change']?>">
            <b><?=$rate['value']?></b> KZT
        </span>
    </div>    
<?php } ?>
<?=CHtml::closeTag($tagName);?>
