<?php 
    $refreshvalue = 10; //value in seconds to refresh page
    $pagetitle = "NSN ASA NOC Dashboard";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title><?php echo($pagetitle); ?></title>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js">
        </script>
        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
            }
            
            body {
                font-family: sans-serif;
                line-height: 1.3em;
		overflow-x: hidden;
		font-size: 1.2em;
            }
            
            table {
                border-collapse: collapse;
                width: 100%;
            }
            
            td {
                padding: .1em 1em;
            }
            
            h1 {
                display: inline-block;
                margin-left: 10px;
            }
            
            .head {
                background: lightGray;
                color: black;
                text-align: left;
            }
            
            .head th {
                border-right: 1px solid #888;
                padding: .2em 10px;
            }
            
            .critical {
                background: #b40000;
                color: white;
                font-size: 1.4em;
                line-height: 1.2em;
            }
            
            .critical td {
                border-bottom: 2px solid #7f0000;
                border-right: 2px solid #7f0000;
                padding: .1em 10px;
            }
            
            .warning {
                background: yellow;
                color: black;
                font-size: 1em;
            }
            .warning td{
                border-bottom: 1px solid #bdbf00;
                border-right: 1px solid #bdbf00;
            }
            .statusinfo {
                font-size: 14px !important;
            }
            #nagios_placeholder {
            }
            #loading {
                background: transparent url(throbber.gif) no-repeat center center;
                width: 214px;
                height: 13px;
                display: inline-block;
            }
            #refreshing {
                color: gray;
                display: inline-block;
                font-family: monospace;
            }
            #refreshing_countdown {
                display: inline-block;
                width: 15px;
                text-align: center;
            }
            #refreshing, #loading, h1 {
                line-height: 50px;
                font-size: 1em;
            }
        </style>
    </head>
    <body>
        <script type="text/javascript">

            var placeHolder,
            refreshValue = <?php print $refreshvalue; ?>;
            
            $().ready(function(){
                placeHolder = $("#nagios_placeholder");
                updateNagiosData(placeHolder);
                window.setInterval(updateCountDown, 1000);
            });
            
            function updateNagiosData(block){
                $("#loading").fadeIn(200);
                block.load("nagios_get.php", function(response){
                    $(this).empty();
                    $(this).html(response);
                    $("#loading").fadeOut(200);
                });
            }
            
            function updateCountDown(){
                var countdown = $("#refreshing_countdown"); 
                var remaining = parseInt(countdown.text());
                if(remaining == 0){
                    updateNagiosData(placeHolder);
                    countdown.text(refreshValue);
                }
                else {
                    countdown.text(remaining - 1);
                }
            }
            
        </script>
	<div id="nagios_placeholder"></div>
	<p id="refreshing">Refresh in <span id="refreshing_countdown"><?php print $refreshvalue; ?></span> seconds</p>
        <div id="loading"></div>
    </body>
</html>
