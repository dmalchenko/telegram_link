<?php

/* @var $this yii\web\View */
/* @var $item String */

use yii\helpers\Html;

$this->title = 'Open in telegram';
?>

<div class="telebutton">
    <a id="abc" class="btn btn-lg btn-primary btn-block">Open in Telegram</a>
</div>
<script>
    var url=window.location.pathname.split('/');
    var search = window.location.search.replace("?", "&");
    if (url[1]=="joinchat"){
        var str="tg://join?invite="+url[2]
    } else {
        var str="tg://resolve?domain="+url[1]+search;
        if (url[2]) str=str+"&post="+url[2]
    }
    document.getElementById("abc").href=str;
</script>
<script>
    var url=window.location.pathname.split('/');
    var search = window.location.search.replace("?", "&");
    if (url[1]=="joinchat"){
        var str="tg://join?invite="+url[2]
    } else {
        var str="tg://resolve?domain="+url[1]+search;
        if (url[2]) str=str+"&post="+url[2]
    }
    window.location.replace(str);
</script>