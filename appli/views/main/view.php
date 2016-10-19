<html>
    <head>
        <base href="/" >
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <?php $this->render('main/wJavascript'); ?>
        <?php $this->render('main/wCss'); ?>
        <link rel="icon" type="image/png" href="startup/images/icones/fav.gif" />
        <title>Startup Simulator</title>
    </head>
    <body>
        <div class="header">
            <div class="userMenu">
                <div class="header">
                    <a href="<?php echo ($this->context->get('user_id')) ? 'home' : 'subscribe'; ?>">Startup Simulator</a>
                    <span class="startup">Zob Inc.</span>
                </div>
            </div>
        </div>
        <div class="site">
            <div class="title"><?php if (!empty($this->getTitle())) echo $this->getTitle(); ?></div>
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
                            <br/>
                            <span>Outlook</span>
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