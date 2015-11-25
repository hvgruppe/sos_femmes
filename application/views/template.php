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

        <style type='text/css'>


            .container {
                width: 100%;
            }
            .nav .dropdown{
                top: -100px;
                left: 160px;
                position:relative;
            }

            .dropdown-submenu {
                position: relative;
            }

            .dropdown-submenu>.dropdown-menu {
                top: 0;
                left: 100%;
                margin-top: -6px;
                margin-left: -1px;
                -webkit-border-radius: 0 6px 6px 6px;
                -moz-border-radius: 0 6px 6px;
                border-radius: 0 6px 6px 6px;
            }

            .dropdown-submenu:hover>.dropdown-menu {
                display: block;
            }

            .dropdown-submenu>a:after {
                display: block;
                content: " ";
                float: right;
                width: 0;
                height: 0;
                border-color: transparent;
                border-style: solid;
                border-width: 5px 0 5px 5px;
                border-left-color: #ccc;
                margin-top: 5px;
                margin-right: -10px;
            }

            .dropdown-submenu:hover>a:after {
                border-left-color: #fff;
            }

            .dropdown-submenu.pull-left {
                float: none;
            }

            .dropdown-submenu.pull-left>.dropdown-menu {
                left: -100%;
                margin-left: 10px;
                -webkit-border-radius: 6px 0 6px 6px;
                -moz-border-radius: 6px 0 6px 6px;
                border-radius: 6px 0 6px 6px;
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
