<ul class="menuLine" style="list-style-type:none;margin-right: 20px;">
    <li style="margin-top:10px;display:inline-block;">
        <ul id="cbp-tm-menu" class="cbp-tm-menu">
            <li>
                <a href="#" style="font-size: 25px;">
                    <img src="startup/images/icones/alert.png" />
                </a>
                <ul class="cbp-tm-submenu" style="z-index: 2000; width:430px;margin-left:-216px;">
                    <?php if (!empty($this->context->get('notification'))) : ?>
                        <?php foreach ($this->context->get('notification') as $notification) : ?>
                            <?php $this->render('main/wNotification', array('notification' => $notification)); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Aucune notification.</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </li>
    <?php if (!empty($this->context->get('notification'))) : ?>
        <li class="counter" style="margin-bottom: 30px;margin-left: -10px;">
            <?php echo count($this->context->get('notification')); ?>
        </li>
    <?php endif; ?>
    <li style="margin-left: 5px;display: inline-block;">
        <a href="mailbox">
            <img src="startup/images/icones/message.png" />
        </a>
    </li>
    <?php if (!empty($this->context->get('new_messages'))) : ?>
        <li class="counter" style="margin-bottom: 30px;margin-left: -10px;">
            <?php echo $this->context->get('new_messages'); ?>
        </li>
    <?php endif; ?>
    <li style="margin-left:5px;display: inline-block;">
        <?php $photo = empty($this->context->get('user_photo_url')) ? 'unknown.png' : $this->context->get('user_photo_url'); ?>
        <img  class="connectedPhoto" src="startup/photos/profile/<?php echo $photo; ?>" />
    </li>
    <li>
        <ul id="cbp-tm-menu" class="cbp-tm-menu" style="position:relative;margin-left:130px;">
            <li>
                <a href="#" style="font-size: 25px;">
                    <?php echo $this->context->get('user_prenom'); ?>
                    <i class="caret" aria-hidden="true"></i>
                </a>
                <ul class="cbp-tm-submenu">
                    <li><a style="border: 4px solid #fff;" href="profile/edit" class="cbp-tm-icon-cog">Editer le profil</a></li>
                    <li><a style="border: 4px solid #fff;" href="plan/feed/1" class="cbp-tm-icon-users">Mes paiements</a></li>
                    <li><a style="border: 4px solid #fff;" href="auth/disconnect" class="cbp-tm-icon-contract">Déconnexion</a></li>
                </ul>
            </li>
        </ul>
    </li>
</ul>
<script>
    var menu = new cbpTooltipMenu( document.getElementById( 'cbp-tm-menu' ) );
</script>
