
<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php foreach ($output->css_files as $file): ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
        <?php endforeach; ?>
        <?php foreach ($output->js_files as $file): ?>
            <script src="<?php echo $file; ?>"></script>
        <?php endforeach; ?>

        <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap.css"); ?>">

        <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url("assets/js/bootstrap.js"); ?>"></script>

        <style type='text/css'>
            body {
                font: 11px 'Lucida Grande', Tahoma, Verdana, sans-serif;
                color: #404040;

            }
            a.disabled-link,
            a.disabled-link:visited ,
            a.disabled-link:active,
            a.disabled-link:hover {
                background-color:#d9d9d9 !important;
                color:#aaa !important;
            }
            .sf-menu{
                top: 10px;
                left: 160px;
                position:absolute;
            }
            .dropdown{
                top: -100px;
                left: 160px;
                position:relative;
            }
            #footer{
                clear: both;
                height: 10%;
                border-top: 1px solid #cccccc;
                padding: 10px;
                margin: 0;
            }

            .pull-right {
                float: right;
            }
            .nav-menu {
                background-color: #eee; /* show a solid color for older browsers */
                background-image: -moz-linear-gradient(#fff, #eee);
                background-image: -o-linear-gradient(#fff, #eee);
                background-image: -webkit-linear-gradient(#fff, #eee);
                clear: both;
                display: block;
                float: left;
                margin: 0;
                width: 80%;
                font-size: 11px;
            }
            .nav-menu ul {
                list-style: none;
                margin: 0;
                padding-left: 0;
            }

            .nav-menu > ul > li.has-subpages > a, .nav-menu > ul > li.has-subpages > .menu-item {
                position: relative;
            }
            .nav-menu > ul > li.has-subpages > a:after, .nav-menu > ul > li.has-subpages > .menu-item:after { /* adding arrow "to bottom" */
                content: "";
                position: absolute;
                display: block;
                right: 2px;
                top: 50%;
                margin-top: -2px;
                opacity: 0.5;
                width: 0;
                height: 0;
                border-left: 4px solid transparent; /* magic triangle */
                border-right: 4px solid transparent;
                border-top: 4px solid #555;
            }

            .nav-menu > ul li.has-subpages li.has-subpages > a, .nav-menu > ul li.has-subpages li.has-subpages > .menu-item {
                position: relative;
            }

            .nav-menu > ul li.has-subpages li.has-subpages > a:after, .nav-menu > ul li.has-subpages li.has-subpages > .menu-item:after { /* adding arrow "to right" */
                content: "";
                position: absolute;
                display: block;
                right: 2px;
                top: 50%;
                margin-top: -2px;
                opacity: 0.5;
                width: 0;
                height: 0;
                border-top: 4px solid transparent; /* magic triangle */
                border-bottom: 4px solid transparent;
                border-left: 4px solid #555;
            }
            .nav-menu > ul.pull-right li.has-subpages li.has-subpages > a:after, .nav-menu > ul.pull-right li.has-subpages li.has-subpages > .menu-item:after { /* adding arrow "to left" */
                right: auto;
                left: 2px;
                border-top: 4px solid transparent; /* magic triangle */
                border-bottom: 4px solid transparent;
                border-right: 4px solid #555;
                border-left: 0;
            }

            .nav-menu > ul > li {
                border-right: 1px dotted #ddd;
                border-bottom: 1px dotted #ddd;
            }
            .nav-menu > ul > li:last-child {
                border-right: 0;
            }
            .nav-menu li {
                float: left;
                position: relative;
            }
            .nav-menu a, .nav-menu .menu-item {
                color: #222;
                display: block;
                line-height: 3.333em;
                padding: 0 1.2125em;
                text-decoration: none;
            }
            .nav-menu ul ul {
                border-left: 1px dotted #ddd;
                display: none;
                float: left;
                margin: 0;
                position: absolute;
                top: 3.333em;
                left: 0;
                width: 220px;
                z-index: 99999;
            }
            .nav-menu ul ul ul {
                left: 100%;
                top: 0;
            }
            .nav-menu ul.pull-right ul {
                left: auto;
                right: 0;
            }
            .nav-menu ul.pull-right ul ul {
                left: -100%;
                top: 0;
            }
            .nav-menu ul ul a {
                background: #f9f9f9;
                border-bottom: 1px dotted #ddd;
                color: #222;
                font-weight: normal;
                height: auto;
                line-height: 1.4em;
                padding: 10px 10px;
                width: 200px;
            }

            .nav-menu li.current_page_item > a, .nav-menu li.current-page-item > a {
                background-color: #ccc;
            }
            .nav-menu li.current_page_ancestor > a, .nav-menu li.current-page-ancestor > a, .nav-menu li.current_page_ancestor > .menu-item, .nav-menu li.current-page-ancestor > .menu-item {
                background-color: #eee;
            }

            .nav-menu li:hover > a,
            /*.nav-menu ul ul :hover > a,*/
            .nav-menu a:focus {
                background: #ddd;
            }
            /*.nav-menu li:hover > a,
            .nav-menu a:focus {
                background-color: #e5e5e5;
                color: #373737;
            }*/
            .nav-menu ul li:hover > ul {
                display: block;
            }
            .nav-menu .current-menu-item > a,
            .nav-menu .current-menu-ancestor > a,
            .nav-menu .current_page_item > a,
            .nav-menu .current-page-item > a,
            .nav-menu .current_page_ancestor > a,
            .nav-menu .current-page-ancestor > a {
                font-weight: bold;
            }

        </style>

    </head>
    <body>

            <div id="header">
                <a href="/home">
                    <img src="<?php echo base_url('img/sos_femmes_logo.jpg') ?>" alt="SOS Femmes 93">
                </a>
                <?php echo $header; ?>
            </div>
            <div id="main">
                <?php echo $output->output; ?>
            </div>
            <div id="footer">
                <p> <img src="<?php echo base_url('img/logo.jpg') ?>" height="30" width="30" alt="Efthymios Pavlidis"></p>
            </div>

    </body>

</html>
