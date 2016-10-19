<script src="startup/libraries/jquery-2.0.0.min.js"></script>
<script src="startup/appli/js/startup.js"></script>
<script type="text/javascript" src="startup/libraries/growler/js/gritter.js"></script>
<script type="text/javascript" src="startup/libraries/modal/js/jquery.magnific-popup.js"></script>
<script type="text/javascript" src="startup/appli/js/modal.js"></script>

<script>
    $.extend($.gritter.options, {
        position: 'bottom-right'
    });
</script>
<?php if (is_array($this->_growlerMessages) && count($this->_growlerMessages) > 0) :
    foreach($this->_growlerMessages as $message) :
        echo $message;
    endforeach;
endif; ?>

