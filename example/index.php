<?php

namespace mnhcc\yql\example;

use mnhcc\yql\classes as yql,
    \example\Helper as Helper;
{
    ini_set('xdebug.var_display_max_children', -1);
    ini_set('xdebug.var_display_max_depth', -1);

    /**
     * display youtube uploads
     * @param string $result the rss description result
     * @param string $user the username
     */
    function youtube_uploads($result, $user) {
        $youtube = '<h1>New Movies from ' . $user . '</h1>';
        $youtube .= '<ul id="youtube_uploads" class="youtube_block">';
        foreach ($result as $video) {
            $cleanHTML = Helper::undoYouTubeMarkupCrimes($video->description);
            $youtube .= '<li>' . $cleanHTML . '</li>';
        }
        $youtube .= '</ul>';
        echo $youtube;
    }

    /**
     * display youtube new subscription
     * @param string $result the rss description result
     * @param string $user the username
     */
    function youtube_newsubscriptionvideos($result, $user) {
        $youtube = '<h1>News from ' . $user . '</h1>';
        $youtube .= '<ul id="youtube_newsubscriptionvideos" class="youtube_block">';
        foreach ($result as $video) {
            $cleanHTML = Helper::undoYouTubeMarkupCrimes($video->description);
            $youtube .= '<li>' . $cleanHTML . '</li>';
        }
        $youtube .= '</ul>';
        echo $youtube;
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Example for php_yql_class</title>
            <link rel="stylesheet/less" type="text/css" href="less/bootstrap.less">
            <script src="assets/js/less-1.4.1.min.js" type="text/javascript"></script>
            <script type="text/javascript">less.watch();</script>
            <style>
                ul.youtube_block li {
                    list-style: none;
                    border: 1px solid #000;
                    padding: 5px;
                    margin-bottom: 1em;
                }
                ul.youtube_block .youtube.col_0 {
                    float: left;
                }

                .youtube.col_0 div {
                    border-radius: 4px;
                }

                .youtube.col_0 div a img {
                    padding: 4px;
                    line-height: 1.428571429;
                    background-color: #ffffff;
                    border: 1px solid #dddddd;
                    border-radius: 4px;
                    -webkit-transition: all 0.2s ease-in-out;
                    transition: all 0.2s ease-in-out;
                }

            </style>
        </head>
        <body>
            <div>
                <form>
                    <?php
                    require_once './initial.php';
                    $yql = new yql\YQL();
                    $statement = 'select description from rss(5) 
    where url="http://gdata.youtube.com/feeds/base/users/:YOUTUBENAME/uploads\\?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile";';

                    $nrt = $yql->prepare($statement, yql\YQL::QUERY_TYPE_MULTI);
                    $nrt->addToQery('select description from rss(10) 
    where url="http://gdata.youtube.com/feeds/base/users/:YOUTUBENAME/newsubscriptionvideos\\?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile";');
                    $user = '';
                    $nrt->bindParam(':YOUTUBENAME', $user);

                    $user = (isset($_REQUEST['youtube'])) ? $_REQUEST['youtube'] : 'carschrotter98';

                    if ($nrt->execute()) {
                        foreach ($nrt as $queryindex => $result) {
                            if ($queryindex == 0) {
                                youtube_uploads($result, $user);
                            }
                            if ($queryindex == 1) {
                                youtube_newsubscriptionvideos($result, $user);
                            }
                        }
                    }
                }
                ?>          

                <br />
                See state for: <input type="text" name="youtube" id="youtube" value="<?= $user ?>" />
                <input type="submit" name="success" id="success" value="refresh"  />
            </form> 
        </div>
    </body>
</html>
