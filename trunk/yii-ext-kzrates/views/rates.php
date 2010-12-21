<?php


?>
<?/*
<script type="text/javascript">
jQuery.getJSON('http://madeinkazakhstan-magazine.com/currency/getrates.php', function(data) {
  for(i in data) {
      jQuery("#rates").append('<div id="'+data[i].name+'">'+'<span class="'+data[i].change+'"><b>'+data[i].value+ "</b> KZT</span></div>");
  }
});
</script>
 *
 */?>

<?=CHtml::openTag($tagName,$htmlOptions);?>
<?php foreach($rates as $rate)  { ?>
    <div class="<?=$rate['name']?>">
        <span class="<?=$rate['change']?>">
            <b><?=$rate['value']?></b> KZT
        </span>
    </div>    
<?php } ?>
<?=CHtml::closeTag($tagName);?>
