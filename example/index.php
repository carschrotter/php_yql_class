<?php

namespace mnhcc\yql\example;
use mnhcc\yql\classes as yql, 
        \example\Helper as Helper; {
    
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Example for php_yql_class</title>
        </head>
        <body>
            <div>
            <form>
            <?php
            require_once './initial.php';
            $yql = new yql\YQL();
            $statement = 'select description from rss(5) 
where url="http://gdata.youtube.com/feeds/base/users/:YOUTUBENAME/uploads\\?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile";';
            $nrt = $yql->prepare($statement);
            $user = '';
            $nrt->bindParam(':YOUTUBENAME', $user);
            $user = (isset($_REQUEST['youtube'])) ? $_REQUEST['youtube'] : 'carschrotter98';
            $youtube = '<h1>New Movies from <input type="text" name="youtube" id="youtube" value="'.$user.'" /></h1>';
            $youtube .= '<ul id="youtube">';
            if ($nrt->execute()) {
                foreach ($nrt as $video) {
                    $cleanHTML = Helper::undoYouTubeMarkupCrimes($video->description);
                    $youtube .= '<li>' . $cleanHTML . '</li>';
                }
            }
            $youtube .= '</ul>';
            echo $youtube;
        }
        ?>          

                <br />
                <input type="submit" name="success" id="success" value="refresh"  />
            </form> 
        </div>
    </body>
</html>
