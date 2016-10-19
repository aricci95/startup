<li style="height:65px;" class="notification" data-id="<?php echo $this->notification['notification_id']; ?>" data-url="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . $this->notification['notification_link']; ?>">
    <a class="<?php echo ($this->notification['notification_read']) ? '' : 'unread'; ?>" href="<?php echo $this->notification['notification_link']; ?>" style="border: 1px solid #DDDDDD;">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td style="background-image:url(startup/photos/profile/<?php echo $this->notification['notification_photo_url']; ?>);width:55px;height:55px;background-size: 55px 55px;">
                </td>
                <td style="padding-left: 10px;">
                    <?php echo $this->notification['notification_content']; ?>
                    <br/>
                    <span style="font-size: 12px;">
                        <?php echo Tools::timeConvert($this->notification['notification_date']); ?>
                    </span>
                </td>
            </tr>
        </table>
    </a>
</li>