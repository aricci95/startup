<html>
    <head>
        <base href="/" >
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <?php $this->render('main/wJavascript'); ?>
        <?php $this->render('main/wCss'); ?>
        <link rel="icon" type="image/png" href="startup/images/icones/fav.gif" />
    </head>
    <body>
        <div class="site">
            <div class="title">Startup simulator</div>
            <div class="content" align="center">
                <?php include($this->getViewFileName()); ?>
            </div>
            <div id="windows_menu">

            </div>
            <div class="menu">
                <a href="#" class="">
                    <img id="windows_icon" src="startup/images/icones/menu/windows.png"/>
                </a>
                <div class="container">
                    <div class="entry">Staff</div>
                    <div class="entry outlook">
                        <a href="#">
                            <img src="startup/images/icones/menu/outlook.png" />
                        </a>
                    </div>
                    <div class="entry">Alertes</div>
                    <div class="entry">VOUS</div>
                    <div class="entry">Tendances</div>
                    <div class="entry">Café</div>
                    <div class="entry">Actions</div>
                </div>
            </div>
        </div>
    </body>
</html>