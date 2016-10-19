<div style="float:right;margin-top:8px;">
    <form action="auth/login" method="post" style="margin: 0;padding: 0;">
        <table cellspacing="0" class="auth">
            <tbody>
                <tr style="color:white;">
                    <td>
                        <label for="user_mail">Email</label>
                    </td>
                    <td>
                        <label for="user_pwd">Mot de passe</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input name="user_mail" tabindex="1" style="width:150px;height:23px;border: 1px black solid;padding:3px">
                    </td>
                    <td>
                        <input name="user_pwd" tabindex="2" style="width:150px;height:23px;border: 1px black solid;padding:3px" type="password">
                    </td>
                    <td>
                        <input value="Go !" tabindex="4" type="submit">
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <div style="color:#808080;">
                                <input id="savepwd" type="checkbox" name="savepwd" value="1" checked="1">
                                <label for="savepwd">Se souvenir de moi</label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="lostpwd">Mot de passe oublié?</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>