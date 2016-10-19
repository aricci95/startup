<form action="subscribe/save" method="post">
    <div style="margin: 0 auto;width: 942px;min-height:650px;">
        <div style="float:left">
            <img src="startup/images/structure/startuplike.png" />
            <h1>Bienvenue !</h1>
            <h2>Vous ne skierez plus jamais seul.</h2>
        </div>
        <div style="float:right;text-align: left;">
            <h1>S'inscrire !</h1>
            <h2>Et en plus c'est gratos.</h2>
            <table class="subscribeForm" style="margin-top:20px;">
                <tr>
                    <td>
                        <label for="role_1">
                            <input style="border:none;" id="role_1" type="radio" name="role_id" value="<?php echo Auth::ROLE_SKI; ?>" checked="checked" />
                            Skieur
                        </label>
                        <label for="role_2">
                            <input style="border:none;" id="role_2" type="radio" name="role_id" value="<?php echo Auth::ROLE_OWNER; ?>"/>
                            Propriétaire
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input class="roundedInput" style="width:194px;" name="user_prenom" value="<?php echo $this->context->getParam('user_prenom'); ?>" placeholder="Prénom" />
                        <input class="roundedInput" style="width:194px;" name="user_nom" value="<?php echo $this->context->getParam('user_nom'); ?>" placeholder="Nom" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <input class="roundedInput" placeholder="Email" name="user_mail" type="text" <?php if(!empty($this->context->params['user_mail'])) : ?> value="<?php echo $this->context->params['user_mail']; ?>"<?php endif; ?> />
                    </td>
                </tr>
                <tr>
                    <td>
                        <input class="roundedInput" placeholder="Mot de passe" name="user_pwd" type="password" <?php if(!empty($this->context->params['user_pwd'])) : ?> value="<?php echo $this->context->params['user_pwd']; ?>"<?php endif; ?> />
                    </td>
                </tr>
                <tr>
                    <td>
                        <input class="roundedInput" placeholder="Vérification mot de passe" name="verif_pwd" type="password" <?php if(!empty($this->context->params['verif_pwd'])) : ?> value="<?php echo $this->context->params['verif_pwd']; ?>"<?php endif; ?> />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="user_gender_1">
                            <input style="border:none;" id="user_gender_1" type="radio" name="user_gender" value="1" <?php if($this->context->getParam('user_gender') == "1") : ?> checked="checked" <?php endif; ?> />
                            Homme
                        </label>
                        <label for="user_gender_2">
                            <input style="border:none;" id="user_gender_2" type="radio" name="user_gender" value="2" <?php if($this->context->getParam('user_gender') == "2") : ?> checked="checked" <?php endif; ?> />
                            Femme
                        </label>
                    </td>
                </tr>
                <tr>
                    <td style="height:60px;width:80px;font-size: 12px;">
                        En validant l'inscription, vous acceptez et vous avez lu nos <a href="subscribe/terms" target="_blank">mentions légales</a> ainsi que notre <a href="">usage des cookies</a>.
                    </td>
                </tr>
                <tr>
                    <td style="color:red;">
                        <br/>
                        <i>Pensez à vérifier vos spams si vous ne recevez aucune confirmation</i>
                        <br/><br/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input class="submitButton" size="20" type="submit" value="S'inscrire !" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
