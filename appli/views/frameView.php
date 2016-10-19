<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <?php if(!empty($this->_title)) : ?><h1><?php echo $this->_title; ?></h1><?php endif; ?>
        <?php include($this->getViewFileName()); ?>
    </body>
</html>