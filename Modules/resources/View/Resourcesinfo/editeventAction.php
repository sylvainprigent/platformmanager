<?php include 'Modules/resources/View/layout.php' ?>

<!-- body -->     
<?php startblock('content') ?>
    
    <div class="col-md-12 pm-content">

    <?php include "Modules/resources/View/Resourcesinfo/edittabs.php" ?>
    <div class="col-xs-12"><p></p></div>
    
    
    <div class="col-xs-12 col-md-7 pm-form">
        <?php echo $formEvent ?>
    </div>
    
    <?php if ($id_event > 0){ ?>
    <div class="col-xs-12 col-md-5">
        <div class="col-xs-12 pm-form">
            <?php echo $formDownload ?>
        </div>
        <div class="col-xs-12 pm-table">
            <?php echo $filesTable ?>
        </div>
    </div>
    <?php } ?>
</div>

<?php endblock();