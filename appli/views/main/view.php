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
            <div id="windows_menu"></div>
            <div id="notifications_menu">
                <h2>Centre de notifications</h2>
                Aucune nouvelle notification
            </div>
            <div class="menu">
                <div class="container">
                    <img id="windows_icon" menu="windows" src="startup/images/icones/menu/windows.png"/>
                    <img class="menuEntry" OnClick="render('staff')" src="startup/images/icones/menu/staff.png" />
                    <img class="menuEntry" OnClick="render('outlook')" src="startup/images/icones/menu/outlook.png" />
                </div>
                <div id="windows_date">11-10-2016</div>
                <a href="#">
                    <img id="alert_icon" menu="notifications" src="startup/images/icones/menu/alert.png" />
                </a>
            </div>
        </div>
    </body>
</html>